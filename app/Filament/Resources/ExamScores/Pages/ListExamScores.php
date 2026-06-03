<?php

namespace App\Filament\Resources\ExamScores\Pages;

use App\Filament\Resources\ExamScores\ExamScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListExamScores extends ListRecords
{
    protected static string $resource = ExamScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'UD_I' => Tab::make('Ujian Dinas Tk. I (UD I)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('exam_type', 'UD_I'))
                ->icon('heroicon-m-academic-cap'),
            'UD_II' => Tab::make('Ujian Dinas Tk. II (UD II)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('exam_type', 'UD_II'))
                ->icon('heroicon-m-document-text'),
            'UPKP' => Tab::make('UPKP')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('exam_type', 'UPKP'))
                ->icon('heroicon-m-presentation-chart-line'),
        ];
    }
}
