<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicShortLinkRequest;
use App\Models\ShortLink;

class PublicShortLinkController extends Controller
{
    public function index()
    {
        return view('shortlink-public');
    }

    public function store(StorePublicShortLinkRequest $request)
    {
        $validated = $request->validated();

        $shortCode = $validated['short_code'] ?? null;
        if (empty($shortCode)) {
            $shortCode = ShortLink::generateUniqueCode();
        }

        // Sanitasi URL tujuan: pastikan tidak mengandung trailing whitespace
        $destinationUrl = trim($validated['destination_url']);

        $shortLink = ShortLink::create([
            'destination_url' => $destinationUrl,
            'short_code' => $shortCode,
            'is_active' => true,
        ]);

        return back()->with('success', 'Short Link berhasil dibuat!')->with('short_url', url($shortLink->short_code));
    }
}
