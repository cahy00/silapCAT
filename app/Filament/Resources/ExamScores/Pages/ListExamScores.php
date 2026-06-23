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
            \Filament\Actions\Action::make('uploadTemplate')
                ->label('Upload Template Sertifikat')
                ->icon('heroicon-m-document-arrow-up')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('event_id')
                        ->label('Pilih Event / Kegiatan')
                        ->options(function () {
                            $query = \App\Models\Event::query();
                            $query->whereHas('procurementType', function ($q) {
                                $q->whereIn('name', ['UD', 'UPKP', 'UD/UPKP']);
                            });
                            $user = auth()->user();
                            if ($user && $user->hasRole('admin_instansi')) {
                                $query->whereHas('eventLocations.eventLocationInstitutions', function ($q) use ($user) {
                                    $q->where('institution_id', $user->institution_id);
                                });
                            }
                            return $query->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $event = \App\Models\Event::find($state);
                            $set('certificate_template', $event?->certificate_template);
                        }),
                    \Filament\Forms\Components\FileUpload::make('certificate_template')
                        ->label('Template Sertifikat (Gambar JPG/PNG)')
                        ->image()
                        ->directory('certificate-templates')
                        ->helperText(new \Illuminate\Support\HtmlString('Contoh: Admin instansi bisa mengupload template sertifikat kosong mereka sendiri. Nantinya sistem akan mencetak nama peserta di atas sertifikat ini sesuai dengan letak penulisan <code>&lt;&lt;name&gt;&gt;</code> jika dibutuhkan.<br><br><b>Download Contoh Template:</b><br><a href="/examples/template_sertifikat.pdf" target="_blank" style="color: blue; text-decoration: underline;">Contoh PDF</a> | <a href="/examples/template_sertifikat.pptx" target="_blank" style="color: blue; text-decoration: underline;">Contoh PPT</a>'))
                ])
                ->action(function (array $data) {
                    $eventId = $data['event_id'] ?? null;
                    if ($eventId) {
                        $event = \App\Models\Event::find($eventId);
                        if ($event) {
                            $event->update(['certificate_template' => $data['certificate_template']]);
                            \Filament\Notifications\Notification::make()
                                ->title('Template berhasil disimpan')
                                ->success()
                                ->send();
                        }
                    }
                }),
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
