@extends('layouts.app')
@section('title', 'Contact | ' . \App\Models\Setting::get('site_name', config('app.name')))

@section('content')
<div class="mx-auto grid max-w-5xl gap-10 px-4 py-12 md:grid-cols-2">
    <div>
        <h1 class="text-3xl font-bold">Contact</h1>
        <p class="mt-4">{{ \App\Models\Setting::get('address') }}</p>
        <p>Phone: {{ \App\Models\Setting::get('phone') }}</p>
        <p>Email: {{ \App\Models\Setting::get('email') }}</p>
    </div>

    <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">
        @csrf
        @if(session('success'))
            <p class="rounded bg-green-100 p-3 text-green-800">{{ session('success') }}</p>
        @endif

        {{-- honeypot --}}
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

        <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded border p-2" required>
            @error('name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded border p-2" required>
            @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Phone (optional)</label>
            <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded border p-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Message</label>
            <textarea name="message" rows="5" class="mt-1 w-full rounded border p-2" required>{{ old('message') }}</textarea>
            @error('message')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button class="rounded bg-black px-5 py-2 text-white">Send message</button>
    </form>
</div>
@endsection
