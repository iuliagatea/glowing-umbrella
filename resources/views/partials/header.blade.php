<header class="border-b border-outline bg-white">
    <nav class="mx-auto flex max-w-6xl items-center justify-between p-4">
        <a href="{{ route('home') }}" class="text-xl font-bold text-ink">{{ \App\Models\Setting::get('site_name', config('app.name')) }}</a>
        <ul class="flex gap-6 text-sm font-medium text-ink">
            <li><a href="{{ route('home') }}" class="hover:text-brand">Home</a></li>
            <li><a href="{{ route('prices') }}" class="hover:text-brand">Prices</a></li>
            @if($hasPublishedArticles)
                <li><a href="{{ route('posts.index') }}" class="hover:text-brand">Articles</a></li>
            @endif
            <li><a href="{{ route('about') }}" class="hover:text-brand">About us</a></li>
            <li><a href="{{ route('contact') }}" class="hover:text-brand">Contact</a></li>
        </ul>
    </nav>
</header>