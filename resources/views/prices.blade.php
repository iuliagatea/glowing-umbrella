@extends('layouts.app')
@section('title', 'Prices | ' . \App\Models\Setting::get('site_name', config('app.name')))

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-bold">Service prices</h1>

    @foreach($categories as $category)
        @continue($category->services->isEmpty())
        <h2 class="mt-10 text-xl font-semibold">{{ $category->name }}</h2>
        <table class="mt-3 w-full text-left text-sm">
            <tbody class="divide-y">
            @foreach($category->services as $service)
                <tr>
                    <td class="py-3 pr-4">
                        <p class="font-medium">{{ $service->name }}</p>
                        @if($service->description)<p class="text-gray-500">{{ $service->description }}</p>@endif
                    </td>
                    <td class="py-3 text-right font-semibold whitespace-nowrap">
                        {{ $service->price_note }} {{ number_format($service->price, 2) }} RON
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection
