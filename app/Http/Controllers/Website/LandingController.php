<?php

namespace App\Http\Controllers\Website;

use App\Models\Faq;
use App\Models\Post;
use App\Models\Banner;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class LandingController extends Controller
{
    public function index()
    {
        $banner = Banner::select('file')->where('category', 'banner')->get();
        $headline = Post::with(['categories'])->orderBy('created_at', 'desc')->where('status', 1)->where('is_headline', 1)->take(1)->get();
        $article = Post::with(['categories'])->where('category_id', 2)->where('status', 1)->orderBy('created_at', 'desc')->take(6)->get();
        $news = Post::with(['categories'])->where('category_id', 1)->where('status', 1)->orderBy('created_at', 'desc')->take(6)->get();

        $faq = Faq::orderBy('created_at', 'desc')->take(5)->get();
        $announcement = Announcement::where('is_active', 1)->orderBy('created_at', 'DESC')->take(6)->get();

        $events = [];
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => env('APP_API_KEY'),
            ])->get(env('API_WEBINAR_URL') . '/api/agendas');

            if ($response->successful()) {
                $json = $response->json();
                $events = $json['data'] ?? [];
            } else {
                $events = [];
            }
        } catch (\Exception $e) {
            \Log::error("Gagal mengambil API Webinar: " . $e->getMessage());
        }

        return view('website.pages.landing', compact('banner', 'headline', 'article', 'news', 'announcement', 'events', 'faq'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $news = Post::DataSide()->get();
        return view('website.pages.detail-post', compact('post', 'news'));
    }

    public function sidedata()
    {
        $news = Post::DataSide()->get();
        return view('website.layout-detail', compact('news'));
    }
}
