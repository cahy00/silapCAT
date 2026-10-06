<x-filament-widgets::widget>
    @php
        $mapId = 'silapcat_map_' . str_replace('.', '_', uniqid('', true));
        $locations = $this->mapLocations;
    @endphp

    <style>
        .custom-map-marker { background: transparent !important; border: none !important; }
        @@keyframes silapcat-ping { 75%, 100% { transform: scale(2); opacity: 0; } }
        @@keyframes silapcat-spin { to { transform: rotate(360deg); } }
    </style>

    <div
        x-data="{
            loaded: false,
            init() {
                let self = this;
                let mapId = '{{ $mapId }}';
                let locations = {{ Js::from($locations) }};

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
                        script.onload = () => {
                            setTimeout(callback, 100);
                        };
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
                        self.renderMap(mapId, locations);
                    });
                });
            },
            renderMap(mapId, locations) {
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

                (locations || []).forEach(function(loc) {
                    let color = '#3b82f6';
                    let isPulse = false;

                    if (loc.has_active_event) {
                        color = '#22c55e';
                        isPulse = true;
                    } else if (loc.feasibility !== 'feasible') {
                        color = '#f59e0b';
                    }

                    let ph = isPulse
                        ? '<span style=\'position:absolute;top:-5px;left:-5px;width:28px;height:28px;border-radius:9999px;background:' + color + ';opacity:0.4;animation:silapcat-ping 1.5s cubic-bezier(0,0,0.2,1) infinite;\'></span>'
                        : '';
                    let icon = L.divIcon({
                        className: 'custom-map-marker',
                        html: '<div style=\'position:relative;width:18px;height:18px;border-radius:9999px;background:' + color + ';border:2.5px solid #fff;box-shadow:0 3px 8px rgba(0,0,0,0.4);\'>' + ph + '</div>',
                        iconSize: [18, 18],
                        iconAnchor: [9, 9],
                        popupAnchor: [0, -10]
                    });

                    let marker = L.marker([loc.lat, loc.lng], { icon: icon }).addTo(map);
                    bounds.push([loc.lat, loc.lng]);

                    let badge = loc.has_active_event
                        ? '<span style=\'display:inline-block;padding:2px 7px;border-radius:9999px;font-size:10px;font-weight:700;background:#dcfce7;color:#15803d;margin-bottom:4px;\'>🟢 SEDANG UJIAN AKTIF</span>'
                        : '<span style=\'display:inline-block;padding:2px 7px;border-radius:9999px;font-size:10px;font-weight:700;background:#dbeafe;color:#1d4ed8;margin-bottom:4px;\'>🔵 SIAP / STANDBY</span>';

                    let evHtml = '';
                    if (loc.has_active_event && loc.active_events && loc.active_events.length > 0) {
                        evHtml = '<div style=\'margin-top:6px;padding:6px;background:#f0fdf4;border-radius:6px;border:1px solid #bbf7d0;font-size:11px;color:#166534;\'><strong>Kegiatan:</strong> ' + loc.active_events.join(', ') + '</div>';
                    }

                    marker.bindPopup(
                        '<div style=\'min-width:200px;font-family:inherit;color:#0f172a;line-height:1.4;\'>' +
                            badge +
                            '<div style=\'font-size:13px;font-weight:700;color:#0f172a;margin-bottom:2px;\'>' + loc.name + '</div>' +
                            '<div style=\'font-size:11px;color:#64748b;margin-bottom:6px;\'>' + loc.city + ' &bull; ' + loc.type + '</div>' +
                            '<div style=\'font-size:11px;color:#334155;margin-bottom:4px;\'>📍 ' + loc.address + '</div>' +
                            '<div style=\'display:flex;gap:8px;margin-top:6px;padding-top:6px;border-top:1px solid #e2e8f0;font-size:11px;\'>' +
                                '<div>🖥️ <strong>' + (loc.pc_count || '-') + '</strong> PC</div>' +
                                '<div>🚪 <strong>' + (loc.room_count || '-') + '</strong> Ruang</div>' +
                                '<div>📋 <strong>' + loc.total_events_count + '</strong> Event</div>' +
                            '</div>' +
                            evHtml +
                            '<div style=\'margin-top:8px;text-align:right;\'>' +
                                '<a href=\'https://maps.google.com/?q=' + loc.lat + ',' + loc.lng + '\' target=\'_blank\' style=\'font-size:11px;color:#2563eb;text-decoration:none;font-weight:600;\'>Buka Google Maps →</a>' +
                            '</div>' +
                        '</div>'
                    );
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 10 });
                }

                self.loaded = true;
                setTimeout(function() { map.invalidateSize(); }, 300);
                setTimeout(function() { map.invalidateSize(); }, 1000);
            }
        }"
        style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); color: #0f172a;"
        class="dark:!bg-slate-900 dark:!border-slate-800 dark:!text-slate-100"
    >
        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #eff6ff; border-radius: 0.6rem; color: #2563eb;" class="dark:!bg-indigo-950 dark:!text-indigo-400">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; line-height: 1.2;">
                        Peta Sebaran Titik Lokasi Ujian CAT
                    </h3>
                    <p style="font-size: 0.78rem; color: #64748b; margin: 0.15rem 0 0 0;" class="dark:!text-slate-400">
                        Distribusi infrastruktur titik lokasi CAT & pemantauan status seleksi
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.75rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; color: #16a34a; font-weight: 600;">
                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 9999px; background: #22c55e;"></span>
                    Ujian Aktif
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; color: #2563eb; font-weight: 600;">
                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 9999px; background: #3b82f6;"></span>
                    Siap / Standby
                </span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; color: #d97706; font-weight: 600;">
                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 9999px; background: #f59e0b;"></span>
                    Hasil Survey
                </span>
            </div>
        </div>

        {{-- Map --}}
        <div wire:ignore style="position: relative; width: 100%; height: 420px; border-radius: 0.75rem; overflow: hidden; border: 1px solid #cbd5e1; background: #f8fafc;" class="dark:!border-slate-700 dark:!bg-slate-800">
            <div id="{{ $mapId }}" style="width: 100%; height: 100%; min-height: 420px; z-index: 10;"></div>

            <div x-show="!loaded" x-transition.opacity style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(248,250,252,0.9); z-index: 20;" class="dark:!bg-slate-800/90">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; color: #475569;" class="dark:!text-slate-300">
                    <svg style="width: 20px; height: 20px; animation: silapcat-spin 1s linear infinite;" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity: 0.75;"></path>
                    </svg>
                    Memuat Peta Sebaran Tilok...
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
