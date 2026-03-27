<div>
    @php
        $mediaUrl = function (?string $path): ?string {
            $path = is_string($path) ? trim($path) : null;
            if (! $path) {
                return null;
            }
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
                return $path;
            }
            if (str_starts_with($path, '/media/')) {
                return $path;
            }
            if (str_starts_with($path, '/storage/')) {
                $path = substr($path, strlen('/storage/'));
            }

            return \Illuminate\Support\Str::start('media/' . ltrim($path, '/'), '/');
        };
    @endphp

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">My Appointments</h2>
        <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Manage your upcoming and past appointments.</p>
    </div>

    @if (session()->has('error'))
        <div class="mb-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 ring-1 ring-rose-200/70 dark:bg-rose-900/20 dark:text-rose-200 dark:ring-rose-800/40">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('message'))
        <div class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 ring-1 ring-emerald-200/70 dark:bg-emerald-900/20 dark:text-emerald-200 dark:ring-emerald-800/40">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
            <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $counts['total'] ?? 0 }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Total Appointments</div>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
            <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $counts['pending'] ?? 0 }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Pending Appointments</div>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
            <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $counts['confirmed'] ?? 0 }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Confirmed Appointments</div>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
            <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $counts['completed'] ?? 0 }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Completed Appointments</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white p-4 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-lg">
                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.250ms="search" type="text" placeholder="Search Appointments..."
                       class="w-full rounded-2xl bg-slate-50 px-10 py-2.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-950/30 dark:text-white dark:ring-white/10 dark:focus:ring-indigo-800/50">
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" wire:click="setViewMode('list')"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl ring-1 transition
                        {{ $viewMode === 'list' ? 'bg-indigo-600 text-white ring-indigo-500' : 'bg-slate-50 text-slate-600 ring-slate-200 hover:bg-slate-100 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-800' }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M4 6h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M4 12h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
                <button type="button" wire:click="setViewMode('compact')"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl ring-1 transition
                        {{ $viewMode === 'compact' ? 'bg-indigo-600 text-white ring-indigo-500' : 'bg-slate-50 text-slate-600 ring-slate-200 hover:bg-slate-100 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-800' }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M4 6h7v7H4V6Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M13 6h7v7h-7V6Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 15h7v3H4v-3Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M13 15h7v3h-7v-3Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'all' => 'All',
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <button type="button" wire:click="setStatusFilter('{{ $key }}')"
                        class="rounded-xl px-3 py-2 text-xs font-semibold ring-1 transition
                        {{ $statusFilter === $key ? 'bg-indigo-600 text-white ring-indigo-500' : 'bg-slate-50 text-slate-700 ring-slate-200 hover:bg-slate-100 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-800' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="mt-5 {{ $viewMode === 'compact' ? 'grid grid-cols-1 gap-4 lg:grid-cols-2' : 'space-y-4' }}">
        @forelse($bookings as $booking)
            @php
                $services = $booking->services->isNotEmpty()
                    ? $booking->services
                    : ($booking->service ? collect([$booking->service]) : collect());

                $serviceNames = $services->pluck('name')->filter()->values()->all();
                $primaryService = $services->first();
                $thumb = $primaryService?->icon ? $mediaUrl($primaryService->icon) : null;
                $price = (float) $services->sum(fn ($svc) => (float) ($svc->price ?? 0));
                $duration = (int) $services->sum(fn ($svc) => (int) ($svc->duration_minutes ?? 0));

                $therapists = [];
                if ($booking->relationLoaded('bookingServiceTherapists') && $booking->bookingServiceTherapists->isNotEmpty()) {
                    $therapists = $booking->bookingServiceTherapists
                        ->map(fn ($row) => $row->therapist?->name)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();
                }
                if (empty($therapists) && $booking->therapist) {
                    $therapists = [$booking->therapist->name];
                }

                $roomLabel = $booking->room
                    ? trim(($booking->room->name ?? '') . ($booking->room->code ? ' - ' . $booking->room->code : ''))
                    : null;

                $status = (string) ($booking->status ?? 'pending');
                $statusBadge = match ($status) {
                    'confirmed' => ['Confirmed', 'bg-emerald-50 text-emerald-700 ring-emerald-200/70 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-800/50'],
                    'completed' => ['Completed', 'bg-indigo-50 text-indigo-700 ring-indigo-200/70 dark:bg-indigo-900/25 dark:text-indigo-200 dark:ring-indigo-800/50'],
                    'cancelled' => ['Cancelled', 'bg-rose-50 text-rose-700 ring-rose-200/70 dark:bg-rose-900/25 dark:text-rose-200 dark:ring-rose-800/50'],
                    default => ['Pending', 'bg-amber-50 text-amber-700 ring-amber-200/70 dark:bg-amber-900/25 dark:text-amber-200 dark:ring-amber-800/50'],
                };
                $bookingDate = $booking->booking_date?->format('M d, Y') ?? '—';
                $bookingTime = $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') : '—';
                $canCancel = in_array($status, ['pending', 'confirmed'], true);
            @endphp

            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="h-14 w-20 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                            @if($thumb)
                                <img src="{{ $thumb }}" alt="{{ $primaryService?->name ?? 'Service' }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                            @else
                                <div class="h-full w-full bg-gradient-to-br from-indigo-600/25 via-violet-600/15 to-sky-500/20"></div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold ring-1 {{ $statusBadge[1] }}">{{ $statusBadge[0] }}</span>
                                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">#{{ $booking->id }}</span>
                            </div>
                            <div class="mt-1 truncate text-sm font-semibold text-slate-900 dark:text-white">
                                {{ implode(', ', $serviceNames) }}
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <path d="M8 7V3m8 4V3m-9 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    {{ $bookingDate }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    {{ $bookingTime }}
                                </span>
                                @if($duration > 0)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        {{ $duration }} min
                                    </span>
                                @endif
                                @if(!empty($therapists) && $viewMode !== 'compact')
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="2"/>
                                            <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        {{ implode(', ', $therapists) }}
                                    </span>
                                @endif
                                @if($roomLabel && $viewMode !== 'compact')
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                            <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        {{ $roomLabel }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-2 sm:items-end">
                        <div class="text-sm font-semibold text-slate-900 dark:text-white">₱{{ number_format($price, 2) }}</div>
                        <div class="flex items-center gap-2">
                            @if($status === 'completed')
                                <button type="button" wire:click="bookAgain({{ $booking->id }})"
                                        class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-[0_16px_34px_-26px_rgba(37,99,235,0.9)] hover:bg-indigo-700">
                                    Book Again
                                </button>
                            @elseif($canCancel)
                                <button type="button"
                                        x-data
                                        x-on:click.prevent="if(confirm('Cancel this booking?')) { $wire.cancelBooking({{ $booking->id }}) }"
                                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                                    Cancel
                                </button>
                            @endif

                            <button type="button" wire:click="viewBooking({{ $booking->id }})"
                                    class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-200 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-800">
                                View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-white px-5 py-10 text-center ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                    <svg class="h-6 w-6 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">No appointments</div>
                <div class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">You haven't made any bookings yet.</div>
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_18px_40px_-26px_rgba(37,99,235,0.95)] hover:bg-indigo-700">
                        Book Appointment
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>

    @if($viewBooking)
        @php
            $services = $viewBooking->services->isNotEmpty()
                ? $viewBooking->services
                : ($viewBooking->service ? collect([$viewBooking->service]) : collect());
            $date = $viewBooking->booking_date?->format('F j, Y') ?? '—';
            $time = $viewBooking->booking_time ? \Carbon\Carbon::parse($viewBooking->booking_time)->format('h:i A') : '—';
            $price = (float) $services->sum(fn ($svc) => (float) ($svc->price ?? 0));
            $pairs = [];
            if ($viewBooking->relationLoaded('bookingServiceTherapists') && $viewBooking->bookingServiceTherapists->isNotEmpty()) {
                foreach ($viewBooking->bookingServiceTherapists as $row) {
                    $pairs[] = [
                        'service' => $row->service?->name ?? 'Service',
                        'therapist' => $row->therapist?->name ?? 'Unassigned',
                    ];
                }
            } else {
                foreach ($services as $svc) {
                    $pairs[] = [
                        'service' => $svc->name ?? 'Service',
                        'therapist' => $viewBooking->therapist?->name ?? 'Unassigned',
                    ];
                }
            }
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white ring-1 ring-slate-200 shadow-xl dark:bg-slate-900/70 dark:ring-white/10">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                    <div class="text-sm font-semibold text-slate-900 dark:text-white">Appointment Details</div>
                    <button type="button" wire:click="closeView" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-200 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-800">
                        Close
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Date</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $date }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Time</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $time }}</div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Services & Therapists</div>
                        <div class="mt-3 space-y-2">
                            @foreach($pairs as $pair)
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-white px-4 py-3 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:ring-white/10">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $pair['service'] }}</div>
                                        <div class="mt-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $pair['therapist'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl bg-gradient-to-r from-violet-600 via-indigo-600 to-sky-600 px-5 py-4 text-white ring-1 ring-white/10">
                        <div class="text-sm font-semibold">Total</div>
                        <div class="text-lg font-bold">₱{{ number_format($price, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
