<?php

namespace App\Filament\Resources\ExamScores\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ExamScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Import Massal dari Excel / CSV')
                    ->description('Unggah file CSV hasil ekspor dari Excel untuk mengimpor banyak data sekaligus tanpa harus mengisi form di bawah.')
                    ->icon('heroicon-o-document-arrow-up')
                    ->schema([
                        Select::make('import_event_id')
                            ->relationship('event', 'name', modifyQueryUsing: function ($query) {
                                $query->whereHas('procurementType', function ($q) {
                                    $q->whereIn('name', ['UD', 'UPKP', 'UD/UPKP']);
                                });
                            })
                            ->label('Pilih Event / Kegiatan')
                            ->placeholder('Pilih Event untuk Seluruh Data Excel')
                            ->prefixIcon('heroicon-m-calendar')
                            ->required(fn (callable $get) => !empty($get('import_file'))),

                        \Filament\Forms\Components\FileUpload::make('import_file')
                            ->label('Pilih File Excel / CSV')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->directory('temp-imports')
                            ->storeFiles(false)
                            ->live()
                            ->helperText(new \Illuminate\Support\HtmlString('<strong>Format Kolom:</strong> NIP, Nama, Jabatan, Instansi, Tipe Ujian (UD_I / UD_II / UPKP), Tanggal Ujian, Nilai CAT BKN, Nilai Wawancara, Catatan. <br><a href="' . route('template.exam-score-import') . '" class="text-primary-600 dark:text-primary-400 underline font-bold"><i class="heroicon-o-arrow-down-tray"></i> ⬇ Unduh Template Excel (.xlsx)</a>')),

                    ])
                    ->visible(fn ($record) => $record === null)
                    ->columnSpanFull(),

                Group::make()
                    ->schema([
                        Section::make('Identitas Peserta Ujian')
                            ->description('Masukkan data diri lengkap peserta ujian dinas / UPKP.')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Select::make('event_id')
                                    ->relationship('event', 'name', modifyQueryUsing: function ($query) {
                                        $query->whereHas('procurementType', function ($q) {
                                            $q->whereIn('name', ['UD', 'UPKP', 'UD/UPKP']);
                                        });
                                    })
                                    ->label('Event / Kegiatan')
                                    ->placeholder('Pilih Event')
                                    ->required(fn (callable $get) => empty($get('import_file')))
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->columnSpan(2),
                                TextInput::make('employee_number')
                                    ->label('NIP / Nomor Identitas')
                                    ->required(fn (callable $get) => empty($get('import_file')))
                                    ->placeholder('19xxxxxxxxxxxxxx')
                                    ->prefixIcon('heroicon-m-credit-card'),
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required(fn (callable $get) => empty($get('import_file')))
                                    ->placeholder('Nama Lengkap & Gelar')
                                    ->prefixIcon('heroicon-m-user'),
                                TextInput::make('position')
                                    ->label('Jabatan')
                                    ->placeholder('Contoh: Analis Kepegawaian Ahli Pertama')
                                    ->prefixIcon('heroicon-m-briefcase'),
                                TextInput::make('institution')
                                    ->label('Instansi / Unit Kerja')
                                    ->placeholder('Contoh: Badan Kepegawaian Negara')
                                    ->prefixIcon('heroicon-m-building-office'),
                            ])->columns(2),

                        Section::make('Hasil Penilaian & Kelulusan')
                            ->description('Kelola detail pelaksanaan ujian dan nilai ujian.')
                            ->icon('heroicon-o-document-chart-bar')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('exam_type')
                                        ->label('Jenis & Tingkat Ujian')
                                        ->options([
                                            'UD_I' => 'Ujian Dinas Tingkat I (UD I)',
                                            'UD_II' => 'Ujian Dinas Tingkat II (UD II)',
                                            'UPKP' => 'Ujian Penyesuaian Kenaikan Pangkat (UPKP)',
                                        ])
                                        ->required(fn (callable $get) => empty($get('import_file')))
                                        ->live()
                                        ->afterStateUpdated(fn ($state, callable $get, callable $set) => self::updateTotalAndStatus($get, $set))
                                        ->prefixIcon('heroicon-m-academic-cap'),
                                    DatePicker::make('exam_date')
                                        ->label('Tanggal Ujian')
                                        ->required(fn (callable $get) => empty($get('import_file')))
                                        ->default(now())
                                        ->prefixIcon('heroicon-m-calendar'),
                                ]),

                                Grid::make(2)->schema([
                                    TextInput::make('cat_score')
                                        ->label('Nilai CAT BKN (Raw)')
                                        ->numeric()
                                        ->required(fn (callable $get) => empty($get('import_file')))
                                        ->minValue(0)
                                        ->maxValue(500)
                                        ->placeholder('Skala 0 - 500')
                                        ->helperText('Diisi nilai asli CAT dari BKN (maksimal 500). Sistem akan membagi 5 secara otomatis untuk konversi skala 100.')
                                        ->live()
                                        ->afterStateUpdated(fn ($state, callable $get, callable $set) => self::updateTotalAndStatus($get, $set))
                                        ->prefixIcon('heroicon-m-computer-desktop'),
                                    TextInput::make('interview_score')
                                        ->label('Nilai Wawancara Makalah')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->placeholder('Skala 0 - 100')
                                        ->helperText('Diisi nilai wawancara / presentasi makalah. Di-disable otomatis jika memilih Ujian Dinas Tingkat I (UD I).')
                                        ->live()
                                        ->afterStateUpdated(fn ($state, callable $get, callable $set) => self::updateTotalAndStatus($get, $set))
                                        ->disabled(fn (callable $get) => $get('exam_type') === 'UD_I')
                                        ->dehydrated()
                                        ->prefixIcon('heroicon-m-chat-bubble-left-right'),
                                ]),

                                Grid::make(2)->schema([
                                    TextInput::make('total_score')
                                        ->label('Nilai Akhir (Skala 100)')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->placeholder('Otomatis dihitung')
                                        ->prefixIcon('heroicon-m-calculator'),
                                    TextInput::make('status')
                                        ->label('Status Kelulusan')
                                        ->disabled()
                                        ->dehydrated()
                                        ->placeholder('Otomatis ditentukan')
                                        ->prefixIcon('heroicon-m-check-badge'),
                                ]),
                            ])->columns(1),
                    ])->columnSpan(['lg' => 8]),

                Group::make()
                    ->schema([
                        Section::make('Catatan Tambahan')
                            ->description('Catatan opsional mengenai hasil ujian peserta.')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Textarea::make('notes')
                                    ->label('Keterangan')
                                    ->placeholder('Tulis catatan atau keterangan tambahan di sini...')
                                    ->rows(6),
                            ]),
                    ])->columnSpan(['lg' => 4]),
            ]);
    }

    public static function updateTotalAndStatus(callable $get, callable $set)
    {
        $examType = $get('exam_type');
        $catRaw = floatval($get('cat_score') ?? 0);
        $catScaled = $catRaw / 5;

        if (empty($examType)) {
            $set('total_score', null);
            $set('status', null);
            return;
        }

        if ($examType === 'UD_I') {
            $total = $catScaled;
            $set('interview_score', null);
        } elseif ($examType === 'UD_II') {
            $interview = floatval($get('interview_score') ?? 0);
            $total = ($catScaled * 0.6) + ($interview * 0.4);
        } else { // UPKP
            $interview = floatval($get('interview_score') ?? 0);
            $total = ($catScaled * 0.5) + ($interview * 0.5);
        }

        if ($get('cat_score') !== null) {
            $set('total_score', round($total, 2));
            $set('status', $total >= 70 ? 'Lulus' : 'Tidak Lulus');
        } else {
            $set('total_score', null);
            $set('status', null);
        }
    }
}
