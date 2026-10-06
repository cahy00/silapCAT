<x-filament-widgets::widget>
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 1rem; border: 1px solid #334155; padding: 1.5rem; color: #ffffff; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);">
        <!-- Top Bar Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="position: relative; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span style="position: absolute; top: -2px; right: -2px; display: flex; height: 0.65rem; width: 0.65rem;">
                        <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background-color: #4ade80; opacity: 0.75; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                        <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 0.65rem; width: 0.65rem; background-color: #22c55e;"></span>
                    </span>
                </div>
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 700; color: #f8fafc; margin: 0; line-height: 1.2;">
                        Live Monitoring Pelaksanaan Ujian CAT
                    </h2>
                    <p style="font-size: 0.8rem; color: #94a3b8; margin: 0.2rem 0 0 0;">
                        Pemantauan real-time titik lokasi ujian, petugas lapangan & kehadiran peserta hari ini
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
                    <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #22c55e;"></span>
                    Sistem Aktif • {{ now()->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>

        <!-- Events List / Cards -->
        @if (empty($this->activeTiloks))
            <div style="text-align: center; padding: 2.5rem 1rem; background: rgba(15, 23, 42, 0.5); border-radius: 0.75rem; border: 1px dashed #334155;">
                <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem auto; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <div style="font-size: 0.95rem; font-weight: 600; color: #cbd5e1;">Tidak Ada Kegiatan Ujian yang Sedang Berjalan Hari Ini</div>
                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem;">Semua sesi ujian sebelumnya telah selesai atau belum dimulai.</div>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
                @foreach ($this->activeTiloks as $item)
                    <div style="background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(148, 163, 184, 0.15); border-radius: 0.85rem; padding: 1.15rem; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s, border-color 0.2s;">
                        <div>
                            <!-- Card Header -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <div>
                                    <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 0.375rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3); margin-bottom: 0.35rem;">
                                        {{ $item['procurement_type'] }} • T.A {{ $item['formation_year'] ?? '-' }}
                                    </span>
                                    <h3 style="font-size: 1rem; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.3;">
                                        {{ $item['name'] }}
                                    </h3>
                                </div>
                                <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; {{ $item['is_live_today'] ? 'background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4);' : 'background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3);' }}">
                                    @if ($item['is_live_today'])
                                        <span style="width: 0.4rem; height: 0.4rem; border-radius: 9999px; background-color: #22c55e;"></span>
                                        LIVE
                                    @else
                                        {{ strtoupper($item['status']) }}
                                    @endif
                                </span>
                            </div>

                            <!-- Progress Kehadiran -->
                            <div style="background: rgba(15, 23, 42, 0.6); border-radius: 0.6rem; padding: 0.75rem; margin-bottom: 0.85rem; border: 1px solid rgba(255, 255, 255, 0.05);">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; color: #94a3b8;">Progres Kehadiran Peserta</span>
                                    <span style="font-size: 0.85rem; font-weight: 800; color: #38bdf8;">{{ $item['attendance_rate'] }}%</span>
                                </div>
                                <!-- Progress Bar Container -->
                                <div style="width: 100%; height: 0.45rem; background: #334155; border-radius: 9999px; overflow: hidden; margin-bottom: 0.5rem;">
                                    <div style="width: {{ min(100, max(0, $item['attendance_rate'])) }}%; height: 100%; background: linear-gradient(90deg, #38bdf8, #22c55e); border-radius: 9999px;"></div>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); text-align: center; gap: 0.25rem; font-size: 0.7rem;">
                                    <div style="background: rgba(255, 255, 255, 0.03); padding: 0.25rem; border-radius: 0.3rem;">
                                        <div style="color: #94a3b8;">Target</div>
                                        <div style="font-weight: 700; color: #f8fafc;">{{ number_format($item['total_target']) }}</div>
                                    </div>
                                    <div style="background: rgba(34, 197, 94, 0.08); padding: 0.25rem; border-radius: 0.3rem;">
                                        <div style="color: #4ade80;">Hadir</div>
                                        <div style="font-weight: 700; color: #4ade80;">{{ number_format($item['present']) }}</div>
                                    </div>
                                    <div style="background: rgba(239, 68, 68, 0.08); padding: 0.25rem; border-radius: 0.3rem;">
                                        <div style="color: #f87171;">Tdk Hadir</div>
                                        <div style="font-weight: 700; color: #f87171;">{{ number_format($item['absent']) }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Titik Lokasi & Jadwal -->
                            <div style="font-size: 0.75rem; color: #cbd5e1; margin-bottom: 0.6rem;">
                                <div style="font-weight: 600; color: #94a3b8; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                    <svg style="width: 0.85rem; height: 0.85rem; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Titik Lokasi (Tilok):
                                </div>
                                @foreach ($item['locations'] as $loc)
                                    <div style="padding-left: 1.1rem; margin-bottom: 0.2rem; display: flex; justify-content: space-between; align-items: center;">
                                        <span>• <strong>{{ $loc['name'] }}</strong> {{ $loc['city'] ? "({$loc['city']})" : "" }}</span>
                                        <span style="font-size: 0.68rem; color: #64748b;">{{ $loc['pc_count'] > 0 ? "{$loc['pc_count']} PC" : "" }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Petugas Lapangan -->
                            <div style="font-size: 0.72rem; color: #cbd5e1; margin-bottom: 0.75rem;">
                                <div style="font-weight: 600; color: #94a3b8; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                    <svg style="width: 0.85rem; height: 0.85rem; color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Petugas Lapangan:
                                </div>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.3rem; padding-left: 1.1rem;">
                                    @if (!empty($item['koordinators']))
                                        <span style="background: rgba(234, 179, 8, 0.15); color: #fde047; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-size: 0.68rem; border: 1px solid rgba(234, 179, 8, 0.3);">
                                            Koor: {{ implode(', ', $item['koordinators']) }}
                                        </span>
                                    @endif
                                    @if (!empty($item['it_staff']))
                                        <span style="background: rgba(59, 130, 246, 0.15); color: #93c5fd; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-size: 0.68rem; border: 1px solid rgba(59, 130, 246, 0.3);">
                                            IT: {{ implode(', ', $item['it_staff']) }}
                                        </span>
                                    @endif
                                    @if (!empty($item['pengawas']))
                                        <span style="background: rgba(168, 85, 247, 0.15); color: #d8b4fe; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-size: 0.68rem; border: 1px solid rgba(168, 85, 247, 0.3);">
                                            Pengawas: {{ implode(', ', $item['pengawas']) }}
                                        </span>
                                    @endif
                                    @if (empty($item['koordinators']) && empty($item['it_staff']) && empty($item['pengawas']))
                                        <span style="color: #64748b; font-style: italic;">Belum ada penugasan</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Links -->
                        <div style="border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.7rem; color: #94a3b8;">
                                Total Laporan: <strong>{{ $item['reports_count'] }}</strong>
                            </span>
                            <a href="{{ route('filament.admin.resources.events.view', $item['event_id']) }}" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #60a5fa; text-decoration: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
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
