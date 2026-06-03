<div class="hidden items-center gap-x-3 px-3 py-2 sm:flex">
    <div class="flex flex-col items-end">
        <span class="text-sm font-bold text-gray-900 dark:text-white" id="navbar-clock-time">
            {{ now()->format('H:i:s') }}
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ now()->translatedFormat('l, d F Y') }}
        </span>
    </div>
    <div class="h-8 w-px bg-gray-200 dark:bg-gray-700"></div>
</div>

<script>
    function updateNavbarClock() {
        const timeElement = document.getElementById('navbar-clock-time');
        if (timeElement) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    setInterval(updateNavbarClock, 1000);
</script>
