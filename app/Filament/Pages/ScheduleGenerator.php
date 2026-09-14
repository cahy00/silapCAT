<?php

namespace App\Filament\Pages;

use App\Models\Institution;
use App\Models\Location;
use App\Models\ProcurementType;
use App\Services\ScheduleService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

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

    // Data Options
    public array $institutionsOptions = [];

    public array $locationsOptions = [];

    public array $procurementTypesOptions = [];

    public array $employeesOptions = [];

    public function mount(): void
    {
        $this->formationYear = (int) date('Y');
        $this->institutionsOptions = Institution::orderBy('name')->pluck('name', 'id')->toArray();
        $this->locationsOptions = Location::orderBy('name')->pluck('name', 'id')->toArray();
        $this->procurementTypesOptions = ProcurementType::orderBy('name')->pluck('name', 'id')->toArray();
        $this->employeesOptions = \App\Models\Employee::orderBy('name')->pluck('name', 'id')->toArray();

        // Add 3 default rows for quick start
        $this->addRow();
        $this->addRow();
        $this->addRow();
    }

    public function addRow(): void
    {
        $this->items[] = [
            'id' => uniqid(),
            'institution_id' => null,
            'location_id' => null,
            'koordinator_id' => null,
            'it_id' => null,
            'pengawas_id' => null,
            'pc_capacity' => 100,
            'participants_count' => 500,
            'start_date' => now()->format('Y-m-d'),
            'sessions_per_day' => 4,
            'has_opening_day' => false,
        ];

        $this->isGenerated = false;
    }

    public function removeRow(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->isGenerated = false;
        } else {
            Notification::make()
                ->warning()
                ->title('Minimal 1 baris spreadsheet harus diisi.')
                ->send();
        }
    }

    public function updatedItems($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'location_id' && $value) {
            $index = (int) $parts[0];
            $location = Location::with('locationSurvey')->find($value);
            if ($location && $location->locationSurvey?->pc_count) {
                $this->items[$index]['pc_capacity'] = (int) $location->locationSurvey->pc_count;
            }
        }

        $this->isGenerated = false;
    }

    public function generateSchedules(ScheduleService $service): void
    {
        $this->validate([
            'eventName' => 'required|string|max:255',
            'formationYear' => 'required|integer|min:2020|max:2099',
            'items' => 'required|array|min:1',
            'items.*.institution_id' => 'required|integer',
            'items.*.location_id' => 'required|integer',
            'items.*.koordinator_id' => 'required|integer',
            'items.*.it_id' => 'required|integer',
            'items.*.pengawas_id' => 'required|integer',
            'items.*.participants_count' => 'required|integer|min:1',
            'items.*.start_date' => 'required|date',
            'items.*.pc_capacity' => 'required|integer|min:1',
            'items.*.sessions_per_day' => 'required|integer|min:1|max:10',
        ], [
            'eventName.required' => 'Nama Event harus diisi.',
            'items.*.institution_id.required' => 'Instansi pada setiap baris harus dipilih.',
            'items.*.location_id.required' => 'Titik Lokasi (Tilok) pada setiap baris harus dipilih.',
            'items.*.koordinator_id.required' => 'Koordinator harus dipilih.',
            'items.*.it_id.required' => 'Tim IT harus dipilih.',
            'items.*.pengawas_id.required' => 'Pengawas harus dipilih.',
            'items.*.participants_count.required' => 'Jumlah peserta harus diisi.',
            'items.*.start_date.required' => 'Tanggal mulai harus diisi.',
        ]);

        // Group items by location_id to calculate real start & end dates per item
        $groupedByLocation = [];
        foreach ($this->items as $index => $item) {
            $locId = $item['location_id'];
            $groupedByLocation[$locId][] = array_merge($item, ['index' => $index]);
        }

        // Validate Pegawai Conflicts across different Tilok on overlapping dates
        $scheduleCalculations = [];
        foreach ($groupedByLocation as $locationId => $locationItems) {
            $calculated = $service->calculateGroupedLocationSchedule($locationItems);
            foreach ($calculated as $c) {
                $scheduleCalculations[$c['index']] = $c['calculation'];
            }
        }

        // Check assigned employees overlap across different locations
        $assignedEmployeesByDate = []; // date => [ location_id => [ employee_ids ] ]
        foreach ($this->items as $index => $item) {
            $calc = $scheduleCalculations[$index] ?? null;
            if (!$calc) continue;

            $locId = $item['location_id'];
            $locName = $this->locationsOptions[$locId] ?? 'Tilok #' . $locId;
            $rowNum = $index + 1;

            $roles = [
                'Koordinator' => $item['koordinator_id'] ?? null,
                'IT' => $item['it_id'] ?? null,
                'Pengawas' => $item['pengawas_id'] ?? null,
            ];

            foreach ($calc['days'] as $day) {
                $date = $day['date'];
                foreach ($roles as $roleName => $empId) {
                    if (!$empId) continue;

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
                            'role' => $roleName,
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
                $koordinatorName = !empty($item['koordinator_id']) ? ($this->employeesOptions[$item['koordinator_id']] ?? null) : null;
                $itName = !empty($item['it_id']) ? ($this->employeesOptions[$item['it_id']] ?? null) : null;
                $pengawasName = !empty($item['pengawas_id']) ? ($this->employeesOptions[$item['pengawas_id']] ?? null) : null;

                $results[$idx] = [
                    'index' => $idx,
                    'institution_id' => $item['institution_id'],
                    'institution_name' => $instName,
                    'location_id' => $item['location_id'],
                    'location_name' => $locName,
                    'koordinator_id' => $item['koordinator_id'] ?? null,
                    'koordinator_name' => $koordinatorName,
                    'it_id' => $item['it_id'] ?? null,
                    'it_name' => $itName,
                    'pengawas_id' => $item['pengawas_id'] ?? null,
                    'pengawas_name' => $pengawasName,
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
        if (!$this->isGenerated || empty($this->generatedResults)) {
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

            $event = $service->saveEventWithSchedules($eventData, $itemsWithCalculatedDates);

            Notification::make()
                ->success()
                ->title('Penjadwalan Event Berhasil Disimpan!')
                ->body('Event "' . $event->name . '" beserta lokasi dan instansi telah berhasil disimpan ke database utama.')
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
}
