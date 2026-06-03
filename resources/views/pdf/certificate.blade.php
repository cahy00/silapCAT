<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kelulusan - {{ $examScore->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #1e293b;
            -webkit-print-color-adjust: exact;
        }

        /* Certificate Container Frame */
        .certificate-container {
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            padding: 15mm;
            position: relative;
            background-color: #ffffff;
            @if(isset($examScore->event->certificate_template) && $examScore->event->certificate_template)
                background-image: url('{{ public_path('storage/' . $examScore->event->certificate_template) }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            @endif
        }

        /* Elegant Double Border Frame */
        .outer-border {
            @if(isset($examScore->event->certificate_template) && $examScore->event->certificate_template)
                /* Hide border if using template */
                border: none;
            @else
                border: 6px double #d97706; /* Gold Color */
            @endif
            height: 100%;
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            position: relative;
        }

        .inner-border {
            @if(isset($examScore->event->certificate_template) && $examScore->event->certificate_template)
                /* Hide inner border and background gradient if using template */
                border: none;
                background: transparent;
            @else
                border: 1px solid #1e293b; /* Navy Line */
                background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(254,252,246,1) 100%);
            @endif
            height: 100%;
            width: 100%;
            box-sizing: border-box;
            padding: 12mm 15mm;
            position: relative;
        }

        /* Decorative Corners */
        .corner {
            @if(isset($examScore->event->certificate_template) && $examScore->event->certificate_template)
                display: none;
            @else
                position: absolute;
                width: 40px;
                height: 40px;
                border-color: #d97706;
                border-style: solid;
            @endif
        }
        .corner-tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; }
        .corner-tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; }
        .corner-bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; }
        .corner-br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; }

        /* Header Style */
        .header {
            @if(isset($examScore->event->certificate_template) && $examScore->event->certificate_template)
                /* Push content down if using template, or hide logo if template has it */
                margin-top: 10mm;
            @endif
            text-align: center;
            margin-bottom: 6mm;
        }

        .logo-text {
            font-size: 14pt;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .logo-subtext {
            font-size: 9pt;
            color: #64748b;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .main-title {
            text-align: center;
            margin: 8mm 0 4mm 0;
        }

        .main-title h1 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 34pt;
            color: #1e3a8a; /* Deep Navy */
            margin: 0;
            font-weight: normal;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .certificate-number {
            font-size: 10pt;
            color: #64748b;
            font-style: italic;
            margin-top: 3px;
        }

        .award-text {
            text-align: center;
            font-size: 11pt;
            color: #475569;
            margin: 5mm 0;
        }

        .recipient-name {
            text-align: center;
            margin: 4mm 0;
        }

        .recipient-name h2 {
            font-size: 24pt;
            color: #1e293b;
            border-bottom: 2px solid #d97706;
            display: inline-block;
            padding-bottom: 5px;
            margin: 0;
            font-weight: bold;
        }

        .recipient-nip {
            font-size: 12pt;
            color: #334155;
            font-weight: 500;
            margin-top: 5px;
        }

        .details-text {
            text-align: center;
            font-size: 11pt;
            line-height: 1.6;
            margin: 4mm auto;
            max-width: 80%;
            color: #334155;
        }

        /* Score Table block */
        .score-block {
            margin: 8mm auto 4mm auto;
            width: 60%;
        }

        .score-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .score-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #1e3a8a;
            text-align: center;
        }

        .score-table td {
            padding: 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-weight: bold;
        }

        /* Signature block using Table layout to work perfectly in DomPDF */
        .footer-table {
            width: 100%;
            margin-top: 10mm;
            position: absolute;
            bottom: 12mm;
            left: 15mm;
            right: 15mm;
        }

        .footer-cell {
            width: 33.33%;
            vertical-align: bottom;
            text-align: center;
        }

        .qr-placeholder {
            display: inline-block;
            width: 80px;
            height: 80px;
            padding: 5px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
        }

        .qr-placeholder img {
            width: 100%;
            height: 100%;
        }

        .signature-title {
            font-size: 10pt;
            color: #475569;
            margin-bottom: 15mm;
        }

        .signature-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1e293b;
            text-decoration: underline;
        }

        .signature-nip {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <div class="certificate-container">
        <div class="outer-border">
            <div class="inner-border">
                <!-- Decorative Gold Corners inside inner border -->
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>

                <!-- Header / Institution -->
                <div class="header">
                    <div class="logo-text">Badan Kepegawaian Negara</div>
                    <div class="logo-subtext">SISTEM INFORMASI LAYANAN ASESMEN & PENILAIAN CAT (SILAP CAT)</div>
                </div>

                <!-- Main Certificate Title -->
                <div class="main-title">
                    <h1>Sertifikat Kelulusan</h1>
                    <div class="certificate-number">Nomor Sertifikat: B/{{ $examScore->id }}/CAT/{{ date('Y') }}</div>
                </div>

                <!-- Award text -->
                <div class="award-text">
                    Diberikan Kepada:
                </div>

                <!-- Recipient Info -->
                <div class="recipient-name">
                    <h2>{{ $examScore->name }}</h2>
                    <div class="recipient-nip">NIP: {{ $examScore->employee_number }}</div>
                </div>

                <!-- Details Statement -->
                <div class="details-text">
                    Dinyatakan <strong>LULUS</strong> dalam kegiatan 
                    <strong>{{ $examScore->event->name ?? 'Ujian Dinas / UPKP BKN' }}</strong> 
                    pada kualifikasi 
                    <strong>
                        @if ($examScore->exam_type === 'UD_I')
                            Ujian Dinas Tingkat I (UD I)
                        @elseif ($examScore->exam_type === 'UD_II')
                            Ujian Dinas Tingkat II (UD II)
                        @else
                            Ujian Penyesuaian Kenaikan Pangkat (UPKP)
                        @endif
                    </strong> 
                    yang diselenggarakan pada tanggal {{ \Carbon\Carbon::parse($examScore->exam_date)->translatedFormat('d F Y') }} dengan rincian nilai sebagai berikut:
                </div>

                <!-- Score Details Table -->
                <div class="score-block">
                    <table class="score-table">
                        <thead>
                            <tr>
                                <th>Nilai CAT BKN (Raw)</th>
                                @if($examScore->exam_type !== 'UD_I')
                                    <th>Nilai Wawancara</th>
                                @endif
                                <th>Nilai Akhir (Skala 100)</th>
                                <th>Status Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $examScore->cat_score }}</td>
                                @if($examScore->exam_type !== 'UD_I')
                                    <td>{{ $examScore->interview_score ?? 'N/A' }}</td>
                                @endif
                                <td style="color: #1e3a8a; font-size: 11pt;">{{ $examScore->total_score }}</td>
                                <td style="color: #059669;">LULUS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Signatures & Verification (Using Table to render perfectly in DomPDF) -->
                <table class="footer-table">
                    <tr>
                        <!-- Left Side: QR Verification Code -->
                        <td class="footer-cell" style="text-align: left;">
                            <div class="qr-placeholder">
                                <!-- Generate Dynamic Verification QR Code (using a public mock QR generator or local placeholder) -->
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url()->current()) }}" alt="QR Verification">
                            </div>
                            <div style="font-size: 8pt; color: #64748b; margin-top: 5px;">
                                Pindai QR untuk verifikasi<br>keaslian sertifikat digital ini.
                            </div>
                        </td>
                        
                        <!-- Middle Space: Empty spacer cell -->
                        <td class="footer-cell"></td>

                        <!-- Right Side: Official Signature -->
                        <td class="footer-cell" style="text-align: right;">
                            <div class="signature-title">
                                Jakarta, {{ $downloadDate }}<br>
                                Kepala Pusat Penilaian Kompetensi ASN
                            </div>
                            <div class="signature-name">
                                Dr. H. Ahmad Yani, M.Si.
                            </div>
                            <div class="signature-nip">
                                NIP. 19740510 199903 1 002
                            </div>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

</body>
</html>
