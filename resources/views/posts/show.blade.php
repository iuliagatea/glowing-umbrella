@extends('layouts.app')
@section('title', $post->title . ' | ' . \App\Models\Setting::get('site_name', config('app.name')))
@section('description', $post->excerpt ?? 'Sales and repair services.')

@section('content')
<article class="mx-auto max-w-3xl px-4 py-12">
    <time class="text-sm text-gray-500">{{ $post->published_at->format('d M Y') }}</time>
    <h1 class="mt-1 text-3xl font-bold">{{ $post->title }}</h1>
    @if($post->cover_image)
        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="" class="mt-6 w-full rounded">
    @endif
    <div class="prose mt-6 max-w-none">{!! $post->body !!}</div>
</article>
@endsection
