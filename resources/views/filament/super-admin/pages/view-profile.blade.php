<x-filament-panels::page>
    @php
        $avatarUrl = $this->user->profile_picture ? Storage::disk('public')->url($this->user->profile_picture) : null;
        $memberSince = $this->user->created_at ? $this->user->created_at->format('F Y') : 'N/A';
        $editProfileUrl = \App\Filament\SuperAdmin\Pages\EditProfile::getUrl();
    @endphp

    <div class="rb-admin-profile mx-auto w-full max-w-5xl overflow-hidden">
        <div class="rb-admin-profile__hero relative px-6 py-6 sm:px-10">
            <div class="rb-admin-profile__hero-grid absolute inset-0"></div>
            <div class="rb-admin-profile__hero-blob rb-admin-profile__hero-blob--tr absolute -right-24 -top-24"></div>
            <div class="rb-admin-profile__hero-blob rb-admin-profile__hero-blob--bl absolute -left-24 -bottom-28"></div>

            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-5">
                    <div class="rb-admin-profile__avatar relative h-20 w-20 overflow-hidden">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $this->user->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            <div class="rb-admin-profile__avatar-fallback flex h-full w-full items-center justify-center">
                                {{ strtoupper(substr((string) $this->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="rb-admin-profile__avatar-check absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                    <div class="min-w-0">
                        <div class="rb-admin-profile__name truncate">{{ $this->user->name }}</div>
                        <div class="rb-admin-profile__meta mt-1 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M8 7V3m8 4V3m-9 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Member since {{ $memberSince }}
                            </span>
                            <span class="rb-admin-profile__verified inline-flex items-center gap-1.5">
                                <span class="rb-admin-profile__verified-dot h-2 w-2 rounded-full"></span>
                                Super Admin
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ $editProfileUrl }}"
                        class="rb-admin-profile__edit inline-flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="rb-admin-profile__body p-6 sm:p-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rb-admin-profile__card lg:col-span-2 p-6">
                    <div class="rb-admin-profile__card-title flex items-center gap-2">
                        <svg class="h-5 w-5 rb-admin-profile__card-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        About
                    </div>
                    <div class="rb-admin-profile__muted mt-3 text-sm font-medium">
                        No bio provided.
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rb-admin-profile__chip flex items-center gap-3 px-4 py-3">
                            <div class="rb-admin-profile__chip-icon rb-admin-profile__chip-icon--indigo flex h-9 w-9 items-center justify-center">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="rb-admin-profile__chip-label text-[11px] font-semibold">Email</div>
                                <div class="rb-admin-profile__chip-value truncate text-sm font-semibold">{{ $this->user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rb-admin-profile__card p-6">
                    <div class="rb-admin-profile__card-title flex items-center gap-2">
                        <svg class="h-5 w-5 rb-admin-profile__card-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M12 1.5 21 5v6c0 5-3.5 9.5-9 11-5.5-1.5-9-6-9-11V5l9-3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                        Account Details
                    </div>

                    <div class="rb-admin-profile__rows mt-4 text-sm">
                        <div class="rb-admin-profile__row flex items-center justify-between py-3">
                            <div class="rb-admin-profile__row-label text-xs font-semibold">Role</div>
                            <div class="rb-admin-profile__row-value font-semibold">Super Admin</div>
                        </div>
                        <div class="rb-admin-profile__row flex items-center justify-between py-3">
                            <div class="rb-admin-profile__row-label text-xs font-semibold">Status</div>
                            <span class="rb-admin-profile__status inline-flex items-center gap-2">
                                <span class="rb-admin-profile__status-dot h-2 w-2 rounded-full"></span>
                                Verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
