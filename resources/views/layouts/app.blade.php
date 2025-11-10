<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CopyPasteHouse')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
@php
    $theme = 'dark'; // default
    if (auth()->check() && auth()->user()->settings) {
        $userTheme = auth()->user()->settings->theme;
        if ($userTheme === 'light') {
            $theme = 'light';
        } elseif ($userTheme === 'dark') {
            $theme = 'dark';
        } else {
            // system - detect from browser
            $theme = 'dark'; // default to dark for system
        }
    }
@endphp
<body class="{{ $theme === 'light' ? 'bg-gray-100 text-gray-900' : 'bg-gray-900 text-white' }}">
    <x-header />

    @yield('content')

    @stack('scripts')
</body>
</html>
