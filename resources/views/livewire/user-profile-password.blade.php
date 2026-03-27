<div>
    @php
        $inputBase = 'w-full rounded-2xl bg-white/5 px-4 py-2.5 pr-12 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-950/30 dark:text-white dark:ring-white/10 dark:focus:ring-indigo-800/50';
        $labelBase = 'text-xs font-semibold text-slate-500 dark:text-slate-400';
        $subtleCard = 'rounded-3xl bg-slate-50 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800/70';
    @endphp

    @if (session('password_status'))
        <div class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 ring-1 ring-emerald-200/70 dark:bg-emerald-900/20 dark:text-emerald-200 dark:ring-emerald-800/40">
            {{ session('password_status') }}
        </div>
    @endif

    <div class="{{ $subtleCard }} p-6 sm:p-8" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-xl font-semibold text-slate-900 dark:text-white">Security Settings</div>
                <div class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Manage your password and account security preferences</div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl bg-white px-4 py-3 ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/55 dark:ring-white/10">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-900/25 dark:text-indigo-200 dark:ring-indigo-800/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M12 1.5 21 5v6c0 5-3.5 9.5-9 11-5.5-1.5-9-6-9-11V5l9-3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-white">Account Security</div>
                    <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        Your account is protected with encryption. Keep your password secure and change it regularly.
                    </div>
                </div>
            </div>
        </div>

        <form wire:submit="updatePassword" class="mt-6 space-y-5">
            <div>
                <label for="current_password" class="{{ $labelBase }}">Current Password <span class="text-rose-500">*</span></label>
                <div class="relative mt-2">
                    <input wire:model="current_password"
                           id="current_password"
                           :type="showCurrent ? 'text' : 'password'"
                           class="{{ $inputBase }}"
                           placeholder="Enter Current Password">
                    <button type="button"
                            x-on:click="showCurrent = !showCurrent"
                            class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            aria-label="Toggle current password visibility">
                        <svg x-show="!showCurrent" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <svg x-show="showCurrent" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10.6 10.6A2.5 2.5 0 0 0 12 15a2.5 2.5 0 0 0 2.4-3.4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M6.7 6.7C4.1 8.4 2 12 2 12s3.5 7 10 7c1.6 0 3-.3 4.2-.8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9.2 4.2A10.5 10.5 0 0 1 12 4c6.5 0 10 8 10 8s-.9 2-2.6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                @error('current_password') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="password" class="{{ $labelBase }}">Password <span class="text-rose-500">*</span></label>
                <div class="relative mt-2">
                    <input wire:model="password"
                           id="password"
                           :type="showNew ? 'text' : 'password'"
                           class="{{ $inputBase }}"
                           placeholder="Enter New Password">
                    <button type="button"
                            x-on:click="showNew = !showNew"
                            class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            aria-label="Toggle new password visibility">
                        <svg x-show="!showNew" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <svg x-show="showNew" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10.6 10.6A2.5 2.5 0 0 0 12 15a2.5 2.5 0 0 0 2.4-3.4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M6.7 6.7C4.1 8.4 2 12 2 12s3.5 7 10 7c1.6 0 3-.3 4.2-.8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9.2 4.2A10.5 10.5 0 0 1 12 4c6.5 0 10 8 10 8s-.9 2-2.6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-2 space-y-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                        At least 8 characters
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                        One uppercase letter
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                        One number
                    </div>
                </div>

                @error('password') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="{{ $labelBase }}">Confirm New Password <span class="text-rose-500">*</span></label>
                <div class="relative mt-2">
                    <input wire:model="password_confirmation"
                           id="password_confirmation"
                           :type="showConfirm ? 'text' : 'password'"
                           class="{{ $inputBase }}"
                           placeholder="Confirm New Password">
                    <button type="button"
                            x-on:click="showConfirm = !showConfirm"
                            class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            aria-label="Toggle confirm password visibility">
                        <svg x-show="!showConfirm" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <svg x-show="showConfirm" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10.6 10.6A2.5 2.5 0 0 0 12 15a2.5 2.5 0 0 0 2.4-3.4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M6.7 6.7C4.1 8.4 2 12 2 12s3.5 7 10 7c1.6 0 3-.3 4.2-.8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9.2 4.2A10.5 10.5 0 0 1 12 4c6.5 0 10 8 10 8s-.9 2-2.6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <button type="button"
                        wire:click="$set('current_password', null); $set('password', null); $set('password_confirmation', null)"
                        class="rounded-2xl bg-slate-200 px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-300 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Cancel
                </button>
                <button type="submit"
                        class="rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-[0_18px_40px_-26px_rgba(37,99,235,0.95)] hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
