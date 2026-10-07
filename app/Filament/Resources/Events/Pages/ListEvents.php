<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\Action::make('exportAllExcel')
                    ->label('Matriks Excel (Semua Event)')
                    ->icon('heroicon-m-table-cells')
                    ->color('info')
                    ->modalHeading('Export Matriks Data Semua Event & Relasi')
                    ->modalDescription('Unduh spreadsheet Excel lengkap yang berisi semua bidang data event beserta seluruh relasinya (Instansi, Titik Lokasi, Petugas SDM, Delegasi, Laporan, dan Skor).')
                    ->modalSubmitActionLabel('Unduh Excel Matriks')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(3)->schema([
                            \Filament\Forms\Components\Select::make('status')
                                ->label('Filter Status Event')
                                ->options([
                                    'all' => 'Semua Status',
                                    'draft' => 'Draft',
                                    'active' => 'Aktif',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Batal',
                                ])
                                ->default('all')
                                ->required(),
                            \Filament\Forms\Components\Select::make('month')
                                ->label('Filter Bulan Kegiatan')
                                ->options([
                                    'all' => 'Semua Bulan',
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ])
                                ->default('all')
                                ->required(),
                            \Filament\Forms\Components\Select::make('year')
                                ->label('Filter Tahun Formasi / Pelaksanaan')
                                ->options(function () {
                                    $currentYear = now()->year;
                                    $years = ['all' => 'Semua Tahun'];
                                    for ($y = $currentYear - 3; $y <= $currentYear + 3; $y++) {
                                        $years[$y] = (string) $y;
                                    }
                                    return $years;
                                })
                                ->default('all')
                                ->required(),
                        ])
                    ])
                    ->action(function (array $data) {
                        $url = route('events.all-excel', [
                            'status' => $data['status'],
                            'month' => $data['month'],
                            'year' => $data['year'],
                        ]);
                        return redirect()->to($url);
                    }),

                \Filament\Actions\Action::make('exportMonthlyPdf')
                    ->label('Laporan Kegiatan Bulanan (PDF)')
                    ->icon('heroicon-m-printer')
                    ->color('success')
                    ->modalHeading('Cetak Rekapitulasi Kegiatan Bulanan')
                    ->modalDescription('Pilih bulan dan tahun kegiatan yang ingin dicetak dalam format PDF.')
                    ->modalSubmitActionLabel('Cetak PDF')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(2)->schema([
                            \Filament\Forms\Components\Select::make('month')
                                ->label('Bulan')
                                ->options([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ])
                                ->default(now()->month)
                                ->required(),
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
                                ->default(now()->year)
                                ->required(),
                        ])
                    ])
                    ->action(function (array $data) {
                        $url = route('events.monthly-pdf', ['month' => $data['month'], 'year' => $data['year']]);
                        return redirect()->to($url);
                    }),

                \Filament\Actions\Action::make('exportZipDocuments')
                    ->label('Arsip Berkas Dokumen (.ZIP)')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('warning')
                    ->modalHeading('Ekspor Paket Dokumen Kegiatan (.ZIP)')
                    ->modalDescription('Pilih filter periode atau kegiatan untuk mengunduh seluruh berkas dokumen dalam format arsip ZIP.')
                    ->modalSubmitActionLabel('Unduh ZIP')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(2)->schema([
                            \Filament\Forms\Components\Select::make('event_id')
                                ->label('Pilih Kegiatan (Opsional)')
                                ->options(fn() => Event::orderBy('start_date', 'desc')->pluck('name', 'id'))
                                ->placeholder('Semua Kegiatan')
                                ->searchable()
                                ->columnSpan(2),
                            \Filament\Forms\Components\Select::make('month')
                                ->label('Bulan')
                                ->options([
                                    '' => 'Semua Bulan',
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ])
                                ->default(null),
                            \Filament\Forms\Components\Select::make('year')
                                ->label('Tahun')
                                ->options(function () {
                                    $currentYear = now()->year;
                                    $years = ['' => 'Semua Tahun'];
                                    for ($y = $currentYear - 3; $y <= $currentYear + 3; $y++) {
                                        $years[$y] = $y;
                                    }
                                    return $years;
                                })
                                ->default(now()->year),
                        ])
                    ])
                    ->action(function (array $data) {
                        $url = route('events.bulk-documents-zip', [
                            'event_id' => $data['event_id'] ?? null,
                            'month' => $data['month'] ?? null,
                            'year' => $data['year'] ?? null,
                        ]);
                        return redirect()->to($url);
                    }),
            ])
            ->label('Ekspor & Cetak')
            ->icon('heroicon-m-arrow-down-tray')
            ->color('gray')
            ->button(),

            CreateAction::make()
                ->label('Tambah Kegiatan')
                ->icon('heroicon-m-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Events\Widgets\EventOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->icon('heroicon-m-pencil-square'),
            'active' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->icon('heroicon-m-play-circle'),
            'completed' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->icon('heroicon-m-check-circle'),
            'cancelled' => Tab::make('Batal')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->icon('heroicon-m-x-circle'),
        ];
    }
}
