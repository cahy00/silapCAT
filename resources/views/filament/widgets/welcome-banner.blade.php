<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-700 p-8 shadow-lg dark:from-indigo-900 dark:via-purple-900 dark:to-indigo-950">
        <div class="relative z-10">
            <h1 class="text-3xl font-bold tracking-tight text-white">
                Selamat Datang kembali, {{ auth()->user()->name }}! 👋
            </h1>

            <div class="mt-6 flex items-center gap-4">
                <div class="rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white backdrop-blur-md">
                    📅 {{ now()->translatedFormat('l, d F Y') }}
                </div>
                <div class="rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white backdrop-blur-md">
                    ⏰ {{ now()->format('H:i') }} WIB
                </div>
            </div>
        </div>
        
        <!-- Subtle background decoration -->
        <div class="absolute -bottom-12 -right-12 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -top-12 -left-12 h-48 w-48 rounded-full bg-white/5 blur-2xl"></div>
    </div>
</x-filament-widgets::widget>
