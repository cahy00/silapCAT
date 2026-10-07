<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Summary - {{ $event->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 0;
        }
        .header-table td {
            border: none;
            padding: 0;
        }
        .header-logo {
            width: 65px;
            height: auto;
            max-height: 65px;
        }
        .header-text {
            text-align: center;
            padding-left: 10px;
        }
        .header-text .instansi-title {
            margin: 0;
            font-size: 19px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .header-text .kanreg-title {
            margin: 2px 0 0 0;
            font-size: 12.5px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .header-text .doc-title {
            margin: 4px 0 2px 0;
            font-size: 14px;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .header-text .doc-subtitle {
            margin: 0;
            font-size: 9.5px;
            color: #64748b;
        }
        .section-title {
            background-color: #f1f5f9;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11px;
            border-left: 3.5px solid #2563eb;
            color: #0f172a;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table, th, td {
            border: 1px solid #cbd5e1;
        }
        th {
            background-color: #f8fafc;
            padding: 5px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
        }
        td {
            padding: 5px 6px;
            font-size: 10.5px;
        }
        .kpi-table {
            border: none;
            margin-bottom: 10px;
        }
        .kpi-table td {
            border: none;
            padding: 4px;
        }
        .kpi-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            text-align: center;
        }
        .kpi-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: 900;
            margin-top: 2px;
            line-height: 1;
        }
        .kpi-sub {
            font-size: 9px;
            margin-top: 2px;
        }
        .box-note {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 10px;
            color: #334155;
            min-height: 35px;
        }
        .photo-table {
            border: none;
            margin-top: 6px;
        }
        .photo-table td {
            border: none;
            padding: 4px;
            vertical-align: top;
            text-align: center;
        }
        .photo-img {
            max-width: 100%;
            max-height: 120px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
        }
        .photo-caption {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .footer-sig {
            margin-top: 20px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    {{-- Header / Kop Surat --}}
    @php
        $logoPath = null;
        if (file_exists(public_path('assets/bkn/logo_bkn.png'))) {
            $logoPath = public_path('assets/bkn/logo_bkn.png');
        }
    @endphp
    <div class="header">
        <table class="header-table">
            <tr>
                @if ($logoPath)
                    <td style="width: 70px; vertical-align: middle; text-align: center;">
                        <img src="{{ $logoPath }}" class="header-logo" alt="Logo BKN">
                    </td>
                @endif
                <td class="header-text" style="vertical-align: middle;">
                    <div class="instansi-title">BADAN KEPEGAWAIAN NEGARA</div>
                    <div class="kanreg-title">KANTOR REGIONAL XIV BKN MANOKWARI</div>
                    <!-- <div class="doc-title">RINGKASAN EKSEKUTIF PELAKSANAAN UJIAN</div> -->
                    <div class="doc-subtitle">Sistem Informasi Layanan Pelaksanaan CAT (SILAP-CAT) • Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIT</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Informasi Utama Kegiatan --}}
    <table style="margin-bottom: 10px;">
        <tr>
            <th style="width: 20%;">Nama Kegiatan</th>
            <td style="width: 45%; font-weight: bold; color: #0f172a;">{{ $event->name }}</td>
            <th style="width: 15%;">Tahun Formasi</th>
            <td style="width: 20%;">{{ $event->formation_year ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jenis Pengadaan</th>
            <td>{{ $event->procurementType?->name ?? 'Seleksi CAT' }}</td>
            <th>Status Kegiatan</th>
            <td style="font-weight: bold; text-transform: uppercase;">{{ $event->status_label ?? strtoupper($event->status) }}</td>
        </tr>
        <tr>
            <th>Jadwal Pelaksanaan</th>
            <td>
                @if ($event->start_date && $event->end_date)
                    {{ $event->start_date->translatedFormat('d M Y') }} s/d {{ $event->end_date->translatedFormat('d M Y') }}
                    ({{ $event->start_date->diffInDays($event->end_date) + 1 }} Hari)
                @else
                    -
                @endif
            </td>
            <th>Titik Lokasi</th>
            <td>{{ $event->eventLocations->count() }} Lokasi</td>
        </tr>
    </table>

    {{-- KPI Cards Ringkasan Kehadiran & Nilai --}}
    @php
        $totalTarget = 0;
        foreach ($event->eventLocations as $el) {
            $totalTarget += (int) $el->eventLocationInstitutions->sum('participants_count');
        }
        if ($totalTarget === 0) {
            $totalTarget = (int) $event->eventInstitutions->sum('participants_count');
        }
        if ($totalTarget === 0) {
            $totalTarget = (int) $event->reports->sum('total_participants');
        }

        $present = (int) $event->reports->sum('present_count');
        $absent = (int) $event->reports->sum('absent_count');
        $attendanceRate = $totalTarget > 0 ? round(($present / $totalTarget) * 100, 1) : ($present > 0 ? 100 : 0);
        $absentRate = $totalTarget > 0 ? round(($absent / $totalTarget) * 100, 1) : 0;

        $highestScore = $event->reports->max('highest_score') ?? 0;
        $minReports = $event->reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
        $lowestScore = $minReports->isNotEmpty() ? $minReports->min('lowest_score') : ($event->reports->min('lowest_score') ?? 0);

        // Exam Scores Breakdown (if exam_scores exists)
        $examScoresCount = $event->examScores->count();
        $passedCount = $event->examScores->where('status', 'Lulus')->count();
        $failedCount = $event->examScores->where('status', '!=', 'Lulus')->count();
        $passRate = $examScoresCount > 0 ? round(($passedCount / $examScoresCount) * 100, 1) : 0;
    @endphp

    <table class="kpi-table">
        <tr>
            <td style="width: 20%;">
                <div class="kpi-card" style="background: #eef2ff; border-color: #c7d2fe;">
                    <div class="kpi-label" style="color: #4338ca;">Target Kuota</div>
                    <div class="kpi-value" style="color: #312e81;">{{ number_format($totalTarget) }}</div>
                    <div class="kpi-sub" style="color: #6366f1;">Peserta</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div class="kpi-label" style="color: #15803d;">Total Hadir</div>
                    <div class="kpi-value" style="color: #14532d;">{{ number_format($present) }}</div>
                    <div class="kpi-sub" style="color: #16a34a; font-weight: bold;">{{ $attendanceRate }}% Hadir</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="background: #fef2f2; border-color: #fecaca;">
                    <div class="kpi-label" style="color: #b91c1c;">Total Absen</div>
                    <div class="kpi-value" style="color: #7f1d1d;">{{ number_format($absent) }}</div>
                    <div class="kpi-sub" style="color: #dc2626;">{{ $absentRate }}% Absen</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="kpi-label" style="color: #0369a1;">Nilai Tertinggi</div>
                    <div class="kpi-value" style="color: #0c4a6e;">{{ number_format($highestScore) }}</div>
                    <div class="kpi-sub" style="color: #0284c7;">Skor Maksimum</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="background: #fffbeb; border-color: #fde68a;">
                    <div class="kpi-label" style="color: #b45309;">Nilai Terendah</div>
                    <div class="kpi-value" style="color: #78350f;">{{ number_format($lowestScore) }}</div>
                    <div class="kpi-sub" style="color: #d97706;">Skor Minimum</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Distribusi Skor & Kelulusan (Khusus jika ada data exam scores / UD / UPKP) --}}
    @if ($examScoresCount > 0)
        @php
            $eventPg = \App\Support\ScoreBands::passingGrade($event);
            $eventBands = \App\Support\ScoreBands::distribute($event->examScores->pluck('cat_score'), $eventPg);
        @endphp
        <div class="section-title">Distribusi Skor & Kelulusan Peserta</div>
        <table>
            <thead>
                <tr>
                    <th>Kategori Hasil</th>
                    <th style="text-align: center;">Jumlah Peserta</th>
                    <th style="text-align: center;">Persentase</th>
                    <th>Keterangan Standar Kelulusan (Passing Grade: {{ number_format($eventPg, 0) }})</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold; color: #15803d;">LULUS (Memenuhi Standar)</td>
                    <td style="text-align: center; font-weight: bold; color: #15803d;">{{ number_format($passedCount) }}</td>
                    <td style="text-align: center; font-weight: bold; color: #15803d;">{{ $passRate }}%</td>
                    <td>Memenuhi Ambang Batas Nilai / Status Lulus Terverifikasi</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #b91c1c;">TIDAK LULUS</td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">{{ number_format($failedCount) }}</td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">{{ round(100 - $passRate, 1) }}%</td>
                    <td>Di Bawah Ambang Batas Nilai / Tidak Hadir</td>
                </tr>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td>TOTAL TERDATA</td>
                    <td style="text-align: center;">{{ number_format($examScoresCount) }}</td>
                    <td style="text-align: center;">100%</td>
                    <td>Rekapitulasi Hasil Penilaian Terverifikasi</td>
                </tr>
            </tbody>
        </table>

        {{-- Rincian Rentang Skor CAT Terstandarisasi --}}
        <table style="margin-top: 6px;">
            <thead>
                <tr>
                    <th>Rentang Skor CAT</th>
                    <th>Predikat Mutu</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: center;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($eventBands as $band)
                    <tr>
                        <td style="font-weight: bold; color: {{ $band['color'] }};">{{ $band['range_label'] }}</td>
                        <td>{{ $band['label'] }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($band['count']) }}</td>
                        <td style="text-align: center;">{{ $band['pct'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Rincian per Titik Lokasi & Instansi --}}
    <div class="section-title">Rincian Titik Lokasi, Instansi & Sarana Prasarana</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Titik Lokasi (Tilok)</th>
                <th style="width: 35%;">Instansi Peserta</th>
                <th style="width: 15%; text-align: center;">Kapasitas PC</th>
                <th style="width: 15%; text-align: right;">Kuota Peserta</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($event->eventLocations as $index => $el)
                @php
                    $instNames = [];
                    foreach ($el->eventLocationInstitutions as $eli) {
                        if ($eli->institution) {
                            $instNames[] = $eli->institution->name . ' (' . number_format($eli->participants_count) . ')';
                        }
                    }
                    $pcCount = $el->location?->locationSurvey?->pc_count ?? 0;
                    $locTarget = (int) $el->eventLocationInstitutions->sum('participants_count');
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $el->location?->name ?? '-' }}</strong><br>
                        <small style="color: #64748b;">{{ $el->location?->city ?? '' }}</small>
                    </td>
                    <td>
                        @if (!empty($instNames))
                            {{ implode(', ', $instNames) }}
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Belum ada pemetaan instansi</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $pcCount > 0 ? "{$pcCount} PC" : '-' }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($locTarget) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; font-style: italic;">Belum ada data titik lokasi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Kendala Teknis & Langkah Solusi --}}
    <div class="section-title">Laporan Kendala Teknis & Mitigasi Lapangan</div>
    <div class="box-note">
        @if (!empty($event->technical_issues))
            {!! nl2br(e($event->technical_issues)) !!}
        @else
            <em>Selama pelaksanaan ujian berlangsung, seluruh sistem CAT, jaringan LAN/WAN, kelistrikan, dan sarana prasarana terpantau aman, lancar, dan terkendali tanpa kendala teknis yang signifikan.</em>
        @endif
    </div>

    {{-- Catatan Eksekutif / Evaluasi --}}
    <div class="section-title">Catatan & Evaluasi Pelaksanaan</div>
    <div class="box-note">
        @if (!empty($event->executive_notes))
            {!! nl2br(e($event->executive_notes)) !!}
        @else
            <em>Pelaksanaan kegiatan seleksi telah selesai diselenggarakan sesuai dengan Prosedur Operasional Standar (POS) Penyelenggaraan Seleksi CAT BKN dengan integritas dan akuntabilitas terjaga.</em>
        @endif
    </div>

    {{-- Foto Dokumentasi Pelaksanaan --}}
    @if (!empty($event->documentation_photos) && is_array($event->documentation_photos))
        <div class="section-title">Dokumentasi Pelaksanaan Kegiatan</div>
        <table class="photo-table">
            <tr>
                @foreach (array_slice($event->documentation_photos, 0, 4) as $photoPath)
                    @php
                        $fullPhotoPath = null;
                        if (file_exists(storage_path('app/public/' . $photoPath))) {
                            $fullPhotoPath = storage_path('app/public/' . $photoPath);
                        } elseif (file_exists(public_path('storage/' . $photoPath))) {
                            $fullPhotoPath = public_path('storage/' . $photoPath);
                        } elseif (file_exists(public_path($photoPath))) {
                            $fullPhotoPath = public_path($photoPath);
                        }
                    @endphp
                    @if ($fullPhotoPath)
                        <td style="width: 25%;">
                            <img src="{{ $fullPhotoPath }}" class="photo-img">
                            <div class="photo-caption">Dokumentasi Pelaksanaan</div>
                        </td>
                    @endif
                @endforeach
            </tr>
        </table>
    @endif

    {{-- Tim Pelaksana & Pengesahan --}}
    @php
        $koordinators = [];
        $itStaff = [];
        $pengawas = [];
        foreach ($event->eventEmployees as $ee) {
            $empName = $ee->employee?->name ?? '-';
            $roles = (array) $ee->role;
            if (in_array('Koordinator', $roles)) $koordinators[] = $empName;
            if (in_array('IT', $roles)) $itStaff[] = $empName;
            if (in_array('Pengawas', $roles)) $pengawas[] = $empName;
        }
    @endphp

    <div class="footer-sig">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 60%; vertical-align: top; font-size: 10px;">
                    <strong>Tim Pelaksana:</strong><br>
                    • Koordinator: {{ !empty($koordinators) ? implode(', ', $koordinators) : '-' }}<br>
                    • Tim IT: {{ !empty($itStaff) ? implode(', ', $itStaff) : '-' }}<br>
                    • Pengawas: {{ !empty($pengawas) ? implode(', ', $pengawas) : '-' }}
                </td>
                <td style="border: none; width: 40%; text-align: center; vertical-align: top;">
                    Jayapura/Manokwari, {{ now()->translatedFormat('d F Y') }}<br>
                    <strong>Koordinator Pelaksana CAT</strong>
                    <br><br><br><br>
                    <u><strong>{{ !empty($koordinators) ? $koordinators[0] : (auth()->user()?->name ?? 'Koordinator CAT') }}</strong></u>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
