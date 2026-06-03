<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Sertifikat UD & UPKP - SILAP CAT</title>
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #e0e7ff;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --card-bg: rgba(255, 255, 255, 0.85);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: radial-gradient(circle at 10% 20%, rgb(242, 235, 243) 0%, rgb(228, 237, 250) 90.1%);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        h1, h2, h3, .brand {
            font-family: 'Outfit', sans-serif;
        }

        /* Navbar & Header */
        header {
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .brand span {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand i {
            color: var(--primary);
            font-size: 1.75rem;
        }

        /* Container & Main Layout */
        .container {
            max-width: 900px;
            width: 100%;
            margin: 3rem auto;
            padding: 0 1.5rem;
            flex-grow: 1;
        }

        /* Glassmorphic Search Card */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
            transition: all 0.3s ease;
        }

        .intro {
            text-align: center;
            margin-bottom: 2rem;
        }

        .intro h1 {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .intro p {
            color: var(--gray);
            font-size: 1.05rem;
            max-width: 500px;
            margin: 0 auto;
        }

        /* Form Controls */
        .search-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .input-control {
            width: 100%;
            padding: 1.1rem 1.1rem 1.1rem 3.2rem;
            font-size: 1.1rem;
            border-radius: 16px;
            border: 2px solid var(--border);
            background: white;
            color: var(--dark);
            font-weight: 500;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }

        .input-control:focus + i {
            color: var(--primary);
        }

        .btn-submit {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, var(--primary) 0%, #4338ca 100%);
            color: white;
            border: none;
            padding: 1.1rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 16px;
            cursor: pointer;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -5px rgba(79, 70, 229, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Error States */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: var(--danger);
            padding: 1rem 1.25rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Results Layout */
        .results-section {
            margin-top: 3rem;
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .results-header {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
        }

        .results-table-wrapper {
            background: white;
            border-radius: 18px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray);
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 1.25rem;
            font-size: 0.95rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 0.6rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 4px 10px -3px rgba(16, 185, 129, 0.3);
            transition: all 0.2s ease;
        }

        .btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -3px rgba(16, 185, 129, 0.4);
        }

        /* Empty / Not Found State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            background: white;
            border-radius: 18px;
            border: 1px dashed var(--gray);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--gray);
            max-width: 400px;
            margin: 0 auto;
        }

        /* Footer */
        footer {
            padding: 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--gray);
            background: rgba(255, 255, 255, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .glass-card {
                padding: 1.5rem;
            }
            
            td, th {
                padding: 0.75rem;
            }

            .intro h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header>
        <a href="{{ route('certificate.index') }}" class="brand">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>SILAP CAT</span>
        </a>
    </header>

    <!-- Main Content Container -->
    <main class="container">
        <div class="glass-card">
            <div class="intro">
                <h1>Unduh Sertifikat Ujian</h1>
                <p>Silakan masukkan NIP atau Nomor Identitas Anda untuk mencari data hasil ujian dan mengunduh sertifikat resmi.</p>
            </div>

            <!-- Form Pencarian -->
            <form action="{{ route('certificate.search') }}" method="POST" class="search-form">
                @csrf
                
                @if ($errors->any())
                    <div class="alert-error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="input-group">
                    <input 
                        type="text" 
                        name="employee_number" 
                        class="input-control" 
                        placeholder="Masukkan NIP / Nomor Identitas Anda..." 
                        value="{{ $employeeNumber ?? old('employee_number') }}"
                        required
                        autocomplete="off"
                    >
                    <i class="fa-solid fa-id-card"></i>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari Sertifikat</span>
                </button>
            </form>
        </div>

        <!-- Hasil Pencarian -->
        @if (isset($searched))
            <section class="results-section">
                <div class="results-header">
                    <i class="fa-solid fa-list-check"></i> Hasil Pencarian untuk NIP: <strong>{{ $employeeNumber }}</strong>
                </div>

                @if ($results->count() > 0)
                    <div class="results-table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Peserta</th>
                                    <th>Kegiatan</th>
                                    <th>Jenis Ujian</th>
                                    <th>Nilai Akhir</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $score)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--dark);">{{ $score->name }}</div>
                                            <div style="font-size: 0.8rem; color: var(--gray);">{{ $score->position }}</div>
                                        </td>
                                        <td>{{ $score->event->name ?? 'Kegiatan Umum' }}</td>
                                        <td>
                                            @if ($score->exam_type === 'UD_I')
                                                UD Tingkat I
                                            @elseif ($score->exam_type === 'UD_II')
                                                UD Tingkat II
                                            @else
                                                UPKP
                                            @endif
                                        </td>
                                        <td style="font-weight: 700; color: var(--primary);">{{ $score->total_score }}</td>
                                        <td>
                                            @if ($score->status === 'Lulus')
                                                <span class="badge badge-success">
                                                    <i class="fa-solid fa-circle-check"></i> Lulus
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fa-solid fa-circle-xmark"></i> Tidak Lulus
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            @if ($score->status === 'Lulus')
                                                <a href="{{ URL::signedRoute('certificate.download', ['examScore' => $score->id]) }}" class="btn-download" target="_blank">
                                                    <i class="fa-solid fa-cloud-arrow-down"></i> Unduh PDF
                                                </a>
                                            @else
                                                <span style="font-size: 0.85rem; color: var(--gray); font-style: italic;">Tidak Tersedia</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fa-solid fa-magnifying-glass-minus"></i>
                        <h3>Data Tidak Ditemukan</h3>
                        <p>Maaf, kami tidak menemukan riwayat ujian dengan NIP tersebut. Pastikan NIP yang Anda masukkan sudah benar.</p>
                    </div>
                @endif
            </section>
        @endif
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} SILAP CAT v2.0 - Badan Kepegawaian Negara. Hak Cipta Dilindungi.</p>
    </footer>

</body>
</html>
