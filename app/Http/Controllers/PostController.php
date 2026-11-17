<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()->latest('published_at')->paginate(9);

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        abort_unless($post->is_published && $post->published_at <= now(), 404);

        return view('posts.show', compact('post'));
    }
}
