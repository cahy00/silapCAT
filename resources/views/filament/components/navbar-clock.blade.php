<div class="hidden items-center gap-x-3 px-3 py-1.5 sm:flex">
    <div class="flex items-center gap-2 rounded-lg bg-slate-100/90 px-3 py-1 border border-slate-200/80 backdrop-blur dark:bg-slate-800/50 dark:border-white/10">
        <span class="relative flex h-2 w-2">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
        </span>
        <div class="flex flex-col items-end leading-tight">
            <span class="text-xs font-extrabold tracking-tight text-slate-900 dark:text-white" style="font-variant-numeric: tabular-nums;" id="navbar-clock-time">
                {{ now()->format('H:i:s') }} WIT
            </span>
            <span class="text-[10px] font-medium text-slate-500 dark:text-slate-400">
                {{ now()->translatedFormat('l, d M Y') }}
            </span>
        </div>
    </div>
    <div class="h-6 w-px bg-slate-200 dark:bg-white/10"></div>
</div>

<script>
    function updateNavbarClock() {
        const timeElement = document.getElementById('navbar-clock-time');
        if (timeElement) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeElement.textContent = `${hours}:${minutes}:${seconds} WIT`;
        }
    }
    setInterval(updateNavbarClock, 1000);
</script>
