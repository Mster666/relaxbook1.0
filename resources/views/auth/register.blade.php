<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="rb-logo" content="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Create Account - {{ config('app.name', 'RelaxBook') }}</title>
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

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden w-full max-w-6xl flex flex-col md:flex-row min-h-[700px]">
        
        <!-- Left Side (Purple Gradient) -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-indigo-700 to-purple-600 p-8 md:p-12 flex flex-col relative text-white">
            <!-- Back to Login Button -->
            <a href="{{ route('login') }}" class="absolute top-8 left-8 bg-white text-gray-900 px-6 py-2 rounded-full font-bold text-sm shadow-lg hover:bg-gray-100 transition-colors flex items-center gap-1 group">
                Back to login
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <!-- Logo Area -->
            <div class="flex-1 flex flex-col items-center justify-center mt-12 md:mt-0">
                <div class="w-64 h-64 md:w-80 md:h-80 relative mb-8 flex items-center justify-center">
                    <div class="w-full h-full rounded-full border-4 border-purple-400/30 flex items-center justify-center relative overflow-hidden bg-purple-800/20 shadow-xl">
                         <img src="{{ asset('images/logo.png') }}" alt="RelaxBook Logo" class="w-full h-full object-contain" loading="eager" decoding="async">
                    </div>
                </div>
                <h1 class="text-4xl font-bold tracking-tight">RelaxBook</h1>
            </div>
        </div>

        <!-- Right Side (Form) -->
        <div class="w-full md:w-1/2 bg-white p-8 md:p-12 overflow-y-auto">
            <div class="max-w-md mx-auto">
                <h2 class="text-3xl font-bold text-purple-700 mb-2">Create account</h2>
                <p class="text-gray-500 mb-8">Fill in your details to get started.</p>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" class="space-y-5" x-data="{ showPassword: false, showConfirmPassword: false }">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 placeholder-gray-400"
                            placeholder="Full Name: ">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 placeholder-gray-400"
                            placeholder="example@email.com">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-bold text-gray-700 mb-1">Phone number</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 placeholder-gray-400"
                            placeholder="0912 345 6789">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-bold text-gray-700 mb-1">Gender</label>
                        <div class="relative">
                            <select name="gender" id="gender" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 appearance-none bg-white">
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Age -->
                    <div>
                        <label for="age" class="block text-sm font-bold text-gray-700 mb-1">Age</label>
                        <input type="number" name="age" id="age" value="{{ old('age') }}" required min="1" max="120"
                            class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 placeholder-gray-400"
                            placeholder="Age: ">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 placeholder-gray-400"
                                placeholder="********">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800 focus:outline-none">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">Confirm Password</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-400 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-colors text-gray-800 placeholder-gray-400"
                                placeholder="********">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800 focus:outline-none">
                                <svg x-show="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg x-show="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-700 to-purple-600 hover:from-blue-800 hover:to-purple-700 text-white font-bold rounded-full shadow-lg transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 mt-6">
                        Create Account
                    </button>

                    <!-- Login Link -->
                    <div class="text-center mt-4">
                        <p class="text-sm text-gray-500">
                            Already have an account? <a href="{{ route('login') }}" class="text-gray-900 font-bold hover:underline">Go to login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
