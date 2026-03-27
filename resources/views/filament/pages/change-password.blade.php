<x-filament-panels::page>
    @php
        $viewProfileUrl = \App\Filament\Pages\ViewProfile::getUrl();
    @endphp

    <div class="mx-auto w-full max-w-5xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900 sm:p-8">
            <div class="mb-6">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">Security Settings</div>
                <div class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Manage your password and account security preferences</div>
            </div>

            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-900/25 dark:text-indigo-200 dark:ring-indigo-800/50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M12 1.5 21 5v6c0 5-3.5 9.5-9 11-5.5-1.5-9-6-9-11V5l9-3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Account Security</div>
                        <div class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Keep your password secure and change it regularly.</div>
                    </div>
                </div>
            </div>

            <form wire:submit="submit" class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800">
                    {{ $this->form }}
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ $viewProfileUrl }}" class="rb-admin-edit2__btn rb-admin-edit2__btn--ghost">Cancel</a>
                    <button type="submit" class="rb-admin-edit2__btn rb-admin-edit2__btn--primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
