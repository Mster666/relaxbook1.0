<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="rb-logo" content="{{ asset('images/logo.png') }}">
    <title>RelaxBook - Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    @if (file_exists(public_path('js/alpine.min.js')))
        <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    @else
        <script defer src="https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js"></script>
    @endif
    <script defer src="{{ asset('js/rb-loader.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-slate-900 dark:text-slate-100 antialiased">
    <div x-data="{ mobileMenuOpen: false }" class="min-h-screen flex bg-gray-100 dark:bg-gray-900">
        <div x-show="mobileMenuOpen" class="relative z-50 md:hidden" role="dialog" aria-modal="true" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>

            <div class="fixed inset-0 flex">
                <div class="relative mr-16 flex w-full max-w-xs flex-1">
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" @click="mobileMenuOpen = false" class="-m-2.5 p-2.5 rounded-full hover:bg-white/10 transition-colors">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-900 px-6 pb-4 shadow-2xl">
                        <div class="flex h-20 shrink-0 items-center border-b border-gray-100 dark:border-gray-800">
                            <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight flex items-center gap-3">
                                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-9 h-9 rounded-xl object-contain bg-white shadow-lg shadow-indigo-500/20">
                                {{ config('app.name') }}
                            </a>
                        </div>

                        <nav class="flex flex-1 flex-col mt-2">
                            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                <li>
                                    <ul role="list" class="-mx-2 space-y-2">
                                        <li>
                                            <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                                                Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('profile') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                                Profile
                                            </a>
                                        </li>
                                        <li>
                                            <div class="ml-5 space-y-1 border-l-2 border-gray-100 dark:border-gray-800 pl-4 my-2">
                                                <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="block px-2 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                                                    Edit Profile
                                                </a>
                                                <a href="{{ route('profile.password') }}" @click="mobileMenuOpen = false" class="block px-2 py-2 text-sm font-medium rounded-lg text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 transition-colors">
                                                    Change Password
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="{{ route('appointments') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                                                Appointments
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <aside class="w-64 bg-white dark:bg-gray-800 shadow-md flex flex-col z-20 hidden md:flex border-r border-gray-100 dark:border-gray-700">
            <div class="h-16 flex items-center justify-center border-b border-gray-100 dark:border-gray-700">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tighter flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-8 h-8 rounded-xl object-contain bg-white shadow-sm">
                    {{ config('app.name') }}
                </a>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <div class="mb-6">
                    <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu</p>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Profile
                    </a>
                    <div class="ml-4 space-y-1 border-l-2 border-gray-100 dark:border-gray-700 pl-3 my-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Edit Profile
                        </a>
                        <a href="{{ route('profile.password') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 transition-colors">
                            Change Password
                        </a>
                    </div>
                    <a href="{{ route('appointments') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Appointments
                    </a>
                </div>
            </nav>
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    @if (Auth::user()->profile_picture)
                        <img class="w-8 h-8 rounded-full object-cover" src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="{{ Auth::user()->name }}">
                    @else
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-100 dark:bg-gray-900">
            <header class="bg-white dark:bg-gray-800 shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <button @click="mobileMenuOpen = true" type="button" class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-200 md:hidden hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center gap-4" x-data="{ 
                    open: false,
                    theme: localStorage.theme || 'system',
                    setTheme(val) {
                        this.theme = val;
                        if (val === 'system') {
                            localStorage.removeItem('theme');
                            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        } else {
                            localStorage.theme = val;
                            if (val === 'dark') {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        }
                    }
                }">
                    <div class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none group">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">User</p>
                            </div>
                            <div class="relative">
                                @if (Auth::user()->profile_picture)
                                    <img class="rounded-full ring-2 ring-gray-100 dark:ring-gray-700 group-hover:ring-indigo-100 dark:group-hover:ring-indigo-900 transition-all" src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="{{ Auth::user()->name }}" width="40" height="40" style="width: 40px; height: 40px; object-fit: cover;" loading="lazy">
                                @else
                                    <img class="rounded-full ring-2 ring-gray-100 dark:ring-gray-700 group-hover:ring-indigo-100 dark:group-hover:ring-indigo-900 transition-all" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" alt="{{ Auth::user()->name }}" width="40" height="40" style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            </div>
                            <svg :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                             class="absolute right-0 mt-3 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 focus:outline-none z-50 overflow-hidden"
                             style="display: none;">
                            
                            <!-- User Info Header -->
                            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="p-2 space-y-1">
                                <!-- Profile Link -->
                                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    Profile
                                </a>

                                <!-- Theme Switcher -->
                                <div class="px-3 py-2">
                                    <div class="grid grid-cols-3 gap-1 p-1 bg-gray-100 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <button @click="setTheme('light')" 
                                                :class="theme === 'light' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                                                class="flex items-center justify-center py-1.5 rounded-md transition-all duration-200 text-sm font-medium"
                                                title="Light Mode">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </button>
                                        <button @click="setTheme('dark')" 
                                                :class="theme === 'dark' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                                                class="flex items-center justify-center py-1.5 rounded-md transition-all duration-200 text-sm font-medium"
                                                title="Dark Mode">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                            </svg>
                                        </button>
                                        <button @click="setTheme('system')" 
                                                :class="theme === 'system' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                                                class="flex items-center justify-center py-1.5 rounded-md transition-all duration-200 text-sm font-medium"
                                                title="System Mode">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 dark:border-gray-700/50 my-1"></div>

                                <!-- Logout Link -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full text-left px-3 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors group">
                                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400 group-hover:bg-red-200 dark:group-hover:bg-red-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                        </div>
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <div class="max-w-5xl mx-auto">
                    <livewire:user-profile-password />
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
