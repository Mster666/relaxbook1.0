<div>
    <div class="relative overflow-hidden rounded-3xl bg-white/80 shadow-[0_30px_90px_-60px_rgba(15,23,42,0.55)] ring-1 ring-slate-200/70 backdrop-blur-sm dark:bg-slate-900/70 dark:ring-slate-800/70">
        @php
            $storageUrl = function (?string $path): ?string {
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
            $stepLabels = [
                1 => 'Company',
                2 => 'Date & Time',
                3 => 'Room',
                4 => 'Services',
                5 => 'Therapist',
                6 => 'Confirm',
            ];
            $progress = max(0, min(100, (($step - 1) / 5) * 100));
            $selectedCompanyId = session('selected_company_id');
        @endphp

        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-violet-600 to-sky-500 px-6 py-10 text-white sm:px-10">
            <div class="pointer-events-none absolute inset-0 opacity-70">
                <div class="absolute -left-20 -top-24 h-56 w-56 rounded-full bg-white/15 blur-2xl"></div>
                <div class="absolute -bottom-28 -right-24 h-72 w-72 rounded-full bg-white/12 blur-2xl"></div>
                <div class="absolute right-16 top-8 h-28 w-44 -rotate-6 rounded-[36px] bg-white/10 blur-xl"></div>
            </div>

            <div class="relative mx-auto flex max-w-4xl flex-col items-center text-center">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <img src="{{ asset('images/logo.png') }}" alt="RelaxBook" class="h-9 w-9 object-contain">
                    </div>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">RelaxBook</h1>
                </div>
                <p class="mt-2 max-w-xl text-sm font-medium text-white/85 sm:text-base">
                    Book your appointment in a few simple steps
                </p>
            </div>
        </div>

        <div class="border-b border-slate-200/70 bg-white/60 px-6 py-6 backdrop-blur-sm dark:border-slate-800/70 dark:bg-slate-900/40 sm:px-10">
            <div class="mx-auto max-w-4xl">
                @if (session()->has('message'))
                    <div class="mb-5 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 ring-1 ring-emerald-200/60 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-700/40">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="relative overflow-x-auto pb-1">
                    <div class="relative min-w-[720px]">
                        <div class="absolute left-0 right-0 top-5 h-px rounded bg-slate-200 dark:bg-slate-700"></div>
                        <div class="absolute left-0 top-5 h-px rounded bg-gradient-to-r from-indigo-500 via-violet-500 to-sky-400" style="width: {{ $progress }}%"></div>

                        <ol class="relative z-10 flex items-start justify-between gap-2">
                            @foreach ($stepLabels as $index => $label)
                                @php
                                    $isCompleted = $step > $index;
                                    $isActive = $step === $index;
                                    $circleClasses = $isCompleted
                                        ? 'bg-indigo-600 text-white ring-4 ring-indigo-200/60 dark:ring-indigo-900/40'
                                        : ($isActive
                                            ? 'bg-white text-indigo-700 ring-4 ring-indigo-200/60 dark:bg-slate-900 dark:text-indigo-300 dark:ring-indigo-900/40'
                                            : 'bg-white text-slate-500 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800');
                                    $labelClasses = $isCompleted || $isActive
                                        ? 'text-slate-900 dark:text-slate-100'
                                        : 'text-slate-500 dark:text-slate-400';
                                @endphp
                                <li class="min-w-[110px] select-none">
                                    <button type="button" wire:click="goToStep({{ $index }})" class="group flex w-full flex-col items-center gap-2 focus:outline-none">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold shadow-sm transition {{ $circleClasses }}">
                                            @if ($isCompleted)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 11.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                {{ $index }}
                                            @endif
                                        </div>
                                        <div class="text-xs font-semibold {{ $labelClasses }}">{{ $label }}</div>
                                    </button>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>

                @if ($this->selectedCompany)
                    <div class="mt-5 flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-200/60 dark:bg-slate-950/25 dark:ring-slate-800/60">
                        <div class="min-w-0">
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Selected company</div>
                            <div class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">
                                {{ $this->selectedCompany->company_name ?? $this->selectedCompany->name }}
                            </div>
                        </div>
                        <button type="button" wire:click="goToStep(1)" class="inline-flex items-center rounded-xl bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-200 dark:ring-slate-800 dark:hover:bg-slate-800">
                            Change
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-slate-50/50 p-6 dark:bg-slate-950/20 sm:p-8">
            @if ($step === 1)
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Select a Company</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Choose a company to continue your booking.</p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @foreach($this->companies as $company)
                        @php
                            $isSelected = $selectedCompanyId && (int) $selectedCompanyId === (int) $company->id;
                        @endphp
                        <button type="button"
                                wire:click="selectCompany({{ $company->id }})"
                                class="group w-full rounded-2xl bg-white px-5 py-4 text-left shadow-sm ring-1 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900
                                {{ $isSelected ? 'ring-indigo-300 dark:ring-indigo-700' : 'ring-slate-200/70 hover:ring-indigo-200 dark:ring-slate-800/70 dark:hover:ring-indigo-800/60' }}">
                            <div class="flex items-center gap-4">
                                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                    @if($company->company_logo)
                                        <img src="{{ $storageUrl($company->company_logo) }}" alt="{{ $company->company_name ?? $company->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ strtoupper(substr($company->company_name ?? $company->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $company->company_name ?? $company->name }}</div>
                                    <div class="mt-1 space-y-0.5">
                                        <div class="truncate text-xs font-medium text-slate-500 dark:text-slate-400">{{ $company->email }}</div>
                                        @if ($company->phone_number)
                                            <div class="truncate text-xs font-medium text-slate-500 dark:text-slate-400">{{ $company->phone_number }}</div>
                                        @endif
                                        @if ($company->company_address)
                                            <div class="flex items-start gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                                </svg>
                                                <span class="line-clamp-2">{{ $company->company_address }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1
                                        {{ $isSelected ? 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-200 dark:ring-indigo-700/50' : 'bg-slate-50 text-slate-600 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700/60' }}">
                                        {{ $isSelected ? 'Selected' : 'Select' }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300 transition group-hover:text-indigo-400 dark:text-slate-600 dark:group-hover:text-indigo-300" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @elseif ($step === 4)
                @php
                    $selectedServices = collect($this->services)->filter(fn ($svc) => in_array($svc->id, $this->selectedServiceIds));
                @endphp

                <div class="relative overflow-hidden rounded-3xl ring-1 ring-slate-200/70 shadow-[0_30px_90px_-70px_rgba(2,6,23,0.55)] dark:ring-slate-800/70">
                    <div class="absolute inset-0">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-100 via-amber-50 to-slate-50 dark:from-emerald-950 dark:via-slate-950 dark:to-slate-950"></div>
                        <div class="absolute inset-0 opacity-90 dark:opacity-100" style="background:radial-gradient(900px 540px at 18% 18%, rgba(34,197,94,.24), transparent 60%),radial-gradient(760px 520px at 85% 22%, rgba(250,204,21,.20), transparent 60%),radial-gradient(820px 560px at 55% 90%, rgba(59,130,246,.16), transparent 62%)"></div>
                        <div class="absolute -left-24 -top-28 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl dark:bg-emerald-400/10"></div>
                        <div class="absolute -right-28 -top-24 h-72 w-72 rounded-full bg-amber-400/10 blur-3xl dark:bg-amber-300/10"></div>
                        <div class="absolute -bottom-36 left-1/3 h-80 w-80 rounded-full bg-sky-400/10 blur-3xl dark:bg-sky-300/10"></div>
                        <div class="absolute inset-0 opacity-[0.08] dark:opacity-[0.10]" style="background-image:repeating-linear-gradient(0deg, rgba(2,6,23,.16) 0 1px, transparent 1px 6px),repeating-linear-gradient(90deg, rgba(2,6,23,.12) 0 1px, transparent 1px 8px)"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-white/35 via-white/15 to-white/40 dark:from-slate-950/35 dark:via-slate-950/10 dark:to-slate-950/45"></div>
                    </div>

                    <div class="relative p-6 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Select Services</h2>
                                <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300">Choose what you want to include in your session.</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @if($selectedServices->count() > 0)
                                    @foreach($selectedServices as $svc)
                                        <button wire:click="toggleService({{ $svc->id }})" class="inline-flex items-center gap-2 rounded-full bg-white/65 px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm ring-1 ring-white/40 backdrop-blur-md hover:bg-white/80 dark:bg-slate-900/55 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-900/70">
                                            <span class="max-w-[160px] truncate">{{ $svc->name }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 dark:text-slate-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endforeach
                                @else
                                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">No services selected</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($this->services as $service)
                                @php
                                    $selected = in_array($service->id, $this->selectedServiceIds);
                                    $imageUrl = $storageUrl($service->icon);
                                @endphp

                                <div wire:key="service-{{ $service->id }}" class="group relative overflow-hidden rounded-3xl bg-white/70 p-5 shadow-sm ring-1 backdrop-blur-md transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/55
                                    {{ $selected ? 'ring-amber-300/70 shadow-[0_22px_70px_-54px_rgba(245,158,11,0.65)] dark:ring-amber-400/40' : 'ring-white/40 dark:ring-white/10' }}">
                                    <div wire:loading wire:target="toggleService({{ $service->id }})" class="absolute inset-0 z-10 flex items-center justify-center bg-white/65 backdrop-blur-sm dark:bg-slate-950/45">
                                        <svg class="h-8 w-8 animate-spin text-emerald-600 dark:text-emerald-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>

                                    <div class="relative overflow-hidden rounded-2xl">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $service->name }}" class="h-32 w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
                                        @else
                                            <div class="h-32 w-full bg-gradient-to-br from-emerald-500/25 via-amber-400/20 to-slate-900/10 dark:from-emerald-400/15 dark:via-amber-300/10 dark:to-slate-950/30"></div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/55 via-slate-950/10 to-transparent"></div>
                                        <div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-[11px] font-semibold text-white ring-1 ring-white/15 backdrop-blur-md">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Spa Service
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <h3 class="truncate text-base font-semibold text-slate-900 dark:text-white">{{ $service->name }}</h3>
                                                <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300" style="-webkit-line-clamp:2;display:-webkit-box;-webkit-box-orient:vertical;overflow:hidden;">
                                                    {{ $service->description }}
                                                </p>
                                            </div>
                                            <span class="shrink-0 rounded-full px-3 py-1 text-[11px] font-semibold ring-1
                                                {{ $selected ? 'bg-amber-50 text-amber-800 ring-amber-200/70 dark:bg-amber-900/25 dark:text-amber-200 dark:ring-amber-800/50' : 'bg-white/60 text-slate-700 ring-white/40 dark:bg-slate-950/25 dark:text-slate-200 dark:ring-white/10' }}">
                                                {{ $selected ? 'Selected' : 'Select' }}
                                            </span>
                                        </div>

                                        <div class="mt-4 flex items-center justify-between gap-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/60 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-white/40 dark:bg-slate-950/25 dark:text-slate-200 dark:ring-white/10">
                                                    <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-300" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                                                    </svg>
                                                    {{ (int) ($service->duration_minutes ?? 0) > 0 ? ((int) $service->duration_minutes) . ' min' : '—' }}
                                                </span>
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/60 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-white/40 dark:bg-slate-950/25 dark:text-slate-200 dark:ring-white/10">
                                                    <svg class="h-4 w-4 text-amber-600 dark:text-amber-300" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 6v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        <path d="M9 9h5a2 2 0 0 1 0 4H10a2 2 0 0 0 0 4h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    ₱{{ number_format((float) ($service->price ?? 0), 2) }}
                                                </span>
                                            </div>

                                            <button type="button"
                                                    wire:click="toggleService({{ $service->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleService({{ $service->id }})"
                                                    class="shrink-0 rounded-2xl px-4 py-2 text-sm font-semibold transition
                                                    {{ $selected ? 'bg-slate-900 text-white hover:bg-slate-950 dark:bg-white dark:text-slate-900 dark:hover:bg-white/90' : 'bg-amber-500 text-slate-950 hover:bg-amber-400 dark:bg-amber-400 dark:hover:bg-amber-300' }}">
                                                {{ $selected ? 'Selected' : 'Select' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <button @if(count($this->selectedServiceIds)===0) disabled @endif wire:click="goToStep(5)"
                                    class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-[0_20px_48px_-32px_rgba(2,6,23,0.85)] transition hover:bg-slate-950 disabled:opacity-50 dark:bg-white dark:text-slate-900 dark:hover:bg-white/90">
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
            @elseif ($step === 5)
                @php
                    $activeServiceId = (int) ($this->activeServiceId ?? 0);
                    $selectedServices = collect($this->services)->whereIn('id', $this->selectedServiceIds)->values();
                    $activeService = $selectedServices->firstWhere('id', $activeServiceId) ?: $selectedServices->first();
                    $activeServiceId = (int) ($activeService?->id ?? 0);

                    $therapists = $this->therapistsForSelection;
                    $statusById = $this->therapistStatusByIdForActiveService;
                    $totalPrice = (float) $selectedServices->sum(fn ($svc) => (float) ($svc->price ?? 0));
                    $totalDuration = (int) $selectedServices->sum(fn ($svc) => (int) ($svc->duration_minutes ?? 0));

                    $allAssigned = true;
                    foreach ($this->selectedServiceIds as $svcId) {
                        $svcId = (int) $svcId;
                        if (! ((int) ($this->assignedTherapists[$svcId]['id'] ?? 0))) {
                            $allAssigned = false;
                            break;
                        }
                    }
                @endphp

                <div class="overflow-hidden rounded-3xl ring-1 ring-slate-200/70 shadow-[0_30px_90px_-70px_rgba(2,6,23,0.55)] dark:ring-slate-800/70">
                    <div class="relative bg-gradient-to-r from-violet-700 via-indigo-600 to-sky-600 px-6 py-6 sm:px-8">
                        <div class="absolute inset-0 opacity-[0.14]" style="background-image:repeating-linear-gradient(0deg, rgba(255,255,255,.18) 0 1px, transparent 1px 6px),repeating-linear-gradient(90deg, rgba(255,255,255,.12) 0 1px, transparent 1px 8px)"></div>
                        <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                        <div class="absolute -left-24 -bottom-28 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>

                        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-semibold text-white">Select a Therapist</h2>
                                <p class="mt-1 text-sm font-medium text-white/80">Choose a therapist for your selected services.</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <button wire:click="back" class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 backdrop-blur-md hover:bg-white/20">
                                    <span aria-hidden="true">←</span>
                                    Back to Services
                                </button>
                            </div>
                        </div>

                        <div class="relative mt-4 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-white/80">Summary:</span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white ring-1 ring-white/20 backdrop-blur-md">
                                <svg class="h-4 w-4 text-white/90" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 6v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M9 9h5a2 2 0 0 1 0 4H10a2 2 0 0 0 0 4h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Total ₱{{ number_format($totalPrice, 2) }}
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white ring-1 ring-white/20 backdrop-blur-md">
                                <svg class="h-4 w-4 text-white/90" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                {{ $totalDuration > 0 ? ($totalDuration . ' min') : 'Duration —' }}
                            </span>
                        </div>

                        <div class="relative mt-4 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-white/80">Selected Services:</span>
                            @foreach($selectedServices as $svc)
                                @php
                                    $svcId = (int) $svc->id;
                                    $assignedName = $this->assignedTherapists[$svcId]['name'] ?? null;
                                    $isActive = $activeServiceId === $svcId;
                                @endphp
                                <button type="button"
                                        wire:click="setActiveService({{ $svcId }})"
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 backdrop-blur-md transition
                                        {{ $isActive ? 'bg-white/25 text-white ring-white/30' : 'bg-white/15 text-white/90 ring-white/20 hover:bg-white/20' }}">
                                    <span class="max-w-[160px] truncate">{{ $svc->name }}</span>
                                    @if($assignedName)
                                        <span class="rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold text-white/90 ring-1 ring-white/15">
                                            {{ $assignedName }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-rose-500/20 px-2 py-0.5 text-[10px] font-semibold text-white ring-1 ring-white/15">
                                            Not set
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 dark:bg-slate-950/30">
                        <div class="mb-6 rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:ring-slate-800/70">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Now assigning</div>
                                    <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white">
                                        {{ $activeService?->name ?? 'Service' }}
                                    </div>
                                    <div class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300">
                                        Pick a therapist certified for this service. Therapists who aren’t certified are shown but disabled.
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach($selectedServices as $svc)
                                        @php
                                            $svcId = (int) $svc->id;
                                            $assignedName = $this->assignedTherapists[$svcId]['name'] ?? 'Not selected';
                                        @endphp
                                        <div class="rounded-2xl bg-white px-4 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-slate-800">
                                            <span class="text-slate-500 dark:text-slate-400">{{ $svc->name }}:</span>
                                            <span class="ml-1">{{ $assignedName }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if ($therapists->isEmpty())
                            <div class="rounded-3xl bg-slate-50 p-10 text-center ring-1 ring-slate-200 dark:bg-slate-900/55 dark:ring-slate-800/70">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                    <svg class="h-6 w-6 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="mt-4 text-base font-semibold text-slate-900 dark:text-white">No therapists available</div>
                                <div class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Try a different time or adjust your selected services.</div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($therapists as $therapist)
                                    @php
                                        $therapistId = (int) $therapist->id;
                                        $isSelectedForActive = (int) ($this->assignedTherapists[$activeServiceId]['id'] ?? 0) === $therapistId;
                                        $photoUrl = $storageUrl($therapist->photo);
                                        $title = $therapist->title ?: 'Therapist';
                                        $sessions = (int) ($therapist->bookings_count ?? 0);
                                        $gender = $therapist->gender ?? null;
                                        $specializations = $therapist->relationLoaded('services') ? $therapist->services->pluck('name')->values() : collect();
                                        $languages = is_array($therapist->languages ?? null) ? $therapist->languages : [];
                                        $certifications = is_array($therapist->certifications ?? null) ? $therapist->certifications : [];
                                        $status = $statusById[$therapistId] ?? 'available';
                                    @endphp

                                    <div wire:key="therapist-{{ $therapist->id }}" class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/55
                                        {{ $isSelectedForActive ? 'ring-indigo-300 shadow-[0_18px_45px_-34px_rgba(99,102,241,0.65)] dark:ring-indigo-700/60' : 'ring-slate-200/70 dark:ring-white/10' }}
                                        {{ in_array($status, ['in_session', 'not_certified'], true) ? 'opacity-70' : '' }}">
                                        <div class="relative h-44 overflow-hidden">
                                            @if ($photoUrl)
                                                <img src="{{ $photoUrl }}" alt="{{ $therapist->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy" decoding="async">
                                            @else
                                                <div class="h-full w-full bg-gradient-to-br from-indigo-600/20 via-violet-600/10 to-sky-500/20 dark:from-indigo-500/20 dark:via-violet-500/10 dark:to-sky-400/15"></div>
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/65 via-slate-950/15 to-transparent"></div>

                                            <div class="absolute left-4 top-4 inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold ring-1
                                                {{ $status === 'not_certified'
                                                    ? 'bg-slate-50 text-slate-700 ring-slate-200/70 dark:bg-slate-900/35 dark:text-slate-200 dark:ring-white/10'
                                                    : ($status === 'in_session'
                                                        ? 'bg-rose-50 text-rose-700 ring-rose-200/70 dark:bg-rose-900/25 dark:text-rose-200 dark:ring-rose-800/50'
                                                        : 'bg-emerald-50 text-emerald-700 ring-emerald-200/70 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-800/50') }}">
                                                {{ $status === 'not_certified' ? 'Not Certified' : ($status === 'in_session' ? 'In Session' : 'Available') }}
                                            </div>

                                            <div class="absolute right-4 top-4 inline-flex items-center rounded-full bg-white/85 px-3 py-1 text-[11px] font-semibold text-slate-800 ring-1 ring-white/30 backdrop-blur-sm dark:bg-slate-900/70 dark:text-slate-100 dark:ring-white/10">
                                                {{ $sessions }} sessions
                                            </div>
                                        </div>

                                        <div class="p-5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="truncate text-base font-semibold text-slate-900 dark:text-white">{{ $therapist->name }}</div>
                                                    <div class="mt-0.5 text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $title }}</div>
                                                </div>
                                            </div>

                                            @if ($therapist->bio)
                                                <div class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-300" style="-webkit-line-clamp:2;display:-webkit-box;-webkit-box-orient:vertical;overflow:hidden;">
                                                    {{ $therapist->bio }}
                                                </div>
                                            @endif

                                            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-50 px-3 py-1.5 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                                    <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                                                    </svg>
                                                    {{ $sessions }}
                                                </span>
                                                @if ($gender)
                                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1.5 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                                        {{ $gender }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($specializations->isNotEmpty())
                                                <div class="mt-4">
                                                    <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Specializations</div>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach ($specializations->take(4) as $tag)
                                                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-semibold text-indigo-700 ring-1 ring-indigo-200/70 dark:bg-indigo-900/25 dark:text-indigo-200 dark:ring-indigo-800/50">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if (! empty($languages))
                                                <div class="mt-4">
                                                    <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Languages</div>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach (array_slice($languages, 0, 4) as $lang)
                                                            <span class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-slate-800">
                                                                {{ $lang }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if (! empty($certifications))
                                                <div class="mt-4">
                                                    <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Certifications</div>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach (array_slice($certifications, 0, 4) as $cert)
                                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200/70 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-800/50">
                                                                {{ $cert }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="mt-5">
                                                <button type="button"
                                                        wire:click="setAssignedTherapist({{ $activeServiceId }}, {{ $therapistId }})"
                                                        @if($status !== 'available') disabled @endif
                                                        title="{{ $status === 'in_session' ? 'This therapist is currently handling another session' : ($status === 'not_certified' ? 'This therapist is not certified for the selected service' : '') }}"
                                                        class="w-full rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                                                        {{ $isSelectedForActive ? 'bg-slate-900 text-white hover:bg-slate-950 dark:bg-white dark:text-slate-900 dark:hover:bg-white/90' : 'bg-indigo-600 text-white shadow-[0_16px_34px_-26px_rgba(37,99,235,0.9)] hover:bg-indigo-700' }}
                                                        disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-500 disabled:shadow-none dark:disabled:bg-slate-800 dark:disabled:text-slate-400">
                                                    {{ $status === 'in_session' ? 'Unavailable' : ($status === 'not_certified' ? 'Unavailable' : ($isSelectedForActive ? 'Selected' : 'Select Therapist')) }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 flex items-center justify-end">
                                <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:text-white dark:ring-slate-800/70">
                                        Estimated total: <span class="text-indigo-700 dark:text-indigo-300">₱{{ number_format($totalPrice, 2) }}</span>
                                        <span class="ml-2 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $totalDuration > 0 ? ($totalDuration . ' min') : '' }}</span>
                                    </div>

                                    <div class="flex flex-col items-end gap-2">
                                    <button wire:click="goToStep(6)"
                                            aria-disabled="{{ $allAssigned ? 'false' : 'true' }}"
                                            class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-[0_20px_48px_-32px_rgba(2,6,23,0.85)] transition hover:bg-slate-950 dark:bg-white dark:text-slate-900 dark:hover:bg-white/90 {{ $allAssigned ? '' : 'opacity-50' }}">
                                    Continue
                                    </button>
                                    @if(! $allAssigned)
                                        <div class="text-xs font-semibold text-rose-600 dark:text-rose-300">
                                            Select a therapist for each service (see “Not set” tags above).
                                        </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($step === 2)
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Select Date &amp; Time</h2>
                        <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Choose the date and time for your appointment.</p>
                    </div>
                    <button wire:click="back" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-300">
                        <span aria-hidden="true">←</span>
                        Back to Company
                    </button>
                </div>

                <div class="mb-6 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200/70 dark:bg-slate-900 dark:ring-slate-800/70">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Selected</span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-950/40 dark:text-slate-200 dark:ring-slate-800">
                                <svg class="h-4 w-4 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none">
                                    <path d="M8 7V3m8 4V3m-9 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                @if($selectedDate)
                                    {{ \Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                                @else
                                    No date
                                @endif
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-950/40 dark:text-slate-200 dark:ring-slate-800">
                                <svg class="h-4 w-4 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                @if($selectedTime)
                                    {{ $selectedTime }}
                                @else
                                    No time
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3 sm:justify-end">
                            <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-indigo-500"></span>Today</span>
                                <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-indigo-600"></span>Selected</span>
                                <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Unavailable</span>
                            </div>
                            @if($selectedDate || $selectedTime)
                                <button type="button" wire:click="clearDateTime" class="rounded-xl bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-slate-800 dark:hover:bg-slate-800">
                                    Clear
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Calendar Section -->
                    <div class="relative rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70 dark:bg-slate-900 dark:ring-slate-800/70">
                        <div wire:loading.flex wire:target="decrementMonth,incrementMonth,selectDate" class="absolute inset-0 z-10 items-center justify-center rounded-2xl bg-white/70 backdrop-blur-sm dark:bg-slate-950/45">
                            <svg class="h-8 w-8 animate-spin text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($currentCalendarMonth)->format('F Y') }}
                            </h3>
                            <div class="flex gap-2">
                                <button wire:click="decrementMonth" wire:loading.attr="disabled" wire:target="decrementMonth,incrementMonth" class="rounded-xl p-2 text-slate-600 hover:bg-slate-100 disabled:opacity-50 dark:text-slate-300 dark:hover:bg-slate-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button wire:click="incrementMonth" wire:loading.attr="disabled" wire:target="decrementMonth,incrementMonth" class="rounded-xl p-2 text-slate-600 hover:bg-slate-100 disabled:opacity-50 dark:text-slate-300 dark:hover:bg-slate-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-7 text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-2">
                            <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                        </div>
                        
                        <div class="grid grid-cols-7 gap-1">
                            @php
                                $month = \Carbon\Carbon::parse($currentCalendarMonth);
                                $startOfMonth = $month->copy()->startOfMonth();
                                $endOfMonth = $month->copy()->endOfMonth();
                                $startOfWeek = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                                $endOfWeek = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                                $today = \Carbon\Carbon::now($this->timezone ?? 'Asia/Manila')->startOfDay();
                                $holidays = $this->holidays;
                            @endphp
                            
                            @for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay())
                                @php
                                    $isCurrentMonth = $date->month === $month->month;
                                    $isSunday = $date->isSunday();
                                    $isPast = $date->lt($today);
                                    $isToday = $date->isSameDay($today);
                                    $dateString = $date->format('Y-m-d');
                                    $isSelected = $selectedDate && $dateString === $selectedDate;
                                    
                                    $isHoliday = array_key_exists($dateString, $holidays);
                                    $holidayName = $isHoliday ? $holidays[$dateString] : null;

                                    $baseClasses = "h-10 w-full flex items-center justify-center rounded-xl text-sm font-semibold transition relative focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800/50";
                                    
                                    if (!$isCurrentMonth) {
                                        $classes = "text-slate-300 dark:text-slate-700 cursor-default";
                                    } elseif ($isPast) {
                                        $classes = "text-slate-300 dark:text-slate-600 cursor-not-allowed line-through decoration-slate-300 dark:decoration-slate-700";
                                    } elseif ($isHoliday) {
                                        $classes = "bg-rose-50 text-rose-700 ring-1 ring-rose-200/70 cursor-not-allowed dark:bg-rose-900/25 dark:text-rose-200 dark:ring-rose-800/50";
                                    } elseif ($isSunday) {
                                        $classes = "bg-rose-50 text-rose-700 ring-1 ring-rose-200/70 cursor-not-allowed dark:bg-rose-900/25 dark:text-rose-200 dark:ring-rose-800/50";
                                    } elseif ($isSelected) {
                                        $classes = "bg-indigo-600 text-white shadow-sm ring-1 ring-indigo-400/40";
                                    } elseif ($isToday) {
                                        $classes = "bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200 hover:bg-indigo-100 cursor-pointer dark:bg-indigo-900/25 dark:text-indigo-200 dark:ring-indigo-800/50 dark:hover:bg-indigo-900/35";
                                    } else {
                                        $classes = "text-slate-700 hover:bg-slate-100 cursor-pointer dark:text-slate-200 dark:hover:bg-slate-800";
                                    }
                                @endphp
                                
                                <div wire:key="day-{{ $dateString }}" class="aspect-square relative group">
                                    @if($isCurrentMonth && !$isPast && !$isSunday && !$isHoliday)
                                        <button wire:click="selectDate('{{ $dateString }}')" class="{{ $baseClasses }} {{ $classes }}">
                                            {{ $date->day }}
                                            @if($isToday && !$isSelected)
                                                <span class="absolute bottom-1 h-1 w-1 rounded-full bg-indigo-500"></span>
                                            @endif
                                        </button>
                                    @else
                                        <div class="{{ $baseClasses }} {{ $classes }}">
                                            {{ $date->day }}
                                        </div>
                                    @endif
                                    
                                    @if($isHoliday && $isCurrentMonth)
                                        <div class="absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 whitespace-nowrap rounded-xl bg-slate-900 px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                            {{ $holidayName }}
                                        </div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                    
                    <!-- Time Slots Section -->
                    <div class="relative flex flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 dark:bg-slate-900 dark:ring-slate-800/70">
                        <div wire:loading.flex wire:target="selectDate,selectTime" class="absolute inset-0 z-10 items-center justify-center rounded-2xl bg-white/70 backdrop-blur-sm dark:bg-slate-950/45">
                            <svg class="h-8 w-8 animate-spin text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                        <h3 class="mb-4 text-lg font-semibold text-slate-900 dark:text-white">
                            @if($selectedDate)
                                Time Slots for {{ \Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                            @else
                                Select a Date
                            @endif
                        </h3>

                        @if($selectedDate && $scheduleInfo)
                            <div class="mb-4 rounded-2xl bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:text-slate-300 dark:ring-slate-800">
                                @if(($scheduleInfo['is_closed'] ?? false) === true)
                                    Closed on this day.
                                @else
                                    Business Hours:
                                    <span class="text-slate-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($scheduleInfo['open'] ?? '00:00')->format('g:i A') }}
                                        –
                                        {{ \Carbon\Carbon::parse($scheduleInfo['close'] ?? '00:00')->format('g:i A') }}
                                    </span>
                                    @if(!empty($scheduleInfo['breaks'] ?? []))
                                        <span class="ml-2 text-slate-400 dark:text-slate-500">•</span>
                                        @foreach(($scheduleInfo['breaks'] ?? []) as $break)
                                            <span class="ml-2 inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:text-slate-200 dark:ring-white/10">
                                                {{ $break['label'] ?? 'Break' }}:
                                                {{ \Carbon\Carbon::parse($break['start'] ?? '00:00')->format('g:i A') }}
                                                –
                                                {{ \Carbon\Carbon::parse($break['end'] ?? '00:00')->format('g:i A') }}
                                            </span>
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                        @endif

                        @if($selectedDate)
                            <div class="flex-1 overflow-y-auto max-h-[300px]">
                                @if(count($availableSlots) > 0)
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach($availableSlots as $slotData)
                                            @php
                                                $time = $slotData['time'];
                                                $isBooked = $slotData['booked'] ?? false;
                                                $isDisabled = (bool) ($slotData['disabled'] ?? false);
                                                $reason = $slotData['reason'] ?? null;
                                                $baseClasses = "py-3 px-4 rounded-xl ring-1 text-sm font-semibold transition flex justify-center items-center";
                                                $isSelectedTime = $selectedTime && $selectedTime === $time;
                                                $classes = $isDisabled
                                                    ? "bg-slate-100 text-slate-400 ring-slate-200 cursor-not-allowed dark:bg-slate-800/60 dark:text-slate-500 dark:ring-slate-700/50"
                                                    : ($isSelectedTime
                                                        ? "bg-indigo-600 text-white ring-indigo-400/40 shadow-sm"
                                                        : "bg-white ring-slate-200 text-slate-700 hover:bg-indigo-50 hover:ring-indigo-200 hover:text-indigo-700 dark:bg-slate-950/40 dark:ring-slate-800 dark:text-slate-200 dark:hover:bg-indigo-900/25 dark:hover:ring-indigo-700/50 dark:hover:text-indigo-200 cursor-pointer");
                                            @endphp
                                            <button
                                                wire:key="slot-{{ $time }}-{{ $isDisabled ? 'disabled' : 'enabled' }}"
                                                @if(! $isDisabled) wire:click="selectTime('{{ $time }}')" @endif
                                                @if($isDisabled) disabled @endif
                                                title="{{ $isDisabled ? ($reason ?? 'Unavailable') : '' }}"
                                                class="{{ $baseClasses }} {{ $classes }}">
                                                <span>{{ $time }}</span>
                                                @if($isBooked)
                                                    <span class="ml-2 inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-200/60 dark:bg-amber-900/25 dark:text-amber-200 dark:ring-amber-800/50">
                                                        Busy
                                                    </span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                    <div class="mt-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                                        Busy means some rooms may already be occupied, but you can still proceed and pick an available room next.
                                    </div>
                                @else
                                    <div class="flex h-40 flex-col items-center justify-center text-slate-500 dark:text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-center">No slots available for this date.</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex h-full flex-col items-center justify-center text-slate-500 dark:text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-center">Select a date from the calendar to view available time slots.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($step === 3)
                @php
                    $isAdminView = auth()->guard('admin')->check();
                    $amenitySelection = is_array($roomAmenities ?? null) ? $roomAmenities : [];
                    $roomItems = $this->filteredRooms;
                    $roomCounts = $this->roomBookingCounts;
                    $durationMinutes = (int) $this->selectedServicesDurationMinutes();
                    $durationIsEstimated = $durationMinutes <= 0;
                    $durationMinutes = $durationMinutes > 0 ? $durationMinutes : 60;
                @endphp

                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Select a Room</h2>
                        <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">
                            Rooms are reserved for <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $durationMinutes }} min</span>
                            @if($durationIsEstimated)
                                <span class="text-slate-400 dark:text-slate-500">(select services to calculate exact duration)</span>
                            @endif
                        </p>
                    </div>
                    <button wire:click="back" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-300">
                        <span aria-hidden="true">←</span>
                        Back to Date &amp; Time
                    </button>
                </div>

                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70 dark:bg-slate-900 dark:ring-slate-800/70">
                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-12 lg:items-end">
                        <div class="lg:col-span-4">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Search</label>
                            <div class="relative mt-2">
                                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <input wire:model.live.debounce.250ms="roomSearch" type="text" placeholder="Search by name or code"
                                       class="w-full rounded-2xl bg-slate-50 px-10 py-2.5 text-sm font-medium text-slate-900 ring-1 ring-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-950/40 dark:text-white dark:ring-slate-800 dark:focus:ring-indigo-800/50">
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Capacity</label>
                            <select wire:model.live="roomCapacity" class="mt-2 w-full rounded-2xl bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-950/40 dark:text-white dark:ring-slate-800 dark:focus:ring-indigo-800/50">
                                <option value="any">Any</option>
                                <option value="private">Private (1 person)</option>
                                <option value="small">Small (2 persons)</option>
                                <option value="medium">Medium (3–4 persons)</option>
                                <option value="group">Group (5–6 persons)</option>
                            </select>
                        </div>

                        <div class="lg:col-span-3">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Amenities</label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @php
                                    $amenities = [
                                        'wifi' => 'WiFi',
                                        'ac' => 'AC',
                                        'coffee' => 'Coffee',
                                        'tv' => 'TV',
                                    ];
                                @endphp
                                @foreach ($amenities as $key => $label)
                                    @php
                                        $checked = in_array($key, $amenitySelection, true);
                                        $pill = $checked
                                            ? 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-200 dark:ring-indigo-700/50'
                                            : 'bg-slate-50 text-slate-700 ring-slate-200 hover:bg-slate-100 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-slate-800 dark:hover:bg-slate-800';
                                    @endphp
                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-full px-3 py-2 text-xs font-semibold ring-1 transition {{ $pill }}">
                                        <input type="checkbox" class="sr-only" value="{{ $key }}" wire:model.live="roomAmenities">
                                        @if ($key === 'wifi')
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M5 12.55a11 11 0 0 1 14 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M8.5 16a6 6 0 0 1 7 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".85"/>
                                                <path d="M12 19h.01" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                                            </svg>
                                        @elseif ($key === 'ac')
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M7 11h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".85"/>
                                                <path d="M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".7"/>
                                                <path d="M8 19c2 0 3-1 4-2s2-2 4-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".6"/>
                                            </svg>
                                        @elseif ($key === 'coffee')
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M6 8h9v5a4 4 0 0 1-4 4H9a3 3 0 0 1-3-3V8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                <path d="M15 9h2a2 2 0 0 1 0 4h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M6 19h11" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".7"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M4 7h16v10H4V7Z" stroke="currentColor" stroke-width="2" />
                                                <path d="M8 19h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".8"/>
                                                <path d="M9 11h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".7"/>
                                            </svg>
                                        @endif
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="lg:col-span-3">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Availability</label>
                            <select wire:model.live="roomAvailability" class="mt-2 w-full rounded-2xl bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-950/40 dark:text-white dark:ring-slate-800 dark:focus:ring-indigo-800/50">
                                <option value="any">All</option>
                                <option value="available">Not Occupied</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 relative">
                    <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center rounded-3xl bg-white/60 backdrop-blur-sm dark:bg-slate-950/45">
                        <svg class="h-8 w-8 animate-spin text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    @if ($roomItems->isEmpty())
                        <div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200/70 dark:bg-slate-900 dark:ring-slate-800/70">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                <svg class="h-6 w-6 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7h16v10H4V7Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 11h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".8"/>
                                </svg>
                            </div>
                            <div class="mt-4 text-base font-semibold text-slate-900 dark:text-white">No rooms available</div>
                            <div class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Try adjusting filters or choose a different time.</div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach($roomItems as $item)
                                @php
                                    $room = $item['room'];
                                    $computedStatus = $item['computed_status'];
                                    $isAvailable = $item['is_available'];
                                    $occupiedUntil = $item['occupied_until'] ?? null;
                                    $capacityMax = (int) ($room->capacity_max ?? 0);
                                    $type = in_array($room->room_type ?? null, ['private', 'couple', 'family', 'group'], true) ? $room->room_type : null;
                                    $type = $type ?: match (true) {
                                        $capacityMax <= 1 => 'private',
                                        $capacityMax === 2 => 'couple',
                                        $capacityMax <= 4 => 'family',
                                        default => 'group',
                                    };
                                    $typeLabel = match ($type) {
                                        'private' => 'Private Room',
                                        'couple' => 'Small Room',
                                        'family' => 'Medium Room',
                                        default => 'Group Room',
                                    };
                                    $capLabel = match ($type) {
                                        'private' => '1 person',
                                        'couple' => '2 persons',
                                        'family' => '3–4 persons',
                                        default => '5–6 persons',
                                    };
                                    $capacityLabel = "{$typeLabel} – {$capLabel}";
                                    $code = $room->code ?: ('R' . str_pad((string) $room->id, 3, '0', STR_PAD_LEFT));
                                    $roomAmenities = is_array($room->amenities) ? $room->amenities : [];
                                    $isSelected = (int) ($selectedRoomId ?? 0) === (int) $room->id;

                                    $statusBadge = match ($computedStatus) {
                                        'available' => ['Not Occupied', 'bg-emerald-50 text-emerald-700 ring-emerald-200/60 dark:bg-emerald-900/25 dark:text-emerald-200 dark:ring-emerald-800/50'],
                                        'occupied' => [$occupiedUntil ? "Occupied until {$occupiedUntil}" : 'Occupied', 'bg-rose-50 text-rose-700 ring-rose-200/60 dark:bg-rose-900/25 dark:text-rose-200 dark:ring-rose-800/50'],
                                        'maintenance' => ['Under Maintenance', 'bg-amber-50 text-amber-700 ring-amber-200/60 dark:bg-amber-900/25 dark:text-amber-200 dark:ring-amber-800/50'],
                                        default => ['Unknown', 'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700/60'],
                                    };
                                @endphp

                                <div wire:key="room-card-{{ $room->id }}" class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900
                                    {{ $isSelected ? 'ring-indigo-300 shadow-[0_18px_45px_-34px_rgba(99,102,241,0.65)] dark:ring-indigo-700/60' : 'ring-slate-200/70 dark:ring-slate-800/70' }}
                                    {{ $isAvailable ? '' : 'opacity-70' }}">
                                    <div class="relative h-40 overflow-hidden">
                                        @if ($room->image)
                                            <img src="{{ $storageUrl($room->image) }}" alt="{{ $room->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy" decoding="async">
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-indigo-600/20 via-violet-600/10 to-sky-500/20 dark:from-indigo-500/20 dark:via-violet-500/10 dark:to-sky-400/15"></div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/55 via-slate-950/10 to-transparent"></div>

                                        <div class="absolute left-4 top-4 inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold ring-1 {{ $statusBadge[1] }}">
                                            {{ $statusBadge[0] }}
                                        </div>

                                        @if ($isAdminView)
                                            @php
                                                $count = (int) ($roomCounts[$room->id] ?? 0);
                                            @endphp
                                            <div class="absolute right-4 top-4 inline-flex items-center rounded-full bg-white/85 px-3 py-1 text-[11px] font-semibold text-slate-800 ring-1 ring-white/30 backdrop-blur-sm dark:bg-slate-900/70 dark:text-slate-100 dark:ring-slate-700/60">
                                                {{ $count }} today
                                            </div>
                                        @endif

                                        <div class="absolute bottom-4 left-4 right-4">
                                            <div class="flex items-end justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-semibold text-white">
                                                        {{ $room->name }}
                                                        <span class="ml-1 text-xs font-semibold text-white/70">{{ $code }}</span>
                                                    </div>
                                                </div>
                                                <div class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/85 ring-1 ring-white/15">
                                                    {{ $capacityLabel }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <div class="flex flex-wrap gap-2">
                                            @php
                                                $amenityIcons = [
                                                    'wifi' => 'WiFi',
                                                    'ac' => 'AC',
                                                    'coffee' => 'Coffee',
                                                    'tv' => 'TV',
                                                ];
                                            @endphp
                                            @foreach ($amenityIcons as $key => $label)
                                                @php $hasAmenity = in_array($key, $roomAmenities, true); @endphp
                                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1
                                                    {{ $hasAmenity ? 'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700/60' : 'bg-slate-50/60 text-slate-400 ring-slate-200/50 dark:bg-slate-800/30 dark:text-slate-500 dark:ring-slate-700/40' }}">
                                                    @if ($key === 'wifi')
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 12.55a11 11 0 0 1 14 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            <path d="M8.5 16a6 6 0 0 1 7 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".85"/>
                                                            <path d="M12 19h.01" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                                                        </svg>
                                                    @elseif ($key === 'coffee')
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                                            <path d="M6 8h9v5a4 4 0 0 1-4 4H9a3 3 0 0 1-3-3V8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                            <path d="M15 9h2a2 2 0 0 1 0 4h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        </svg>
                                                    @elseif ($key === 'ac')
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                                            <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            <path d="M7 11h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".85"/>
                                                            <path d="M9 15h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".7"/>
                                                        </svg>
                                                    @else
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                                            <path d="M4 7h16v10H4V7Z" stroke="currentColor" stroke-width="2" />
                                                            <path d="M8 19h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".8"/>
                                                        </svg>
                                                    @endif
                                                    <span>{{ $label }}</span>
                                                </span>
                                            @endforeach
                                        </div>

                                        <div class="mt-4">
                                            @if ($isAdminView)
                                                @php
                                                    $editRouteName = 'filament.admin.resources.rooms.edit';
                                                    $editUrl = \Illuminate\Support\Facades\Route::has($editRouteName)
                                                        ? route($editRouteName, ['record' => $room->id])
                                                        : null;
                                                    $scheduleRouteName = 'filament.admin.resources.bookings.index';
                                                    $scheduleUrl = \Illuminate\Support\Facades\Route::has($scheduleRouteName)
                                                        ? route($scheduleRouteName)
                                                        : null;
                                                @endphp
                                                <div class="grid grid-cols-4 gap-2">
                                                    @if ($editUrl)
                                                        <a href="{{ $editUrl }}" class="rounded-2xl bg-white px-3 py-2 text-center text-xs font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-slate-800 dark:hover:bg-slate-800">
                                                            Edit
                                                        </a>
                                                        <a href="{{ $editUrl }}" class="rounded-2xl bg-rose-50 px-3 py-2 text-center text-xs font-semibold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-100 dark:bg-rose-900/25 dark:text-rose-200 dark:ring-rose-800/50 dark:hover:bg-rose-900/35">
                                                            Delete
                                                        </a>
                                                    @else
                                                        <span class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-semibold text-slate-400 ring-1 ring-slate-200 dark:bg-slate-800/40 dark:text-slate-500 dark:ring-slate-700/40">
                                                            Edit
                                                        </span>
                                                        <span class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-semibold text-slate-400 ring-1 ring-slate-200 dark:bg-slate-800/40 dark:text-slate-500 dark:ring-slate-700/40">
                                                            Delete
                                                        </span>
                                                    @endif

                                                    <button type="button" wire:click="toggleRoomMaintenance({{ $room->id }})" class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-100 dark:bg-slate-950/30 dark:text-slate-200 dark:ring-slate-800 dark:hover:bg-slate-800">
                                                        {{ ($room->status ?? 'available') === 'maintenance' ? 'Enable' : 'Maintenance' }}
                                                    </button>

                                                    @if ($scheduleUrl)
                                                        <a href="{{ $scheduleUrl }}" class="rounded-2xl bg-indigo-600 px-3 py-2 text-center text-xs font-semibold text-white shadow-[0_16px_34px_-26px_rgba(37,99,235,0.9)] hover:bg-indigo-700">
                                                            Schedule
                                                        </a>
                                                    @else
                                                        <span class="rounded-2xl bg-slate-50 px-3 py-2 text-center text-xs font-semibold text-slate-400 ring-1 ring-slate-200 dark:bg-slate-800/40 dark:text-slate-500 dark:ring-slate-700/40">
                                                            Schedule
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <button type="button"
                                                        wire:click="selectRoom({{ $room->id }})"
                                                        @if (! $isAvailable) disabled @endif
                                                        title="{{ ! $isAvailable ? ($statusBadge[0] ?? 'Unavailable') : '' }}"
                                                        class="w-full rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                                                            {{ $isAvailable ? 'bg-indigo-600 text-white shadow-[0_16px_34px_-26px_rgba(37,99,235,0.9)] hover:bg-indigo-700' : 'bg-slate-100 text-slate-400 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-500 dark:ring-slate-700/50 cursor-not-allowed' }}">
                                                    Select Room
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                        <button wire:click="selectRoom(null)" class="w-full sm:w-auto rounded-2xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-200 dark:ring-slate-800 dark:hover:bg-slate-800">
                            Auto-assign room
                        </button>
                        <button wire:click="goToStep(4)" class="w-full sm:w-auto rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-[0_16px_34px_-26px_rgba(37,99,235,0.9)] hover:bg-indigo-700">
                            Continue to Services
                        </button>
                    </div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 text-center sm:text-right">
                        This is under testing only!!.
                    </p>
                </div>
            @elseif ($step === 6)
                @php
                    $company = $this->selectedCompany;
                    $servicesById = collect($this->services)->keyBy('id');
                    $selectedServicesOrdered = collect($this->selectedServiceIds)
                        ->map(fn ($id) => $servicesById->get((int) $id))
                        ->filter()
                        ->values();
                    $totalPrice = (float) $selectedServicesOrdered->sum(fn ($svc) => (float) ($svc->price ?? 0));
                @endphp

                <div class="overflow-hidden rounded-3xl ring-1 ring-slate-200/70 shadow-[0_30px_90px_-70px_rgba(2,6,23,0.55)] dark:ring-slate-800/70 transition-colors">
                    <div class="relative bg-gradient-to-r from-violet-700 via-indigo-600 to-sky-600 px-6 py-6 sm:px-8">
                        <div class="absolute inset-0 opacity-[0.14]" style="background-image:repeating-linear-gradient(0deg, rgba(255,255,255,.18) 0 1px, transparent 1px 6px),repeating-linear-gradient(90deg, rgba(255,255,255,.12) 0 1px, transparent 1px 8px)"></div>
                        <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                        <div class="absolute -left-24 -bottom-28 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>

                        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-semibold text-white">Confirm Booking</h2>
                                <p class="mt-1 text-sm font-medium text-white/80">Review details and confirm your appointment.</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <button wire:click="back" class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 backdrop-blur-md hover:bg-white/20">
                                    <span aria-hidden="true">←</span>
                                    Back to Therapist
                                </button>

                                <button type="button"
                                        onclick="(() => { const root = document.documentElement; const isDark = root.classList.contains('dark'); if (isDark) { root.classList.remove('dark'); localStorage.theme = 'light'; } else { root.classList.add('dark'); localStorage.theme = 'dark'; } })()"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/20 backdrop-blur-md hover:bg-white/20"
                                        aria-label="Toggle theme">
                                    <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M12 19v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M4.22 4.22l1.42 1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M18.36 18.36l1.42 1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M3 12h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M19 12h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M4.22 19.78l1.42-1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 13.2A7.8 7.8 0 0 1 10.8 3a9 9 0 1 0 10.2 10.2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="relative mt-4 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-white/80">Total:</span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white ring-1 ring-white/20 backdrop-blur-md">
                                ₱{{ number_format($totalPrice, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 dark:bg-slate-950/30 transition-colors">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200 dark:bg-slate-900/55 dark:ring-slate-800/70">
                                <div class="text-sm font-semibold text-slate-900 dark:text-white">Booking Summary</div>

                                <div class="mt-4 space-y-4 text-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-white ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                                <path d="M4 21V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M9 21V9h6v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Selected Company</div>
                                            <div class="mt-0.5 truncate font-semibold text-slate-900 dark:text-white">
                                                {{ $company?->company_name ?? $company?->name ?? '—' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-white ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                                <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M4 12h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Selected Services</div>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach($selectedServicesOrdered as $svc)
                                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-slate-800 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:text-slate-100 dark:ring-slate-800">
                                                        {{ $svc->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-white ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                                    <path d="M8 7V3m8 4V3m-9 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Date</div>
                                                <div class="mt-0.5 font-semibold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</div>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-white ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Time</div>
                                                <div class="mt-0.5 font-semibold text-slate-900 dark:text-white">{{ $selectedTime }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/55 dark:ring-white/10">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">Price Breakdown</div>
                                </div>

                                <div class="mt-4 space-y-3">
                                    @foreach($selectedServicesOrdered as $svc)
                                        @php
                                            $svcId = (int) $svc->id;
                                            $assignedName = $this->assignedTherapists[$svcId]['name'] ?? 'Unassigned';
                                            $price = (float) ($svc->price ?? 0);
                                        @endphp
                                        <div class="flex items-start justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800">
                                            <div class="min-w-0">
                                                <div class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $svc->name }}</div>
                                                <div class="mt-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                    {{ $assignedName }}
                                                </div>
                                            </div>
                                            <div class="shrink-0 text-sm font-semibold text-slate-900 dark:text-white">
                                                ₱{{ number_format($price, 2) }}
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="mt-4 flex items-center justify-between rounded-2xl bg-gradient-to-r from-violet-600 via-indigo-600 to-sky-600 px-4 py-3 text-white ring-1 ring-white/15">
                                        <div class="text-sm font-semibold">Total</div>
                                        <div class="text-lg font-bold">₱{{ number_format($totalPrice, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/55 dark:ring-white/10">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Notes (Optional)</label>
                            <textarea wire:model="notes" rows="4" placeholder="Any special requests (e.g., focus on shoulders, preferred pressure)..."
                                      class="w-full rounded-2xl bg-white px-4 py-3 text-sm font-medium text-slate-900 ring-1 ring-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-950/40 dark:text-white dark:ring-slate-800 dark:focus:ring-indigo-800/50"></textarea>
                        </div>

                        <div class="mt-6 flex items-center justify-center">
                            <button wire:click="book"
                                    class="inline-flex w-full max-w-xl items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 via-indigo-600 to-sky-600 px-6 py-3.5 text-sm font-semibold text-white shadow-[0_22px_60px_-40px_rgba(37,99,235,0.9)] transition hover:brightness-110">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] transition-opacity" aria-hidden="true"></div>

                <!-- Modal Panel -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Booking Confirmed!
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Your appointment has been successfully booked. Please take a screenshot of this summary for your records.
                                    </p>
                                    
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                        <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200 dark:border-gray-600">
                                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Booking Reference</span>
                                            <span class="font-mono text-indigo-600 dark:text-indigo-400 font-bold">#{{ str_pad($bookedDetails['id'], 6, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        <dl class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500 dark:text-gray-400">Services:</dt>
                                                <dd class="font-medium text-gray-900 dark:text-white">
                                                    {{ implode(', ', $bookedDetails['services'] ?? []) }}
                                                </dd>
                                            </div>
                                            <div>
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-500 dark:text-gray-400">Therapists:</dt>
                                                    <dd class="font-medium text-gray-900 dark:text-white">
                                                        <ul class="space-y-1">
                                                            @foreach($bookedDetails['service_therapists'] ?? [] as $pair)
                                                                <li>
                                                                    <span class="font-semibold">{{ $pair['service'] }}</span>:
                                                                    <span>{{ $pair['therapist'] }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </dd>
                                                </div>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500 dark:text-gray-400">Room:</dt>
                                                <dd class="font-medium text-gray-900 dark:text-white">
                                                    {{ $bookedDetails['room'] ?? 'Any available room' }}
                                                </dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500 dark:text-gray-400">Date:</dt>
                                                <dd class="font-medium text-gray-900 dark:text-white">{{ $bookedDetails['date'] }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-gray-500 dark:text-gray-400">Time:</dt>
                                                <dd class="font-medium text-gray-900 dark:text-white">{{ $bookedDetails['time'] }}</dd>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                                <dt class="font-bold text-gray-700 dark:text-gray-300">Total:</dt>
                                                <dd class="font-bold text-indigo-600 dark:text-indigo-400">₱{{ number_format($bookedDetails['total'] ?? 0, 2) }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="finishBooking" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
