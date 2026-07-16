<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ShortLinkController extends Controller
{
    public function redirect(string $shortCode): RedirectResponse
    {
        $shortLink = ShortLink::where('short_code', $shortCode)
            ->where('is_active', true)
            ->firstOrFail();

        $shortLink->increment('click_count');

        return redirect()->away($shortLink->destination_url, Response::HTTP_FOUND);
    }
}
