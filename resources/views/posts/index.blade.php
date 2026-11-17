@extends('layouts.app')
@section('title', 'Articles | ' . \App\Models\Setting::get('site_name', config('app.name')))
@section('description', 'News, guides and updates from ' . \App\Models\Setting::get('site_name', config('app.name')) . '.')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12">
    <h1 class="text-3xl font-bold text-ink">Articles</h1>

    @if($posts->isEmpty())
        <p class="mt-6 text-slate-600">No articles have been published yet.</p>
    @else
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach($posts as $post)
                <article class="overflow-hidden rounded-xl border border-outline bg-white shadow-sm">
                    @if($post->cover_image)
                        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-44 w-full object-cover">
                    @endif
                    <div class="p-5">
                        <time class="text-xs text-slate-500">{{ $post->published_at->format('d M Y') }}</time>
                        <h2 class="mt-1 font-semibold text-ink">
                            <a href="{{ route('posts.show', $post) }}" class="hover:text-brand">{{ $post->title }}</a>
                        </h2>
                        <p class="mt-2 text-sm text-slate-600">{{ $post->excerpt }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
