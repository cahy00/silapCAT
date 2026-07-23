<?php

namespace App\Filament\Resources\Reports;

use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Filament\Resources\Reports\Pages\EditReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Filament\Resources\Reports\Schemas\ReportForm;
use App\Filament\Resources\Reports\Tables\ReportsTable;
use App\Models\Report;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    
    protected static ?string $modelLabel = 'Laporan';
    protected static ?string $pluralModelLabel = 'Laporan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Nilai';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return ReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReportsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('operator')) {
            // Hanya melihat laporan miliknya
            $query->where('user_id', $user->id);
            
            // Berdasarkan event & lokasi yang di-set pada delegasi
            $delegations = \App\Models\EventDelegation::where('user_id', $user->id)->get();
            $query->where(function ($q) use ($delegations) {
                if ($delegations->isEmpty()) {
                    $q->whereRaw('1 = 0');
                } else {
                    foreach ($delegations as $delegation) {
                        $q->orWhere(function ($subQ) use ($delegation) {
                            $subQ->where('event_id', $delegation->event_id)
                                 ->where('event_location_id', $delegation->event_location_id);
                        });
                    }
                }
            });
        }

        // Jangan tampilkan laporan dari kegiatan yang statusnya selesai (kecuali super_admin)
        if (!$user->hasRole('super_admin')) {
            $query->whereHas('event', function ($q) {
                $q->where(function($sub) {
                    $sub->whereDate('end_date', '>=', now()->startOfDay())
                        ->orWhereNull('end_date');
                })->where(function($sub) {
                    $sub->where('status', '!=', 'selesai')
                        ->orWhereNull('status');
                });
            });
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\Reports\Widgets\ReportStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
            'create' => CreateReport::route('/create'),
            'edit' => EditReport::route('/{record}/edit'),
        ];
    }
}
