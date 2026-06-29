<x-filament-panels::page>
    @php
        $stats = $this->getStats();
    @endphp

    <style>
        .cal-stats-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 640px) {
            .cal-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .cal-stats-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .cal-card {
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
            border-radius: 0.875rem;
            padding: 1.25rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .dark .cal-card {
            background-color: #18181b;
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }
        .cal-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cal-icon-box {
            padding: 0.625rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cal-val {
            font-size: 1.875rem;
            line-height: 2.25rem;
            font-weight: 900;
        }
        .cal-lbl {
            margin-top: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
        }
        .dark .cal-lbl {
            color: #9ca3af;
        }
        .cal-legend-box {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.5rem;
            row-gap: 0.75rem;
        }
        .cal-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #374151;
        }
        .dark .cal-legend-item {
            color: #e5e7eb;
        }
        .cal-divider {
            border-left: 1px solid #e5e7eb;
            height: 1.25rem;
            display: none;
        }
        @media (min-width: 768px) {
            .cal-divider {
                display: inline-block;
            }
        }
        .dark .cal-divider {
            border-color: #374151;
        }
    </style>

    {{-- Stats Cards --}}
    <div class="cal-stats-grid">
        <div class="cal-card">
            <div class="cal-card-header">
                <div class="cal-icon-box" style="background-color: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="cal-val" style="color: #4f46e5;">{{ $stats['total'] }}</span>
            </div>
            <p class="cal-lbl">Total Jadwal</p>
        </div>
        <div class="cal-card">
            <div class="cal-card-header">
                <div class="cal-icon-box" style="background-color: rgba(16, 185, 129, 0.12); color: #059669;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="cal-val" style="color: #059669;">{{ $stats['active'] }}</span>
            </div>
            <p class="cal-lbl">Sedang Berjalan</p>
        </div>
        <div class="cal-card">
            <div class="cal-card-header">
                <div class="cal-icon-box" style="background-color: rgba(245, 158, 11, 0.12); color: #d97706;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="cal-val" style="color: #d97706;">{{ $stats['upcoming'] }}</span>
            </div>
            <p class="cal-lbl">Akan Datang</p>
        </div>
        <div class="cal-card">
            <div class="cal-card-header">
                <div class="cal-icon-box" style="background-color: rgba(59, 130, 246, 0.12); color: #2563eb;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="cal-val" style="color: #2563eb;">{{ $stats['completed'] }}</span>
            </div>
            <p class="cal-lbl">Selesai</p>
        </div>
    </div>

    {{-- Legend --}}
    <div class="cal-card" style="margin-bottom: 1.5rem;">
        <div class="cal-legend-box">
            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">Jenis:</span>
            <div class="cal-legend-item"><span style="width:12px; height:12px; border-radius:9999px; background:#4f46e5; display:inline-block;"></span><span>CPNS</span></div>
            <div class="cal-legend-item"><span style="width:12px; height:12px; border-radius:9999px; background:#0891b2; display:inline-block;"></span><span>PPPK</span></div>
            <div class="cal-legend-item"><span style="width:12px; height:12px; border-radius:9999px; background:#d97706; display:inline-block;"></span><span>UD</span></div>
            <div class="cal-legend-item"><span style="width:12px; height:12px; border-radius:9999px; background:#059669; display:inline-block;"></span><span>UPKP</span></div>
            <div class="cal-legend-item"><span style="width:12px; height:12px; border-radius:9999px; background:#7c3aed; display:inline-block;"></span><span>UD/UPKP</span></div>

            <span class="cal-divider"></span>
            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">Tips:</span>
            <span style="font-size: 0.8125rem; color: #6b7280; line-height: 1.5;">
                <strong style="color: #4f46e5;">Klik & seret</strong> pada tanggal untuk menambah jadwal &bull;
                <strong style="color: #4f46e5;">Seret event</strong> untuk menggeser jadwal &bull;
                <strong style="color: #4f46e5;">Tarik ujung event</strong> untuk mengubah durasi
            </span>
        </div>
    </div>

    <div wire:ignore
         x-data="calendarApp()"
         x-on:calendar-refresh.window="refreshCalendar()"
         >

        {{-- Calendar --}}
        <div id="calendar" class="cal-card" style="min-height: 650px; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: center; height: 650px; color: #9ca3af; font-weight: 600;">Memuat Kalender...</div>
        </div>

        {{-- Detail Modal --}}
        <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @click.self="showDetailModal = false" @keydown.escape.window="showDetailModal = false">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border dark:border-gray-700 overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white truncate pr-4" x-text="detailTitle"></h3>
                        <button @click="showDetailModal = false" class="text-white/70 hover:text-white transition p-1 rounded-lg hover:bg-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <div class="text-sm text-gray-700 dark:text-gray-300 w-full" x-html="detailBody"></div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex items-center justify-between gap-3">
                    <button @click="confirmDelete()" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-red-600 bg-red-50 dark:bg-red-950/50 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition border border-red-200 dark:border-red-800">
                        <svg style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:4px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Jadwal
                    </button>
                    <button @click="showDetailModal = false" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-500 hover:shadow transition">Tutup</button>
                </div>
            </div>
        </div>

        {{-- Create Modal (for drag-select) --}}
        <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @click.self="showCreateModal = false" @keydown.escape.window="showCreateModal = false">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border dark:border-gray-700 overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">

                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white">Tambah Jadwal Kegiatan</h3>
                            <p class="text-emerald-100 text-xs mt-0.5">Pilih kegiatan dan lokasi untuk jadwal baru</p>
                        </div>
                        <button @click="showCreateModal = false" class="text-white/70 hover:text-white transition p-1 rounded-lg hover:bg-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Date Range Display --}}
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                <svg style="width:20px;height:20px;" class="text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rentang Tanggal</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <input type="date" x-model="createStartDate" class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <span class="text-gray-400 text-xs font-bold">s/d</span>
                                    <input type="date" x-model="createEndDate" class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Event Select --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            <svg style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:4px;" class="text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Pilih Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <select x-model="createEventId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">— Pilih Kegiatan —</option>
                            @foreach($this->getEventsList() as $event)
                                <option value="{{ $event['id'] }}">{{ $event['name'] }} ({{ $event['year'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Location Select --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            <svg style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:4px;" class="text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pilih Lokasi <span class="text-red-500">*</span>
                        </label>
                        <select x-model="createLocationId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">— Pilih Lokasi —</option>
                            @foreach($this->getLocations() as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex justify-end gap-3">
                    <button @click="showCreateModal = false" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition border border-gray-300 dark:border-gray-600">Batal</button>
                    <button @click="submitCreate()" :disabled="!createEventId || !createLocationId" :class="{'opacity-50 cursor-not-allowed': !createEventId || !createLocationId}" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-white bg-emerald-600 rounded-lg shadow-sm hover:bg-emerald-500 hover:shadow transition">
                        <svg style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:4px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Jadwal
                    </button>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @keydown.escape.window="showDeleteModal = false">
            <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border dark:border-gray-700 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6 text-center">
                    <div class="mx-auto bg-red-50 dark:bg-red-950/50 rounded-full flex items-center justify-center mb-4" style="width:56px;height:56px;">
                        <svg style="width:28px;height:28px;" class="text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Jadwal?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus jadwal ini dari kalender? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex justify-center gap-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition border border-gray-300 dark:border-gray-600">Batal</button>
                    <button @click="executeDelete()" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-500 hover:shadow transition">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* FullCalendar Customizations for Light & Dark Mode */
        .fc .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 800 !important; color: #1e293b !important; }
        .dark .fc .fc-toolbar-title { color: #f8fafc !important; }
        
        .fc .fc-button { font-size: 0.75rem !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; padding: 0.4rem 0.8rem !important; border-radius: 0.5rem !important; transition: all 0.15s !important; }
        .fc .fc-button-primary { background-color: #4f46e5 !important; border-color: #4338ca !important; color: #ffffff !important; }
        .fc .fc-button-primary:hover { background-color: #4338ca !important; box-shadow: 0 2px 8px rgba(79,70,229,0.3) !important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #3730a3 !important; border-color: #312e81 !important; }
        
        /* Table Headers */
        .fc .fc-col-header-cell { background-color: #f8fafc !important; border-color: #e2e8f0 !important; padding: 8px 0 !important; }
        .dark .fc .fc-col-header-cell { background-color: #1e293b !important; border-color: #334155 !important; }
        .fc .fc-col-header-cell-cushion { font-weight: 800 !important; text-transform: uppercase !important; font-size: 0.7rem !important; letter-spacing: 0.08em !important; color: #475569 !important; }
        .dark .fc .fc-col-header-cell-cushion { color: #cbd5e1 !important; }
        
        /* Grid Cells */
        .fc .fc-daygrid-day { background-color: #ffffff !important; border-color: #e2e8f0 !important; }
        .dark .fc .fc-daygrid-day { background-color: #0f172a !important; border-color: #1e293b !important; }
        .fc .fc-daygrid-day:hover { background-color: #f1f5f9 !important; }
        .dark .fc .fc-daygrid-day:hover { background-color: rgba(79,70,229,0.08) !important; }
        
        /* Day Numbers */
        .fc .fc-daygrid-day-number { font-weight: 700 !important; font-size: 0.8rem !important; color: #334155 !important; padding: 8px !important; }
        .dark .fc .fc-daygrid-day-number { color: #94a3b8 !important; }
        
        /* Today Highlight */
        .fc .fc-day-today { background-color: #e0e7ff !important; }
        .dark .fc .fc-day-today { background-color: rgba(79,70,229,0.15) !important; }
        .fc .fc-day-today .fc-daygrid-day-number { color: #4f46e5 !important; font-weight: 900 !important; }
        
        /* Borders & Events */
        .fc td, .fc th { border-color: #e2e8f0 !important; }
        .dark .fc td, .dark .fc th { border-color: #1e293b !important; }
        .fc .fc-event { border-radius: 0.4rem !important; padding: 2px 6px !important; font-size: 0.7rem !important; font-weight: 600 !important; cursor: pointer !important; transition: transform 0.1s, box-shadow 0.1s !important; }
        .fc .fc-event:hover { transform: translateY(-1px) !important; box-shadow: 0 3px 12px rgba(0,0,0,0.15) !important; }
        .fc .fc-highlight { background: rgba(79,70,229,0.12) !important; border: 2px dashed rgba(79,70,229,0.4) !important; border-radius: 0.5rem !important; }
        .fc .fc-event-dragging { opacity: 0.7 !important; box-shadow: 0 8px 25px rgba(0,0,0,0.2) !important; }
        .fc .fc-event-resizing { opacity: 0.7 !important; }
    </style>

    <script>
    function calendarApp() {
        return {
            calendar: null,
            showDetailModal: false,
            showCreateModal: false,
            showDeleteModal: false,
            detailTitle: '',
            detailBody: '',
            detailEventLocationId: null,
            createStartDate: '',
            createEndDate: '',
            createEventId: '',
            createLocationId: '',

            init() {
                if (typeof FullCalendar === 'undefined') {
                    let script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js';
                    script.onload = () => this.renderCalendar();
                    document.head.appendChild(script);
                } else {
                    this.renderCalendar();
                }
            },

            renderCalendar() {
                const calendarEl = document.getElementById('calendar');
                if (!calendarEl) return;
                if (calendarEl.innerHTML !== '') calendarEl.innerHTML = '';

                const eventsData = @js($this->getEvents());
                const self = this;

                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    buttonText: { today: 'Hari Ini', month: 'Bulan', week: 'Minggu', list: 'Daftar' },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listMonth'
                    },
                    events: eventsData,
                    height: 'auto',
                    eventDisplay: 'block',
                    selectable: true,
                    editable: true,
                    eventResizableFromStart: true,
                    eventStartEditable: true,
                    eventDurationEditable: true,
                    dayMaxEvents: 3,
                    navLinks: true,

                    // Drag-select to create
                    select: (info) => {
                        const endDate = new Date(info.end);
                        endDate.setDate(endDate.getDate() - 1);

                        self.createStartDate = info.startStr;
                        self.createEndDate = endDate.toISOString().split('T')[0];
                        self.createEventId = '';
                        self.createLocationId = '';
                        self.showCreateModal = true;
                        self.calendar.unselect();
                    },

                    // Click event to view details
                    eventClick: (info) => {
                        self.detailTitle = info.event.title;
                        self.detailBody = info.event.extendedProps.details;
                        self.detailEventLocationId = info.event.extendedProps.eventLocationId;
                        self.showDetailModal = true;
                    },

                    // Drag event to move
                    eventDrop: (info) => {
                        const endDate = info.event.end ? new Date(info.event.end) : new Date(info.event.start);
                        if (info.event.end) endDate.setDate(endDate.getDate() - 1);

                        const startStr = info.event.start.toISOString().split('T')[0];
                        const endStr = endDate.toISOString().split('T')[0];

                        self.$wire.updateEventLocationDates(
                            info.event.extendedProps.eventLocationId,
                            startStr,
                            endStr
                        );
                    },

                    // Resize event to change duration
                    eventResize: (info) => {
                        const endDate = new Date(info.event.end);
                        endDate.setDate(endDate.getDate() - 1);

                        const startStr = info.event.start.toISOString().split('T')[0];
                        const endStr = endDate.toISOString().split('T')[0];

                        self.$wire.updateEventLocationDates(
                            info.event.extendedProps.eventLocationId,
                            startStr,
                            endStr
                        );
                    },
                });

                this.calendar.render();
            },

            refreshCalendar() {
                if (this.calendar) {
                    this.calendar.destroy();
                }
                this.$wire.$refresh().then(() => {
                    this.$nextTick(() => {
                        this.renderCalendar();
                    });
                });
            },

            submitCreate() {
                if (!this.createEventId || !this.createLocationId) return;
                this.$wire.saveEventLocation(
                    parseInt(this.createEventId),
                    parseInt(this.createLocationId),
                    this.createStartDate,
                    this.createEndDate
                );
                this.showCreateModal = false;
            },

            confirmDelete() {
                this.showDetailModal = false;
                this.showDeleteModal = true;
            },

            executeDelete() {
                this.$wire.deleteEventLocation(this.detailEventLocationId);
                this.showDeleteModal = false;
            },
        };
    }
    </script>
</x-filament-panels::page>
