<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kegiatan - {{ $event->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 5px 10px;
            font-weight: bold;
            border-left: 4px solid #4f46e5;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .grid-col {
            display: table-cell;
            width: 50%;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DETAIL KEGIATAN</h1>
        <p>Sistem Informasi Layanan CAT (SILAPCAT)</p>
    </div>

    <div class="section">
        <div class="section-title">Informasi Utama</div>
        <table>
            <tr>
                <th width="30%">Nama Kegiatan</th>
                <td>{{ $event->name }}</td>
            </tr>
            <tr>
                <th>Jenis Pengadaan</th>
                <td>{{ $event->procurementType?->name }}</td>
            </tr>
            <tr>
                <th>Tahun Formasi</th>
                <td>{{ $event->formation_year }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ strtoupper($event->status) }}</td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $event->description ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Daftar Instansi</div>
        <table>
            <thead>
                <tr>
                    <th width="50px">No</th>
                    <th>Nama Instansi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->eventInstitutions as $ei)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ei->institution->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Titik Lokasi & Jadwal</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Lokasi</th>
                    <th>Kuota</th>
                    <th>Jadwal</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->eventLocations as $el)
                <tr>
                    <td><strong>{{ $el->location->name }}</strong></td>
                    <td>{{ number_format($el->participants_count) }}</td>
                    <td>
                        {{ $el->start_date?->format('d/m/Y') ?? '-' }} s/d<br>
                        {{ $el->end_date?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td><small>{{ $el->location->city }}, {{ $el->location->address }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Tim Pelaksana</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Pegawai</th>
                    <th>NIP</th>
                    <th>Peran/Penugasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->eventEmployees as $ee)
                <tr>
                    <td>{{ $ee->employee->name }}</td>
                    <td>{{ $ee->employee->employee_number }}</td>
                    <td>{{ is_array($ee->role) ? implode(', ', $ee->role) : $ee->role }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i') }} WIB<br>
        Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>
