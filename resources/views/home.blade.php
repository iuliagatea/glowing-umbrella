@extends('layouts.app')
@section('title', \App\Models\Setting::get('site_name', config('app.name')) . ' | Sales & repairs')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-surface" aria-roledescription="carousel" aria-label="Announcements">
        <div class="hero-slider relative" data-slider @if($announcements->count() > 1) data-autoplay="6000" @endif>
            <div class="flex transition-transform duration-700 ease-out" data-slider-track>
                @forelse($announcements as $index => $announcement)
                    <div class="w-full shrink-0" data-slider-slide role="group" aria-roledescription="slide" aria-label="{{ $index + 1 }} of {{ $announcements->count() }}" @if($index > 0) aria-hidden="true" @endif>
                        <div class="relative flex min-h-[26rem] items-center overflow-hidden md:min-h-[30rem]">
                            @if($announcement->image)
                                <img src="{{ asset('storage/'.$announcement->image) }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                                <div class="absolute inset-0 bg-ink/60"></div>
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-brand to-brand-dark"></div>
                            @endif
                            <div class="relative mx-auto w-full max-w-6xl px-4 py-24 text-white">
                                @if($announcement->published_at)
                                    <time class="text-xs font-semibold uppercase tracking-wide text-white/80">{{ $announcement->published_at->format('d M Y') }}</time>
                                @endif
                                <h1 class="mt-2 max-w-2xl text-4xl font-bold tracking-tight md:text-5xl">{{ $announcement->title }}</h1>
                                <p class="mt-4 max-w-xl text-lg text-white/90">{{ str($announcement->body)->limit(160) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="w-full shrink-0" data-slider-slide>
                        <div class="mx-auto max-w-6xl px-4 py-24">
                            <h1 class="text-4xl font-bold tracking-tight text-ink md:text-5xl">Quality products and fast repairs</h1>
                            <p class="mt-4 max-w-xl text-lg text-slate-600">Short company pitch goes here.</p>
                            <a href="{{ route('prices') }}" class="mt-8 inline-flex items-center rounded-lg bg-brand px-6 py-3 font-medium text-white shadow-sm transition hover:bg-brand-dark">See our prices</a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($announcements->count() > 1)
                <button type="button" data-slider-prev aria-label="Previous slide" class="absolute top-1/2 left-4 -translate-y-1/2 rounded-full bg-white/80 p-2 text-ink shadow-sm transition hover:bg-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button type="button" data-slider-next aria-label="Next slide" class="absolute top-1/2 right-4 -translate-y-1/2 rounded-full bg-white/80 p-2 text-ink shadow-sm transition hover:bg-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>

                <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2" data-slider-dots>
                    @foreach($announcements as $index => $announcement)
                        <button type="button" data-slider-dot data-slider-index="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" class="h-2.5 w-2.5 rounded-full bg-white/60 transition hover:bg-white aria-[current=true]:bg-white"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>


    {{-- Products & repairs --}}
    {{-- What we do --}}
    <section class="mx-auto grid max-w-6xl gap-6 px-4 py-16 md:grid-cols-2">
        <div class="rounded border p-6"><h2 class="text-xl font-semibold">Our products</h2><p class="mt-2">Describe what you sell in the store.</p></div>
        <div class="rounded border p-6"><h2 class="text-xl font-semibold">Repair service</h2><p class="mt-2">Describe what you repair.</p></div>
    </section>

    {{-- Latest articles --}}
    @if($posts->isNotEmpty())
    <section class="bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-16">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-ink">From our blog</h2>
                <a href="{{ route('posts.index') }}" class="text-sm font-medium text-brand hover:text-brand-dark">View all articles</a>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach($posts as $post)
                    <article class="overflow-hidden rounded-xl border border-outline bg-white shadow-sm">
                        @if($post->cover_image)
                            <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-44 w-full object-cover">
                        @endif
                        <div class="p-5">
                            <time class="text-xs text-slate-500">{{ $post->published_at->format('d M Y') }}</time>
                            <h3 class="mt-1 font-semibold text-ink">
                                <a href="{{ route('posts.show', $post) }}" class="hover:text-brand">{{ $post->title }}</a>
                            </h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $post->excerpt }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
