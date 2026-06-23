<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Employee;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(route('template.employee-import'))
                ->openUrlInNewTab(),
            Action::make('import')
                ->label('Import Pegawai')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel (.xlsx)')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->disk('public')
                        ->directory('imports'),
                ])
                ->action(function (array $data) {
                    $filePath = Storage::disk('public')->path($data['file']);
                    
                    try {
                        $spreadsheet = IOFactory::load($filePath);
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray();
                        
                        // Hapus baris header
                        array_shift($rows);
                        
                        $count = 0;
                        foreach ($rows as $row) {
                            $nip = trim($row[0] ?? '');
                            $nama = trim($row[1] ?? '');
                            $jabatan = trim($row[2] ?? '');
                            $statusString = trim($row[3] ?? '');
                            
                            // Abaikan jika NIP atau Nama kosong (atau jika itu adalah baris panduan)
                            if (empty($nip) || empty($nama) || str_contains(strtolower($nip), 'petunjuk') || str_contains(strtolower($nip), 'kolom')) {
                                continue;
                            }
                            
                            // Parse status
                            $statusArray = [];
                            if (!empty($statusString)) {
                                $exploded = explode(',', $statusString);
                                foreach ($exploded as $s) {
                                    $s = trim($s);
                                    if (!empty($s)) {
                                        $statusArray[] = $s;
                                    }
                                }
                            }
                            
                            Employee::updateOrCreate(
                                ['employee_number' => $nip],
                                [
                                    'name' => $nama,
                                    'position' => empty($jabatan) ? null : $jabatan,
                                    'status' => empty($statusArray) ? null : $statusArray,
                                ]
                            );
                            $count++;
                        }
                        
                        Notification::make()
                            ->title('Import Berhasil')
                            ->body("Berhasil mengimpor {$count} data pegawai.")
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Gagal')
                            ->body('Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pegawai'),
            'Koordinator' => Tab::make('Koordinator')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'like', '%Koordinator%'))
                ->badge(Employee::where('status', 'like', '%Koordinator%')->count()),
            'IT' => Tab::make('Tim IT')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'like', '%IT%'))
                ->badge(Employee::where('status', 'like', '%IT%')->count()),
            'Pengawas' => Tab::make('Pengawas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'like', '%Pengawas%'))
                ->badge(Employee::where('status', 'like', '%Pengawas%')->count()),
        ];
    }
}
