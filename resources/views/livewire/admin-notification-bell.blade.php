<div class="dash-actions flex items-center gap-2 relative z-50" wire:poll.15s>
    <div x-data="{ open: false }" class="relative z-50">
        <button @click="open = !open" @click.away="open = false" type="button" class="dash-icon-btn dash-icon-btn--notify relative {{ $notificationCount > 0 ? 'text-rose-600 ring-rose-200 dark:ring-rose-900 bg-rose-50 dark:bg-rose-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}" aria-label="Notifications" style="display: flex; align-items: center; justify-content: center; height: 2.25rem; width: 2.25rem; border-radius: 0.5rem; transition: all 0.2s;">
            @if($notificationCount > 0)
                <div class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-gray-900 z-10">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</div>
            @endif
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M13.7 21a2 2 0 0 1-3.4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        <div x-show="open" style="display: none;"
             x-transition:enter="transition ease-out duration-150" 
             x-transition:enter-start="transform opacity-0 scale-95 translate-y-2" 
             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-100" 
             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="transform opacity-0 scale-95 translate-y-2" 
             class="absolute top-[calc(100%+0.75rem)] w-[360px] max-w-[calc(100vw-2rem)] rounded-2xl bg-white dark:bg-gray-900 p-2 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.2)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] ring-1 ring-gray-900/10 dark:ring-white/10 z-[1000] isolate origin-top right-0 sm:right-auto sm:left-1/2 sm:-translate-x-[85%] md:-translate-x-[90%] mt-2">
            
            <div class="mb-2 flex items-center justify-between px-3 pt-2">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Notifications</h3>
                @if($notificationCount > 0)
                    <span class="rounded-full bg-primary-50 dark:bg-primary-500/10 px-2 py-0.5 text-[11px] font-bold text-primary-600 dark:text-primary-400">{{ $notificationCount }} New</span>
                @endif
            </div>
            
            <div class="max-h-[380px] overflow-y-auto space-y-1 p-2 custom-scrollbar">
                @if ($notificationBookings && count($notificationBookings) > 0)
                    @foreach ($notificationBookings as $notif)
                        @php
                            $notifStatus = $notif['status'] ?: 'pending';
                            $statusMeta = [
                                'pending' => ['label' => 'Pending', 'pill' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 ring-1 ring-amber-100 dark:ring-amber-500/20', 'dot' => '#f59e0b'],
                                'confirmed' => ['label' => 'Confirmed', 'pill' => 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 ring-1 ring-sky-100 dark:ring-sky-500/20', 'dot' => '#0ea5e9'],
                                'completed' => ['label' => 'Completed', 'pill' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-100 dark:ring-emerald-500/20', 'dot' => '#10b981'],
                                'cancelled' => ['label' => 'Cancelled', 'pill' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 ring-1 ring-rose-100 dark:ring-rose-500/20', 'dot' => '#fb7185'],
                            ];
                            $notifMeta = $statusMeta[$notifStatus] ?? $statusMeta['pending'];
                            $notifName = $notif['user']['name'] ?? 'Client';
                            $notifInitial = strtoupper(substr($notifName, 0, 1));
                        @endphp
                        <a href="{{ route('filament.admin.resources.bookings.edit', ['record' => $notif['id']]) }}" class="flex items-start gap-4 rounded-xl p-3 hover:bg-gray-50 dark:hover:bg-white/5 transition border border-transparent hover:border-gray-100 dark:hover:border-white/10 group">
                            <div class="mt-1 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-white/5 text-sm font-bold text-gray-600 dark:text-gray-300 shadow-inner group-hover:bg-white dark:group-hover:bg-white/10 group-hover:shadow-sm transition">
                                {{ $notifInitial }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $notifName }} 
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">booked a service</div>
                                <div class="mt-2.5 flex items-center justify-between">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wide" style="background-color: {{ $notifMeta['dot'] }}15; color: {{ $notifMeta['dot'] }}; border: 1px solid {{ $notifMeta['dot'] }}30;">
                                        {{ $notifMeta['label'] }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500 pl-2 text-right">{{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                        You're all caught up!
                    </div>
                @endif
            </div>
            
            @if($notificationCount > 10)
                <div class="mt-1 border-t border-gray-100 dark:border-white/10 p-2 text-center">
                    <a href="{{ route('filament.admin.resources.bookings.index') }}" class="block rounded-lg px-4 py-2 text-xs font-semibold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition">View all bookings</a>
                </div>
            @endif
        </div>
    </div>
</div>
