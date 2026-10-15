<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function adminPageUris(): array
{
    return collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($r) => in_array('GET', $r->methods()))
        ->map(fn ($r) => $r->uri())
        ->filter(fn ($uri) => str_starts_with($uri, 'admin') && ! str_contains($uri, '{'))
        ->reject(fn ($uri) => str_contains($uri, 'logout') || str_contains($uri, 'login')
            || str_contains($uri, 'password-reset') || str_contains($uri, 'email-verification'))
        ->unique()->values()->all();
}

test('all admin module pages render without server error for super_admin', function () {
    Role::findOrCreate('super_admin', 'web');
    Role::findOrCreate('operator', 'web');
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $failures = [];
    foreach (adminPageUris() as $uri) {
        try {
            $status = $this->actingAs($user)->get('/' . $uri)->status();
        } catch (\Throwable $e) {
            $status = 'EXC: ' . class_basename($e) . ' - ' . str($e->getMessage())->limit(150);
        }
        if (! is_int($status) || $status >= 500) {
            $failures[] = "/{$uri} => {$status}";
        }
    }

    expect($failures)->toBe([], "Halaman gagal:\n" . implode("\n", $failures));
});

test('public pages load', function (string $uri) {
    $this->get($uri)->assertSuccessful();
})->with(['/admin/login', '/sertifikat', '/buat-link']);

test('v1 api endpoints respond with valid key', function (string $uri) {
    $key = config('app.api_key', env('API_KEY', 'silapcat-secret-key-2026'));
    $this->withHeaders(['X-API-KEY' => $key])->getJson($uri)->assertOk()->assertJsonPath('success', true);
})->with(['/api/v1/events', '/api/v1/reports', '/api/v1/exam-scores', '/api/v1/employees', '/api/v1/institutions', '/api/v1/documents']);
