<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\Institution;
use App\Models\Location;
use App\Models\ProcurementType;
use App\Models\ScheduleScenario;
use App\Services\GeneticSchedulerService;
use App\Services\ScheduleService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleGenerator extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Kegiatan';

    protected static ?string $title = 'Spreadsheet Penjadwalan Event';

    protected static ?string $navigationLabel = 'Generator Penjadwalan';

    protected string $view = 'filament.pages.schedule-generator';

    // Header Form Data
    public string $eventName = '';

    public ?int $formationYear = null;

    public ?int $procurementTypeId = null;

    public string $status = 'draft';

    public ?string $description = '';

    // Spreadsheet Rows Input
    public array $items = [];

    // Generated Schedule Matrix Result
    public array $generatedResults = [];

    public bool $isGenerated = false;

    // View Mode: 'matrix' or 'calendar'
    public string $activeView = 'matrix';

    // Scenario / Draft Modal and State
    public bool $showSaveScenarioModal = false;

    public bool $showLoadScenarioModal = false;

    public string $scenarioName = '';

    public ?string $scenarioDescription = '';

    public ?int $selectedScenarioId = null;

    // Data Options
    public array $institutionsOptions = [];

    public array $locationsOptions = [];

    public array $procurementTypesOptions = [];

    public array $employeesOptions = [];

    // Employee pools by role (for filtered selects)
    public array $koordinatorOptions = [];

    public array $itOptions = [];

    public array $pengawasOptions = [];

    // GA Recommendation tracking
    public bool $isGaApplied = false;

    public array $gaWarnings = [];

    public function mount(): void
    {
        $this->formationYear = (int) date('Y');
        $this->institutionsOptions = Institution::orderBy('name')->pluck('name', 'id')->toArray();
        $this->locationsOptions = Location::orderBy('name')->pluck('name', 'id')->toArray();
        $this->procurementTypesOptions = ProcurementType::orderBy('name')->pluck('name', 'id')->toArray();
        $this->employeesOptions = Employee::orderBy('name')->pluck('name', 'id')->toArray();

        // Build role-filtered employee options
        $this->buildRoleOptions();

        // Add 1 default row for quick start
        $this->addRow();
    }

    /**
     * Build employee options filtered by role from the status JSON field.
     */
    protected function buildRoleOptions(): void
    {
        $employees = Employee::orderBy('name')->get();

        $this->koordinatorOptions = [];
        $this->itOptions = [];
        $this->pengawasOptions = [];

        foreach ($employees as $emp) {
            $roles = is_array($emp->status) ? $emp->status : [];
            foreach ($roles as $role) {
                $normalized = strtolower(trim($role));
                if ($normalized === 'koordinator') {
                    $this->koordinatorOptions[$emp->id] = $emp->name;
                }
                if ($normalized === 'it') {
                    $this->itOptions[$emp->id] = $emp->name;
                }
                if ($normalized === 'pengawas') {
                    $this->pengawasOptions[$emp->id] = $emp->name;
                }
            }
        }
    }

    public function addRow(): void
    {
        $this->items[] = [
            'id' => uniqid(),
            'institution_id' => null,
            'location_id' => null,
            'koordinator_ids' => [],
            'it_ids' => [],
            'pengawas_ids' => [],
            'pc_capacity' => 100,
            'room_count' => 1,
            'participants_count' => 500,
            'start_date' => now()->format('Y-m-d'),
            'sessions_per_day' => 4,
            'has_opening_day' => false,
            'is_ga_recommendation' => false,
        ];

        $this->isGenerated = false;
        $this->isGaApplied = false;
    }

    public function removeRow(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->isGenerated = false;
            $this->isGaApplied = false;
        } else {
            Notification::make()
                ->warning()
                ->title('Minimal 1 baris spreadsheet harus diisi.')
                ->send();
        }
    }

    // Modal Petugas State
    public ?int $activeOfficerModalIndex = null;

    public bool $showOfficerModal = false;

    public array $modalKoordinatorIds = [];

    public array $modalItIds = [];

    public array $modalPengawasIds = [];

    public array $modalRequirements = [];

    public function openOfficerModal(int $index): void
    {
        $this->activeOfficerModalIndex = $index;
        $item = $this->items[$index] ?? [];

        $this->modalKoordinatorIds = array_values(array_unique(array_map('intval', array_filter((array) ($item['koordinator_ids'] ?? [])))));
        $this->modalItIds = array_values(array_unique(array_map('intval', array_filter((array) ($item['it_ids'] ?? [])))));
        $this->modalPengawasIds = array_values(array_unique(array_map('intval', array_filter((array) ($item['pengawas_ids'] ?? [])))));

        // Calculate requirements for display
        $gaService = app(GeneticSchedulerService::class);
        $this->modalRequirements = $gaService->calculateRequirements($item);

        $this->showOfficerModal = true;
    }

    public function saveOfficerModal(): void
    {
        if ($this->activeOfficerModalIndex !== null && isset($this->items[$this->activeOfficerModalIndex])) {
            $kIds = array_values(array_unique(array_map('intval', array_filter((array) $this->modalKoordinatorIds))));
            $iIds = array_values(array_unique(array_map('intval', array_filter((array) $this->modalItIds))));
            $pIds = array_values(array_unique(array_map('intval', array_filter((array) $this->modalPengawasIds))));

            $this->items[$this->activeOfficerModalIndex]['koordinator_ids'] = $kIds;
            $this->items[$this->activeOfficerModalIndex]['it_ids'] = $iIds;
            $this->items[$this->activeOfficerModalIndex]['pengawas_ids'] = $pIds;

            // Mark as manually edited (no longer pure GA recommendation)
            $this->items[$this->activeOfficerModalIndex]['is_ga_recommendation'] = false;

            // If already generated, sync directly to generatedResults as well
            if (isset($this->generatedResults[$this->activeOfficerModalIndex])) {
                $koordinatorNames = array_map(fn ($id) => $this->employeesOptions[$id] ?? 'Pegawai #' . $id, $kIds);
                $itNames = array_map(fn ($id) => $this->employeesOptions[$id] ?? 'Pegawai #' . $id, $iIds);
                $pengawasNames = array_map(fn ($id) => $this->employeesOptions[$id] ?? 'Pegawai #' . $id, $pIds);

                $this->generatedResults[$this->activeOfficerModalIndex]['koordinator_ids'] = $kIds;
                $this->generatedResults[$this->activeOfficerModalIndex]['it_ids'] = $iIds;
                $this->generatedResults[$this->activeOfficerModalIndex]['pengawas_ids'] = $pIds;
                $this->generatedResults[$this->activeOfficerModalIndex]['koordinator_names'] = implode(', ', $koordinatorNames);
                $this->generatedResults[$this->activeOfficerModalIndex]['it_names'] = implode(', ', $itNames);
                $this->generatedResults[$this->activeOfficerModalIndex]['pengawas_names'] = implode(', ', $pengawasNames);
                $this->generatedResults[$this->activeOfficerModalIndex]['is_ga_recommendation'] = false;
            }
        }

        $this->closeOfficerModal();
    }

    public function closeOfficerModal(): void
    {
        $this->showOfficerModal = false;
        $this->activeOfficerModalIndex = null;
        $this->modalKoordinatorIds = [];
        $this->modalItIds = [];
        $this->modalPengawasIds = [];
        $this->modalRequirements = [];
    }

    /**
     * Auto-assign officers using Genetic Algorithm.
     */
    public function autoAssignOfficers(): void
    {
        // Validate minimum required fields before running GA
        $hasErrors = false;
        foreach ($this->items as $idx => $item) {
            if (empty($item['pc_capacity']) || (int) $item['pc_capacity'] < 1) {
                $hasErrors = true;
            }
            if (empty($item['start_date'])) {
                $hasErrors = true;
            }
            if (empty($item['location_id'])) {
                $hasErrors = true;
            }
        }

        if ($hasErrors) {
            Notification::make()
                ->warning()
                ->title('Data Belum Lengkap')
                ->body('Pastikan setiap baris memiliki Tilok, Kapasitas PC, dan Tanggal Mulai sebelum menjalankan Auto-Assign.')
                ->send();

            return;
        }

        $employees = Employee::all();

        if ($employees->isEmpty()) {
            Notification::make()
                ->danger()
                ->title('Tidak Ada Data Pegawai')
                ->body('Silakan tambahkan data pegawai terlebih dahulu sebelum menjalankan Auto-Assign.')
                ->send();

            return;
        }

        // Pre-calculate schedules for accurate date ranges
        $scheduleService = app(ScheduleService::class);
        $groupedByLocation = [];
        foreach ($this->items as $index => $item) {
            $locId = $item['location_id'];
            $groupedByLocation[$locId][] = array_merge($item, ['index' => $index]);
        }

        $scheduleCalculations = [];
        foreach ($groupedByLocation as $locationId => $locationItems) {
            $calculated = $scheduleService->calculateGroupedLocationSchedule($locationItems);
            foreach ($calculated as $c) {
                $scheduleCalculations[$c['index']] = $c['calculation'];
            }
        }

        // Run Genetic Algorithm
        $gaService = app(GeneticSchedulerService::class);
        $result = $gaService->run($this->items, $employees, $scheduleCalculations);

        if (empty($result['assignments'])) {
            Notification::make()
                ->warning()
                ->title('GA Tidak Menghasilkan Rekomendasi')
                ->body('Tidak ada petugas yang tersedia untuk di-assign.')
                ->send();

            return;
        }

        // Apply GA recommendations to items
        foreach ($result['assignments'] as $idx => $assignment) {
            if (isset($this->items[$idx])) {
                $this->items[$idx]['koordinator_ids'] = $assignment['koordinator_ids'];
                $this->items[$idx]['it_ids'] = $assignment['it_ids'];
                $this->items[$idx]['pengawas_ids'] = $assignment['pengawas_ids'];
                $this->items[$idx]['is_ga_recommendation'] = true;
            }
        }

        $this->isGaApplied = true;
        $this->gaWarnings = $result['warnings'] ?? [];
        $this->isGenerated = false;

        // Show warnings if any
        if (! empty($result['warnings'])) {
            $warningMessages = [];
            foreach ($result['warnings'] as $idx => $itemWarnings) {
                $locName = $this->locationsOptions[$this->items[$idx]['location_id'] ?? 0] ?? 'Tilok #' . ($idx + 1);
                $warningMessages[] = "Baris #" . ($idx + 1) . " ({$locName}): " . implode(', ', $itemWarnings);
            }

            Notification::make()
                ->warning()
                ->title('⚠️ Petugas Tidak Mencukupi')
                ->body(implode("\n", $warningMessages))
                ->persistent()
                ->send();
        }

        if ($result['has_conflicts']) {
            Notification::make()
                ->warning()
                ->title('⚠️ Masih Ada Bentrokan Jadwal')
                ->body('GA tidak menemukan solusi tanpa bentrokan. Silakan review dan edit manual assignment yang bertabrakan.')
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->success()
                ->title('🧬 Rekomendasi GA Berhasil')
                ->body('Petugas telah di-assign secara otomatis tanpa bentrokan jadwal berdasarkan kapasitas PC dan ruangan ujian.')
                ->send();
        }
    }

    public function updatedItems($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'location_id' && $value) {
            $index = (int) $parts[0];
            $location = Location::with('locationSurvey')->find($value);
            if ($location && $location->locationSurvey) {
                if ($location->locationSurvey->pc_count) {
                    $this->items[$index]['pc_capacity'] = (int) $location->locationSurvey->pc_count;
                }
                if ($location->locationSurvey->room_count) {
                    $this->items[$index]['room_count'] = (int) $location->locationSurvey->room_count;
                }
            }
        }

        $this->isGenerated = false;
    }

    /**
     * Collect all officer IDs from the 3-role structure for a given item.
     */
    protected function collectOfficerIds(array $item): array
    {
        return array_values(array_unique(array_filter(array_merge(
            array_map('intval', (array) ($item['koordinator_ids'] ?? [])),
            array_map('intval', (array) ($item['it_ids'] ?? [])),
            array_map('intval', (array) ($item['pengawas_ids'] ?? []))
        ))));
    }

    public function generateSchedules(ScheduleService $service): void
    {
        $this->validate([
            'eventName' => 'required|string|max:255',
            'formationYear' => 'required|integer|min:2020|max:2099',
            'items' => 'required|array|min:1',
            'items.*.institution_id' => 'required|integer',
            'items.*.location_id' => 'required|integer',
            'items.*.participants_count' => 'required|integer|min:1',
            'items.*.start_date' => 'required|date',
            'items.*.pc_capacity' => 'required|integer|min:1',
            'items.*.sessions_per_day' => 'required|integer|min:1|max:10',
        ], [
            'eventName.required' => 'Nama Event harus diisi.',
            'items.*.institution_id.required' => 'Instansi pada setiap baris harus dipilih.',
            'items.*.location_id.required' => 'Titik Lokasi (Tilok) pada setiap baris harus dipilih.',
            'items.*.participants_count.required' => 'Jumlah peserta harus diisi.',
            'items.*.start_date.required' => 'Tanggal mulai harus diisi.',
        ]);

        // Check if any row has missing officers. If so, automatically run GA assignment!
        $needsOfficerAssignment = false;
        foreach ($this->items as $item) {
            if (empty($this->collectOfficerIds($item))) {
                $needsOfficerAssignment = true;
                break;
            }
        }

        // Group items by location_id to calculate real start & end dates per item
        $groupedByLocation = [];
        foreach ($this->items as $index => $item) {
            $locId = $item['location_id'];
            $groupedByLocation[$locId][] = array_merge($item, ['index' => $index]);
        }

        // Calculate schedule dates
        $scheduleCalculations = [];
        foreach ($groupedByLocation as $locationId => $locationItems) {
            $calculated = $service->calculateGroupedLocationSchedule($locationItems);
            foreach ($calculated as $c) {
                $scheduleCalculations[$c['index']] = $c['calculation'];
            }
        }

        if ($needsOfficerAssignment) {
            $employees = Employee::all();
            if ($employees->isNotEmpty()) {
                $gaService = app(GeneticSchedulerService::class);

                // ===================================================================
                // Penugasan petugas per TILOK (lokasi), bukan per instansi.
                // Jika beberapa instansi berada di tilok yang sama, mereka berbagi
                // tim yang sama (Koordinator, IT, Pengawas) agar lebih efisien.
                // ===================================================================

                // 1. Build representative items per location for GA
                $locationRepresentatives = []; // location_id => representative item + merged capacity
                $locationItemIndices = [];     // location_id => [item indices]

                foreach ($this->items as $idx => $item) {
                    $locId = $item['location_id'];
                    $locationItemIndices[$locId][] = $idx;

                    if (!isset($locationRepresentatives[$locId])) {
                        $locationRepresentatives[$locId] = $item;
                        $locationRepresentatives[$locId]['_ga_index'] = $locId; // Track by location
                    } else {
                        // Merge: use max PC capacity (shared infra), sum participants
                        $locationRepresentatives[$locId]['participants_count'] =
                            (int) ($locationRepresentatives[$locId]['participants_count'] ?? 0)
                            + (int) ($item['participants_count'] ?? 0);
                        // Keep the highest pc_capacity and room_count (shared physical infrastructure)
                        $locationRepresentatives[$locId]['pc_capacity'] = max(
                            (int) ($locationRepresentatives[$locId]['pc_capacity'] ?? 0),
                            (int) ($item['pc_capacity'] ?? 0)
                        );
                        $locationRepresentatives[$locId]['room_count'] = max(
                            (int) ($locationRepresentatives[$locId]['room_count'] ?? 0),
                            (int) ($item['room_count'] ?? 0)
                        );
                        // Use earliest start_date
                        if (($item['start_date'] ?? '') < ($locationRepresentatives[$locId]['start_date'] ?? '')) {
                            $locationRepresentatives[$locId]['start_date'] = $item['start_date'];
                        }
                    }
                }

                // 2. Run GA on representative items (1 per location)
                $gaItems = array_values($locationRepresentatives);
                $gaLocationIds = array_keys($locationRepresentatives);

                // Build schedule calculations for GA representative items
                $gaScheduleCalcs = [];
                foreach ($gaItems as $gaIdx => $gaItem) {
                    $locId = $gaLocationIds[$gaIdx];
                    // Use the first item's schedule calculation as representative
                    $firstItemIdx = $locationItemIndices[$locId][0];
                    if (isset($scheduleCalculations[$firstItemIdx])) {
                        $gaScheduleCalcs[$gaIdx] = $scheduleCalculations[$firstItemIdx];
                    }
                }

                $gaResult = $gaService->run($gaItems, $employees, $gaScheduleCalcs);

                // 3. Propagate GA assignments to ALL items sharing each location
                if (!empty($gaResult['assignments'])) {
                    foreach ($gaResult['assignments'] as $gaIdx => $assignment) {
                        $locId = $gaLocationIds[$gaIdx] ?? null;
                        if ($locId === null) continue;

                        // Apply the same officer set to every item at this location
                        foreach ($locationItemIndices[$locId] as $itemIdx) {
                            $this->items[$itemIdx]['koordinator_ids'] = $assignment['koordinator_ids'];
                            $this->items[$itemIdx]['it_ids'] = $assignment['it_ids'];
                            $this->items[$itemIdx]['pengawas_ids'] = $assignment['pengawas_ids'];
                            $this->items[$itemIdx]['is_ga_recommendation'] = true;
                        }
                    }
                    $this->isGaApplied = true;
                    $this->gaWarnings = $gaResult['warnings'] ?? [];
                }
            }
        }

        // Check assigned employees overlap across different locations
        $assignedEmployeesByDate = []; // date => [ employee_id => location info ]
        foreach ($this->items as $index => $item) {
            $calc = $scheduleCalculations[$index] ?? null;
            if (! $calc) {
                continue;
            }

            $locId = $item['location_id'];
            $locName = $this->locationsOptions[$locId] ?? 'Tilok #' . $locId;
            $rowNum = $index + 1;

            $allRowEmpIds = $this->collectOfficerIds($item);

            foreach ($calc['days'] as $day) {
                $date = $day['date'];
                foreach ($allRowEmpIds as $empId) {
                    if (! $empId) {
                        continue;
                    }

                    if (isset($assignedEmployeesByDate[$date][$empId])) {
                        $prev = $assignedEmployeesByDate[$date][$empId];
                        if ($prev['location_id'] !== $locId) {
                            $empName = $this->employeesOptions[$empId] ?? 'Pegawai #' . $empId;
                            Notification::make()
                                ->danger()
                                ->title('Bentrokan Penugasan Pegawai!')
                                ->body("Pegawai <strong>{$empName}</strong> bertugas di dua Tilok berbeda pada tanggal <strong>" . \Carbon\Carbon::parse($date)->format('d M Y') . "</strong> (Baris #{$prev['row']} [{$prev['location_name']}] dan Baris #{$rowNum} [{$locName}]).")
                                ->persistent()
                                ->send();

                            return;
                        }
                    } else {
                        $assignedEmployeesByDate[$date][$empId] = [
                            'location_id' => $locId,
                            'location_name' => $locName,
                            'row' => $rowNum,
                        ];
                    }
                }
            }
        }

        $results = array_fill(0, count($this->items), null);

        foreach ($groupedByLocation as $locationId => $locationItems) {
            $calculatedItems = $service->calculateGroupedLocationSchedule($locationItems);
            foreach ($calculatedItems as $calcItem) {
                $idx = $calcItem['index'];
                $item = $this->items[$idx];

                $instName = $this->institutionsOptions[$item['institution_id']] ?? 'Instansi #' . $item['institution_id'];
                $locName = $this->locationsOptions[$item['location_id']] ?? 'Tilok #' . $item['location_id'];

                // Build officer names by role
                $koordinatorNames = array_map(fn ($id) => $this->employeesOptions[$id] ?? 'Pegawai #' . $id, array_filter((array) ($item['koordinator_ids'] ?? [])));
                $itNames = array_map(fn ($id) => $this->employeesOptions[$id] ?? 'Pegawai #' . $id, array_filter((array) ($item['it_ids'] ?? [])));
                $pengawasNames = array_map(fn ($id) => $this->employeesOptions[$id] ?? 'Pegawai #' . $id, array_filter((array) ($item['pengawas_ids'] ?? [])));

                $results[$idx] = [
                    'index' => $idx,
                    'institution_id' => $item['institution_id'],
                    'institution_name' => $instName,
                    'location_id' => $item['location_id'],
                    'location_name' => $locName,
                    'koordinator_ids' => array_values(array_filter((array) ($item['koordinator_ids'] ?? []))),
                    'it_ids' => array_values(array_filter((array) ($item['it_ids'] ?? []))),
                    'pengawas_ids' => array_values(array_filter((array) ($item['pengawas_ids'] ?? []))),
                    'koordinator_names' => implode(', ', $koordinatorNames),
                    'it_names' => implode(', ', $itNames),
                    'pengawas_names' => implode(', ', $pengawasNames),
                    'is_ga_recommendation' => $item['is_ga_recommendation'] ?? false,
                    'calculation' => $calcItem['calculation'],
                ];
            }
        }

        ksort($results);
        $this->generatedResults = array_values($results);
        $this->isGenerated = true;

        Notification::make()
            ->success()
            ->title('Berhasil Meng-generate Penjadwalan')
            ->body('Penjadwalan peserta per hari dan sesi berhasil dihitung.')
            ->send();
    }

    public function updateGeneratedSessionQuota(int $resultIndex, int $dayIndex, string $sessionKey, int $value): void
    {
        if (isset($this->generatedResults[$resultIndex]['calculation']['days'][$dayIndex]['sessions'][$sessionKey])) {
            $this->generatedResults[$resultIndex]['calculation']['days'][$dayIndex]['sessions'][$sessionKey] = max($value, 0);

            // Recalculate day_total
            $daySessions = $this->generatedResults[$resultIndex]['calculation']['days'][$dayIndex]['sessions'];
            $this->generatedResults[$resultIndex]['calculation']['days'][$dayIndex]['day_total'] = array_sum($daySessions);

            // Recalculate total scheduled
            $allDays = $this->generatedResults[$resultIndex]['calculation']['days'];
            $newTotal = array_sum(array_column($allDays, 'day_total'));
            $this->generatedResults[$resultIndex]['calculation']['total_participants'] = $newTotal;
        }
    }

    public function saveSchedules(ScheduleService $service): void
    {
        if (! $this->isGenerated || empty($this->generatedResults)) {
            Notification::make()
                ->danger()
                ->title('Silakan Generate Penjadwalan Terlebih Dahulu')
                ->send();

            return;
        }

        try {
            $eventData = [
                'name' => $this->eventName,
                'formation_year' => $this->formationYear,
                'procurement_type_id' => $this->procurementTypeId,
                'status' => $this->status,
                'description' => $this->description,
            ];

            $itemsWithCalculatedDates = $this->items;
            foreach ($itemsWithCalculatedDates as $idx => &$item) {
                if (isset($this->generatedResults[$idx]['calculation']['end_date'])) {
                    $item['end_date'] = $this->generatedResults[$idx]['calculation']['end_date'];
                }
            }

            $createdEvents = $service->saveEventWithSchedules($eventData, $itemsWithCalculatedDates);
            $count = count($createdEvents);
            $eventNames = implode(', ', array_map(fn($e) => '"' . $e->name . '"', $createdEvents));

            Notification::make()
                ->success()
                ->title('Penjadwalan Berhasil Disimpan!')
                ->body("Sebanyak {$count} kegiatan ({$eventNames}) telah berhasil dibuat terpisah per titik lokasi ke database utama.")
                ->persistent()
                ->send();

            // Redirect to Events List page in Filament
            $this->redirect(\App\Filament\Resources\Events\EventResource::getUrl('index'));
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Gagal Menyimpan Penjadwalan')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function exportExcel(): StreamedResponse|null
    {
        if (! $this->isGenerated || empty($this->generatedResults)) {
            Notification::make()
                ->danger()
                ->title('Silakan Generate Penjadwalan Terlebih Dahulu')
                ->send();
            return null;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Event');

        // Headers
        $headers = [
            'No', 'Instansi', 'Titik Lokasi', 'Koordinator', 'Tim IT', 'Pengawas', 
            'Total Peserta', 'Total Hari', 'Rentang Tanggal', 'Detail Hari & Sesi'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $row = 2;
        foreach ($this->generatedResults as $index => $res) {
            $calc = $res['calculation'];
            
            $detailHari = [];
            foreach ($calc['days'] as $day) {
                $dateStr = \Carbon\Carbon::parse($day['date'])->format('d/m/Y');
                $sesiStr = [];
                for ($s = 1; $s <= $calc['sessions_per_day']; $s++) {
                    $val = $day['sessions']["session_$s"] ?? 0;
                    $sesiStr[] = "Sesi $s: $val";
                }
                $detailHari[] = $dateStr . ' (' . implode(', ', $sesiStr) . ')';
            }

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $res['institution_name'] ?? '');
            $sheet->setCellValue('C' . $row, $res['location_name'] ?? '');
            $sheet->setCellValue('D' . $row, $res['koordinator_names'] ?? '');
            $sheet->setCellValue('E' . $row, $res['it_names'] ?? '');
            $sheet->setCellValue('F' . $row, $res['pengawas_names'] ?? '');
            $sheet->setCellValue('G' . $row, $calc['total_participants'] ?? 0);
            $sheet->setCellValue('H' . $row, $calc['total_exam_days'] ?? 0);
            $sheet->setCellValue('I' . $row, \Carbon\Carbon::parse($calc['start_date'])->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($calc['end_date'])->format('d/m/Y'));
            $sheet->setCellValue('J' . $row, implode("\n", $detailHari));
            
            $sheet->getStyle('J' . $row)->getAlignment()->setWrapText(true);
            
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, 'Jadwal_Event_' . date('Ymd_His') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ==========================================
    // SCENARIO / DRAFT MANAGEMENT METHODS
    // ==========================================

    public function openSaveScenarioModal(): void
    {
        if (empty($this->items)) {
            Notification::make()
                ->warning()
                ->title('Tidak Ada Data')
                ->body('Tambahkan minimal 1 baris instansi sebelum menyimpan draft skenario.')
                ->send();
            return;
        }

        if (empty($this->scenarioName)) {
            $this->scenarioName = 'Skenario ' . ($this->eventName ? $this->eventName . ' - ' : '') . date('d/m/Y H:i');
        }

        $this->showSaveScenarioModal = true;
    }

    public function closeSaveScenarioModal(): void
    {
        $this->showSaveScenarioModal = false;
        $this->scenarioName = '';
        $this->scenarioDescription = '';
    }

    public function saveScenario(): void
    {
        if (empty(trim($this->scenarioName))) {
            Notification::make()
                ->danger()
                ->title('Nama Skenario Wajib Diisi')
                ->send();
            return;
        }

        $totalPeserta = 0;
        $totalTilok = count($this->items);
        foreach ($this->items as $item) {
            $totalPeserta += (int) ($item['participants_count'] ?? 0);
        }

        $summaryMeta = [
            'total_items' => $totalTilok,
            'total_participants' => $totalPeserta,
            'is_generated' => $this->isGenerated,
            'is_ga_applied' => $this->isGaApplied,
            'saved_at' => now()->toDateTimeString(),
        ];

        ScheduleScenario::create([
            'name' => trim($this->scenarioName),
            'event_name' => $this->eventName,
            'formation_year' => $this->formationYear,
            'procurement_type_id' => $this->procurementTypeId,
            'description' => $this->scenarioDescription,
            'items_data' => $this->items,
            'results_data' => $this->generatedResults,
            'summary_meta' => $summaryMeta,
        ]);

        $this->closeSaveScenarioModal();

        Notification::make()
            ->success()
            ->title('Draft Skenario Berhasil Disimpan!')
            ->body('Anda dapat memuat kembali skenario ini kapan saja untuk simulasi atau perbandingan.')
            ->send();
    }

    public function openLoadScenarioModal(): void
    {
        $this->showLoadScenarioModal = true;
    }

    public function closeLoadScenarioModal(): void
    {
        $this->showLoadScenarioModal = false;
    }

    public function getScenariosProperty()
    {
        return ScheduleScenario::with('procurementType')->latest()->get();
    }

    public function loadScenario(int $scenarioId): void
    {
        $scenario = ScheduleScenario::find($scenarioId);
        if (! $scenario) {
            Notification::make()
                ->danger()
                ->title('Skenario Tidak Ditemukan')
                ->send();
            return;
        }

        $this->eventName = $scenario->event_name ?? '';
        $this->formationYear = $scenario->formation_year ?? (int) date('Y');
        $this->procurementTypeId = $scenario->procurement_type_id;
        $this->description = $scenario->description ?? '';
        $this->items = $scenario->items_data ?? [];
        $this->generatedResults = $scenario->results_data ?? [];
        $this->isGenerated = ! empty($this->generatedResults);
        $this->isGaApplied = $scenario->summary_meta['is_ga_applied'] ?? false;

        $this->closeLoadScenarioModal();

        Notification::make()
            ->success()
            ->title("Skenario \"{$scenario->name}\" Berhasil Dimuat")
            ->body('Semua parameter input, matriks sesi, dan penugasan petugas telah dipulihkan.')
            ->send();
    }

    public function setViewMode(string $view): void
    {
        $this->activeView = in_array($view, ['matrix', 'calendar']) ? $view : 'matrix';
    }

    /**
     * Compute visual calendar events grouped by date.
     */
    public function getCalendarDatesProperty(): array
    {
        if (empty($this->generatedResults)) {
            return [];
        }

        $datesMap = [];

        foreach ($this->generatedResults as $rIndex => $res) {
            $calc = $res['calculation'] ?? [];
            $days = $calc['days'] ?? [];

            foreach ($days as $dIndex => $day) {
                $dateKey = $day['date'] ?? null;
                if (! $dateKey) {
                    continue;
                }

                if (! isset($datesMap[$dateKey])) {
                    $datesMap[$dateKey] = [
                        'date' => $dateKey,
                        'day_name' => \Carbon\Carbon::parse($dateKey)->translatedFormat('l'),
                        'formatted_date' => \Carbon\Carbon::parse($dateKey)->translatedFormat('d F Y'),
                        'events' => [],
                    ];
                }

                $datesMap[$dateKey]['events'][] = [
                    'result_index' => $rIndex,
                    'day_index' => $dIndex,
                    'institution_name' => $res['institution_name'] ?? 'Instansi',
                    'location_name' => $res['location_name'] ?? 'Lokasi',
                    'day_number' => $day['day_number'] ?? ($dIndex + 1),
                    'day_total' => $day['day_total'] ?? 0,
                    'sessions' => $day['sessions'] ?? [],
                    'sessions_per_day' => $calc['sessions_per_day'] ?? 4,
                    'koordinator_names' => $res['koordinator_names'] ?? '',
                    'it_names' => $res['it_names'] ?? '',
                    'pengawas_names' => $res['pengawas_names'] ?? '',
                    'is_ga_recommendation' => $res['is_ga_recommendation'] ?? false,
                ];
            }
        }

        // Sort by date key ascending
        ksort($datesMap);

        return array_values($datesMap);
    }

    public function deleteScenario(int $scenarioId): void
    {
        $scenario = ScheduleScenario::find($scenarioId);
        if ($scenario) {
            $name = $scenario->name;
            $scenario->delete();

            Notification::make()
                ->success()
                ->title("Skenario \"{$name}\" Berhasil Dihapus")
                ->send();
        }
    }
}
