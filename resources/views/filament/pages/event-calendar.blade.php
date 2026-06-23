<x-filament-panels::page>
    <div wire:ignore 
         x-data="{
            showModal: false,
            modalTitle: '',
            modalBody: '',
            calendar: null,
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
                
                // Hapus instance sebelumnya jika ada saat livewire mere-render
                if (calendarEl.innerHTML !== '') {
                    calendarEl.innerHTML = '';
                }

                const eventsData = @js($this->getEvents());

                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        list: 'Daftar'
                    },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    events: eventsData,
                    eventClick: (info) => {
                        this.modalTitle = info.event.title;
                        this.modalBody = info.event.extendedProps.details;
                        this.showModal = true;
                    },
                    eventColor: '#4f46e5',
                    height: 'auto',
                    eventDisplay: 'block',
                });

                this.calendar.render();
            }
         }">
         
        <div id="calendar" class="p-4 bg-white rounded-xl shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10" style="min-height: 600px;">
            <div class='flex items-center justify-center h-full text-gray-400'>Memuat Kalender...</div>
        </div>

        <!-- Modal untuk Rincian Event -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @click.self="showModal = false">
            <div class="w-full max-w-2xl p-6 bg-white rounded-xl shadow-xl dark:bg-gray-800 border dark:border-gray-700" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">
                 
                <div class="flex items-center justify-between mb-4 border-b pb-3 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="text-sm text-gray-700 dark:text-gray-300 w-full" x-html="modalBody"></div>
                
                <div class="mt-5 flex justify-end pt-3 border-t dark:border-gray-700">
                    <button @click="showModal = false" class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-500 hover:shadow transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
