<x-filament-panels::page>
    @php
        $admin = auth()->guard('admin')->user();

        $dashboardUrl = route('filament.admin.pages.dashboard');
        $bookingsUrl = route('filament.admin.resources.bookings.index');
        $servicesUrl = route('filament.admin.resources.services.index');
        $therapistsUrl = route('filament.admin.resources.therapists.index');
        $clientsUrl = $bookingsUrl;
        $scheduleUrl = route('filament.admin.resources.holidays.index');
        $settingsUrl = \Illuminate\Support\Facades\Route::has('filament.admin.pages.view-profile')
            ? route('filament.admin.pages.view-profile')
            : $dashboardUrl;

        $menuItems = [
            ['label' => 'Dashboard', 'url' => $dashboardUrl, 'active' => true, 'icon' => 'grid'],
            ['label' => 'Bookings', 'url' => $bookingsUrl, 'active' => false, 'icon' => 'calendar'],
            ['label' => 'Services', 'url' => $servicesUrl, 'active' => false, 'icon' => 'sparkle'],
            ['label' => 'Therapists', 'url' => $therapistsUrl, 'active' => false, 'icon' => 'users'],
            ['label' => 'Clients', 'url' => $clientsUrl, 'active' => false, 'icon' => 'user'],
            ['label' => 'Schedule', 'url' => $scheduleUrl, 'active' => false, 'icon' => 'clock'],
            ['label' => 'Settings', 'url' => $settingsUrl, 'active' => false, 'icon' => 'cog'],
        ];

        $statusMeta = [
            'pending' => ['label' => 'Pending', 'pill' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-100', 'dot' => '#f59e0b'],
            'confirmed' => ['label' => 'Confirmed', 'pill' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-100', 'dot' => '#0ea5e9'],
            'completed' => ['label' => 'Completed', 'pill' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100', 'dot' => '#10b981'],
            'cancelled' => ['label' => 'Cancelled', 'pill' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-100', 'dot' => '#fb7185'],
        ];

        $donutOrder = ['completed', 'confirmed', 'pending', 'cancelled'];
        $donutRadius = 38;
        $donutCircumference = 2 * pi() * $donutRadius;
        $donutSegments = [];
        $donutOffset = 0;
        $donutTotal = (int) $bookingSegmentationTotal;

        foreach ($donutOrder as $key) {
            $value = (int) ($bookingSegmentation[$key] ?? 0);
            $ratio = $donutTotal > 0 ? ($value / $donutTotal) : 0;
            $dash = $ratio * $donutCircumference;
            $donutSegments[] = [
                'key' => $key,
                'value' => $value,
                'ratio' => $ratio,
                'dash' => $dash,
                'offset' => $donutOffset,
            ];
            $donutOffset += $dash;
        }

        $trendLabelsSafe = is_array($trendLabels) ? $trendLabels : [];
        $trendValuesSafe = is_array($trendValues) ? array_map('intval', $trendValues) : [];
        $trendCount = max(count($trendValuesSafe), 1);
        $trendValueMax = max($trendValuesSafe ?: [0]);
        $trendTickCount = 4;
        $trendAxisStep = max(1, (int) ceil($trendValueMax / $trendTickCount));
        $trendAxisMax = max(4, $trendAxisStep * $trendTickCount);

        $trendW = 640;
        $trendH = 240;
        $trendPadL = 44;
        $trendPadR = 18;
        $trendPadT = 18;
        $trendPadB = 34;
        $trendPlotW = $trendW - $trendPadL - $trendPadR;
        $trendPlotH = $trendH - $trendPadT - $trendPadB;
        $trendBottomY = $trendPadT + $trendPlotH;

        $trendPoints = [];
        for ($i = 0; $i < count($trendValuesSafe); $i++) {
            $x = $trendPadL + (count($trendValuesSafe) === 1 ? 0 : ($i / (count($trendValuesSafe) - 1)) * $trendPlotW);
            $y = $trendPadT + (1 - ($trendValuesSafe[$i] / $trendAxisMax)) * $trendPlotH;
            $trendPoints[] = ['x' => $x, 'y' => $y];
        }

        $trendLinePath = '';
        $trendAreaPath = '';
        if (count($trendPoints) > 0) {
            $trendLinePath = 'M ' . $trendPoints[0]['x'] . ' ' . $trendPoints[0]['y'];
            for ($i = 1; $i < count($trendPoints); $i++) {
                $trendLinePath .= ' L ' . $trendPoints[$i]['x'] . ' ' . $trendPoints[$i]['y'];
            }
            $trendAreaPath = $trendLinePath . ' L ' . $trendPoints[count($trendPoints) - 1]['x'] . ' ' . $trendBottomY . ' L ' . $trendPoints[0]['x'] . ' ' . $trendBottomY . ' Z';
        }

        $serviceLabelsSafe = is_array($serviceTrendLabels) ? $serviceTrendLabels : [];
        $serviceValuesSafe = is_array($serviceTrendValues) ? array_map('intval', $serviceTrendValues) : [];
        $serviceValueMax = max($serviceValuesSafe ?: [0]);
        $serviceTickCount = 4;
        $serviceAxisStep = max(1, (int) ceil($serviceValueMax / $serviceTickCount));
        $serviceAxisMax = max(4, $serviceAxisStep * $serviceTickCount);

        $barW = 640;
        $barH = 240;
        $barPadL = 44;
        $barPadR = 18;
        $barPadT = 18;
        $barPadB = 34;
        $barPlotW = $barW - $barPadL - $barPadR;
        $barPlotH = $barH - $barPadT - $barPadB;
        $barBottomY = $barPadT + $barPlotH;

        $barCount = max(count($serviceValuesSafe), 1);
        $barGap = 14;
        $barWidth = ($barPlotW - ($barGap * ($barCount - 1))) / $barCount;
        $barHighlightIndex = min(3, $barCount - 1);
    @endphp

    <div class="relative w-full" wire:poll.15s>
        <div class="relative mx-auto w-full z-10">
            <div class="w-full relative overflow-visible">

                <div class="w-full">
                    <section class="py-4 md:py-6 dash-main relative z-10">
                        <div class="dash-topbar relative z-50 mb-6" style="display:flex;align-items:center;justify-content:space-between;padding:0 0.75rem;">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Hello,</div>
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $admin?->name ?? 'Admin' }}</div>
                            </div>
                            
                            <div class="dash-actions flex items-center gap-2 relative z-50">
                                
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 relative z-0" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                            <div class="rb-kpi rb-kpi--blue p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M7 7h10M7 12h10M7 17h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" opacity=".65"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Total Services</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalServices) }}</div>
                                    </div>
                                    <svg width="112" height="80" class="-mr-2 shrink-0" viewBox="0 0 96 64" fill="none">
                                        <path d="M6 46c8-10 16-7 24-18 8-11 16-9 24 3 8 12 16 3 18-2" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" opacity=".3"/>
                                        <path d="M6 54c8-10 16-7 24-18 8-11 16-9 24 3 8 12 16 3 18-2" stroke="#3b82f6" stroke-width="3" stroke-linecap="round"/>
                                        <circle cx="72" cy="40" r="4" fill="#3b82f6"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="rb-kpi rb-kpi--emerald p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M7 4v3M17 4v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M4 8h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".7"/>
                                                <path d="M6 6h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Total Bookings</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($monthlyBookings) }}</div>
                                    </div>
                                    <svg width="112" height="80" class="-mr-2 shrink-0" viewBox="0 0 96 64" fill="none">
                                        <rect x="8" y="34" width="10" height="22" rx="4" fill="#10b981" opacity=".2"/>
                                        <rect x="24" y="26" width="10" height="30" rx="4" fill="#10b981" opacity=".35"/>
                                        <rect x="40" y="16" width="10" height="40" rx="4" fill="#10b981" opacity=".55"/>
                                        <rect x="56" y="10" width="10" height="46" rx="4" fill="#10b981"/>
                                        <rect x="72" y="24" width="10" height="32" rx="4" fill="#10b981" opacity=".35"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="rb-kpi rb-kpi--violet p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="2"/>
                                                <path d="M4 20a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".8"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Total Therapists</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalTherapists) }}</div>
                                    </div>
                                    <svg width="112" height="80" class="-mr-2 shrink-0" viewBox="0 0 96 64" fill="none">
                                        <path d="M6 36c10-18 18 10 30-8s18 16 30-6 18 10 24-2" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round" opacity=".25"/>
                                        <path d="M6 46c10-18 18 10 30-8s18 16 30-6 18 10 24-2" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="rb-kpi rb-kpi--sky p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="2"/>
                                                <path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".8"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalClients) }}</div>
                                    </div>
                                    <svg width="112" height="80" class="-mr-2 shrink-0" viewBox="0 0 96 64" fill="none">
                                        <path d="M8 48c10-6 16-2 24-12s16-14 24-8 16 22 32-6" stroke="#0ea5e9" stroke-width="3" stroke-linecap="round"/>
                                        <path d="M8 56c10-6 16-2 24-12s16-14 24-8 16 22 32-6" stroke="#0ea5e9" stroke-width="3" stroke-linecap="round" opacity=".25"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-white/10">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Booking Trend</div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Last 6 months</div>
                                </div>

                                <div class="mt-4">
                                    <svg viewBox="0 0 {{ $trendW }} {{ $trendH }}" class="w-full">
                                        <defs>
                                            <linearGradient id="rbTrendFill" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25"/>
                                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                                            </linearGradient>
                                        </defs>

                                        @for ($i = 0; $i <= 4; $i++)
                                            @php
                                                $y = $trendPadT + ($trendPlotH / 4) * $i;
                                            @endphp
                                            <line x1="{{ $trendPadL }}" y1="{{ $y }}" x2="{{ $trendW - $trendPadR }}" y2="{{ $y }}" stroke="currentColor" class="text-gray-100 dark:text-gray-700/50" stroke-width="2"/>
                                        @endfor

                                        @if ($trendAreaPath)
                                            <path d="{{ $trendAreaPath }}" fill="url(#rbTrendFill)"></path>
                                        @endif

                                        @if ($trendLinePath)
                                            <path d="{{ $trendLinePath }}" fill="none" stroke="#3b82f6" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
                                        @endif

                                        @foreach ($trendPoints as $pt)
                                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4" fill="#3b82f6"></circle>
                                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="9" fill="#3b82f6" opacity=".08"></circle>
                                        @endforeach

                                        @foreach ($trendLabelsSafe as $i => $label)
                                            @php
                                                $x = $trendPadL + (count($trendLabelsSafe) === 1 ? 0 : ($i / (count($trendLabelsSafe) - 1)) * $trendPlotW);
                                            @endphp
                                            <text x="{{ $x }}" y="{{ $trendH - 10 }}" text-anchor="middle" font-size="12" fill="currentColor" class="text-gray-500 dark:text-gray-400 font-semibold">{{ $label }}</text>
                                        @endforeach

                                        @for ($i = 0; $i <= 4; $i++)
                                            @php
                                                $y = $trendPadT + ($trendPlotH / 4) * $i;
                                                $val = $trendAxisMax - ($trendAxisStep * $i);
                                                $valStr = $val >= 1000 ? round($val / 1000, 1) . 'k' : (string) $val;
                                            @endphp
                                            <text x="{{ $trendPadL - 10 }}" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="currentColor" class="text-gray-400 dark:text-gray-500">{{ $valStr }}</text>
                                        @endfor
                                    </svg>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-white/10">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Services-wise Bookings</div>
                                        <div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $topServiceName }}</div>
                                    </div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Last 6 months</div>
                                </div>

                                <div class="mt-4">
                                    <svg viewBox="0 0 {{ $barW }} {{ $barH }}" class="w-full">
                                        <defs>
                                            <pattern id="rbStripe" patternUnits="userSpaceOnUse" width="10" height="10" patternTransform="rotate(45)">
                                                <rect width="10" height="10" fill="currentColor" class="text-blue-100 dark:text-blue-900/20"></rect>
                                                <rect width="4" height="10" fill="currentColor" class="text-blue-300 dark:text-blue-800/40"></rect>
                                            </pattern>
                                        </defs>

                                        @for ($i = 0; $i <= 4; $i++)
                                            @php
                                                $y = $barPadT + ($barPlotH / 4) * $i;
                                            @endphp
                                            <line x1="{{ $barPadL }}" y1="{{ $y }}" x2="{{ $barW - $barPadR }}" y2="{{ $y }}" stroke="currentColor" class="text-gray-100 dark:text-gray-700/50" stroke-width="2"/>
                                        @endfor

                                        @for ($i = 0; $i < count($serviceValuesSafe); $i++)
                                            @php
                                                $value = $serviceValuesSafe[$i];
                                                $h = ($value / $serviceAxisMax) * $barPlotH;
                                                $x = $barPadL + ($i * ($barWidth + $barGap));
                                                $y = $barBottomY - $h;
                                                $isHighlight = $i === $barHighlightIndex;
                                            @endphp
                                            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ $h }}" rx="14"
                                                  fill="#3b82f6" opacity="{{ $isHighlight ? '1' : '0.4' }}"></rect>
                                        @endfor

                                        @for ($i = 0; $i <= 4; $i++)
                                            @php
                                                $y = $barPadT + ($barPlotH / 4) * $i;
                                                $val = $serviceAxisMax - ($serviceAxisStep * $i);
                                                $valStr = $val >= 1000 ? round($val / 1000, 1) . 'k' : (string) $val;
                                            @endphp
                                            <text x="{{ $barPadL - 10 }}" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="currentColor" class="text-gray-400 dark:text-gray-500">{{ $valStr }}</text>
                                        @endfor

                                        @foreach ($serviceLabelsSafe as $i => $label)
                                            @php
                                                $x = $barPadL + ($i * ($barWidth + $barGap)) + ($barWidth / 2);
                                            @endphp
                                            <text x="{{ $x }}" y="{{ $barH - 10 }}" text-anchor="middle" font-size="12" fill="currentColor" class="text-gray-500 dark:text-gray-400 font-semibold">{{ $label }}</text>
                                        @endforeach
                                    </svg>
                                </div>
                            </div>
                        </div>

                        
                        <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div class="dash-panel dash-panel--seg bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm p-6 flex flex-col">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-bold text-sm text-gray-900 dark:text-white">Booking Segmentation</div>
                                    <div class="font-bold text-xs text-gray-500 dark:text-gray-400">This month</div>
                                </div>

                                <div class="mt-5 flex flex-col sm:flex-row items-center justify-center gap-8 flex-1">
                                    <div class="relative flex items-center justify-center">
                                        <svg width="180" height="180" viewBox="0 0 120 120">
                                            <circle cx="60" cy="60" r="{{ $donutRadius }}" stroke="#e8eef7" stroke-width="12" fill="none" />
                                            @foreach ($donutSegments as $seg)
                                                @php
                                                    $key = $seg['key'];
                                                    $meta = $statusMeta[$key] ?? $statusMeta['pending'];
                                                    $color = $meta['dot'];
                                                @endphp
                                                <circle
                                                    cx="60"
                                                    cy="60"
                                                    r="{{ $donutRadius }}"
                                                    stroke="{{ $color }}"
                                                    stroke-width="12"
                                                    stroke-linecap="round"
                                                    fill="none"
                                                    stroke-dasharray="{{ $seg['dash'] }} {{ max($donutCircumference - $seg['dash'], 0.0001) }}"
                                                    stroke-dashoffset="{{ -$seg['offset'] }}"
                                                    transform="rotate(-90 60 60)"
                                                />
                                            @endforeach
                                        </svg>
                                        <div class="absolute text-center">
                                            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $completedPercent }}%</div>
                                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Completed</div>
                                        </div>
                                    </div>

                                    <div class="space-y-4 w-full sm:w-[200px]">
                                        @foreach ($donutOrder as $key)
                                            @php
                                                $meta = $statusMeta[$key] ?? $statusMeta['pending'];
                                                $value = (int) ($bookingSegmentation[$key] ?? 0);
                                                $pct = $donutTotal > 0 ? (int) round(($value / $donutTotal) * 100) : 0;
                                            @endphp
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="block h-3 w-3 flex-shrink-0 rounded-full" style="background-color: {{ $meta['dot'] }}"></span>
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $meta['label'] }}</span>
                                                </div>
                                                <div class="flex items-baseline gap-3">
                                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $pct }}%</span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 w-4 text-right">{{ number_format($value) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="dash-panel dash-panel--recent bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm p-6 flex flex-col">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-bold text-sm text-gray-900 dark:text-white">Today's Bookings</div>
                                    <a href="{{ $bookingsUrl }}" wire:navigate class="font-bold text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 hover:underline">View all</a>
                                </div>
                                <div class="mt-4 space-y-3 flex-1">
                                    @if ($todaysBookings && $todaysBookings->isNotEmpty())
                                        @foreach ($todaysBookings as $booking)
                                            @php
                                                $statusKey = $booking->status ?: 'pending';
                                                $meta = $statusMeta[$statusKey] ?? $statusMeta['pending'];
                                                $name = $booking->user?->name ?? 'Client';
                                                $initial = strtoupper(substr($name, 0, 1));
                                                $sub = current(array_filter([$booking->service?->name, $booking->therapist?->name])) ?: 'Service';
                                            @endphp
                                            <a href="{{ route('filament.admin.resources.bookings.edit', ['record' => $booking->id]) }}" wire:navigate class="flex items-center gap-3 rounded-2xl bg-gray-50 dark:bg-white/5 px-4 py-3 ring-1 ring-gray-900/5 dark:ring-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition cursor-pointer">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white dark:bg-white/10 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm">
                                                    {{ $initial }}
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $name }}</div>
                                                    <div class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $sub }}</div>
                                                </div>
                                                <div class="flex flex-col items-end gap-1">
                                                    <span class="{{ $meta['pill'] }} inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold dark:bg-opacity-20 dark:border-opacity-20">
                                                        {{ $meta['label'] }}
                                                    </span>
                                                    <div class="text-[11px] font-medium text-gray-500 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/80 px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400 ring-1 ring-gray-900/5 dark:ring-white/5">
                                            No bookings yet.
                                        </div>
                                    @endif
                                </div>
                            </div>


                        <!-- graphs moved to the top grid -->
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
