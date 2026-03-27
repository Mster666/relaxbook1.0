<div>
    @php
        $avatarUrl = null;
        if ($user->profile_picture) {
            $path = trim((string) $user->profile_picture);
            $avatarUrl = str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')
                ? $path
                : \Illuminate\Support\Str::start('media/' . ltrim($path, '/'), '/');
        }
        $memberSince = $user->created_at ? $user->created_at->format('F Y') : 'N/A';
    @endphp

    <div class="overflow-hidden rounded-3xl ring-1 ring-slate-200/70 shadow-[0_30px_90px_-70px_rgba(2,6,23,0.55)] dark:ring-slate-800/70">
        <div class="relative bg-gradient-to-r from-violet-700 via-indigo-600 to-sky-600 px-6 py-6 sm:px-10">
            <div class="absolute inset-0 opacity-[0.14]" style="background-image:repeating-linear-gradient(0deg, rgba(255,255,255,.18) 0 1px, transparent 1px 6px),repeating-linear-gradient(90deg, rgba(255,255,255,.12) 0 1px, transparent 1px 8px)"></div>
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -left-24 -bottom-28 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-5">
                    <div class="relative h-20 w-20 overflow-hidden rounded-2xl bg-white/15 ring-1 ring-white/20 backdrop-blur-md">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4f46e5&color=fff" alt="{{ $user->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @endif
                        <div class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-white text-emerald-600 ring-2 ring-white/70">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                    <div class="min-w-0">
                        <div class="truncate text-2xl font-semibold text-white">{{ $user->name }}</div>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-sm font-medium text-white/80">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M8 7V3m8 4V3m-9 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Member since {{ $memberSince }}
                            </span>
                            @if ($user->hasVerifiedEmail())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-white ring-1 ring-white/15">
                                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                                    Verified
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-white/30 hover:bg-white/90">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Edit Profile
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 sm:p-8 dark:bg-slate-950/30 transition-colors">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 rounded-3xl bg-slate-50 p-6 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:ring-slate-800/70">
                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        About
                    </div>
                    <div class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-300">
                        No bio provided.
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-900/25 dark:text-indigo-200 dark:ring-indigo-800/50">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Email</div>
                                <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-800/50">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 4h6l2 5-3 2c1.2 2.6 3.4 4.8 6 6l2-3 5 2v6c-10 0-18-8-18-18Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Phone</div>
                                <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->phone_number ?? 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-slate-50 p-6 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:ring-slate-800/70">
                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                            <path d="M12 1.5 21 5v6c0 5-3.5 9.5-9 11-5.5-1.5-9-6-9-11V5l9-3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                        Personal Details
                    </div>

                    <div class="mt-4 divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <div class="flex items-center justify-between py-3">
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Age</div>
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $user->age ?? '—' }}</div>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Gender</div>
                            <div class="font-semibold text-slate-900 dark:text-white capitalize">{{ $user->gender ?? '—' }}</div>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status</div>
                            @if ($user->hasVerifiedEmail())
                                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200/70 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-800/50">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200/70 dark:bg-amber-900/25 dark:text-amber-200 dark:ring-amber-800/50">
                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                    Unverified
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-3xl bg-slate-50 p-6 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:ring-slate-800/70">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                            <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Recent Activity in 1 week
                    </div>
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                        Since {{ $recentSince->format('M j, Y • H:i') }}
                    </div>
                </div>

                @if(($recentBookings ?? collect())->isEmpty())
                    <div class="mt-6 rounded-2xl bg-white px-5 py-6 text-center ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                        <div class="text-sm font-semibold text-slate-900 dark:text-white">No activity in the last 7 days</div>
                        <div class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Your recent bookings will appear here.</div>
                    </div>
                @else
                    <div class="mt-5 space-y-3">
                        @foreach($recentBookings as $booking)
                            @php
                                $services = $booking->services->isNotEmpty() ? $booking->services : ($booking->service ? collect([$booking->service]) : collect());
                                $serviceNames = $services->pluck('name')->filter()->values()->all();
                                $date = $booking->booking_date?->format('M j, Y') ?? '—';
                                $time = (string) ($booking->booking_time ?? '—');
                                $status = (string) ($booking->status ?? 'pending');

                                $pairs = [];
                                if ($booking->relationLoaded('bookingServiceTherapists') && $booking->bookingServiceTherapists->isNotEmpty()) {
                                    foreach ($booking->bookingServiceTherapists as $row) {
                                        $pairs[] = [
                                            'service' => $row->service?->name ?? 'Service',
                                            'therapist' => $row->therapist?->name ?? 'Unassigned',
                                        ];
                                    }
                                } else {
                                    foreach ($services as $svc) {
                                        $pairs[] = [
                                            'service' => $svc->name ?? 'Service',
                                            'therapist' => $booking->therapist?->name ?? 'Unassigned',
                                        ];
                                    }
                                }

                                $statusBadge = match ($status) {
                                    'confirmed' => ['Confirmed', 'bg-emerald-50 text-emerald-700 ring-emerald-200/70 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-800/50'],
                                    'completed' => ['Completed', 'bg-indigo-50 text-indigo-700 ring-indigo-200/70 dark:bg-indigo-900/25 dark:text-indigo-200 dark:ring-indigo-800/50'],
                                    'cancelled' => ['Cancelled', 'bg-rose-50 text-rose-700 ring-rose-200/70 dark:bg-rose-900/25 dark:text-rose-200 dark:ring-rose-800/50'],
                                    default => ['Pending', 'bg-amber-50 text-amber-700 ring-amber-200/70 dark:bg-amber-900/25 dark:text-amber-200 dark:ring-amber-800/50'],
                                };
                            @endphp

                            <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-slate-200 shadow-sm dark:bg-slate-950/30 dark:ring-slate-800">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">
                                                {{ implode(', ', $serviceNames) }}
                                            </div>
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold ring-1 {{ $statusBadge[1] }}">
                                                {{ $statusBadge[0] }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            {{ $date }} • {{ $time }}
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        @foreach($pairs as $pair)
                                            <span class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:text-slate-200 dark:ring-slate-800/70">
                                                <span class="font-semibold">{{ $pair['service'] }}</span>
                                                <span class="mx-1 text-slate-400 dark:text-slate-500">→</span>
                                                <span>{{ $pair['therapist'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
