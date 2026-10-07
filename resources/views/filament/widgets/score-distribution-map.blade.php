<x-filament-widgets::widget>
    @php
        $mapId = 'silapcat_score_map_' . str_replace('.', '_', uniqid('', true));
        $data = $this->scoreDistributionData;
        $markers = $data['map_markers'];
        $rankings = $data['tilok_rankings'];
    @endphp

    <style>
        .sdm-container {
            border-radius: 1rem;
            padding: 1.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .dark .sdm-container {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-color: #334155;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .sdm-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .dark .sdm-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .sdm-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.75rem;
            font-size: 1.25rem;
        }
        .dark .sdm-icon-box {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .sdm-title {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.2;
            color: #0f172a;
            margin: 0;
        }
        .dark .sdm-title {
            color: #ffffff;
        }

        .sdm-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0.2rem 0 0 0;
        }
        .dark .sdm-subtitle {
            color: #94a3b8;
        }

        /* KPI Cards */
        .sdm-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .sdm-kpi-card {
            border-radius: 0.85rem;
            padding: 0.9rem 1.1rem;
            border: 1px solid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sdm-kpi-emerald {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }
        .dark .sdm-kpi-emerald {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.25);
        }

        .sdm-kpi-rose {
            background: #fef2f2;
            border-color: #fecaca;
        }
        .dark .sdm-kpi-rose {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.25);
        }

        .sdm-kpi-sky {
            background: #f0f9ff;
            border-color: #bae6fd;
        }
        .dark .sdm-kpi-sky {
            background: rgba(14, 165, 233, 0.12);
            border-color: rgba(14, 165, 233, 0.25);
        }

        .sdm-kpi-indigo {
            background: #eef2ff;
            border-color: #c7d2fe;
        }
        .dark .sdm-kpi-indigo {
            background: rgba(99, 102, 241, 0.12);
            border-color: rgba(99, 102, 241, 0.25);
        }

        .sdm-kpi-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .sdm-kpi-value {
            font-size: 1.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin-top: 0.25rem;
        }
        .sdm-kpi-sub {
            font-size: 0.72rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        /* Layout Grid */
        .sdm-main-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 1.25rem;
        }
        @media (max-width: 1024px) {
            .sdm-main-grid {
                grid-template-columns: 1fr;
            }
        }

        .sdm-section-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .dark .sdm-section-label {
            color: #cbd5e1;
        }

        .sdm-map-wrapper {
            position: relative;
            width: 100%;
            height: 380px;
            border-radius: 0.85rem;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }
        .dark .sdm-map-wrapper {
            border-color: #334155;
            background: #0f172a;
        }

        /* Progress Bars */
        .sdm-grade-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.85rem;
            padding: 1rem 1.1rem;
            margin-bottom: 1rem;
        }
        .dark .sdm-grade-panel {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .sdm-grade-row {
            margin-bottom: 0.65rem;
        }
        .sdm-grade-row:last-child {
            margin-bottom: 0;
        }
        .sdm-grade-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
        }
        .sdm-grade-bar-bg {
            height: 7px;
            width: 100%;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .dark .sdm-grade-bar-bg {
            background: #334155;
        }
        .sdm-grade-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.4s ease;
        }

        /* Tilok Ranking List */
        .sdm-rank-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.75rem;
            border-radius: 0.6rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 0.75rem;
            margin-bottom: 0.4rem;
        }
        .dark .sdm-rank-item {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .custom-score-marker { background: transparent !important; border: none !important; }
        @@keyframes score-ping { 75%, 100% { transform: scale(2.2); opacity: 0; } }
        @@keyframes sdm-spin { to { transform: rotate(360deg); } }
    </style>

    <div
        class="sdm-container"
        x-data="{
            loaded: false,
            init() {
                let self = this;
                let mapId = '{{ $mapId }}';
                let markers = {{ Js::from($markers) }};

                function loadLeaflet(callback) {
                    if (typeof L !== 'undefined' && typeof L.map === 'function') {
                        callback();
                        return;
                    }

                    if (!document.getElementById('leaflet-css')) {
                        let link = document.createElement('link');
                        link.id = 'leaflet-css';
                        link.rel = 'stylesheet';
                        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(link);
                    }

                    if (!document.getElementById('leaflet-js')) {
                        let script = document.createElement('script');
                        script.id = 'leaflet-js';
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.onload = () => { setTimeout(callback, 100); };
                        document.head.appendChild(script);
                    } else {
                        let interval = setInterval(() => {
                            if (typeof L !== 'undefined' && typeof L.map === 'function') {
                                clearInterval(interval);
                                callback();
                            }
                        }, 100);
                    }
                }

                self.$nextTick(function() {
                    loadLeaflet(() => {
                        self.renderMap(mapId, markers);
                    });
                });
            },
            renderMap(mapId, markers) {
                let self = this;
                let el = document.getElementById(mapId);
                if (!el) return;
                if (el._leaflet_id) { self.loaded = true; return; }

                let map = L.map(mapId, { scrollWheelZoom: false }).setView([-1.35, 133.5], 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '© OpenStreetMap | SILAPCAT BKN'
                }).addTo(map);

                let bounds = [];

                (markers || []).forEach(function(loc) {
                    let avg = loc.avg_score || 0;
                    let color = '#94a3b8';
                    let isHighlight = false;

                    if (avg >= 350) {
                        color = '#10b981';
                        isHighlight = true;
                    } else if (avg >= 300) {
                        color = '#3b82f6';
                    } else if (avg >= 250) {
                        color = '#f59e0b';
                    } else if (avg > 0) {
                        color = '#ef4444';
                    }

                    let pulseHtml = isHighlight
                        ? '<span style=\'position:absolute;top:-4px;left:-4px;width:32px;height:32px;border-radius:9999px;background:' + color + ';opacity:0.4;animation:score-ping 1.5s cubic-bezier(0,0,0.2,1) infinite;\'></span>'
                        : '';

                    let icon = L.divIcon({
                        className: 'custom-score-marker',
                        html: '<div style=\'position:relative;width:24px;height:24px;border-radius:9999px;background:' + color + ';border:2px solid #ffffff;box-shadow:0 3px 8px rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;color:#ffffff;font-weight:900;font-size:10px;\'>' + pulseHtml + '<span style=\'position:relative;z-index:2;\'>' + (avg > 0 ? '★' : '•') + '</span></div>',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12],
                        popupAnchor: [0, -12]
                    });

                    let marker = L.marker([loc.lat, loc.lng], { icon: icon }).addTo(map);
                    bounds.push([loc.lat, loc.lng]);

                    let avgDisplay = avg > 0 ? (avg + ' Skor') : 'Belum ada data nilai';
                    let maxDisplay = loc.max_score > 0 ? loc.max_score : '-';
                    let minDisplay = loc.min_score > 0 ? loc.min_score : '-';

                    marker.bindPopup(
                        '<div style=\'min-width:210px;font-family:inherit;color:#0f172a;line-height:1.4;\'>' +
                            '<div style=\'font-size:10px;font-weight:800;color:' + color + ';text-transform:uppercase;margin-bottom:2px;\'>📍 ' + loc.city + '</div>' +
                            '<div style=\'font-size:13px;font-weight:800;color:#0f172a;margin-bottom:6px;\'>' + loc.name + '</div>' +
                            '<div style=\'background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:8px;margin-bottom:6px;\'>' +
                                '<div style=\'display:flex;justify-content:space-between;margin-bottom:4px;\'>' +
                                    '<span style=\'font-size:11px;color:#64748b;\'>Rata-rata Skor:</span>' +
                                    '<span style=\'font-size:13px;font-weight:900;color:#4338ca;\'>' + avgDisplay + '</span>' +
                                '</div>' +
                                '<div style=\'display:flex;justify-content:space-between;font-size:11px;\'>' +
                                    '<span style=\'color:#16a34a;font-weight:700;\'>Max: ' + maxDisplay + '</span>' +
                                    '<span style=\'color:#d97706;font-weight:700;\'>Min: ' + minDisplay + '</span>' +
                                '</div>' +
                            '</div>' +
                            '<div style=\'font-size:11px;color:#475569;\'>👥 Peserta Hadir: <strong>' + (loc.present_count || 0).toLocaleString() + '</strong> orang</div>' +
                        '</div>'
                    );
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 10 });
                }

                self.loaded = true;
                setTimeout(function() { map.invalidateSize(); }, 300);
                setTimeout(function() { map.invalidateSize(); }, 1000);
            }
        }"
    >
        {{-- Header Widget --}}
        <div class="sdm-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="sdm-icon-box">
                    🎯
                </div>
                <div>
                    <h3 class="sdm-title">
                        Peta Infografis Distribusi Skor & Passing Grade
                        <span style="font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.6rem; border-radius: 9999px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; margin-left: 0.4rem;">
                            {{ $data['pass_rate'] }}% Lolos PG
                        </span>
                    </h3>
                    <p class="sdm-subtitle">
                        Pemetaan spasial capaian nilai rata-rata per titik lokasi & analisis mutu kelulusan passing grade
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 0.5rem; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                    <span style="width: 0.45rem; height: 0.45rem; border-radius: 50%; background: #10b981;"></span> ≥ 350 (Sangat Baik)
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 0.5rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                    <span style="width: 0.45rem; height: 0.45rem; border-radius: 50%; background: #3b82f6;"></span> 300 - 349 (Standar)
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 0.5rem; background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                    <span style="width: 0.45rem; height: 0.45rem; border-radius: 50%; background: #f59e0b;"></span> 250 - 299 (Cukup)
                </span>
            </div>
        </div>

        {{-- KPI Cards Ringkasan Skor & Passing Grade --}}
        <div class="sdm-kpi-grid">
            <div class="sdm-kpi-card sdm-kpi-emerald">
                <div class="sdm-kpi-label" style="color: #166534;">Lulus Passing Grade</div>
                <div class="sdm-kpi-value" style="color: #15803d;">
                    {{ number_format($data['passed_count']) }} <span style="font-size: 0.8rem; font-weight: 600;">Orang</span>
                </div>
                <div class="sdm-kpi-sub" style="color: #16a34a;">
                    {{ $data['pass_rate'] }}% Rasio Kelulusan
                </div>
            </div>

            <div class="sdm-kpi-card sdm-kpi-rose">
                <div class="sdm-kpi-label" style="color: #991b1b;">Tidak Lolos PG</div>
                <div class="sdm-kpi-value" style="color: #b91c1c;">
                    {{ number_format($data['failed_count']) }} <span style="font-size: 0.8rem; font-weight: 600;">Orang</span>
                </div>
                <div class="sdm-kpi-sub" style="color: #dc2626;">
                    {{ $data['fail_rate'] }}% Belum Memenuhi
                </div>
            </div>

            <div class="sdm-kpi-card sdm-kpi-sky">
                <div class="sdm-kpi-label" style="color: #0369a1;">Rata-rata Skor CAT</div>
                <div class="sdm-kpi-value" style="color: #0c4a6e;">
                    {{ number_format($data['avg_cat'], 2) }}
                </div>
                <div class="sdm-kpi-sub" style="color: #0284c7;">
                    Skor Berbasis CAT
                </div>
            </div>

            <div class="sdm-kpi-card sdm-kpi-indigo">
                <div class="sdm-kpi-label" style="color: #4338ca;">Rata-rata Nilai Akhir</div>
                <div class="sdm-kpi-value" style="color: #312e81;">
                    {{ number_format($data['avg_total'], 2) }}
                </div>
                <div class="sdm-kpi-sub" style="color: #6366f1;">
                    Komposit Penilaian
                </div>
            </div>
        </div>

        {{-- Konten Utama: Peta Spasial (Kiri) & Sebaran Rentang Grade (Kanan) --}}
        <div class="sdm-main-grid">
            {{-- Peta Spasial Nilai --}}
            <div>
                <div class="sdm-section-label">
                    <span>🗺️ Peta Nilai Titik Lokasi Ujian</span>
                    <span style="font-size: 0.68rem; font-weight: 500; color: #94a3b8;">(Klik marker untuk rincian skor)</span>
                </div>
                <div wire:ignore class="sdm-map-wrapper">
                    <div id="{{ $mapId }}" style="width: 100%; height: 100%; min-height: 380px; z-index: 10;"></div>

                    <div x-show="!loaded" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(248,250,252,0.9); z-index: 20;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: #475569;">
                            <svg style="width: 20px; height: 20px; animation: sdm-spin 1s linear infinite;" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity: 0.75;"></path>
                            </svg>
                            Memuat Peta Infografis Skor...
                        </div>
                    </div>
                </div>
            </div>

            {{-- Infografis Sebaran Rentang Nilai (Grade Breakdown) & Top Tilok --}}
            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="sdm-section-label">
                        📊 Distribusi Rentang Skor CAT
                    </div>

                    <div class="sdm-grade-panel">
                        <div class="sdm-grade-row">
                            <div class="sdm-grade-info">
                                <span style="font-weight: 800; color: #1e3a8a;">💎 ≥ 400 (Sangat Memuaskan)</span>
                                <span style="font-weight: 900; color: #1e3a8a;">{{ $data['range_a'] }} org ({{ $data['pct_a'] }}%)</span>
                            </div>
                            <div class="sdm-grade-bar-bg">
                                <div class="sdm-grade-bar-fill" style="width: {{ $data['pct_a'] }}%; background: #1e3a8a;"></div>
                            </div>
                        </div>

                        <div class="sdm-grade-row">
                            <div class="sdm-grade-info">
                                <span style="font-weight: 800; color: #0284c7;">⭐ 350 - 399 (Memuaskan)</span>
                                <span style="font-weight: 900; color: #0284c7;">{{ $data['range_b'] }} org ({{ $data['pct_b'] }}%)</span>
                            </div>
                            <div class="sdm-grade-bar-bg">
                                <div class="sdm-grade-bar-fill" style="width: {{ $data['pct_b'] }}%; background: #0284c7;"></div>
                            </div>
                        </div>

                        <div class="sdm-grade-row">
                            <div class="sdm-grade-info">
                                <span style="font-weight: 800; color: #059669;">✔️ 300 - 349 (Standar / Cukup)</span>
                                <span style="font-weight: 900; color: #059669;">{{ $data['range_c'] }} org ({{ $data['pct_c'] }}%)</span>
                            </div>
                            <div class="sdm-grade-bar-bg">
                                <div class="sdm-grade-bar-fill" style="width: {{ $data['pct_c'] }}%; background: #059669;"></div>
                            </div>
                        </div>

                        <div class="sdm-grade-row">
                            <div class="sdm-grade-info">
                                <span style="font-weight: 800; color: #d97706;">⚠️ 250 - 299 (Di Bawah Standar)</span>
                                <span style="font-weight: 900; color: #d97706;">{{ $data['range_d'] }} org ({{ $data['pct_d'] }}%)</span>
                            </div>
                            <div class="sdm-grade-bar-bg">
                                <div class="sdm-grade-bar-fill" style="width: {{ $data['pct_d'] }}%; background: #d97706;"></div>
                            </div>
                        </div>

                        <div class="sdm-grade-row">
                            <div class="sdm-grade-info">
                                <span style="font-weight: 800; color: #dc2626;">❌ < 250 (Tidak Lolos PG)</span>
                                <span style="font-weight: 900; color: #dc2626;">{{ $data['range_e'] }} org ({{ $data['pct_e'] }}%)</span>
                            </div>
                            <div class="sdm-grade-bar-bg">
                                <div class="sdm-grade-bar-fill" style="width: {{ $data['pct_e'] }}%; background: #dc2626;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mini Table Ranking Skor Tilok --}}
                @if (!empty($rankings))
                    <div>
                        <div class="sdm-section-label">
                            🏆 Rata-rata Skor Tertinggi per Tilok
                        </div>
                        <div style="max-height: 140px; overflow-y: auto; padding-right: 2px;">
                            @foreach (array_slice($rankings, 0, 4) as $idx => $rnk)
                                <div class="sdm-rank-item">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                                        <span style="width: 1.25rem; height: 1.25rem; border-radius: 50%; background: #e0e7ff; color: #4338ca; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; flex-shrink: 0;">
                                            {{ $idx + 1 }}
                                        </span>
                                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <div style="font-weight: 800; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" class="dark:!text-slate-200">{{ $rnk['name'] }}</div>
                                            <div style="font-size: 0.65rem; color: #64748b;">{{ $rnk['city'] }} • {{ $rnk['present_count'] }} Hadir</div>
                                        </div>
                                    </div>
                                    <div style="text-align: right; flex-shrink: 0; margin-left: 0.5rem;">
                                        <div style="font-weight: 900; color: #4f46e5; font-size: 0.85rem;" class="dark:!text-indigo-400">{{ number_format($rnk['avg_score'], 1) }}</div>
                                        <div style="font-size: 0.6rem; color: #94a3b8;">Rerata Skor</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
