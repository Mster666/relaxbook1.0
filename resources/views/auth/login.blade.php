<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="rb-logo" content="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Login - {{ config('app.name', 'RelaxBook') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <script defer src="{{ asset('js/rb-loader.js') }}"></script>
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom font import if needed */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-800 to-teal-700 min-h-screen flex items-center justify-center p-4">

    <!-- Card Container -->
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left Side (Logo & Branding) -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-purple-600 to-indigo-700 p-8 flex flex-col items-center justify-center text-white relative overflow-hidden">

            <div class="z-10 flex flex-col items-center text-center">
                <!-- LOGO PLACEHOLDER -->
                <div class="mb-6 w-40 h-40 bg-white/20 rounded-full flex items-center justify-center border-4 border-white/30 shadow-md overflow-hidden">
                    <!-- 
                        INSTRUCTIONS FOR USER:
                        1. Create a folder named 'images' inside your 'public' folder.
                        2. Upload your logo file there and name it 'logo.png'.
                        3. The code below calls that image.
                    -->
                    <img src="{{ asset('images/logo.png') }}" alt="RelaxBook Logo" class="w-full h-full object-contain" loading="eager" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <!-- Fallback if no logo -->
                    <span class="text-4xl font-bold hidden">R</span>
                </div>

                <h1 class="text-3xl font-bold mb-2 tracking-wide">RelaxBook</h1>
                
                <a href="{{ url('/') }}" data-rb-no-loader class="mt-8 px-8 py-2 bg-white text-indigo-700 font-bold rounded-full shadow-lg hover:bg-gray-100 transition transform hover:scale-105">
                    Get started >
                </a>
            </div>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="w-full md:w-1/2 bg-white p-8 md:p-12">
            <div class="max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-purple-700 mb-2">Welcome Back!</h2>
                <p class="text-gray-500 mb-8 text-sm">Enter your details to continue.</p>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6" data-rb-loader="full">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition outline-none"
                            placeholder="email@domain.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition outline-none pr-10"
                                placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-purple-600 focus:outline-none">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-500">
                            Remember me
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" id="login-btn" class="w-full py-3 bg-gradient-to-r from-blue-700 to-purple-600 hover:from-blue-800 hover:to-purple-700 text-white font-bold rounded-lg shadow-md transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Login
                    </button>

                    <!-- Create Account Link -->
                    <div class="text-center mt-6">
                        <p class="text-xs text-gray-500">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-bold text-gray-900 hover:text-purple-700">Create account</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
