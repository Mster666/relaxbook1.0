<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="rb-logo" content="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Verify Email - {{ config('app.name', 'RelaxBook') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f5f3ff',
                                100: '#ede9fe',
                                200: '#ddd6fe',
                                300: '#c4b5fd',
                                400: '#a78bfa',
                                500: '#8b5cf6',
                                600: '#7c3aed',
                                700: '#6d28d9',
                                800: '#5b21b6',
                                900: '#4c1d95',
                            }
                        }
                    }
                }
            }
        </script>
    @endif
    <script defer src="{{ asset('js/rb-loader.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-teal-700/50 min-h-screen flex items-center justify-center p-4 font-sans antialiased" style="background: linear-gradient(135deg, #5eead4 0%, #0f766e 100%);">

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden w-full max-w-5xl flex flex-col md:flex-row min-h-[600px]">
        
        <!-- Left Side (Purple Gradient) -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-indigo-700 to-purple-600 p-8 md:p-12 flex flex-col relative text-white items-center justify-center">
            <!-- Back to Login Button -->
            <a href="{{ route('login') }}" class="absolute top-8 left-8 bg-white text-gray-900 px-6 py-2 rounded-full font-bold text-sm shadow-lg hover:bg-gray-100 transition-colors flex items-center gap-1 group">
                Back to login
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <!-- Logo Area -->
            <div class="flex flex-col items-center justify-center">
                <div class="w-64 h-64 md:w-80 md:h-80 relative mb-8 flex items-center justify-center">
                    <div class="w-full h-full rounded-full border-4 border-purple-400/30 flex items-center justify-center relative overflow-hidden bg-purple-800/20 shadow-xl">
                         <img src="{{ asset('images/logo.png') }}" alt="RelaxBook Logo" class="w-full h-full object-contain" loading="eager" decoding="async">
                    </div>
                </div>
                <h1 class="text-4xl font-bold tracking-tight">RelaxBook</h1>
            </div>
        </div>

        <!-- Right Side (Form) -->
        <div class="w-full md:w-1/2 bg-white p-8 md:p-12 flex flex-col justify-center">
            <div class="max-w-md mx-auto w-full">
                <h2 class="text-3xl font-bold text-purple-700 mb-2">Verify Email</h2>
                <p class="text-gray-500 mb-8">Enter the 6-digits code sent to <br> <span class="font-semibold text-gray-700">{{ auth()->user()->email ?? ($email ?? 'your email address') }}</span></p>

                <!-- Status Message -->
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        A new verification code has been sent.
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.verify.post') }}" class="space-y-6">
                    @csrf

                    <!-- Verification Code -->
                    <div>
                        <label for="code" class="block text-sm font-bold text-gray-700 mb-1">Verification Code</label>
                        <input type="text" name="code" id="code" required autofocus maxlength="6"
                            class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 placeholder-gray-400 text-center tracking-widest text-2xl font-mono"
                            placeholder="123456">
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-full shadow-lg transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Verify Email
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="text-purple-600 hover:text-purple-800 text-sm font-semibold hover:underline transition-colors">
                            Resend Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
