<?php

namespace App\Http\Controllers\Website;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function allNews()
    {
        $berita = Post::with(['categories'])->where('category_id', 1)->where('status', 1)->orderBy('created_at', 'desc')->paginate(10);
        $news = Post::DataSide()->get();
        return view('website.pages.all-news', compact('berita', 'news'));
    }

    public function allArticle()
    {
        $artikel = Post::with(['categories'])->where('category_id', 2)->where('status', 1)->orderBy('created_at', 'desc')->paginate(10);
        $news = Post::DataSide()->get();
        return view('website.pages.all-article', compact('artikel', 'news'));
    }
}
