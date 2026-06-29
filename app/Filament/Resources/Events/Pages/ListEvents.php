<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
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
            \Filament\Actions\Action::make('exportMonthlyPdf')
                ->label('Cetak Kegiatan Bulanan')
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
            CreateAction::make(),
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
