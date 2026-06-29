<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kegiatan Bulan {{ $monthName }} {{ $year }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 4px 0;
            font-size: 20px;
            color: #1e3a8a;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0 0 4px 0;
            font-size: 14px;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #64748b;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .summary-title {
            font-size: 10px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 18px;
            color: #1e3a8a;
            font-weight: bold;
            margin-top: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            vertical-align: top;
        }
        .data-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-active { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-completed { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-draft { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-cancelled { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .location-item {
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .location-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
            font-size: 10px;
            color: #64748b;
        }
        .footer-left {
            float: left;
        }
        .footer-right {
            float: right;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Badan Kepegawaian Negara</h1>
        <h2>Sistem Informasi Layanan Asesmen &amp; Penilaian CAT (SILAP CAT)</h2>
        <p>Rekapitulasi Pelaksanaan &amp; Jadwal Kegiatan &mdash; Bulan {{ $monthName }} {{ $year }}</p>
    </div>

    @php
        $totalPeserta = $events->sum(function($ev) {
            return $ev->eventLocations->flatMap(fn($l) => $l->eventLocationInstitutions)->sum('participants_count');
        });
        $totalLokasi = $events->sum(function($ev) {
            return $ev->eventLocations->count();
        });
        $totalSesi = $events->sum(fn($ev) => $ev->reports->count());
        $totalHadirRiil = $events->sum(fn($ev) => $ev->reports->sum('present_count'));
        $totalPesertaSesi = $events->sum(fn($ev) => $ev->reports->sum('total_participants'));
    @endphp

    <table class="summary-table">
        <tr>
            <td width="25%" style="padding-right: 8px;">
                <div class="summary-box">
                    <div class="summary-title">Total Kegiatan</div>
                    <div class="summary-value">{{ number_format($events->count()) }}</div>
                </div>
            </td>
            <td width="25%" style="padding: 0 4px;">
                <div class="summary-box">
                    <div class="summary-title">Total Titik Lokasi</div>
                    <div class="summary-value">{{ number_format($totalLokasi) }}</div>
                </div>
            </td>
            <td width="25%" style="padding: 0 4px;">
                <div class="summary-box">
                    <div class="summary-title">Alokasi Kuota Peserta</div>
                    <div class="summary-value">{{ number_format($totalPeserta) }}</div>
                </div>
            </td>
            <td width="25%" style="padding-left: 8px;">
                <div class="summary-box">
                    <div class="summary-title">Kehadiran Riil (Sesi)</div>
                    <div class="summary-value" style="color: #059669;">
                        {{ number_format($totalHadirRiil) }} 
                        <span style="font-size: 11px; color: #64748b; font-weight: normal;">/ {{ number_format($totalPesertaSesi) }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div style="font-size: 13px; font-weight: bold; color: #1e3a8a; margin-bottom: 8px; text-transform: uppercase;">
        A. Daftar Kegiatan &amp; Jadwal Pelaksanaan
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="22%">Informasi Kegiatan</th>
                <th width="12%">Jenis Pengadaan</th>
                <th width="20%">Instansi Terkait</th>
                <th width="26%">Titik Lokasi &amp; Jadwal</th>
                <th width="8%">Peserta</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>
                    <strong style="color: #1e293b; font-size: 12px;">{{ $event->name }}</strong><br>
                    <span style="color: #64748b; font-size: 10px;">Formasi: {{ $event->formation_year ?? '-' }}</span>
                </td>
                <td style="text-align: center;">
                    <strong>{{ $event->procurementType->name ?? '-' }}</strong><br>
                    <span style="color: #64748b; font-size: 9px;">{{ $event->procurementType->procurementCategory->name ?? '' }}</span>
                </td>
                <td>
                    @php
                        $instansiList = $event->eventLocations->flatMap(fn($loc) => $loc->eventLocationInstitutions)
                            ->map(fn($ei) => $ei->institution)
                            ->unique('id');
                    @endphp
                    @if($instansiList->count() > 0)
                        <ul style="margin: 0; padding-left: 14px;">
                            @foreach($instansiList as $inst)
                                <li style="margin-bottom: 2px;">{{ $inst->name ?? '-' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span style="color: #94a3b8; font-style: italic;">Belum ada instansi</span>
                    @endif
                </td>
                <td>
                    @if($event->eventLocations->count() > 0)
                        @foreach($event->eventLocations as $el)
                            <div class="location-item">
                                <strong style="color: #334155;">{{ $el->location->name ?? '-' }}</strong><br>
                                <span style="color: #475569; font-size: 10px;">
                                    📅 {{ $el->start_date ? \Carbon\Carbon::parse($el->start_date)->translatedFormat('d M Y') : '-' }} s/d {{ $el->end_date ? \Carbon\Carbon::parse($el->end_date)->translatedFormat('d M Y') : '-' }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <span style="color: #94a3b8; font-style: italic;">
                            @if($event->start_date && $event->end_date)
                                📅 {{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($event->end_date)->translatedFormat('d M Y') }}
                            @else
                                Belum ada jadwal/lokasi
                            @endif
                        </span>
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold; font-size: 12px; color: #1e3a8a;">
                    @php
                        $peserta = $event->eventLocations->flatMap(fn($l) => $l->eventLocationInstitutions)->sum('participants_count');
                    @endphp
                    {{ number_format($peserta) }}
                </td>
                <td style="text-align: center;">
                    @php
                        $statusClass = match(strtolower($event->status)) {
                            'active' => 'badge-active',
                            'completed' => 'badge-completed',
                            'cancelled' => 'badge-cancelled',
                            default => 'badge-draft'
                        };
                        $statusText = match(strtolower($event->status)) {
                            'active' => 'Aktif',
                            'completed' => 'Selesai',
                            'cancelled' => 'Batal',
                            default => 'Draft'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 25px; color: #64748b; font-style: italic;">
                    Tidak ada kegiatan pada bulan {{ $monthName }} {{ $year }}.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-inside: avoid; margin-top: 25px;">
        <div style="font-size: 13px; font-weight: bold; color: #1e3a8a; margin-bottom: 8px; text-transform: uppercase;">
            B. Rekapitulasi Kehadiran &amp; Nilai Ujian (Berdasarkan Laporan Harian)
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="28%">Nama Kegiatan</th>
                    <th width="10%">Total Sesi</th>
                    <th width="12%">Peserta Terdaftar</th>
                    <th width="12%">Hadir</th>
                    <th width="12%">Tidak Hadir</th>
                    <th width="10%">% Kehadiran</th>
                    <th width="12%">Nilai Tertinggi / Terendah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                @php
                    $sesiCount = $event->reports->count();
                    $pesertaSesi = $event->reports->sum('total_participants');
                    $hadir = $event->reports->sum('present_count');
                    $tidakHadir = $event->reports->sum('absent_count');
                    $persen = $pesertaSesi > 0 ? ($hadir / $pesertaSesi) * 100 : 0;
                    $maxScore = $event->reports->max('highest_score');
                    $minScore = $event->reports->min('lowest_score');
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>
                        <strong style="color: #1e293b;">{{ $event->name }}</strong>
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($sesiCount) }} Sesi</td>
                    <td style="text-align: center;">{{ number_format($pesertaSesi) }}</td>
                    <td style="text-align: center; color: #059669; font-weight: bold;">{{ number_format($hadir) }}</td>
                    <td style="text-align: center; color: #dc2626;">{{ number_format($tidakHadir) }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($sesiCount > 0)
                            {{ number_format($persen, 1) }}%
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($sesiCount > 0 && $maxScore !== null)
                            <span style="color: #059669; font-weight: bold;">{{ number_format($maxScore, 2, ',', '.') }}</span> / 
                            <span style="color: #dc2626;">{{ $minScore !== null ? number_format($minScore, 2, ',', '.') : '-' }}</span>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Belum ada laporan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #64748b; font-style: italic;">
                        Tidak ada data laporan harian pada bulan {{ $monthName }} {{ $year }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="footer-left">
            Dokumen dihasilkan oleh sistem SILAP CAT pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
        </div>
        <div class="footer-right">
            Diunduh oleh: {{ auth()->user()->name ?? 'Administrator' }}
        </div>
    </div>
</body>
</html>
