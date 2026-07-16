<div class="flex flex-col items-center justify-center p-4">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->margin(1)->generate($url) !!}
    </div>
    <div class="mt-4 text-center">
        <p class="text-sm text-gray-500 mb-2">Scan QR Code di atas atau bagikan URL ini:</p>
        <a href="{{ $url }}" target="_blank" class="text-primary-600 font-medium hover:underline break-all">{{ $url }}</a>
    </div>
</div>
