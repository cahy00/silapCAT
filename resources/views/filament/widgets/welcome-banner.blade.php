<x-filament-widgets::widget>
    <div style="position: relative; overflow: hidden; border-radius: 1rem; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #4338ca 100%); padding: 2rem 2.5rem; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.35);">
        <div style="position: relative; z-index: 10;">
            <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; color: #ffffff; margin: 0; line-height: 1.3;">
                Selamat Datang kembali, {{ auth()->user()->name }}! 👋
            </h1>

            <div style="margin-top: 1.25rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <div style="border-radius: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; color: #ffffff; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                    📅 {{ now()->translatedFormat('l, d F Y') }}
                </div>
                <div style="border-radius: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; color: #ffffff; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                    ⏰ {{ now()->format('H:i') }} WIB
                </div>
            </div>
        </div>

        <!-- Subtle background decoration -->
        <div style="position: absolute; bottom: -3rem; right: -3rem; height: 16rem; width: 16rem; border-radius: 50%; background: rgba(255,255,255,0.1); filter: blur(48px);"></div>
        <div style="position: absolute; top: -3rem; left: -3rem; height: 12rem; width: 12rem; border-radius: 50%; background: rgba(255,255,255,0.05); filter: blur(32px);"></div>
    </div>
</x-filament-widgets::widget>
