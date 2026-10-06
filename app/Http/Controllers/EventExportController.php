<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EventExportController extends Controller
{
    public function monthlyPdf(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        // Validate month and year bounds
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2020 || $year > 2035) {
            $year = now()->year;
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');

        // Query events active or scheduled in that month/year
        $events = Event::with([
            'procurementType.procurementCategory',
            'eventInstitutions.institution',
            'eventLocations.location',
            'eventLocations.eventLocationInstitutions.institution',
            'eventEmployees.employee',
            'reports'
        ])
        ->where(function ($q) use ($month, $year) {
            $q->where(function ($q1) use ($month, $year) {
                $q1->whereMonth('start_date', $month)->whereYear('start_date', $year);
            })
            ->orWhere(function ($q2) use ($month, $year) {
                $q2->whereMonth('end_date', $month)->whereYear('end_date', $year);
            })
            ->orWhere(function ($q3) use ($month, $year) {
                // Event spanning across the month
                $startDate = sprintf('%04d-%02d-01', $year, $month);
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
                $q3->where('start_date', '<=', $endDate)->where('end_date', '>=', $startDate);
            })
            ->orWhereHas('eventLocations', function ($q4) use ($month, $year) {
                $startDate = sprintf('%04d-%02d-01', $year, $month);
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
                $q4->where(function ($lq) use ($month, $year, $startDate, $endDate) {
                    $lq->whereMonth('start_date', $month)->whereYear('start_date', $year)
                       ->orWhereMonth('end_date', $month)->whereYear('end_date', $year)
                       ->orWhere(function ($spanQ) use ($startDate, $endDate) {
                           $spanQ->where('start_date', '<=', $endDate)->where('end_date', '>=', $startDate);
                       });
                });
            })
            ->orWhere(function ($qFallback) use ($month, $year) {
                // Fallback for events with no dates yet created in that month
                $qFallback->whereNull('start_date')
                          ->whereDoesntHave('eventLocations', function ($lq) {
                              $lq->whereNotNull('start_date');
                          })
                          ->whereMonth('created_at', $month)
                          ->whereYear('created_at', $year);
            });
        })
        ->orderBy('start_date', 'asc')
        ->orderBy('created_at', 'desc')
        ->get();

        $pdf = Pdf::loadView('pdf.events-monthly', [
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = sprintf('Laporan_Kegiatan_Bulan_%s_%d.pdf', $monthName, $year);

        return $pdf->stream($filename);
    }

    public function allEventsExcel(Request $request)
    {
        $query = Event::with([
            'procurementType.procurementCategory',
            'eventInstitutions.institution',
            'eventLocations.location',
            'eventLocations.eventLocationInstitutions.institution',
            'eventEmployees.employee',
            'delegations',
            'reports',
            'examScores',
        ]);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $filterMonth = $request->filled('month') && $request->month !== 'all' ? (int) $request->month : null;
        $filterYear = $request->filled('year') && $request->year !== 'all' ? (int) $request->year : null;

        if ($filterMonth && $filterYear) {
            $startDate = sprintf('%04d-%02d-01', $filterYear, $filterMonth);
            $endDate = Carbon::createFromDate($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

            $query->where(function ($q) use ($filterMonth, $filterYear, $startDate, $endDate) {
                $q->where(function ($q1) use ($filterMonth, $filterYear) {
                    $q1->whereMonth('start_date', $filterMonth)->whereYear('start_date', $filterYear);
                })
                ->orWhere(function ($q2) use ($filterMonth, $filterYear) {
                    $q2->whereMonth('end_date', $filterMonth)->whereYear('end_date', $filterYear);
                })
                ->orWhere(function ($q3) use ($startDate, $endDate) {
                    $q3->where('start_date', '<=', $endDate)->where('end_date', '>=', $startDate);
                })
                ->orWhereHas('eventLocations', function ($q4) use ($filterMonth, $filterYear, $startDate, $endDate) {
                    $q4->where(function ($lq) use ($filterMonth, $filterYear, $startDate, $endDate) {
                        $lq->whereMonth('start_date', $filterMonth)->whereYear('start_date', $filterYear)
                           ->orWhereMonth('end_date', $filterMonth)->whereYear('end_date', $filterYear)
                           ->orWhere(function ($spanQ) use ($startDate, $endDate) {
                               $spanQ->where('start_date', '<=', $endDate)->where('end_date', '>=', $startDate);
                           });
                    });
                })
                ->orWhere(function ($qFallback) use ($filterMonth, $filterYear) {
                    $qFallback->where(function ($fb) use ($filterMonth, $filterYear) {
                        $fb->whereNull('start_date')
                           ->whereDoesntHave('eventLocations', function ($lq) {
                               $lq->whereNotNull('start_date');
                           })
                           ->whereMonth('created_at', $filterMonth)
                           ->whereYear('created_at', $filterYear);
                    })
                    ->orWhere(function ($fb2) use ($filterMonth, $filterYear) {
                        $fb2->where('formation_year', (string) $filterYear)
                            ->whereMonth('created_at', $filterMonth);
                    });
                });
            });
        } elseif ($filterMonth) {
            $query->where(function ($q) use ($filterMonth) {
                $q->whereMonth('start_date', $filterMonth)
                  ->orWhereMonth('end_date', $filterMonth)
                  ->orWhereHas('eventLocations', function ($lq) use ($filterMonth) {
                      $lq->whereMonth('start_date', $filterMonth)
                         ->orWhereMonth('end_date', $filterMonth);
                  })
                  ->orWhere(function ($qFallback) use ($filterMonth) {
                      $qFallback->whereNull('start_date')
                                ->whereDoesntHave('eventLocations', function ($lq) {
                                    $lq->whereNotNull('start_date');
                                })
                                ->whereMonth('created_at', $filterMonth);
                  });
            });
        } elseif ($filterYear) {
            $query->where(function ($q) use ($filterYear) {
                $q->where('formation_year', (string) $filterYear)
                  ->orWhereYear('start_date', $filterYear)
                  ->orWhereYear('end_date', $filterYear)
                  ->orWhereHas('eventLocations', function ($lq) use ($filterYear) {
                      $lq->whereYear('start_date', $filterYear)
                         ->orWhereYear('end_date', $filterYear);
                  });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('contract_number', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Event');

        // Title Block
        $sheet->setCellValue('A1', 'MATRIKS DATA SEMUA EVENT & RELASI PELAKSANAAN UJIAN CAT');
        $sheet->mergeCells('A1:Y1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $filterText = 'Total Event: ' . $events->count() . ' Data';
        if ($request->filled('status') && $request->status !== 'all') {
            $filterText .= ' | Status: ' . strtoupper($request->status);
        }
        if ($filterMonth && isset($monthNames[$filterMonth])) {
            $filterText .= ' | Bulan: ' . $monthNames[$filterMonth];
        }
        if ($filterYear) {
            $filterText .= ' | Tahun: ' . $filterYear;
        }
        $filterText .= ' | Tanggal Cetak: ' . now()->translatedFormat('d F Y H:i');

        $sheet->setCellValue('A2', $filterText);
        $sheet->mergeCells('A2:Y2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '4B5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Summary Statistics Box
        $totalLocationsSum = 0;
        $totalParticipantsSum = 0;
        $totalEmployeesSum = 0;
        $totalPresentSum = 0;

        foreach ($events as $ev) {
            $totalLocationsSum += $ev->eventLocations->count();
            $evParticipants = 0;
            foreach ($ev->eventLocations as $el) {
                $evParticipants += $el->eventLocationInstitutions->sum('participants_count');
            }
            if ($evParticipants === 0 && $ev->eventInstitutions->count() > 0) {
                $evParticipants = $ev->eventInstitutions->sum('participants_count');
            }
            if ($evParticipants === 0 && $ev->examScores->count() > 0) {
                $evParticipants = $ev->examScores->count();
            }
            $totalParticipantsSum += $evParticipants;
            $totalEmployeesSum += $ev->eventEmployees->count();
            $totalPresentSum += $ev->reports->sum('present_count');
        }

        $sheet->setCellValue('B4', 'Total Event');
        $sheet->setCellValue('B5', $events->count() . ' Event');
        $sheet->setCellValue('E4', 'Total Titik Lokasi');
        $sheet->setCellValue('E5', $totalLocationsSum . ' Lokasi');
        $sheet->setCellValue('H4', 'Total Target Peserta');
        $sheet->setCellValue('H5', number_format($totalParticipantsSum, 0, ',', '.') . ' Peserta');
        $sheet->setCellValue('K4', 'Total Peserta Hadir');
        $sheet->setCellValue('K5', number_format($totalPresentSum, 0, ',', '.') . ' Peserta');

        $sheet->getStyle('B4:K4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);
        $sheet->getStyle('B5:K5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);

        // Table Headers (25 Columns)
        $headers = [
            'No', 'ID Event', 'Nama Event', 'Tahun Formasi', 'Kategori Pengadaan',
            'Jenis Pengadaan', 'Status', 'Tanggal Mulai', 'Tanggal Selesai',
            'Instansi & Peserta (Relasi)', 'Total Peserta', 'Peserta Hadir', 'Peserta Tidak Hadir',
            'Nilai Tertinggi', 'Nilai Terendah', 'Titik Lokasi (Relasi)', 'Jumlah Lokasi',
            'Koordinator (Relasi)', 'Tim IT (Relasi)', 'Pengawas (Relasi)', 'Total Petugas',
            'Tim Pendamping / Delegasi (Relasi)', 'Total Laporan', 'Total Skor CAT', 'Tanggal Dibuat'
        ];

        foreach ($headers as $colIdx => $header) {
            $colString = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colString . '7', $header);
        }

        $sheet->getStyle('A7:Y7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(28);

        // Data Rows starting Row 8
        $row = 8;
        foreach ($events as $idx => $ev) {
            // Instansi Peserta detail (dikumpulkan dari eventLocationInstitutions, eventInstitutions, atau examScores)
            $institutionMap = []; // [nama_instansi => total_peserta]

            // 1. Dari Titik Lokasi -> eventLocationInstitutions (struktur utama)
            foreach ($ev->eventLocations as $el) {
                foreach ($el->eventLocationInstitutions as $eli) {
                    $name = $eli->institution?->name ?? ($eli->institution_id ? 'Instansi #' . $eli->institution_id : null);
                    if ($name) {
                        $count = (int) ($eli->participants_count ?? 0);
                        $institutionMap[$name] = ($institutionMap[$name] ?? 0) + $count;
                    }
                }
            }

            // 2. Dari relasi langsung eventInstitutions (fallback / legacy)
            foreach ($ev->eventInstitutions as $ei) {
                $name = $ei->institution?->name ?? ($ei->institution_id ? 'Instansi #' . $ei->institution_id : null);
                if ($name) {
                    $count = (int) ($ei->participants_count ?? 0);
                    $institutionMap[$name] = ($institutionMap[$name] ?? 0) + $count;
                }
            }

            // 3. Dari examScores jika instansi belum terdaftar di lokasi
            if (empty($institutionMap) && $ev->examScores->isNotEmpty()) {
                foreach ($ev->examScores as $score) {
                    if (!empty($score->institution)) {
                        $institutionMap[$score->institution] = ($institutionMap[$score->institution] ?? 0) + 1;
                    }
                }
            }

            $instansiList = [];
            foreach ($institutionMap as $name => $count) {
                if ($count > 0) {
                    $instansiList[] = "{$name} (" . number_format($count, 0, ',', '.') . " pes)";
                } else {
                    $instansiList[] = $name;
                }
            }

            // Total Target Participants
            $totalParticipants = 0;
            foreach ($ev->eventLocations as $el) {
                foreach ($el->eventLocationInstitutions as $eli) {
                    $totalParticipants += (int) $eli->participants_count;
                }
            }
            if ($totalParticipants === 0 && $ev->eventInstitutions->count() > 0) {
                $totalParticipants = (int) $ev->eventInstitutions->sum('participants_count');
            }
            if ($totalParticipants === 0 && $ev->examScores->count() > 0) {
                $totalParticipants = (int) $ev->examScores->count();
            }

            // Peserta Hadir & Tidak Hadir dari laporan
            $presentCount = (int) $ev->reports->sum('present_count');
            $absentCount = (int) $ev->reports->sum('absent_count');

            // Nilai Tertinggi & Terendah
            $highestReport = $ev->reports->max('highest_score');
            $highestExam = $ev->examScores->max('cat_score');
            $highestCandidates = array_filter([$highestReport, $highestExam], fn($v) => !is_null($v) && $v > 0);
            $highestScore = !empty($highestCandidates) ? max($highestCandidates) : null;

            $minReportList = $ev->reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0)->pluck('lowest_score')->toArray();
            $minExamList = $ev->examScores->whereNotNull('cat_score')->where('cat_score', '>', 0)->pluck('cat_score')->toArray();
            $allMinScores = array_merge($minReportList, $minExamList);
            $lowestScore = !empty($allMinScores) ? min($allMinScores) : null;

            // Titik Lokasi detail
            $locationList = [];
            foreach ($ev->eventLocations as $el) {
                $locName = $el->location->name ?? 'Lokasi #' . $el->location_id;
                $locParticipants = $el->eventLocationInstitutions->sum('participants_count');
                $details = [];
                if ($el->start_date && $el->end_date) {
                    $details[] = $el->start_date->format('d/m/Y') . ' - ' . $el->end_date->format('d/m/Y');
                }
                if ($locParticipants > 0) {
                    $details[] = number_format($locParticipants, 0, ',', '.') . ' pes';
                }
                if (!empty($details)) {
                    $locName .= ' (' . implode(', ', $details) . ')';
                }
                $locationList[] = $locName;
            }

            // Petugas per Role
            $koordinators = [];
            $itList = [];
            $pengawas = [];

            foreach ($ev->eventEmployees as $ee) {
                $empName = $ee->employee ? $ee->employee->name . ' (' . ($ee->employee->employee_number ?? '-') . ')' : 'Petugas #' . $ee->employee_id;
                $roles = is_array($ee->role) ? $ee->role : (array) $ee->role;
                if (in_array('Koordinator', $roles)) $koordinators[] = $empName;
                if (in_array('IT', $roles)) $itList[] = $empName;
                if (in_array('Pengawas', $roles)) $pengawas[] = $empName;
            }

            // Delegasi
            $delegationList = [];
            foreach ($ev->delegations as $del) {
                $delInfo = $del->name;
                if (!empty($del->position)) $delInfo .= ' - ' . $del->position;
                if (!empty($del->agency)) $delInfo .= ' (' . $del->agency . ')';
                $delegationList[] = $delInfo;
            }

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $ev->id);
            $sheet->setCellValue('C' . $row, $ev->name);
            $sheet->setCellValue('D' . $row, $ev->formation_year ?? '-');
            $sheet->setCellValue('E' . $row, $ev->procurementType->procurementCategory->name ?? '-');
            $sheet->setCellValue('F' . $row, $ev->procurementType->name ?? '-');
            $sheet->setCellValue('G' . $row, strtoupper($ev->status));
            $sheet->setCellValue('H' . $row, $ev->start_date ? $ev->start_date->format('Y-m-d') : '-');
            $sheet->setCellValue('I' . $row, $ev->end_date ? $ev->end_date->format('Y-m-d') : '-');
            $sheet->setCellValue('J' . $row, implode("\n", $instansiList) ?: '-');
            $sheet->setCellValue('K' . $row, $totalParticipants);
            $sheet->setCellValue('L' . $row, $presentCount);
            $sheet->setCellValue('M' . $row, $absentCount);
            $sheet->setCellValue('N' . $row, $highestScore !== null ? number_format($highestScore, 2, ',', '.') : '-');
            $sheet->setCellValue('O' . $row, $lowestScore !== null ? number_format($lowestScore, 2, ',', '.') : '-');
            $sheet->setCellValue('P' . $row, implode("\n", $locationList) ?: '-');
            $sheet->setCellValue('Q' . $row, $ev->eventLocations->count());
            $sheet->setCellValue('R' . $row, implode("\n", $koordinators) ?: '-');
            $sheet->setCellValue('S' . $row, implode("\n", $itList) ?: '-');
            $sheet->setCellValue('T' . $row, implode("\n", $pengawas) ?: '-');
            $sheet->setCellValue('U' . $row, $ev->eventEmployees->count());
            $sheet->setCellValue('V' . $row, implode("\n", $delegationList) ?: '-');
            $sheet->setCellValue('W' . $row, $ev->reports->count());
            $sheet->setCellValue('X' . $row, $ev->examScores->count());
            $sheet->setCellValue('Y' . $row, $ev->created_at ? $ev->created_at->format('Y-m-d H:i') : '-');

            // Alignments & Wrap
            $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $row . ':O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('Q' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('U' . $row . ':Y' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('J' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('P' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('R' . $row . ':T' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('V' . $row)->getAlignment()->setWrapText(true);

            $row++;
        }

        if ($events->count() > 0) {
            $lastRow = $row - 1;
            $sheet->getStyle('A8:Y' . $lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
            ]);
        }

        // Auto-size columns
        foreach (range('A', 'Y') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Matriks_Data_Event_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

