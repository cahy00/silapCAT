<x-filament-widgets::widget>
    <style>
        .lem-container {
            border-radius: 1rem;
            padding: 1.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .dark .lem-container {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-color: #334155;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .lem-header-divider {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .dark .lem-header-divider {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .lem-icon-box {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.75rem;
        }
        .dark .lem-icon-box {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .lem-icon {
            width: 1.25rem;
            height: 1.25rem;
            color: #2563eb;
        }
        .dark .lem-icon {
            color: #60a5fa;
        }

        .lem-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }
        .dark .lem-title {
            color: #f8fafc;
        }

        .lem-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0.2rem 0 0 0;
        }
        .dark .lem-subtitle {
            color: #94a3b8;
        }

        .lem-badge-active {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .dark .lem-badge-active {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border-color: rgba(34, 197, 94, 0.3);
        }

        .lem-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.85rem;
            padding: 1.15rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .dark .lem-card {
            background: rgba(30, 41, 59, 0.75);
            border-color: rgba(148, 163, 184, 0.15);
        }

        .lem-proc-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid rgba(99, 102, 241, 0.25);
            margin-bottom: 0.35rem;
        }
        .dark .lem-proc-badge {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            border-color: rgba(99, 102, 241, 0.3);
        }

        .lem-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.3;
        }
        .dark .lem-card-title {
            color: #ffffff;
        }

        .lem-progress-box {
            background: #ffffff;
            border-radius: 0.6rem;
            padding: 0.75rem;
            margin-bottom: 0.85rem;
            border: 1px solid #e2e8f0;
        }
        .dark .lem-progress-box {
            background: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .lem-progress-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
        }
        .dark .lem-progress-title {
            color: #94a3b8;
        }

        .lem-progress-pct {
            font-size: 0.85rem;
            font-weight: 800;
            color: #0284c7;
        }
        .dark .lem-progress-pct {
            color: #38bdf8;
        }

        .lem-progress-track {
            width: 100%;
            height: 0.45rem;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }
        .dark .lem-progress-track {
            background: #334155;
        }

        .lem-stat-target {
            background: #f1f5f9;
            padding: 0.25rem;
            border-radius: 0.35rem;
            border: 1px solid #e2e8f0;
        }
        .dark .lem-stat-target {
            background: rgba(255, 255, 255, 0.03);
            border-color: transparent;
        }

        .lem-stat-hadir {
            background: #f0fdf4;
            padding: 0.25rem;
            border-radius: 0.35rem;
            border: 1px solid #bbf7d0;
        }
        .dark .lem-stat-hadir {
            background: rgba(34, 197, 94, 0.08);
            border-color: transparent;
        }

        .lem-stat-absen {
            background: #fef2f2;
            padding: 0.25rem;
            border-radius: 0.35rem;
            border: 1px solid #fecaca;
        }
        .dark .lem-stat-absen {
            background: rgba(239, 68, 68, 0.08);
            border-color: transparent;
        }

        .lem-target-val { color: #1e293b; font-weight: 700; }
        .dark .lem-target-val { color: #f8fafc; }

        .lem-hadir-val { color: #16a34a; font-weight: 700; }
        .dark .lem-hadir-val { color: #4ade80; }

        .lem-absen-val { color: #dc2626; font-weight: 700; }
        .dark .lem-absen-val { color: #f87171; }

        .lem-section-label {
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .dark .lem-section-label {
            color: #94a3b8;
        }

        .lem-loc-item {
            padding-left: 1.1rem;
            margin-bottom: 0.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #334155;
            font-size: 0.75rem;
        }
        .dark .lem-loc-item {
            color: #cbd5e1;
        }

        .lem-tag-koor {
            background: #fef3c7;
            color: #92400e;
            padding: 0.1rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.68rem;
            font-weight: 600;
            border: 1px solid #fde68a;
        }
        .dark .lem-tag-koor {
            background: rgba(234, 179, 8, 0.15);
            color: #fde047;
            border-color: rgba(234, 179, 8, 0.3);
        }

        .lem-tag-it {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.1rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.68rem;
            font-weight: 600;
            border: 1px solid #bfdbfe;
        }
        .dark .lem-tag-it {
            background: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .lem-tag-pengawas {
            background: #f3e8ff;
            color: #6b21a8;
            padding: 0.1rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.68rem;
            font-weight: 600;
            border: 1px solid #e9d5ff;
        }
        .dark .lem-tag-pengawas {
            background: rgba(168, 85, 247, 0.15);
            color: #d8b4fe;
            border-color: rgba(168, 85, 247, 0.3);
        }

        .lem-card-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dark .lem-card-footer {
            border-top-color: rgba(255, 255, 255, 0.08);
        }

        .lem-footer-sub {
            font-size: 0.7rem;
            color: #64748b;
        }
        .dark .lem-footer-sub {
            color: #94a3b8;
        }

        .lem-detail-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
            padding: 0.25rem 0.55rem;
            border-radius: 0.375rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            transition: all 0.15s ease;
        }
        .lem-detail-btn:hover {
            background: #dbeafe;
        }
        .dark .lem-detail-btn {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.25);
        }
        .dark .lem-detail-btn:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .lem-empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            border: 1px dashed #cbd5e1;
        }
        .dark .lem-empty-state {
            background: rgba(15, 23, 42, 0.5);
            border-color: #334155;
        }
    </style>

    <div class="lem-container">
        <!-- Top Bar Header -->
        <div class="lem-header-divider">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="lem-icon-box">
                    <svg class="lem-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span style="position: absolute; top: -2px; right: -2px; display: flex; height: 0.65rem; width: 0.65rem;">
                        <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background-color: #4ade80; opacity: 0.75; animation: silapcat-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                        <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 0.65rem; width: 0.65rem; background-color: #22c55e;"></span>
                    </span>
                </div>
                <div>
                    <h2 class="lem-title">
                        Live Monitoring Pelaksanaan Ujian CAT
                    </h2>
                    <p class="lem-subtitle">
                        Pemantauan real-time titik lokasi ujian, petugas lapangan & kehadiran peserta hari ini
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="lem-badge-active">
                    <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #22c55e;"></span>
                    Sistem Aktif • {{ now()->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>

        <!-- Events List / Cards -->
        @if (empty($this->activeTiloks))
            <div class="lem-empty-state">
                <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem auto; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <div style="font-size: 0.95rem; font-weight: 700;" class="lem-title">Tidak Ada Kegiatan Ujian yang Sedang Berjalan Hari Ini</div>
                <div class="lem-subtitle" style="margin-top: 0.25rem;">Semua sesi ujian sebelumnya telah selesai atau belum dimulai.</div>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
                @foreach ($this->activeTiloks as $item)
                    <div class="lem-card">
                        <div>
                            <!-- Card Header -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.35rem;">
                                        <span class="lem-proc-badge" style="margin-bottom: 0;">
                                            {{ $item['procurement_type'] }} • T.A {{ $item['formation_year'] ?? '-' }}
                                        </span>
                                        @if (!empty($item['date_range']))
                                            <span class="lem-proc-badge" style="margin-bottom: 0; background: rgba(14, 165, 233, 0.1); color: #0284c7; border-color: rgba(14, 165, 233, 0.25);">
                                                📅 {{ $item['date_range'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="lem-card-title">
                                        {{ $item['name'] }}
                                    </h3>
                                </div>
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; {{ $item['is_live_today'] ? 'background: rgba(34, 197, 94, 0.15); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);' : 'background: rgba(234, 179, 8, 0.15); color: #d97706; border: 1px solid rgba(234, 179, 8, 0.3);' }}">
                                    @if ($item['is_live_today'])
                                        <span style="width: 0.4rem; height: 0.4rem; border-radius: 9999px; background-color: #22c55e;"></span>
                                        LIVE
                                    @else
                                        {{ strtoupper($item['status']) }}
                                    @endif
                                </span>
                            </div>

                            <!-- Progress Kehadiran -->
                            <div class="lem-progress-box">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                                    <span class="lem-progress-title">Progres Kehadiran Peserta</span>
                                    <span class="lem-progress-pct">{{ $item['attendance_rate'] }}%</span>
                                </div>
                                <!-- Progress Bar Container -->
                                <div class="lem-progress-track">
                                    <div style="width: {{ min(100, max(0, $item['attendance_rate'])) }}%; height: 100%; background: linear-gradient(90deg, #38bdf8, #22c55e); border-radius: 9999px;"></div>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); text-align: center; gap: 0.25rem; font-size: 0.7rem;">
                                    <div class="lem-stat-target">
                                        <div class="lem-subtitle" style="font-size: 0.68rem;">Kuota Peserta</div>
                                        <div class="lem-target-val">{{ number_format($item['total_target']) }}</div>
                                    </div>
                                    <div class="lem-stat-hadir">
                                        <div style="color: #16a34a; font-size: 0.68rem;">Hadir</div>
                                        <div class="lem-hadir-val">{{ number_format($item['present']) }}</div>
                                    </div>
                                    <div class="lem-stat-absen">
                                        <div style="color: #dc2626; font-size: 0.68rem;">Tdk Hadir</div>
                                        <div class="lem-absen-val">{{ number_format($item['absent']) }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Titik Lokasi & Instansi -->
                            <div style="font-size: 0.75rem; margin-bottom: 0.65rem;">
                                <div class="lem-section-label">
                                    <svg style="width: 0.85rem; height: 0.85rem; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Titik Lokasi & Instansi:
                                </div>
                                @foreach ($item['locations'] as $loc)
                                    <div style="margin-bottom: 0.35rem; padding-left: 0.25rem;">
                                        <div class="lem-loc-item" style="padding-left: 0.85rem; font-weight: 700;">
                                            <span>📍 {{ $loc['name'] }} {{ $loc['city'] ? "· {$loc['city']}" : "" }}</span>
                                            <span class="lem-loc-pc">{{ $loc['pc_count'] > 0 ? "{$loc['pc_count']} PC" : "" }}</span>
                                        </div>
                                        @if (!empty($loc['institutions']))
                                            <div style="padding-left: 1.6rem; display: flex; flex-direction: column; gap: 0.2rem; margin-top: 0.15rem;">
                                                @foreach ($loc['institutions'] as $inst)
                                                    <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.72rem;">
                                                        <span class="lem-subtitle" style="font-weight: 600;">🏢 {{ $inst['name'] }}</span>
                                                        <span style="font-weight: 800; color: #6366f1; font-size: 0.7rem;">({{ number_format($inst['participants_count']) }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if (empty($item['locations']) && !empty($item['all_institutions']))
                                    <div style="padding-left: 1.1rem; display: flex; flex-direction: column; gap: 0.2rem;">
                                        @foreach ($item['all_institutions'] as $inst)
                                            <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.72rem;">
                                                <span class="lem-subtitle" style="font-weight: 600;">🏢 {{ $inst['name'] }}</span>
                                                <span style="font-weight: 800; color: #6366f1; font-size: 0.7rem;">({{ number_format($inst['participants_count']) }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Petugas Lapangan -->
                            <div style="font-size: 0.72rem; margin-bottom: 0.75rem;">
                                <div class="lem-section-label">
                                    <svg style="width: 0.85rem; height: 0.85rem; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Petugas Lapangan:
                                </div>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.3rem; padding-left: 1.1rem;">
                                    @if (!empty($item['koordinators']))
                                        <span class="lem-tag-koor">
                                            Koor: {{ implode(', ', $item['koordinators']) }}
                                        </span>
                                    @endif
                                    @if (!empty($item['it_staff']))
                                        <span class="lem-tag-it">
                                            IT: {{ implode(', ', $item['it_staff']) }}
                                        </span>
                                    @endif
                                    @if (!empty($item['pengawas']))
                                        <span class="lem-tag-pengawas">
                                            Pengawas: {{ implode(', ', $item['pengawas']) }}
                                        </span>
                                    @endif
                                    @if (empty($item['koordinators']) && empty($item['it_staff']) && empty($item['pengawas']))
                                        <span class="lem-subtitle" style="font-style: italic;">Belum ada penugasan</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Links -->
                        <div class="lem-card-footer">
                            <span class="lem-footer-sub">
                                Total Laporan: <strong style="color: inherit;">{{ $item['reports_count'] }}</strong>
                            </span>
                            <a href="{{ route('filament.admin.resources.events.view', $item['event_id']) }}" class="lem-detail-btn">
                                Detail Sesi
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
