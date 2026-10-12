<x-filament-widgets::widget>
    <div class="fi-welcome-banner">
        <style>
            .fi-welcome-banner {
                position: relative;
                overflow: hidden;
                border-radius: 1.25rem;
                padding: 2.25rem 2.5rem;
                background: linear-gradient(135deg, #4338ca 0%, #6366f1 45%, #4f46e5 100%);
                box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.35), inset 0 1px 0 0 rgba(255, 255, 255, 0.2);
                color: #ffffff;
                transition: all 0.3s ease;
            }

            .dark .fi-welcome-banner {
                background: linear-gradient(135deg, #0e1424 0%, #161e36 50%, #11182c 100%) !important;
                border: 1px solid rgba(99, 102, 241, 0.3) !important;
                box-shadow: 0 14px 34px -6px rgba(0, 0, 0, 0.6), inset 0 1px 0 0 rgba(255, 255, 255, 0.08) !important;
            }

            .wb-content {
                position: relative;
                z-index: 10;
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
            }

            .wb-header-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.35rem 0.85rem;
                border-radius: 9999px;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                background: rgba(255, 255, 255, 0.18);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.25);
                color: #ffffff;
                width: fit-content;
            }

            .dark .wb-header-badge {
                background: rgba(99, 102, 241, 0.15) !important;
                border-color: rgba(99, 102, 241, 0.35) !important;
                color: #a5b4fc !important;
            }

            .wb-pulse-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background-color: #34d399;
                display: inline-block;
                animation: wb-pulse 1.8s infinite;
            }

            @keyframes wb-pulse {
                0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.8); }
                70% { box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
                100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
            }

            .wb-title {
                font-size: 1.85rem;
                font-weight: 800;
                letter-spacing: -0.03em;
                color: #ffffff;
                margin: 0;
                line-height: 1.25;
            }

            .wb-subtitle {
                font-size: 0.925rem;
                color: rgba(255, 255, 255, 0.85);
                margin: 0.35rem 0 0 0;
                line-height: 1.5;
                max-width: 680px;
                font-weight: 400;
            }

            .dark .wb-subtitle {
                color: #94a3b8;
            }

            .wb-pills-row {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                flex-wrap: wrap;
            }

            .wb-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                border-radius: 0.65rem;
                background: rgba(255, 255, 255, 0.18);
                padding: 0.45rem 0.95rem;
                font-size: 0.8125rem;
                font-weight: 600;
                color: #ffffff;
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                border: 1px solid rgba(255, 255, 255, 0.25);
                transition: all 0.2s ease;
            }

            .wb-pill:hover {
                background: rgba(255, 255, 255, 0.25);
                transform: translateY(-1px);
            }

            .dark .wb-pill {
                background: rgba(255, 255, 255, 0.05) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                color: #e2e8f0 !important;
            }

            .dark .wb-pill:hover {
                background: rgba(255, 255, 255, 0.08) !important;
                border-color: rgba(99, 102, 241, 0.35) !important;
            }

            .wb-pill svg {
                width: 1rem;
                height: 1rem;
                opacity: 0.9;
                flex-shrink: 0;
            }

            /* Ambient background shapes */
            .wb-orb-1 {
                position: absolute;
                bottom: -4rem;
                right: -4rem;
                height: 20rem;
                width: 20rem;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(99, 102, 241, 0.45) 0%, rgba(99, 102, 241, 0) 70%);
                filter: blur(40px);
                pointer-events: none;
            }

            .wb-orb-2 {
                position: absolute;
                top: -3rem;
                right: 25%;
                height: 14rem;
                width: 14rem;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(14, 165, 233, 0.35) 0%, rgba(14, 165, 233, 0) 70%);
                filter: blur(35px);
                pointer-events: none;
            }

            .wb-grid-overlay {
                position: absolute;
                inset: 0;
                background-image: linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                                  linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
                background-size: 24px 24px;
                opacity: 0.6;
                pointer-events: none;
            }
        </style>

        <div class="wb-grid-overlay"></div>
        <div class="wb-orb-1"></div>
        <div class="wb-orb-2"></div>

        <div class="wb-content">
            <div class="wb-header-badge">
                <span class="wb-pulse-dot"></span>
                <span>KANREG XIV BKN MANOKWARI • MONITORING LIVE</span>
            </div>

            <div>
                <h1 class="wb-title">
                    Selamat Datang kembali, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="wb-subtitle">
                    Dashboard pemantauan kegiatan seleksi CASN berbasis Computer Assisted Test (CAT) secara terpadu, akurat, dan real-time.
                </p>
            </div>

            <div class="wb-pills-row">
                <div class="wb-pill">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"></line>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"></line>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"></line>
                    </svg>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="wb-pill">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                        <polyline points="12 6 12 12 16 14" stroke-width="2"></polyline>
                    </svg>
                    <span>{{ now()->format('H:i') }} WIT (Zona Waktu Lokal)</span>
                </div>
                <div class="wb-pill" style="color: #34d399; font-weight: 700;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Server Aktif & Sinkron</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
