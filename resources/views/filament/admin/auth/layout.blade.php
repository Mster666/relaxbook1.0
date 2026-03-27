<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="rb-logo" content="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Admin Login - {{ config('app.name', 'RelaxBook') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <script defer src="{{ asset('js/rb-loader.js') }}"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-800 to-teal-700 min-h-screen flex items-center justify-center p-4">
    {{ $slot }}
    @livewireScripts
</body>
</html>
