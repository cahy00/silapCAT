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
                        ->label('Template Sertifikat (PDF / PPTX)')
                        ->directory('certificate-templates')
                        ->disk('public')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        ])
                        ->maxSize(10240)
                        ->downloadable()
                        ->openable()
                        ->previewable(false)
                        ->helperText(new \Illuminate\Support\HtmlString(
                            '<div style="margin-top:6px;">'
                            . '<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;margin-bottom:10px;">'
                            . '<strong style="color:#1e40af;">📥 Download Contoh Template:</strong><br>'
                            . '<a href="/examples/template_sertifikat.pdf" target="_blank" download style="color:#2563eb;text-decoration:underline;font-weight:600;margin-right:16px;">📄 Contoh Template PDF</a>'
                            . '<a href="/examples/template_sertifikat.pptx" target="_blank" download style="color:#2563eb;text-decoration:underline;font-weight:600;">📊 Contoh Template PPTX</a>'
                            . '</div>'
                            . '<strong>Format yang diterima:</strong> PDF atau PPTX (PowerPoint). Maks 10MB.<br><br>'
                            . '<strong>Placeholder yang tersedia:</strong>'
                            . '<table style="margin-top:4px;font-size:12px;border-collapse:collapse;width:100%;">'
                            . '<tr style="background:#f8fafc;"><td style="padding:3px 8px;border:1px solid #e2e8f0;"><code>&lt;&lt;name&gt;&gt;</code></td><td style="padding:3px 8px;border:1px solid #e2e8f0;">Nama Peserta</td></tr>'
                            . '<tr><td style="padding:3px 8px;border:1px solid #e2e8f0;"><code>&lt;&lt;nip&gt;&gt;</code></td><td style="padding:3px 8px;border:1px solid #e2e8f0;">NIP / Nomor Identitas</td></tr>'
                            . '<tr style="background:#f8fafc;"><td style="padding:3px 8px;border:1px solid #e2e8f0;"><code>&lt;&lt;exam_type&gt;&gt;</code></td><td style="padding:3px 8px;border:1px solid #e2e8f0;">Jenis Ujian (UD I / UD II / UPKP)</td></tr>'
                            . '<tr><td style="padding:3px 8px;border:1px solid #e2e8f0;"><code>&lt;&lt;score&gt;&gt;</code></td><td style="padding:3px 8px;border:1px solid #e2e8f0;">Nilai Akhir</td></tr>'
                            . '<tr style="background:#f8fafc;"><td style="padding:3px 8px;border:1px solid #e2e8f0;"><code>&lt;&lt;status&gt;&gt;</code></td><td style="padding:3px 8px;border:1px solid #e2e8f0;">Status Kelulusan</td></tr>'
                            . '<tr><td style="padding:3px 8px;border:1px solid #e2e8f0;"><code>&lt;&lt;date&gt;&gt;</code></td><td style="padding:3px 8px;border:1px solid #e2e8f0;">Tanggal Ujian</td></tr>'
                            . '</table>'
                            . '</div>'
                        ))
                ])
                ->action(function (array $data) {
                    $eventId = $data['event_id'] ?? null;
                    if ($eventId) {
                        $event = \App\Models\Event::find($eventId);
                        if ($event) {
                            $event->update(['certificate_template' => $data['certificate_template']]);
                            \Filament\Notifications\Notification::make()
                                ->title('Template sertifikat berhasil disimpan')
                                ->body('Template telah diupload dan dikaitkan dengan kegiatan.')
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
