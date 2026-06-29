<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Laporan Harian</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            text-transform: uppercase;
            color: #111827;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            color: #4b5563;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .summary-box .label {
            font-size: 10px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .summary-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #334155;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
        }
        .data-table td {
            font-size: 11px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge-session {
            background: #e0e7ff;
            color: #3730a3;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer td {
            vertical-align: top;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAPITULASI LAPORAN HARIAN PELAKSANAAN UJIAN</h1>
        <p>
            @if($event)
                Kegiatan: <strong>{{ $event->name }}</strong>
            @else
                <strong>Semua Kegiatan</strong>
            @endif
            @if($monthName && $year)
                &mdash; Periode: {{ $monthName }} {{ $year }}
            @elseif($year)
                &mdash; Tahun: {{ $year }}
            @endif
        </p>
    </div>

    @php
        $totalReports = $reports->count();
        $presentCount = $reports->sum('present_count');
        $absentCount = $reports->sum('absent_count');
        $totalParticipants = $reports->sum('total_participants');
        $highestScore = $reports->max('highest_score') ?? 0;
        $minReports = $reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
        $lowestScore = $minReports->isNotEmpty() ? $minReports->min('lowest_score') : ($reports->min('lowest_score') ?? 0);
    @endphp

    <table class="summary-box">
        <tr>
            <td style="width: 20%;">
                <div class="label">Total Sesi Laporan</div>
                <div class="value" style="color: #4338ca;">{{ number_format($totalReports, 0, ',', '.') }} Sesi</div>
            </td>
            <td style="width: 20%;">
                <div class="label">Total Peserta Hadir</div>
                <div class="value" style="color: #15803d;">{{ number_format($presentCount, 0, ',', '.') }} Peserta</div>
            </td>
            <td style="width: 20%;">
                <div class="label">Total Tidak Hadir</div>
                <div class="value" style="color: #b91c1c;">{{ number_format($absentCount, 0, ',', '.') }} Peserta</div>
            </td>
            <td style="width: 20%;">
                <div class="label">Nilai Tertinggi</div>
                <div class="value" style="color: #0369a1;">{{ number_format($highestScore, 2, ',', '.') }}</div>
            </td>
            <td style="width: 20%;">
                <div class="label">Nilai Terendah</div>
                <div class="value" style="color: #b45309;">{{ number_format($lowestScore, 2, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">Tanggal & Sesi</th>
                <th style="width: 24%;">Kegiatan</th>
                <th style="width: 18%;">Titik Lokasi</th>
                <th style="width: 16%;">Rekap Kehadiran</th>
                <th style="width: 10%;">Nilai Tertinggi</th>
                <th style="width: 10%;">Nilai Terendah</th>
                <th style="width: 14%;">Petugas Pelapor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $rep)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <strong>{{ $rep->report_date ? $rep->report_date->isoFormat('DD MMM YYYY') : '-' }}</strong><br>
                        <span class="badge-session">{{ $rep->session_name ?? '-' }}</span>
                    </td>
                    <td>{{ $rep->event->name ?? '-' }}</td>
                    <td>{{ $rep->eventLocation->location->name ?? '-' }}</td>
                    <td class="text-center">
                        <span style="color: #15803d; font-weight: bold;">{{ number_format($rep->present_count, 0, ',', '.') }} Hadir</span> /
                        <span style="color: #b91c1c;">{{ number_format($rep->absent_count, 0, ',', '.') }} Absen</span><br>
                        <span style="font-size: 10px; color: #64748b;">Total: {{ number_format($rep->total_participants, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-center" style="font-weight: bold; color: #0369a1;">
                        {{ $rep->highest_score !== null ? number_format($rep->highest_score, 2, ',', '.') : '-' }}
                    </td>
                    <td class="text-center" style="font-weight: bold; color: #b45309;">
                        {{ $rep->lowest_score !== null ? number_format($rep->lowest_score, 2, ',', '.') : '-' }}
                    </td>
                    <td>{{ $rep->user->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; font-style: italic; color: #64748b;">
                        Belum ada data laporan harian yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td style="width: 65%;"></td>
            <td style="width: 35%; text-align: center;">
                <p style="margin: 0 0 70px 0;">Dicetak pada: {{ now()->isoFormat('DD MMMM YYYY') }}<br>Petugas Penanggung Jawab,</p>
                <strong><u>{{ auth()->user()->name ?? 'Administrator' }}</u></strong>
            </td>
        </tr>
    </table>

</body>
</html>
