<x-filament-widgets::widget>
    <style>
        .eow-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 0.75rem;
        }
        @media (min-width: 640px) {
            .eow-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .eow-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        .eow-card {
            position: relative;
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.25rem 1.25rem 1.15rem 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.85rem;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .eow-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .dark .eow-card {
            background: linear-gradient(145deg, #121a2d 0%, #0e1424 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.08), 0 8px 24px -4px rgba(0, 0, 0, 0.45) !important;
        }
        .dark .eow-card:hover {
            border-color: rgba(99, 102, 241, 0.35) !important;
            box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.12), 0 12px 28px -4px rgba(0, 0, 0, 0.65), 0 0 16px -2px rgba(99, 102, 241, 0.25) !important;
        }

        /* Ambient top accent border */
        .eow-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .eow-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .eow-label-wrap {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .eow-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .dark .eow-label {
            color: #94a3b8 !important;
        }

        .eow-val {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.15;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .dark .eow-val {
            color: #ffffff !important;
            text-shadow: none !important;
        }

        .eow-icon-box {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        /* Light Mode Icons */
        .eow-icon-indigo { background: #eef2ff; color: #4f46e5; }
        .eow-icon-emerald { background: #ecfdf5; color: #059669; }
        .eow-icon-sky { background: #f0f9ff; color: #0284c7; }
        .eow-icon-amber { background: #fffbeb; color: #d97706; }

        /* Dark Mode Icons (Deep Translucent Tiles) */
        .dark .eow-icon-indigo {
            background: rgba(99, 102, 241, 0.15) !important;
            color: #818cf8 !important;
            border: 1px solid rgba(99, 102, 241, 0.25);
        }
        .dark .eow-icon-emerald {
            background: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .dark .eow-icon-sky {
            background: rgba(14, 165, 233, 0.15) !important;
            color: #38bdf8 !important;
            border: 1px solid rgba(14, 165, 233, 0.25);
        }
        .dark .eow-icon-amber {
            background: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .eow-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 0.65rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.75rem;
            color: #64748b;
        }
        .dark .eow-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
            color: #94a3b8 !important;
        }

        .eow-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 600;
            font-size: 0.7188rem;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            background: #f1f5f9;
            color: #475569;
        }
        .dark .eow-badge {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
        }
        .eow-badge-success {
            background: #ecfdf5;
            color: #059669;
        }
        .dark .eow-badge-success {
            background: rgba(16, 185, 129, 0.15) !important;
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
            color: #34d399 !important;
        }

        .eow-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .eow-dot-pulse {
            animation: eow-pulse 1.8s infinite;
        }
        @keyframes eow-pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>

    @php
        $stats = $stats ?? (isset($this) ? $this->getStatsData() : []);
    @endphp

    <div class="eow-grid">
        {{-- Card 1: Total Kegiatan --}}
        <div class="eow-card">
            <div class="eow-accent" style="background: linear-gradient(90deg, #6366f1, #a855f7);"></div>
            <div class="eow-header">
                <div class="eow-label-wrap">
                    <span class="eow-label">Total Kegiatan</span>
                    <span class="eow-val">{{ number_format($stats['total_events']) }}</span>
                </div>
                <div class="eow-icon-box eow-icon-indigo">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
            </div>
            <div class="eow-footer">
                <span>Seluruh event terdaftar</span>
                <span class="eow-badge">
                    {{ $stats['completed_events'] }} Selesai
                </span>
            </div>
        </div>

        {{-- Card 2: Kegiatan Aktif --}}
        <div class="eow-card">
            <div class="eow-accent" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
            <div class="eow-header">
                <div class="eow-label-wrap">
                    <span class="eow-label">Kegiatan Aktif</span>
                    <span class="eow-val" style="{{ $stats['active_events'] > 0 ? 'color: #10b981;' : '' }}">
                        {{ $stats['active_events'] }}
                    </span>
                </div>
                <div class="eow-icon-box eow-icon-emerald">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                </div>
            </div>
            <div class="eow-footer">
                @if ($stats['active_events'] > 0)
                    <span style="display: flex; align-items: center; gap: 0.35rem; color: #10b981; font-weight: 600;">
                        <span class="eow-dot eow-dot-pulse" style="background: #10b981;"></span>
                        Sedang berlangsung
                    </span>
                    <span class="eow-badge eow-badge-success">Live</span>
                @else
                    <span>Sedang berlangsung</span>
                    <span class="eow-badge">0 Aktif</span>
                @endif
            </div>
        </div>

        {{-- Card 3: Total Peserta --}}
        <div class="eow-card">
            <div class="eow-accent" style="background: linear-gradient(90deg, #0284c7, #06b6d4);"></div>
            <div class="eow-header">
                <div class="eow-label-wrap">
                    <span class="eow-label">Total Peserta</span>
                    <span class="eow-val">{{ number_format($stats['total_participants']) }}</span>
                </div>
                <div class="eow-icon-box eow-icon-sky">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>
            <div class="eow-footer">
                <span>Akumulasi kuota peserta</span>
                <span class="eow-badge">
                    Target Kuota
                </span>
            </div>
        </div>

        {{-- Card 4: Titik Lokasi Digunakan --}}
        <div class="eow-card">
            <div class="eow-accent" style="background: linear-gradient(90deg, #f59e0b, #f97316);"></div>
            <div class="eow-header">
                <div class="eow-label-wrap">
                    <span class="eow-label">Lokasi Digunakan</span>
                    <span class="eow-val">{{ number_format($stats['locations_count']) }}</span>
                </div>
                <div class="eow-icon-box eow-icon-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
            </div>
            <div class="eow-footer">
                <span>Titik lokasi terlibat</span>
                <span class="eow-badge">
                    Wilayah Ujian
                </span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
