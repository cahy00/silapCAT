<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportRecapPdf')
                ->label('Cetak Rekap PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->modalHeading('Cetak Rekapitulasi Laporan Harian')
                ->modalDescription('Pilih filter kegiatan dan periode waktu untuk mencetak rekapitulasi laporan harian dalam format PDF.')
                ->modalSubmitActionLabel('Cetak PDF')
                ->form([
                    \Filament\Forms\Components\Select::make('event_id')
                        ->label('Filter Kegiatan')
                        ->options(fn () => \App\Models\Event::pluck('name', 'id'))
                        ->placeholder('Semua Kegiatan (Gabungan)')
                        ->searchable(),
                    \Filament\Schemas\Components\Grid::make(2)->schema([
                        \Filament\Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ])
                            ->placeholder('Semua Bulan'),
                        \Filament\Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = [];
                                for ($y = $currentYear - 3; $y <= $currentYear + 3; $y++) {
                                    $years[$y] = $y;
                                }
                                return $years;
                            })
                            ->default(now()->year),
                    ]),
                ])
                ->action(function (array $data) {
                    $params = array_filter([
                        'event_id' => $data['event_id'] ?? null,
                        'month' => $data['month'] ?? null,
                        'year' => $data['year'] ?? null,
                    ]);
                    $url = route('reports.recap-pdf', $params);
                    return redirect()->to($url);
                }),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Reports\Widgets\ReportStatsOverview::class,
        ];
    }
}
