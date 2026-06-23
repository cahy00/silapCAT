<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Harian - {{ $report->event->name ?? 'Kegiatan' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 22px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .info-table td.colon {
            width: 5%;
            text-align: center;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .footer .signature-space {
            margin-top: 80px;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px 12px;
            font-weight: bold;
            margin-bottom: 15px;
            border-left: 4px solid #4F46E5;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN HARIAN PELAKSANAAN UJIAN</h1>
        <p>{{ $report->event->name ?? 'Nama Kegiatan Tidak Tersedia' }}</p>
    </div>

    <div class="section-title">A. Informasi Pelaksanaan</div>
    <table class="info-table">
        <tr>
            <td>Tanggal Laporan</td>
            <td class="colon">:</td>
            <td>{{ $report->report_date ? $report->report_date->isoFormat('DD MMMM YYYY') : '-' }}</td>
        </tr>
        <tr>
            <td>Lokasi Ujian</td>
            <td class="colon">:</td>
            <td>{{ $report->eventLocation->location->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama/Nomor Sesi</td>
            <td class="colon">:</td>
            <td>{{ $report->session_name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Diinput Oleh</td>
            <td class="colon">:</td>
            <td>{{ $report->user->name ?? 'Sistem' }}</td>
        </tr>
        <tr>
            <td>Waktu Cetak Laporan</td>
            <td class="colon">:</td>
            <td>{{ now()->isoFormat('DD MMMM YYYY, HH:mm:ss') }}</td>
        </tr>
    </table>

    <div class="section-title">B. Data Kehadiran Peserta</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Total Peserta</th>
                <th>Hadir</th>
                <th>Tidak Hadir</th>
                <th>Persentase Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($report->total_participants, 0, ',', '.') }}</td>
                <td>{{ number_format($report->present_count, 0, ',', '.') }}</td>
                <td>{{ number_format($report->absent_count, 0, ',', '.') }}</td>
                <td>
                    @if($report->total_participants > 0)
                        {{ number_format(($report->present_count / $report->total_participants) * 100, 1) }}%
                    @else
                        0%
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">C. Ringkasan Nilai Ujian</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nilai Tertinggi</th>
                <th>Nilai Terendah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $report->highest_score !== null ? number_format($report->highest_score, 2, ',', '.') : '-' }}</td>
                <td>{{ $report->lowest_score !== null ? number_format($report->lowest_score, 2, ',', '.') : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Petugas Pelaksana,</p>
        <div class="signature-space">
            <strong><u>{{ $report->user->name ?? '.........................................' }}</u></strong>
        </div>
    </div>

</body>
</html>
