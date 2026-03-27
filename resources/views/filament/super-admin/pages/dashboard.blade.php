<x-filament-panels::page>
    @php
        $superAdmin = Auth::guard('super_admin')->user();

        // Line Chart Calculations (User Registrations)
        $trendLabelsSafe = is_array($trendLabels) ? $trendLabels : [];
        $trendValuesSafe = is_array($trendValues) ? array_map('intval', $trendValues) : [];
        $trendMax = max($trendValuesSafe ?: [0]);
        $trendMax = max($trendMax, 1);

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
            $y = $trendPadT + (1 - ($trendValuesSafe[$i] / $trendMax)) * $trendPlotH;
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

        // Bar Chart Calculations (Total Admins)
        $barLabelsSafe = is_array($barTrendLabels) ? $barTrendLabels : [];
        $barValuesSafe = is_array($barTrendValues) ? array_map('intval', $barTrendValues) : [];
        $barValueMax = max($barValuesSafe ?: [0]);
        $barTickCount = 4;
        $barAxisStep = max(1, (int) ceil($barValueMax / $barTickCount));
        $barAxisMax = max(4, $barAxisStep * $barTickCount);

        $barW = 640;
        $barH = 240;
        $barPadL = 44;
        $barPadR = 18;
        $barPadT = 18;
        $barPadB = 34;
        $barPlotW = $barW - $barPadL - $barPadR;
        $barPlotH = $barH - $barPadT - $barPadB;
        $barBottomY = $barPadT + $barPlotH;

        $barCount = max(count($barValuesSafe), 1);
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
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $superAdmin?->name ?? 'Super Admin' }}</div>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 relative z-0">
                            <div class="rb-kpi rb-kpi--blue p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Total Admins</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalAdmins) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="rb-kpi rb-kpi--emerald p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Active Admins</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($activeAdmins) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="rb-kpi rb-kpi--violet p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalUsers) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="rb-kpi rb-kpi--amber p-6 flex flex-col justify-between bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke="currentColor"/>
                                            </svg>
                                        </div>
                                        <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Total Bookings</div>
                                        <div class="mt-1.5 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalBookings) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Subscribers</div>
                                <div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">Current month</div>
                                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($currentMonthSubscribers) }}</div>
                            </div>

                            <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</div>
                                <div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">Current month</div>
                                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">₱{{ number_format($currentMonthRevenue) }}</div>
                            </div>

                            <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</div>
                                <div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">Overall</div>
                                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">₱{{ number_format($totalRevenue) }}</div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 shadow-sm p-3">
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                <div class="flex items-center justify-between rounded-xl bg-red-50 dark:bg-red-500/10 px-4 py-2 ring-1 ring-red-600/20 dark:ring-red-500/20">
                                    <div class="text-sm font-semibold text-red-700 dark:text-red-400">Expired</div>
                                    <div class="text-sm font-extrabold text-red-700 dark:text-red-400">{{ number_format($expiredSubscriptions) }}</div>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-amber-50 dark:bg-amber-500/10 px-4 py-2 ring-1 ring-amber-600/20 dark:ring-amber-500/20">
                                    <div class="text-sm font-semibold text-amber-700 dark:text-amber-400">Upcoming (7d)</div>
                                    <div class="text-sm font-extrabold text-amber-700 dark:text-amber-400">{{ number_format($upcomingRenewals) }}</div>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-blue-50 dark:bg-blue-500/10 px-4 py-2 ring-1 ring-blue-600/20 dark:ring-blue-500/20">
                                    <div class="text-sm font-semibold text-blue-700 dark:text-blue-400">Plan</div>
                                    <div class="text-sm font-extrabold text-blue-700 dark:text-blue-400">₱24,999/month</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Line Chart (User Registrations) -->
                            <div class="rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-white/10">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">User Registrations Trend</div>
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
                                                $val = $trendMax - ($trendMax / 4) * $i;
                                                $valStr = $val >= 1000 ? round($val/1000, 1) . 'k' : round($val);
                                            @endphp
                                            <text x="{{ $trendPadL - 10 }}" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="currentColor" class="text-gray-400 dark:text-gray-500">{{ $valStr }}</text>
                                        @endfor
                                    </svg>
                                </div>
                            </div>

                            <!-- Bar Chart (Admin Trends) -->
                            <div class="rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-white/10">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Total Admins</div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Last 6 months</div>
                                </div>

                                <div class="mt-4">
                                    <svg viewBox="0 0 {{ $barW }} {{ $barH }}" class="w-full">
                                        @for ($i = 0; $i <= 4; $i++)
                                            @php
                                                $y = $barPadT + ($barPlotH / 4) * $i;
                                            @endphp
                                            <line x1="{{ $barPadL }}" y1="{{ $y }}" x2="{{ $barW - $barPadR }}" y2="{{ $y }}" stroke="currentColor" class="text-gray-100 dark:text-gray-700/50" stroke-width="2"/>
                                        @endfor

                                        @for ($i = 0; $i < count($barValuesSafe); $i++)
                                            @php
                                                $value = $barValuesSafe[$i];
                                                $h = ($value / $barAxisMax) * $barPlotH;
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
                                                $val = $barAxisMax - ($barAxisStep * $i);
                                                $valStr = $val >= 1000 ? round($val / 1000, 1) . 'k' : (string) $val;
                                            @endphp
                                            <text x="{{ $barPadL - 10 }}" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="currentColor" class="text-gray-400 dark:text-gray-500">{{ $valStr }}</text>
                                        @endfor

                                        @foreach ($barLabelsSafe as $i => $label)
                                            @php
                                                $x = $barPadL + ($i * ($barWidth + $barGap)) + ($barWidth / 2);
                                            @endphp
                                            <text x="{{ $x }}" y="{{ $barH - 10 }}" text-anchor="middle" font-size="12" fill="currentColor" class="text-gray-500 dark:text-gray-400 font-semibold">{{ $label }}</text>
                                        @endforeach
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-white/10">
                            <div class="flex items-center justify-between gap-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Monthly Revenue Breakdown</div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">PAID subscriptions</div>
                            </div>

                            <div class="mt-4 overflow-hidden rounded-xl border border-gray-100 dark:border-white/10 w-full">
                                <div class="w-full overflow-x-auto">
                                    <table class="w-full min-w-full text-left border-collapse">
                                        <thead class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10">
                                            <tr>
                                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Month</th>
                                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Subscribers</th>
                                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Profit</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-white/10 bg-white dark:bg-transparent">
                                            @forelse($monthlyRevenueBreakdown as $row)
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $row['month'] ?? '—' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-300">{{ number_format((int) ($row['subscribers'] ?? 0)) }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-300">₱{{ number_format((int) ($row['revenue'] ?? 0)) }}</div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-8 text-sm font-medium text-gray-500 dark:text-gray-400">No subscription revenue data yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            @if($expiringAdmins->isNotEmpty())
                                <div class="rounded-2xl border border-amber-200 bg-white dark:bg-gray-900 dark:border-amber-900/50 p-6 shadow-sm">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="rounded-full bg-amber-100 dark:bg-amber-500/10 p-2">
                                            <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Subscription Alerts</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Admins expiring within the next 7 days</p>
                                        </div>
                                    </div>

                                    <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-white/10 w-full">
                                        <div class="w-full overflow-x-auto">
                                            <table class="w-full min-w-full text-left border-collapse">
                                                <thead class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10">
                                                    <tr>
                                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Admin Name</th>
                                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 hidden md:table-cell">Email</th>
                                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Expiry Date</th>
                                                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 dark:divide-white/10 bg-white dark:bg-transparent">
                                                    @foreach($expiringAdmins as $admin)
                                                        @php
                                                            $expiresAt = \Carbon\Carbon::parse($admin->subscription_expires_at);
                                                            $daysLeft = now()->diffInDays($expiresAt, false);
                                                        @endphp
                                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors group">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="font-bold text-gray-900 dark:text-white">{{ $admin->name }}</div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $admin->email }}</div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-300">{{ $expiresAt->format('M j, Y') }}</div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                @if($daysLeft <= 0)
                                                                    <span class="inline-flex items-center rounded-full bg-red-50 dark:bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-700 dark:text-red-400 ring-1 ring-red-600/20 dark:ring-red-500/20">Expired</span>
                                                                @elseif($daysLeft <= 3)
                                                                    <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-400 ring-1 ring-amber-600/20 dark:ring-amber-500/20">Critical ({{ ceil($daysLeft) }}d)</span>
                                                                @else
                                                                    <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-400 ring-1 ring-blue-600/20 dark:ring-blue-500/20">Warning ({{ ceil($daysLeft) }}d)</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-2xl border border-emerald-100 dark:border-emerald-900/30 bg-emerald-50/50 dark:bg-emerald-900/10 p-6 flex items-center gap-4 shadow-sm">
                                    <div class="rounded-full bg-emerald-100 dark:bg-emerald-500/20 p-3 text-emerald-600 dark:text-emerald-400">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-emerald-900 dark:text-emerald-300">All Systems Normal</h3>
                                        <p class="text-emerald-700 dark:text-emerald-500/80">No admin accounts are currently approaching subscription expiration.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </section>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
