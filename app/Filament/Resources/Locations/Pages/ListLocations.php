<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Resources\Locations\LocationResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Location;
use App\Models\LocationSurvey;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(route('template.location-import'))
                ->openUrlInNewTab(),
            Action::make('import')
                ->label('Import Lokasi')
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
                            $namaLokasi = trim($row[0] ?? '');
                            $tipe = trim($row[1] ?? '');
                            $kota = trim($row[2] ?? '');
                            $alamat = trim($row[3] ?? '');
                            
                            $pcCount = trim($row[4] ?? '');
                            $roomCount = trim($row[5] ?? '');
                            $feasibilityStatus = trim($row[6] ?? '');
                            $surveyorString = trim($row[7] ?? '');
                            $startDate = trim($row[8] ?? '');
                            $endDate = trim($row[9] ?? '');
                            $notes = trim($row[10] ?? '');
                            
                            // Abaikan baris kosong atau baris panduan
                            if (empty($namaLokasi) || empty($tipe) || str_contains(strtolower($namaLokasi), 'petunjuk') || str_contains(strtolower($namaLokasi), 'kolom')) {
                                continue;
                            }
                            
                            // Update or create Location
                            $location = Location::updateOrCreate(
                                ['name' => $namaLokasi],
                                [
                                    'type' => $tipe,
                                    'city' => empty($kota) ? null : $kota,
                                    'address' => empty($alamat) ? null : $alamat,
                                ]
                            );
                            
                            // Update or create LocationSurvey
                            $surveyorArray = [];
                            if (!empty($surveyorString)) {
                                $exploded = explode(',', $surveyorString);
                                foreach ($exploded as $s) {
                                    $s = trim($s);
                                    if (!empty($s)) {
                                        $surveyorArray[] = $s;
                                    }
                                }
                            }
                            
                            LocationSurvey::updateOrCreate(
                                ['location_id' => $location->id],
                                [
                                    'pc_count' => is_numeric($pcCount) ? (int)$pcCount : null,
                                    'room_count' => is_numeric($roomCount) ? (int)$roomCount : null,
                                    'feasibility_status' => empty($feasibilityStatus) ? null : $feasibilityStatus,
                                    'surveyor_name' => empty($surveyorArray) ? null : $surveyorArray,
                                    'survey_start_date' => empty($startDate) ? null : $startDate,
                                    'survey_end_date' => empty($endDate) ? null : $endDate,
                                    'notes' => empty($notes) ? null : $notes,
                                ]
                            );
                            
                            $count++;
                        }
                        
                        Notification::make()
                            ->title('Import Berhasil')
                            ->body("Berhasil mengimpor {$count} data lokasi dan survei.")
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
            'all' => Tab::make('Semua'),
            'mandiri_bkn' => Tab::make('Mandiri BKN')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'mandiri_bkn'))
                ->icon('heroicon-m-building-office'),
            'mandiri_instansi' => Tab::make('Mandiri Instansi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'mandiri_instansi'))
                ->icon('heroicon-m-building-library'),
            'bkn' => Tab::make('BKN')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'bkn'))
                ->icon('heroicon-m-academic-cap'),
        ];
    }
}
