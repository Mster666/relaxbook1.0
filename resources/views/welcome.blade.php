<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="rb-logo" content="{{ asset('images/logo.png') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <title>{{ config('app.name', 'RelaxBook') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    darkMode: 'class',
                }
            </script>
        @endif

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <script defer src="{{ asset('js/rb-loader.js') }}"></script>

        <style>
            html { scroll-behavior: smooth; }
            @media (prefers-reduced-motion: reduce) {
                html { scroll-behavior: auto; }
            }
            body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body>
        <div class="min-h-screen bg-[#f6effa] text-slate-900">
            <header class="sticky top-0 z-50 bg-white/70 backdrop-blur-xl border-b border-slate-200/60">
                <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'RelaxBook') }}" class="h-11 w-11 rounded-xl object-contain">
                        <div class="leading-tight">
                            <div class="text-lg font-extrabold tracking-tight text-fuchsia-700">Relaxbook</div>
                            <div class="text-xs font-semibold text-slate-500 -mt-0.5">Book your relaxation</div>
                        </div>
                    </a>

                    <div class="flex items-center gap-2">
                        <a href="#about" class="hidden sm:inline-flex rounded-md border border-fuchsia-500/50 bg-white px-3 py-1.5 text-xs font-extrabold text-fuchsia-700 shadow-sm hover:bg-fuchsia-50 transition">About us</a>
                        <a href="{{ route('register') }}" class="rounded-md border border-fuchsia-500/50 bg-white px-3 py-1.5 text-xs font-extrabold text-fuchsia-700 shadow-sm hover:bg-fuchsia-50 transition">Sign up</a>
                        <button type="button" data-get-started class="rounded-md bg-gradient-to-r from-fuchsia-600 to-violet-600 px-3.5 py-1.5 text-xs font-extrabold text-white shadow-lg shadow-fuchsia-500/20 hover:from-fuchsia-700 hover:to-violet-700 transition">Get Started</button>
                    </div>
                </div>
            </header>

            <main>
                <section class="relative overflow-hidden bg-[#f6effa]">
                    <div class="absolute inset-0">
                        <div class="absolute left-[-160px] top-[-120px] h-[420px] w-[620px] rounded-full bg-fuchsia-400/15 blur-[90px]"></div>
                        <div class="absolute right-[-160px] top-[80px] h-[420px] w-[560px] rounded-full bg-violet-400/15 blur-[90px]"></div>
                    </div>

                    <div class="relative mx-auto max-w-6xl px-5 pt-10 pb-24 sm:pt-12">
                        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
                            <div>
                                <div class="inline-flex items-center gap-4 rounded-2xl border-2 border-fuchsia-400/30 bg-white/40 px-5 py-4 shadow-sm">
                                    <div class="h-12 w-12 rounded-full bg-white ring-2 ring-fuchsia-400/40 grid place-items-center overflow-hidden">
                                        <img src="{{ asset('images/logo.png') }}" alt="Relaxbook" class="h-10 w-10 object-contain">
                                    </div>
                                    <div>
                                        <div class="text-5xl font-extrabold tracking-tight text-fuchsia-700 sm:text-6xl">Relaxbook</div>
                                        <div class="mt-2 text-lg font-semibold text-slate-700 italic">“Book your relaxation anytime, anywhere”</div>
                                    </div>
                                </div>

                                <div class="mt-7 flex items-center gap-4">
                                    <button type="button" data-get-started class="rounded-md bg-gradient-to-r from-fuchsia-600 to-violet-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-fuchsia-500/20 hover:from-fuchsia-700 hover:to-violet-700 transition">
                                        Get Started
                                    </button>
                                    <a href="{{ route('register') }}" class="rounded-md border-2 border-fuchsia-500/60 bg-white/70 px-6 py-3 text-sm font-extrabold text-fuchsia-700 shadow-sm hover:bg-white transition">
                                        Sign up
                                    </a>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="rounded-2xl bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/5">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-fuchsia-100 text-fuchsia-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-extrabold text-slate-900">Easy Scheduling</div>
                                            <div class="mt-1 text-sm font-medium text-slate-500">Book your appointments with just a few taps</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/5">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-extrabold text-slate-900">Real-Time Availability</div>
                                            <div class="mt-1 text-sm font-medium text-slate-500">See available slots and book instantly</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/5">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 18h4"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-extrabold text-slate-900">Mobile Friendly</div>
                                            <div class="mt-1 text-sm font-medium text-slate-500">Manage bookings on the go from any device</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <svg class="absolute bottom-0 left-0 right-0 w-full" viewBox="0 0 1440 160" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <path fill="#ffffff" d="M0,96L60,96C120,96,240,96,360,106.7C480,117,600,139,720,144C840,149,960,139,1080,122.7C1200,107,1320,85,1380,74.7L1440,64L1440,160L1380,160C1320,160,1200,160,1080,160C960,160,840,160,720,160C600,160,480,160,360,160C240,160,120,160,60,160L0,160Z"></path>
                    </svg>
                </section>

                <section id="about" class="bg-white">
                    <div class="mx-auto max-w-6xl px-5 py-14">
                        <div class="text-center">
                            <div class="text-xs font-extrabold text-fuchsia-700">About RelaxBook</div>
                            <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                                RelaxBook <span class="text-fuchsia-700">Overview</span>
                            </h2>
                            <p class="mx-auto mt-5 max-w-3xl text-sm font-medium leading-relaxed text-slate-600">
                                RelaxBook is an online booking system for massage and wellness services that allows clients to schedule appointments anytime using their devices, without the need for walk-ins.
                                It streamlines booking methods and offers real-time availability, reducing the need for appointments and scheduling conflicts.
                                RelaxBook also helps businesses by providing a simple platform for booking, managing schedules, and organizing client information.
                            </p>
                        </div>

                        <div class="mt-10">
                            <div class="text-center">
                                <div class="text-xl font-extrabold text-fuchsia-700">How the system works?</div>
                                <div class="mt-1 text-sm font-semibold text-slate-500">The system has 6 easy steps</div>
                            </div>

                            <div class="mt-8">
                                @php
                                    $steps = [
                                        ['title' => 'Company', 'desc' => 'Choose your preferred branch or location', 'iconBg' => 'bg-rose-500', 'icon' => 'calendar-days'],
                                        ['title' => 'Date & Time', 'desc' => 'Select your desired schedule easily', 'iconBg' => 'bg-indigo-600', 'icon' => 'calendar'],
                                        ['title' => 'Room', 'desc' => 'Pick a comfortable room for your session', 'iconBg' => 'bg-blue-600', 'icon' => 'room'],
                                        ['title' => 'Services', 'desc' => 'Choose the massage or service you want', 'iconBg' => 'bg-fuchsia-600', 'icon' => 'services'],
                                        ['title' => 'Therapist', 'desc' => 'Select your preferred therapist', 'iconBg' => 'bg-amber-500', 'icon' => 'therapist'],
                                        ['title' => 'Confirm', 'desc' => 'Review details and complete your booking', 'iconBg' => 'bg-emerald-600', 'icon' => 'confirm'],
                                    ];
                                @endphp

                                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-6 lg:gap-6">
                                    @foreach ($steps as $i => $step)
                                        <div class="relative rounded-2xl bg-white px-4 pb-4 pt-5 shadow-[0_10px_18px_rgba(15,23,42,0.10)] ring-1 ring-slate-900/20">
                                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 text-2xl font-extrabold text-fuchsia-700">
                                                {{ $i + 1 }}
                                            </div>
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $step['iconBg'] }} text-white shadow-sm">
                                                    @if ($step['icon'] === 'calendar-days')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                                                        </svg>
                                                    @elseif ($step['icon'] === 'calendar')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M5.25 6.75h13.5A2.25 2.25 0 0 1 21 9v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 19.5V9A2.25 2.25 0 0 1 5.25 6.75Z"/>
                                                        </svg>
                                                    @elseif ($step['icon'] === 'room')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 11V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 11h12v7H6z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 18v2M20 18v2"/>
                                                        </svg>
                                                    @elseif ($step['icon'] === 'services')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                                                        </svg>
                                                    @elseif ($step['icon'] === 'therapist')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-extrabold text-slate-900">{{ $step['title'] }}</div>
                                                    <div class="mt-1 text-xs font-semibold text-slate-600 leading-relaxed">{{ $step['desc'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-[#f6effa]">
                    <div class="mx-auto max-w-6xl px-5 pt-14 pb-0 text-center">
                        <div class="text-xs font-extrabold text-slate-700">this system provided by:</div>
                        <h2 class="mt-2 text-4xl font-extrabold tracking-tight">
                            <span class="text-violet-700">Protech</span> <span class="text-fuchsia-600">I.S Solution</span>
                        </h2>
                        <p class="mx-auto mt-4 max-w-3xl text-sm font-medium leading-relaxed text-slate-700">
                            ProTech I.S. Solutions is a service-oriented information systems provider specializing in digital solutions, system development, IT support, and process optimization.
                            The company is committed to helping businesses enhance operational efficiency and strengthen their technological capabilities through reliable and innovative information system services.
                        </p>

                        @php
                            $teamImagePath = public_path('images/protech-team.png');
                            $teamImageUrl = file_exists($teamImagePath)
                                ? asset('images/protech-team.png')
                                : asset('images/backgrounds/step6.jpg');
                        @endphp

                        <div class="-mb-[6px] mt-10 overflow-hidden rounded-3xl bg-transparent">
                            <img src="{{ $teamImageUrl }}" alt="Protech I.S Solution Team" class="mx-auto block max-h-[560px] w-auto object-contain">
                        </div>
                    </div>
                </section>

                <section id="contact" class="relative z-10 border-t-[6px] border-[#2b0b3a] bg-gradient-to-b from-fuchsia-600 via-violet-700 to-fuchsia-800">
                    <div class="absolute inset-x-0 bottom-0 top-[6px] opacity-30" style="background-image: radial-gradient(rgba(255,255,255,0.35) 1px, transparent 1px); background-size: 26px 26px;"></div>
                    <div class="relative mx-auto max-w-6xl px-5 py-16 text-white">
                        <div class="text-center">
                            <h2 class="text-5xl font-extrabold tracking-tight">
                                Get In <span class="text-yellow-300">Touch</span>
                            </h2>
                            <p class="mx-auto mt-3 max-w-2xl text-base font-medium text-white/80">
                                Ready to streamline your booking system? Let’s discuss how RelaxBook can help your business grow.
                            </p>
                        </div>

                        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 sm:px-10">
                            <div class="rounded-2xl border-2 border-white/60 bg-white/5 p-10 backdrop-blur-sm">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                    </svg>
                                </div>
                                <div class="mt-6 text-center">
                                    <div class="text-xl font-extrabold">Phone</div>
                                    <div class="mt-2 text-sm font-semibold text-white/85">0963-959-2179</div>
                                </div>
                            </div>

                            <div class="rounded-2xl border-2 border-white/60 bg-white/5 p-10 backdrop-blur-sm">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <div class="mt-6 text-center">
                                    <div class="text-xl font-extrabold">Email</div>
                                    <div class="mt-2 text-sm font-semibold text-white/85">protechissolution@gmail.com</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-center">
                            <button type="button" data-get-started class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-8 py-3 text-sm font-extrabold text-fuchsia-700 shadow-xl shadow-black/20 hover:bg-white/90 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 9h8M8 13h6"/>
                                </svg>
                                Start Your Journey with RelaxBook
                            </button>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="bg-[#2b0b3a] text-white/80">
                <div class="mx-auto max-w-6xl px-5 py-10 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'RelaxBook') }}" class="h-9 w-9 rounded-xl object-contain">
                        <div class="text-lg font-extrabold text-fuchsia-400">Relaxbook</div>
                    </div>
                    <div class="mt-2 text-sm font-medium text-white/70">Book your relaxation anytime, anywhere</div>
                    <div class="mt-6 text-xs font-semibold text-white/60">© {{ now()->year }} ProTech I.S. Solution. All rights reserved.</div>
                </div>
            </footer>
        </div>

        <div id="get-started-modal" class="fixed inset-0 z-[999] hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="absolute inset-0 flex items-center justify-center p-5">
                <div class="w-full max-w-xl rounded-3xl bg-white p-6 shadow-[0_30px_90px_rgba(15,23,42,0.35)] ring-1 ring-slate-900/10">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-lg font-extrabold text-slate-900">Choose where you want to sign in</div>
                            <div class="mt-1 text-sm font-medium text-slate-500">Select Customer or Company.</div>
                        </div>
                        <button type="button" data-close-get-started class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <a href="{{ route('user.login.entry') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                            <div class="text-sm font-extrabold text-slate-900">Customer Login</div>
                            <div class="mt-1 text-sm font-medium text-slate-500">Sign in to the main booking site.</div>
                            <div class="mt-3 inline-flex items-center gap-2 text-sm font-extrabold text-emerald-600">
                                Continue <span aria-hidden="true">→</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.login.entry') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                            <div class="text-sm font-extrabold text-slate-900">Company Login</div>
                            <div class="mt-1 text-sm font-medium text-slate-500">Sign in to the company/admin dashboard.</div>
                            <div class="mt-3 inline-flex items-center gap-2 text-sm font-extrabold text-rose-600">
                                Continue <span aria-hidden="true">→</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('get-started-modal');
                if (!modal) return;

                const openButtons = document.querySelectorAll('[data-get-started]');
                const closeButtons = document.querySelectorAll('[data-close-get-started]');

                function openModal(event) {
                    if (event) event.preventDefault();
                    modal.classList.remove('hidden');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }

                openButtons.forEach((btn) => btn.addEventListener('click', openModal));
                closeButtons.forEach((btn) => btn.addEventListener('click', closeModal));

                modal.addEventListener('click', (e) => {
                    if (e.target === modal.firstElementChild) {
                        closeModal();
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                        closeModal();
                    }
                });
            })();
        </script>
    </body>
</html>
