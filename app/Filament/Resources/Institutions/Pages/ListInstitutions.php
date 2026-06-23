<?php

namespace App\Filament\Resources\Institutions\Pages;

use App\Filament\Resources\Institutions\InstitutionResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Institution;
use Filament\Resources\Pages\ListRecords;

class ListInstitutions extends ListRecords
{
    protected static string $resource = InstitutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(route('template.institution-import'))
                ->openUrlInNewTab(),
            Action::make('import')
                ->label('Import Instansi')
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
                            $namaInstansi = trim($row[0] ?? '');
                            $kode = trim($row[1] ?? '');
                            $alamat = trim($row[2] ?? '');
                            $contact = trim($row[3] ?? '');
                            $phone = trim($row[4] ?? '');
                            $email = trim($row[5] ?? '');
                            
                            // Abaikan baris kosong atau baris panduan
                            if (empty($namaInstansi) || str_contains(strtolower($namaInstansi), 'petunjuk') || str_contains(strtolower($namaInstansi), 'kolom')) {
                                continue;
                            }
                            
                            Institution::updateOrCreate(
                                ['name' => $namaInstansi],
                                [
                                    'code' => empty($kode) ? null : $kode,
                                    'address' => empty($alamat) ? null : $alamat,
                                    'contact_person' => empty($contact) ? null : $contact,
                                    'phone' => empty($phone) ? null : $phone,
                                    'email' => empty($email) ? null : $email,
                                ]
                            );
                            
                            $count++;
                        }
                        
                        Notification::make()
                            ->title('Import Berhasil')
                            ->body("Berhasil mengimpor {$count} data instansi.")
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
}
