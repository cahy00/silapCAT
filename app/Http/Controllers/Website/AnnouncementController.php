<?php

namespace App\Http\Controllers\Website;

use App\Models\Post;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    public function index()
    {
        $news = Post::DataSide()->get();
        $announcement = Announcement::where('is_active', 1)->orderBy('created_at', 'desc')->paginate(6);
        return view('website.pages.announcement', compact('news', 'announcement'));
    }

    public function show($id)
    {
        $news = Post::DataSide()->get();
        $announcement = Announcement::findOrFail(decrypt($id));
        return view('website.pages.detail-announcement', compact('news', 'announcement'));
    }
}
