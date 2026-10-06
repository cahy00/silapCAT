<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Filament\Notifications\Notification;

class GoogleSsoController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('filament.admin.auth.login')
                ->with('notification', [
                    'type' => 'danger',
                    'title' => 'Login Gagal',
                    'body' => 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.',
                ]);
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id and avatar if not set
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            // Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null,
            ]);

            // Create or get 'tamu' role with only Event view permission
            $tamuRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'tamu',
                'guard_name' => 'web',
            ]);

            $viewAnyPermission = \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => 'ViewAny:Event',
                'guard_name' => 'web',
            ]);
            $viewPermission = \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => 'View:Event',
                'guard_name' => 'web',
            ]);

            $tamuRole->syncPermissions([$viewAnyPermission, $viewPermission]);

            // Assign tamu role to new user
            $user->assignRole($tamuRole);
        }

        Auth::login($user, remember: false);

        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}
