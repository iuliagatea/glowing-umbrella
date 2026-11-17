@extends('layouts.app')
@section('title', 'About us | ' . \App\Models\Setting::get('site_name', config('app.name')))

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12">
    <h1 class="text-3xl font-bold">About us</h1>
    <div class="mt-6 space-y-4 whitespace-pre-line">{{ \App\Models\Setting::get('about_text', 'Tell your story here.') }}</div>
</div>
@endsection
