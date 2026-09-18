<x-filament-panels::page>
    <style>
        /* =================== Schedule Generator Custom Styles =================== */
        .sg-page { display: flex; flex-direction: column; gap: 1.75rem; }

        /* Hero Banner */
        .sg-hero {
            position: relative; overflow: hidden; border-radius: 1rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            padding: 1.75rem 2rem; color: #fff;
            border: 1px solid rgba(99, 102, 241, 0.15);
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }
        .sg-hero::before {
            content: ''; position: absolute; right: -60px; bottom: -60px;
            width: 260px; height: 260px; border-radius: 50%;
            background: rgba(99, 102, 241, 0.08); filter: blur(60px);
        }
        .sg-hero-inner { position: relative; z-index: 1; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem; }
        .sg-hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 600;
            background: rgba(99, 102, 241, 0.15); color: #a5b4fc;
            border: 1px solid rgba(129, 140, 248, 0.25);
        }
        .sg-hero h1 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.025em; margin: 6px 0 4px; color: #fff; }
        .sg-hero p { font-size: 0.8125rem; color: #94a3b8; line-height: 1.6; max-width: 600px; }
        .sg-hero-stats { display: flex; gap: 10px; }
        .sg-stat-card {
            padding: 12px 18px; border-radius: 12px; text-align: center; min-width: 90px;
            background: rgba(255,255,255,0.04); backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .sg-stat-card .sg-stat-val { display: block; font-size: 1.5rem; font-weight: 900; color: #818cf8; }
        .sg-stat-card .sg-stat-lbl { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; font-weight: 600; }
        .sg-stat-card.sg-stat-green .sg-stat-val { color: #34d399; }
        .sg-stat-card.sg-stat-green { background: rgba(52, 211, 153, 0.06); border-color: rgba(52, 211, 153, 0.15); }
        .sg-stat-card.sg-stat-green .sg-stat-lbl { color: #6ee7b7; }
        .sg-stat-card.sg-stat-purple .sg-stat-val { color: #c084fc; }
        .sg-stat-card.sg-stat-purple { background: rgba(192, 132, 252, 0.06); border-color: rgba(192, 132, 252, 0.15); }
        .sg-stat-card.sg-stat-purple .sg-stat-lbl { color: #d8b4fe; }

        /* Section Cards */
        .sg-card {
            background: #fff; border-radius: 1rem; padding: 1.5rem;
            border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dark .sg-card { background: #0f172a; border-color: #1e293b; }

        .sg-card-header {
            display: flex; align-items: center; gap: 12px;
            padding-bottom: 1rem; margin-bottom: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .dark .sg-card-header { border-bottom-color: #1e293b; }

        .sg-card-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .sg-card-icon svg { width: 18px; height: 18px; }
        .sg-card-icon.sg-icon-indigo { background: #eef2ff; color: #4f46e5; }
        .sg-card-icon.sg-icon-emerald { background: #ecfdf5; color: #059669; }
        .sg-card-icon.sg-icon-teal { background: #f0fdfa; color: #0d9488; }
        .sg-card-icon.sg-icon-purple { background: #faf5ff; color: #9333ea; }
        .dark .sg-card-icon.sg-icon-indigo { background: rgba(79, 70, 229, 0.12); color: #818cf8; }
        .dark .sg-card-icon.sg-icon-emerald { background: rgba(5, 150, 105, 0.12); color: #34d399; }
        .dark .sg-card-icon.sg-icon-teal { background: rgba(13, 148, 136, 0.12); color: #2dd4bf; }
        .dark .sg-card-icon.sg-icon-purple { background: rgba(147, 51, 234, 0.12); color: #c084fc; }

        .sg-card-title { font-size: 0.9375rem; font-weight: 700; color: #0f172a; }
        .sg-card-desc { font-size: 0.75rem; color: #64748b; margin-top: 2px; }
        .dark .sg-card-title { color: #f1f5f9; }
        .dark .sg-card-desc { color: #94a3b8; }

        /* Form Fields */
        .sg-form-grid { display: grid; gap: 1rem; }
        .sg-form-grid.sg-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .sg-form-grid.sg-cols-4 .sg-col-span-2 { grid-column: span 2; }
        @media (max-width: 768px) {
            .sg-form-grid.sg-cols-4 { grid-template-columns: 1fr; }
            .sg-form-grid.sg-cols-4 .sg-col-span-2 { grid-column: span 1; }
        }

        .sg-label {
            display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: #475569; margin-bottom: 6px;
        }
        .sg-label .sg-req { color: #ef4444; }
        .dark .sg-label { color: #94a3b8; }

        .sg-input, .sg-select {
            width: 100%; font-size: 0.8125rem; padding: 9px 12px; border-radius: 10px;
            border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a;
            transition: all 0.15s ease;
            outline: none;
        }
        .sg-input:focus, .sg-select:focus {
            border-color: #6366f1; background: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }
        .dark .sg-input, .dark .sg-select {
            background: #1e293b; border-color: #334155; color: #e2e8f0;
        }
        .dark .sg-input:focus, .dark .sg-select:focus {
            border-color: #818cf8; background: #0f172a;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        .sg-error { font-size: 11px; color: #ef4444; font-weight: 500; margin-top: 3px; }

        /* Toolbar */
        .sg-toolbar {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;
            padding-bottom: 1rem; margin-bottom: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .dark .sg-toolbar { border-bottom-color: #1e293b; }

        .sg-toolbar-left { display: flex; align-items: center; gap: 12px; }
        .sg-toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .sg-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 600;
            border: none; cursor: pointer; transition: all 0.15s ease;
            white-space: nowrap;
        }
        .sg-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
        .sg-btn-secondary {
            background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
        }
        .sg-btn-secondary:hover { background: #e2e8f0; }
        .dark .sg-btn-secondary { background: #1e293b; color: #cbd5e1; border-color: #334155; }
        .dark .sg-btn-secondary:hover { background: #334155; }
        
        .sg-btn-ga {
            background: linear-gradient(135deg, #7c3aed, #9333ea); color: #fff;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
        }
        .sg-btn-ga:hover { box-shadow: 0 4px 16px rgba(124, 58, 237, 0.35); filter: brightness(1.08); }

        .sg-btn-primary {
            background: linear-gradient(135deg, #4f46e5, #3b82f6); color: #fff;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }
        .sg-btn-primary:hover { box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35); filter: brightness(1.05); }
        
        .sg-btn-save {
            padding: 10px 24px; font-size: 13px; font-weight: 700; border-radius: 12px;
            background: linear-gradient(135deg, #059669, #0d9488); color: #fff;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
        }
        .sg-btn-save:hover { box-shadow: 0 4px 16px rgba(5, 150, 105, 0.35); filter: brightness(1.05); }

        /* GA Banner */
        .sg-ga-banner {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 10px 16px; border-radius: 10px; margin-bottom: 1.25rem;
            background: rgba(147, 51, 234, 0.08); border: 1px solid rgba(147, 51, 234, 0.25);
            font-size: 12px; color: #6b21a8;
        }
        .dark .sg-ga-banner { background: rgba(147, 51, 234, 0.15); color: #d8b4fe; border-color: rgba(192, 132, 252, 0.3); }

        /* Spreadsheet Table */
        .sg-table-wrap {
            overflow-x: auto; border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        .dark .sg-table-wrap { border-color: #1e293b; }

        .sg-table {
            width: 100%; font-size: 12px; text-align: left;
            border-collapse: collapse; color: #334155;
        }
        .dark .sg-table { color: #cbd5e1; }

        .sg-table thead th {
            padding: 10px 12px; font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            background: #f8fafc; color: #64748b;
            border-bottom: 2px solid #e2e8f0; white-space: nowrap;
        }
        .dark .sg-table thead th { background: #1e293b; color: #94a3b8; border-bottom-color: #334155; }

        .sg-table tbody td {
            padding: 8px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
        }
        .dark .sg-table tbody td { border-bottom-color: #1e293b; }

        .sg-table tbody tr:hover td { background: rgba(99, 102, 241, 0.03); }
        .dark .sg-table tbody tr:hover td { background: rgba(99, 102, 241, 0.06); }

        .sg-table .sg-cell-num {
            text-align: center; font-weight: 800; color: #94a3b8; width: 40px;
        }
        .sg-table .sg-cell-select select {
            width: 100%; min-width: 90px; font-size: 12px; padding: 5px 4px; border-radius: 8px;
            border: 1px solid #e2e8f0; background: #f8fafc; color: #0f172a;
            outline: none;
        }
        .sg-table .sg-cell-select select:focus {
            border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,0.12);
        }
        .dark .sg-table .sg-cell-select select {
            background: #1e293b; border-color: #334155; color: #e2e8f0;
        }
        .sg-table .sg-cell-input input {
            width: 100%; min-width: 65px; font-size: 12px; padding: 5px 4px; border-radius: 8px;
            border: 1px solid #e2e8f0; background: #f8fafc; color: #0f172a;
            text-align: center; font-weight: 700; outline: none;
        }
        .sg-table .sg-cell-input input:focus {
            border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,0.12);
        }
        .dark .sg-table .sg-cell-input input {
            background: #1e293b; border-color: #334155; color: #e2e8f0;
        }
        .sg-table .sg-cell-input.sg-highlight input {
            background: #eef2ff; border-color: #c7d2fe; color: #3730a3; font-weight: 800;
        }
        .dark .sg-table .sg-cell-input.sg-highlight input {
            background: rgba(99,102,241,0.12); border-color: rgba(129,140,248,0.3); color: #a5b4fc;
        }
        .sg-table .sg-cell-checkbox { text-align: center; }
        .sg-table .sg-cell-checkbox input[type="checkbox"] {
            width: 16px; height: 16px; border-radius: 4px;
            accent-color: #6366f1; cursor: pointer;
        }
        .sg-table .sg-cell-action { text-align: center; width: 44px; }
        .sg-btn-delete {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 8px; border: none;
            background: transparent; color: #94a3b8; cursor: pointer; transition: all 0.15s;
        }
        .sg-btn-delete:hover { background: #fef2f2; color: #ef4444; }
        .dark .sg-btn-delete:hover { background: rgba(239,68,68,0.1); color: #f87171; }
        .sg-btn-delete svg { width: 16px; height: 16px; }

        /* Badge Rekomendasi GA */
        .sg-rec-badge {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 6px;
            background: rgba(147, 51, 234, 0.12); color: #7c3aed;
            border: 1px solid rgba(147, 51, 234, 0.25);
            text-transform: uppercase; letter-spacing: 0.04em;
        }
        .dark .sg-rec-badge { background: rgba(192, 132, 252, 0.15); color: #c084fc; border-color: rgba(192, 132, 252, 0.3); }

        /* Results Section */
        .sg-result-block {
            padding: 1.25rem; border-radius: 12px;
            border: 1px solid #e2e8f0; background: #fafbfc; margin-bottom: 1.25rem;
        }
        .dark .sg-result-block { background: rgba(30, 41, 59, 0.4); border-color: #1e293b; }

        .sg-result-header {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;
            padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9;
        }
        .dark .sg-result-header { border-bottom-color: #1e293b; }

        .sg-result-tag {
            display: inline-block; padding: 3px 10px; border-radius: 6px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            background: #eef2ff; color: #4338ca;
        }
        .dark .sg-result-tag { background: rgba(99,102,241,0.12); color: #a5b4fc; }

        .sg-result-title { font-size: 0.9375rem; font-weight: 800; color: #0f172a; margin-top: 4px; }
        .sg-result-subtitle { font-size: 12px; color: #64748b; margin-top: 2px; }
        .sg-result-subtitle strong { color: #4f46e5; font-weight: 600; }
        .dark .sg-result-title { color: #f1f5f9; }
        .dark .sg-result-subtitle { color: #94a3b8; }
        .dark .sg-result-subtitle strong { color: #818cf8; }

        .sg-result-meta { display: flex; flex-wrap: wrap; gap: 6px; }
        .sg-meta-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 500;
            background: #fff; border: 1px solid #e2e8f0; color: #475569;
        }
        .dark .sg-meta-pill { background: #1e293b; border-color: #334155; color: #cbd5e1; }
        .sg-meta-pill strong { font-weight: 700; color: #0f172a; }
        .dark .sg-meta-pill strong { color: #f1f5f9; }
        .sg-meta-pill strong.sg-green { color: #059669; }
        .dark .sg-meta-pill strong.sg-green { color: #34d399; }

        /* Role Pill in Results */
        .sg-role-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;
        }
        .sg-role-k { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .sg-role-it { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .sg-role-p { background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }
        .dark .sg-role-k { background: rgba(99,102,241,0.15); color: #a5b4fc; border-color: rgba(129,140,248,0.3); }
        .dark .sg-role-it { background: rgba(5,150,105,0.15); color: #6ee7b7; border-color: rgba(52,211,153,0.3); }
        .dark .sg-role-p { background: rgba(234,88,12,0.15); color: #fdba74; border-color: rgba(251,146,60,0.3); }

        /* Matrix Result Table */
        .sg-matrix-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .sg-matrix-table thead th {
            padding: 8px 12px; font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            background: #f1f5f9; color: #64748b;
            border-bottom: 2px solid #e2e8f0; white-space: nowrap;
        }
        .dark .sg-matrix-table thead th { background: #1e293b; color: #94a3b8; border-bottom-color: #334155; }
        .sg-matrix-table thead th.sg-col-total {
            background: #e2e8f0; color: #475569; font-weight: 800;
        }
        .dark .sg-matrix-table thead th.sg-col-total { background: #334155; color: #e2e8f0; }

        .sg-matrix-table tbody td {
            padding: 6px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
        }
        .dark .sg-matrix-table tbody td { border-bottom-color: #1e293b; }
        .sg-matrix-table tbody tr:hover td { background: rgba(99,102,241,0.03); }
        .dark .sg-matrix-table tbody tr:hover td { background: rgba(99,102,241,0.05); }

        .sg-day-num { text-align: center; font-weight: 800; color: #4f46e5; }
        .dark .sg-day-num { color: #818cf8; }
        .sg-day-date { font-weight: 500; color: #334155; }
        .dark .sg-day-date { color: #cbd5e1; }

        .sg-session-input {
            width: 72px; font-size: 12px; text-align: center; font-weight: 700;
            padding: 4px 6px; border-radius: 6px; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #0f172a; outline: none;
        }
        .sg-session-input:focus {
            border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,0.12);
        }
        .dark .sg-session-input { background: #1e293b; border-color: #334155; color: #e2e8f0; }

        .sg-day-total {
            text-align: center; font-weight: 900; color: #059669;
            background: rgba(5, 150, 105, 0.04);
        }
        .dark .sg-day-total { color: #34d399; background: rgba(52, 211, 153, 0.06); }

        /* Save action bar */
        .sg-save-bar {
            display: flex; justify-content: flex-end; padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
        }
        .dark .sg-save-bar { border-top-color: #1e293b; }

        /* Spinner */
        @keyframes sg-spin { to { transform: rotate(360deg); } }
        .sg-spinner { animation: sg-spin 0.7s linear infinite; }
    </style>

    <div class="sg-page">

        {{-- Hero Banner --}}
        <div class="sg-hero">
            <div class="sg-hero-inner">
                <div>
                    <div class="sg-hero-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Modul Matrix Penjadwalan Massal + Algoritma Genetika
                    </div>
                    <h1>Spreadsheet Penjadwalan Event Ujian</h1>
                    <p>Input instansi, titik lokasi (tilok), kapasitas PC, dan ruangan ujian. Sistem mengoptimalkan alokasi petugas (Koordinator 1:1 Tilok, Tim IT 1:50 PC, Pengawas 1:25 PC / Ruangan) menggunakan <strong>Algoritma Genetika</strong> agar bebas bentrokan jadwal.</p>
                </div>

                <div class="sg-hero-stats">
                    <div class="sg-stat-card">
                        <span class="sg-stat-val">{{ count($items) }}</span>
                        <span class="sg-stat-lbl">Baris Input</span>
                    </div>
                    @if($isGaApplied)
                        <div class="sg-stat-card sg-stat-purple">
                            <span class="sg-stat-val">🧬 GA</span>
                            <span class="sg-stat-lbl">Rekomendasi</span>
                        </div>
                    @endif
                    @if($isGenerated)
                        <div class="sg-stat-card sg-stat-green">
                            <span class="sg-stat-val">{{ count($generatedResults) }}</span>
                            <span class="sg-stat-lbl">Ter-generate</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section 1: Parameter Utama Event --}}
        <div class="sg-card">
            <div class="sg-card-header">
                <div class="sg-card-icon sg-icon-indigo">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <div class="sg-card-title">1. Parameter Utama Event / Kegiatan</div>
                    <div class="sg-card-desc">Tentukan nama kegiatan, tahun formasi, dan kategori pengadaan.</div>
                </div>
            </div>

            <div class="sg-form-grid sg-cols-4">
                <div class="sg-col-span-2">
                    <label class="sg-label">Nama Event / Kegiatan <span class="sg-req">*</span></label>
                    <input type="text" wire:model="eventName" placeholder="Contoh: Seleksi CPNS CAT BKN Tahun 2026" class="sg-input" />
                    @error('eventName') <div class="sg-error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="sg-label">Tahun Formasi <span class="sg-req">*</span></label>
                    <input type="number" wire:model="formationYear" placeholder="2026" class="sg-input" />
                    @error('formationYear') <div class="sg-error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="sg-label">Jenis Pengadaan</label>
                    <select wire:model="procurementTypeId" class="sg-select">
                        <option value="">-- Pilih Jenis Pengadaan --</option>
                        @foreach($procurementTypesOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 2: Spreadsheet Input --}}
        <div class="sg-card">
            <div class="sg-toolbar">
                <div class="sg-toolbar-left">
                    <div class="sg-card-icon sg-icon-emerald">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="sg-card-title">2. Spreadsheet Entry (Instansi & Tilok)</div>
                        <div class="sg-card-desc">Masukkan kombinasi instansi, titik lokasi, kuota peserta, dan kapasitas ruangan. Gunakan GA untuk optimasi penugasan petugas.</div>
                    </div>
                </div>

                <div class="sg-toolbar-right">
                    <button type="button" wire:click="addRow" class="sg-btn sg-btn-secondary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Baris
                    </button>

                    <button type="button" wire:click="generateSchedules" wire:loading.attr="disabled" class="sg-btn sg-btn-primary">
                        <span wire:loading.remove wire:target="generateSchedules" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Generate Jadwal & Petugas (GA)
                        </span>
                        <span wire:loading wire:target="generateSchedules" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg class="sg-spinner" fill="none" viewBox="0 0 24 24"><circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Mengkalkulasi & Optimasi GA...
                        </span>
                    </button>
                </div>
            </div>

            @if($isGaApplied)
                <div class="sg-ga-banner">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 16px;">🧬</span>
                        <div>
                            <strong>Algoritma Genetika Diterapkan Otomatis!</strong> Petugas telah dialokasikan sesuai rasio kapasitas (Koordinator 1:1 Tilok, IT 1:50 PC, Pengawas 1:25 PC / Ruangan) bebas bentrokan. Anda dapat menyesuaikan manual pada kartu hasil di bawah.
                        </div>
                    </div>
                </div>
            @endif

            <div class="sg-table-wrap">
                <table class="sg-table">
                    <thead>
                        <tr>
                            <th style="text-align:center; width: 40px;">#</th>
                            <th style="min-width: 200px;">Instansi <span style="color:#ef4444;">*</span></th>
                            <th style="min-width: 200px;">Titik Lokasi (Tilok) <span style="color:#ef4444;">*</span></th>
                            <th style="text-align:center; min-width: 90px; width: 90px;">Kapasitas PC</th>
                            <th style="text-align:center; min-width: 110px; width: 110px;">Peserta <span style="color:#ef4444;">*</span></th>
                            <th style="text-align:center; min-width: 140px; width: 140px;">Tgl Mulai <span style="color:#ef4444;">*</span></th>
                            <th style="text-align:center; min-width: 110px; width: 110px;">Pengaturan Sesi</th>
                            <th style="text-align:center; width: 75px;">H-1 Buka</th>
                            <th style="text-align:center; width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td class="sg-cell-num">{{ $index + 1 }}</td>
                                <td class="sg-cell-select">
                                    <select wire:model.live="items.{{ $index }}.institution_id">
                                        <option value="">-- Pilih Instansi --</option>
                                        @foreach($institutionsOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error("items.{$index}.institution_id")
                                        <div class="sg-error">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="sg-cell-select">
                                    <select wire:model.live="items.{{ $index }}.location_id">
                                        <option value="">-- Pilih Tilok --</option>
                                        @foreach($locationsOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error("items.{$index}.location_id")
                                        <div class="sg-error">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="sg-cell-input">
                                    <input type="number" wire:model.live="items.{{ $index }}.pc_capacity" min="1" placeholder="PC" />
                                </td>
                                <td class="sg-cell-input sg-highlight">
                                    <input type="number" wire:model.live="items.{{ $index }}.participants_count" min="1" placeholder="Peserta" />
                                </td>
                                <td class="sg-cell-input">
                                    <input type="date" wire:model.live="items.{{ $index }}.start_date" style="font-weight: 500;" />
                                </td>
                                <td class="sg-cell-select">
                                    <select wire:model.live="items.{{ $index }}.sessions_per_day" style="text-align: center; font-weight: 600;">
                                        <option value="2">2 Sesi</option>
                                        <option value="3">3 Sesi</option>
                                        <option value="4">4 Sesi</option>
                                    </select>
                                </td>
                                <td class="sg-cell-checkbox">
                                    <input type="checkbox" wire:model.live="items.{{ $index }}.has_opening_day" />
                                </td>
                                <td class="sg-cell-action">
                                    <button type="button" wire:click="removeRow({{ $index }})" class="sg-btn-delete" title="Hapus Baris">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 3: Generated Results Matrix --}}
        @if($isGenerated && !empty($generatedResults))
            <div class="sg-card">
                <div class="sg-card-header">
                    <div class="sg-card-icon sg-icon-teal">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="sg-card-title">3. Matriks Penjadwalan per Hari & Sesi</div>
                        <div class="sg-card-desc">Petugas telah di-assign otomatis. Anda dapat menyesuaikan kuota sesi atau mengubah petugas secara manual jika diperlukan.</div>
                    </div>
                </div>

                @foreach($generatedResults as $rIndex => $res)
                    @php 
                        $calc = $res['calculation']; 
                        $rowItemIdx = $res['index'] ?? $rIndex;
                    @endphp
                    <div class="sg-result-block">
                        <div class="sg-result-header">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="sg-result-tag">Instansi & Tilok #{{ $rIndex + 1 }}</span>
                                    @if(!empty($res['is_ga_recommendation']))
                                        <span class="sg-rec-badge">🧬 Rekomendasi GA</span>
                                    @else
                                        <span class="sg-rec-badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-color: rgba(16, 185, 129, 0.25);">✏️ Manual</span>
                                    @endif
                                </div>
                                <div class="sg-result-title">{{ $res['institution_name'] }}</div>
                                <div class="sg-result-subtitle">
                                    Lokasi Ujian: <strong>{{ $res['location_name'] }}</strong>
                                </div>
                                <div style="margin-top: 10px; display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                                    @if(!empty($res['koordinator_names']))
                                        <span class="sg-role-pill sg-role-k">
                                            👔 Koordinator: <strong>{{ $res['koordinator_names'] }}</strong>
                                        </span>
                                    @endif
                                    @if(!empty($res['it_names']))
                                        <span class="sg-role-pill sg-role-it">
                                            💻 Tim IT: <strong>{{ $res['it_names'] }}</strong>
                                        </span>
                                    @endif
                                    @if(!empty($res['pengawas_names']))
                                        <span class="sg-role-pill sg-role-p">
                                            👁️ Pengawas: <strong>{{ $res['pengawas_names'] }}</strong>
                                        </span>
                                    @endif

                                    {{-- Tombol Edit Petugas Manual Setelah Generate --}}
                                    <button type="button" wire:click="openOfficerModal({{ $rowItemIdx }})"
                                            style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; cursor: pointer; transition: all 0.15s; background: #ffffff; border: 1px solid #c7d2fe; color: #4338ca; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                            class="dark:bg-slate-800 dark:border-slate-700 dark:text-indigo-400">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 13px; height: 13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Edit Petugas
                                    </button>
                                </div>
                            </div>

                            <div class="sg-result-meta">
                                <div class="sg-meta-pill">
                                    Total Peserta: <strong style="margin-left: 4px;">{{ number_format($calc['total_participants']) }}</strong>
                                </div>
                                <div class="sg-meta-pill">
                                    Total Hari: <strong class="sg-green" style="margin-left: 4px;">{{ $calc['total_exam_days'] }} Hari</strong>
                                </div>
                                <div class="sg-meta-pill">
                                    Rentang:
                                    <strong style="margin-left: 4px;">{{ \Carbon\Carbon::parse($calc['start_date'])->format('d M Y') }} – {{ \Carbon\Carbon::parse($calc['end_date'])->format('d M Y') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="sg-table-wrap">
                            <table class="sg-matrix-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; width: 80px;">Hari Ke</th>
                                        <th style="width: 180px;">Waktu & Tanggal</th>
                                        @for($s = 1; $s <= $calc['sessions_per_day']; $s++)
                                            <th style="text-align: center;">Sesi {{ $s }}</th>
                                        @endfor
                                        <th style="text-align: center;" class="sg-col-total">Total Hari Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($calc['days'] as $dIndex => $day)
                                        <tr>
                                            <td class="sg-day-num">Hari {{ $day['day_number'] }}</td>
                                            <td class="sg-day-date">
                                                {{ \Carbon\Carbon::parse($day['date'])->translatedFormat('l, d M Y') }}
                                            </td>

                                            @for($s = 1; $s <= $calc['sessions_per_day']; $s++)
                                                @php $sKey = "session_$s"; @endphp
                                                <td style="text-align: center;">
                                                    <input type="number"
                                                        value="{{ $day['sessions'][$sKey] ?? 0 }}"
                                                        wire:change="updateGeneratedSessionQuota({{ $rIndex }}, {{ $dIndex }}, '{{ $sKey }}', $event.target.value)"
                                                        class="sg-session-input" />
                                                </td>
                                            @endfor

                                            <td class="sg-day-total">
                                                {{ number_format($day['day_total']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                {{-- Save Action --}}
                <div class="sg-save-bar">
                    <button type="button" wire:click="saveSchedules" wire:loading.attr="disabled" class="sg-btn sg-btn-save">
                        <span wire:loading.remove wire:target="saveSchedules" style="display: inline-flex; align-items: center; gap: 8px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Penjadwalan ke Event Utama
                        </span>
                        <span wire:loading wire:target="saveSchedules" style="display: inline-flex; align-items: center; gap: 8px;">
                            <svg class="sg-spinner" fill="none" viewBox="0 0 24 24" style="width:18px;height:18px;"><circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Modal Petugas Tilok (3 Role Sections + Requirements) --}}
        @if($showOfficerModal)
            <div style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); padding: 1rem;">
                <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 580px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; border: 1px solid #e2e8f0;" class="dark:bg-slate-900 dark:border-slate-800">
                    
                    {{-- Modal Header --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; background: #f8fafc; flex-shrink: 0;" class="dark:bg-slate-800/50 dark:border-slate-800">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;" class="dark:text-slate-100">Atur Petugas Tilok</h3>
                                <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;" class="dark:text-slate-400">
                                    Baris #{{ ($activeOfficerModalIndex ?? 0) + 1 }}
                                    @if(isset($items[$activeOfficerModalIndex]['institution_id']) && isset($institutionsOptions[$items[$activeOfficerModalIndex]['institution_id']]))
                                        - {{ $institutionsOptions[$items[$activeOfficerModalIndex]['institution_id']] }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeOfficerModal" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px; border-radius: 6px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div style="padding: 1.25rem 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1.25rem;">
                        
                        {{-- Requirements Summary Badge --}}
                        @if(!empty($modalRequirements))
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; padding: 10px 12px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0;" class="dark:bg-slate-800 dark:border-slate-700">
                                <span style="font-size: 11px; font-weight: 700; color: #64748b; width: 100%;" class="dark:text-slate-400">
                                    📋 Standar Kebutuhan Petugas (Kapasitas: {{ number_format($items[$activeOfficerModalIndex]['pc_capacity'] ?? 0) }} PC | {{ $items[$activeOfficerModalIndex]['room_count'] ?? 1 }} Ruangan):
                                </span>
                                <span class="sg-role-pill sg-role-k">
                                    👔 Koordinator: <strong>{{ $modalRequirements['koordinator'] ?? 1 }} orang</strong>
                                </span>
                                <span class="sg-role-pill sg-role-it">
                                    💻 Tim IT (1:50 PC): <strong>{{ $modalRequirements['it'] ?? 1 }} orang</strong>
                                </span>
                                <span class="sg-role-pill sg-role-p">
                                    👁️ Pengawas (1:25 PC / Ruangan): <strong>{{ $modalRequirements['pengawas'] ?? 1 }} orang</strong>
                                </span>
                            </div>
                        @endif

                        <p style="font-size: 11px; color: #64748b; margin: 0; padding: 6px 10px; background: #f1f5f9; border-radius: 6px;" class="dark:bg-slate-800 dark:text-slate-400">
                            💡 <em>Petunjuk: Tahan tombol <strong>Ctrl</strong> (atau <strong>Cmd</strong> di Mac) untuk memilih lebih dari 1 pegawai. Anda juga dapat memilih pegawai di luar filter jika diperlukan.</em>
                        </p>

                        {{-- Section 1: Koordinator --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                                <label style="font-size: 12px; font-weight: 700; color: #3730a3;" class="dark:text-indigo-400">
                                    👔 Koordinator Tilok <span style="color:#ef4444;">*</span> (Standar: 1 orang per tilok)
                                </label>
                                <span style="font-size: 11px; font-weight: 600; color: #6366f1;">
                                    Terpilih: {{ count($modalKoordinatorIds) }}
                                </span>
                            </div>
                            <select wire:model.live="modalKoordinatorIds" multiple size="4" style="width: 100%; font-size: 12px; padding: 6px 8px; border-radius: 8px; border: 1px solid #c7d2fe; background: #fff; color: #0f172a; outline: none;" class="dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
                                @if(!empty($koordinatorOptions))
                                    <optgroup label="-- Pegawai dengan Status Koordinator --">
                                        @foreach($koordinatorOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="-- Semua Pegawai --">
                                        @foreach($employeesOptions as $id => $name)
                                            @if(!isset($koordinatorOptions[$id]))
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @else
                                    @foreach($employeesOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Section 2: Tim IT --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                                <label style="font-size: 12px; font-weight: 700; color: #065f46;" class="dark:text-emerald-400">
                                    💻 Tim IT <span style="color:#ef4444;">*</span> (Standar: {{ $modalRequirements['it'] ?? 1 }} orang [1:50 PC])
                                </label>
                                <span style="font-size: 11px; font-weight: 600; color: #059669;">
                                    Terpilih: {{ count($modalItIds) }}
                                </span>
                            </div>
                            <select wire:model.live="modalItIds" multiple size="4" style="width: 100%; font-size: 12px; padding: 6px 8px; border-radius: 8px; border: 1px solid #a7f3d0; background: #fff; color: #0f172a; outline: none;" class="dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
                                @if(!empty($itOptions))
                                    <optgroup label="-- Pegawai dengan Status IT --">
                                        @foreach($itOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="-- Semua Pegawai --">
                                        @foreach($employeesOptions as $id => $name)
                                            @if(!isset($itOptions[$id]))
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @else
                                    @foreach($employeesOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Section 3: Pengawas --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                                <label style="font-size: 12px; font-weight: 700; color: #9a3412;" class="dark:text-orange-400">
                                    👁️ Pengawas <span style="color:#ef4444;">*</span> (Standar: {{ $modalRequirements['pengawas'] ?? 1 }} orang [1:25 PC / Ruangan])
                                </label>
                                <span style="font-size: 11px; font-weight: 600; color: #ea580c;">
                                    Terpilih: {{ count($modalPengawasIds) }}
                                </span>
                            </div>
                            <select wire:model.live="modalPengawasIds" multiple size="4" style="width: 100%; font-size: 12px; padding: 6px 8px; border-radius: 8px; border: 1px solid #fed7aa; background: #fff; color: #0f172a; outline: none;" class="dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
                                @if(!empty($pengawasOptions))
                                    <optgroup label="-- Pegawai dengan Status Pengawas --">
                                        @foreach($pengawasOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="-- Semua Pegawai --">
                                        @foreach($employeesOptions as $id => $name)
                                            @if(!isset($pengawasOptions[$id]))
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @else
                                    @foreach($employeesOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; flex-shrink: 0;" class="dark:bg-slate-800/50 dark:border-slate-800">
                        <button type="button" wire:click="closeOfficerModal" class="sg-btn sg-btn-secondary" style="padding: 6px 14px; font-size: 12px;">
                            Batal
                        </button>
                        <button type="button" wire:click="saveOfficerModal" class="sg-btn sg-btn-primary" style="padding: 6px 16px; font-size: 12px;">
                            Simpan Petugas
                        </button>
                    </div>

                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
