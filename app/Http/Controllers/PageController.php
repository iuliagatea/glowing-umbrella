<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Post;
use App\Models\Service;

class PageController extends Controller
{
    public function home()
    {
        $announcements = Announcement::active()->orderBy('sort_order')->take(4)->get();
        $services = Service::where('is_active', true)->orderBy('sort_order')->take(6)->get();
        $posts = Post::published()->latest('published_at')->take(3)->get();

        return view('home', compact('announcements', 'services', 'posts'));
    }

    public function about()
    {
        return view('about');
    }
}
