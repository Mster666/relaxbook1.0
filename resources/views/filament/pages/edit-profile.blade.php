<x-filament-panels::page>
    @php
        $admin = auth()->guard('admin')->user();
        $avatarUrl = $admin?->profile_picture ? Storage::disk('public')->url($admin->profile_picture) : null;
        $viewProfileUrl = \App\Filament\Pages\ViewProfile::getUrl();
    @endphp

    <form wire:submit.prevent="submit" x-data="{ tab: 'personal' }" class="rb-admin-edit2">
        <div class="rb-admin-edit2__grid">
            <div class="rb-admin-edit2__left">
                <div class="rb-admin-edit2__card rb-admin-edit2__card--subtle">
                    <div class="rb-admin-edit2__avatar-wrap">
                        <div class="rb-admin-edit2__avatar">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $admin?->name }}" class="rb-admin-edit2__avatar-img" loading="lazy" decoding="async">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode((string) ($admin?->name ?? 'Admin')) }}&background=4f46e5&color=fff" alt="{{ $admin?->name }}" class="rb-admin-edit2__avatar-img" loading="lazy" decoding="async">
                            @endif

                            <button type="button" x-on:click.prevent="$refs.photo.click()" class="rb-admin-edit2__camera">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7h4l2-2h4l2 2h4v12H4V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M12 11a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>

                            <div wire:loading wire:target="photo" class="rb-admin-edit2__photo-loading">
                                <svg class="rb-admin-edit2__spinner" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="rb-admin-edit2__name">{{ $admin?->name }}</div>
                    <div class="rb-admin-edit2__email">{{ $admin?->email }}</div>

                    <input type="file" class="rb-admin-edit2__file" wire:model.live="photo" x-ref="photo" />
                    @error('photo') <div class="rb-admin-edit2__error">{{ $message }}</div> @enderror

                    <button type="button" x-on:click.prevent="$refs.photo.click()" class="rb-admin-edit2__upload">
                        Upload New Photo
                    </button>

                    <div class="rb-admin-edit2__tabs">
                        <button type="button" x-on:click="tab='personal'" class="rb-admin-edit2__tab" :class="tab==='personal' ? 'is-active' : ''">
                            <span class="rb-admin-edit2__tab-inner">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Personal Info
                            </span>
                        </button>

                        <button type="button" x-on:click="tab='contact'" class="rb-admin-edit2__tab" :class="tab==='contact' ? 'is-active' : ''">
                            <span class="rb-admin-edit2__tab-inner">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Contact Information
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="rb-admin-edit2__right">
                <div class="rb-admin-edit2__card rb-admin-edit2__card--subtle">
                    <div class="rb-admin-edit2__header">
                        <div class="rb-admin-edit2__title" x-text="tab==='personal' ? 'Personal Information' : 'Contact Information'"></div>
                        <div class="rb-admin-edit2__subtitle" x-text="tab==='personal' ? 'Update your personal details' : 'Manage your contact details'"></div>
                    </div>

                    <div x-show="tab==='personal'" x-cloak class="rb-admin-edit2__panel">
                        <div class="rb-admin-edit2__fields">
                            <div class="rb-admin-edit2__field">
                                <label class="rb-admin-edit2__label">Name</label>
                                <input type="text" wire:model="name" class="rb-admin-edit2__input" />
                                @error('name') <div class="rb-admin-edit2__error">{{ $message }}</div> @enderror
                            </div>
                            <div class="rb-admin-edit2__field">
                                <label class="rb-admin-edit2__label">Gender</label>
                                <input type="text" wire:model="gender" class="rb-admin-edit2__input" />
                                @error('gender') <div class="rb-admin-edit2__error">{{ $message }}</div> @enderror
                            </div>
                            <div class="rb-admin-edit2__field">
                                <label class="rb-admin-edit2__label">Age</label>
                                <input type="number" wire:model="age" class="rb-admin-edit2__input" />
                                @error('age') <div class="rb-admin-edit2__error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div x-show="tab==='contact'" x-cloak class="rb-admin-edit2__panel">
                        <div class="rb-admin-edit2__fields rb-admin-edit2__fields--wide">
                            <div class="rb-admin-edit2__field">
                                <label class="rb-admin-edit2__label">Email Address</label>
                                <input type="email" wire:model="email" class="rb-admin-edit2__input" />
                                @error('email') <div class="rb-admin-edit2__error">{{ $message }}</div> @enderror
                            </div>
                            <div class="rb-admin-edit2__field">
                                <label class="rb-admin-edit2__label">Phone Number</label>
                                <input type="tel" wire:model="phone_number" class="rb-admin-edit2__input" />
                                @error('phone_number') <div class="rb-admin-edit2__error">{{ $message }}</div> @enderror
                            </div>
                            <div class="rb-admin-edit2__field" style="grid-column: 1 / -1;">
                                <label class="rb-admin-edit2__label">Company Address</label>
                                <input type="text" wire:model="company_address" class="rb-admin-edit2__input" />
                                @error('company_address') <div class="rb-admin-edit2__error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rb-admin-edit2__actions">
                        <button type="button" wire:click="resetForm" class="rb-admin-edit2__btn rb-admin-edit2__btn--ghost">Cancel</button>
                        <button type="submit" class="rb-admin-edit2__btn rb-admin-edit2__btn--primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-filament-panels::page>
