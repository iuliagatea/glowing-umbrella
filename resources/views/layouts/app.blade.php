<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', \App\Models\Setting::get('site_name', config('app.name')))</title>
    <meta name="description" content="@yield('description', 'Sales and repair services.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 antialiased">
    @include('partials.header')
    <main>@yield('content')</main>
    @include('partials.footer')
</body>
</html>