<?php

namespace App\Filament\Resources\ExamScores;

use App\Filament\Resources\ExamScores\Pages\CreateExamScore;
use App\Filament\Resources\ExamScores\Pages\EditExamScore;
use App\Filament\Resources\ExamScores\Pages\ListExamScores;
use App\Filament\Resources\ExamScores\Schemas\ExamScoreForm;
use App\Filament\Resources\ExamScores\Tables\ExamScoresTable;
use App\Models\ExamScore;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamScoreResource extends Resource
{
    protected static ?string $model = ExamScore::class;
    
    protected static ?string $modelLabel = 'Nilai Ujian';
    protected static ?string $pluralModelLabel = 'Nilai Ujian';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static \UnitEnum|string|null $navigationGroup = 'Pengolahan Nilai';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return ExamScoreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamScoresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamScores::route('/'),
            'create' => CreateExamScore::route('/create'),
            'edit' => EditExamScore::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user && $user->hasRole('admin_instansi')) {
            $query->whereHas('event.eventLocations.eventLocationInstitutions', function ($q) use ($user) {
                $q->where('institution_id', $user->institution_id);
            });
        }
        return $query;
    }
}
