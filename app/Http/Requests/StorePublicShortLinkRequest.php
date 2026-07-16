<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_url' => [
                'required',
                'url:http,https',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $this->rejectDangerousUrl($value, $fail);
                },
            ],
            'short_code' => [
                'nullable',
                'string',
                'alpha_dash',
                'max:50',
                'min:3',
                Rule::unique('short_links', 'short_code'),
                function (string $attribute, mixed $value, \Closure $fail) {
                    $this->rejectReservedCodes($value, $fail);
                },
            ],
            // Honeypot: field ini harus kosong, bot biasanya mengisi semua field
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'destination_url.required'  => 'URL tujuan wajib diisi.',
            'destination_url.url'       => 'Format URL tidak valid. Hanya URL dengan http:// atau https:// yang diperbolehkan.',
            'destination_url.max'       => 'URL terlalu panjang (maks 2048 karakter).',
            'short_code.unique'         => 'Kode pendek ini sudah digunakan. Silakan pilih kode lain.',
            'short_code.alpha_dash'     => 'Kode pendek hanya boleh berisi huruf, angka, strip, atau garis bawah.',
            'short_code.min'            => 'Kode pendek minimal 3 karakter.',
            'website.prohibited'        => 'Terjadi kesalahan validasi.',
        ];
    }

    /**
     * Tolak URL yang mengandung pola berbahaya:
     * - javascript: / data: / vbscript: scheme
     * - Encoded script tags
     * - Domain yang diblokir (phishing / malware)
     */
    private function rejectDangerousUrl(string $url, \Closure $fail): void
    {
        $decoded = urldecode($url);
        $lower = mb_strtolower($decoded);

        // 1. Blokir scheme berbahaya (sudah ditangani oleh 'url:http,https' tapi double-check)
        $dangerousSchemes = ['javascript:', 'data:', 'vbscript:', 'file:', 'ftp:'];
        foreach ($dangerousSchemes as $scheme) {
            if (str_contains($lower, $scheme)) {
                $fail('URL mengandung scheme yang tidak diperbolehkan.');
                return;
            }
        }

        // 2. Blokir tag script / HTML injection dalam URL
        $dangerousPatterns = [
            '/<\s*script/i',
            '/<\s*iframe/i',
            '/<\s*object/i',
            '/<\s*embed/i',
            '/<\s*form/i',
            '/on\w+\s*=/i',        // onclick=, onerror=, dll.
            '/base64[,;]/i',
            '/&#\d+;/',            // HTML entity encoding
            '/&#x[0-9a-f]+;/i',   // Hex HTML entity encoding
        ];
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $decoded)) {
                $fail('URL mengandung konten yang tidak diperbolehkan.');
                return;
            }
        }

        // 3. Validasi bahwa URL benar-benar bisa di-parse
        $parsed = parse_url($url);
        if ($parsed === false || empty($parsed['host'])) {
            $fail('URL tidak valid.');
            return;
        }

        // 4. Blokir domain yang mencurigakan / berbahaya
        $host = mb_strtolower($parsed['host']);
        $blockedDomains = [
            'bit.ly',           // Mencegah double-shortening
            'tinyurl.com',
            'shorturl.at',
            'is.gd',
            't.co',
            'goo.gl',
            'ow.ly',
            'rebrand.ly',
        ];
        foreach ($blockedDomains as $blocked) {
            if ($host === $blocked || str_ends_with($host, '.' . $blocked)) {
                $fail('Tidak diperbolehkan membuat short link dari layanan pemendek URL lain.');
                return;
            }
        }

        // 5. Blokir localhost / IP private (mencegah SSRF)
        if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1'], true)) {
            $fail('URL tidak valid.');
            return;
        }
        if (preg_match('/^(10\.|172\.(1[6-9]|2\d|3[01])\.|192\.168\.)/', $host)) {
            $fail('URL tidak valid.');
            return;
        }
    }

    /**
     * Tolak kode pendek yang sama dengan route yang sudah ada di aplikasi.
     */
    private function rejectReservedCodes(?string $code, \Closure $fail): void
    {
        if (empty($code)) {
            return;
        }

        $reserved = [
            'admin', 'login', 'register', 'api', 'storage', 'buat-link',
            'berita', 'pengumuman', 'konsultasi', 'sertifikat', 'template',
            'events', 'reports', 'css', 'js', 'images', 'assets', 'vendor',
            'semua-berita', 'semua-artikel',
        ];

        if (in_array(mb_strtolower($code), $reserved, true)) {
            $fail('Kode pendek ini sudah digunakan oleh sistem. Silakan pilih kode lain.');
        }
    }
}
