<div>
    @php
        $inputBase = 'w-full rounded-2xl bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-950/30 dark:text-white dark:ring-white/10 dark:focus:ring-indigo-800/50';
        $labelBase = 'text-xs font-semibold text-slate-500 dark:text-slate-400';
        $cardBase = 'rounded-3xl bg-white ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/55 dark:ring-white/10';
        $subtleCard = 'rounded-3xl bg-slate-50 ring-1 ring-slate-200 dark:bg-slate-950/30 dark:ring-slate-800/70';
        $avatarUrl = null;
        if (Auth::user()->profile_picture) {
            $path = trim((string) Auth::user()->profile_picture);
            $avatarUrl = str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')
                ? $path
                : \Illuminate\Support\Str::start('media/' . ltrim($path, '/'), '/');
        }
    @endphp

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 ring-1 ring-emerald-200/70 dark:bg-emerald-900/20 dark:text-emerald-200 dark:ring-emerald-800/40">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="updateProfile" x-data="{ tab: 'personal', photoName: null, photoPreview: null }">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="{{ $subtleCard }} p-6">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative h-28 w-28 overflow-hidden rounded-3xl bg-white ring-1 ring-slate-200 shadow-sm dark:bg-slate-900/60 dark:ring-white/10">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                            @endif
                            <button type="button" x-on:click.prevent="$refs.photo.click()" class="absolute bottom-2 right-2 inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-[0_18px_40px_-26px_rgba(37,99,235,0.95)] hover:bg-indigo-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7h4l2-2h4l2 2h4v12H4V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M12 11a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>

                            <div wire:loading wire:target="photo" class="absolute inset-0 flex items-center justify-center bg-white/60 backdrop-blur-sm dark:bg-slate-950/45">
                                <svg class="h-7 w-7 animate-spin text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                        <div class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</div>

                        <input type="file"
                               class="hidden"
                               accept="image/jpeg,image/png,image/webp"
                               wire:model.live="photo"
                               x-ref="photo"
                               x-on:change="
                                    photoName = $refs.photo.files[0]?.name || null;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL($refs.photo.files[0]);
                               " />

                        <button type="button" x-on:click.prevent="$refs.photo.click()" class="mt-5 w-full rounded-2xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900/55 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-900/70">
                            Upload New Photo
                        </button>

                        @error('photo') <div class="mt-2 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                    </div>

                    <div class="mt-6 space-y-2">
                        <button type="button" x-on:click="tab='personal'" class="w-full rounded-2xl px-4 py-3 text-left text-sm font-semibold ring-1 transition
                            " :class="tab==='personal' ? 'bg-indigo-600 text-white ring-indigo-500 shadow-sm' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900/55 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-900/70'">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Personal Info
                            </span>
                        </button>

                        <button type="button" x-on:click="tab='contact'" class="w-full rounded-2xl px-4 py-3 text-left text-sm font-semibold ring-1 transition
                            " :class="tab==='contact' ? 'bg-indigo-600 text-white ring-indigo-500 shadow-sm' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900/55 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-900/70'">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Contact Information
                            </span>
                        </button>

                        <!-- Preference tab removed -->
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="{{ $subtleCard }} p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xl font-semibold text-slate-900 dark:text-white" x-text="tab==='personal' ? 'Personal Information' : 'Contact Information'"></div>
                            <div class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400" x-text="tab==='personal' ? 'Update your personal details and bio' : 'Manage your contact details and address'"></div>
                        </div>
                    </div>

                    <div class="mt-6" x-show="tab==='personal'" x-cloak>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="{{ $labelBase }}">First Name</label>
                                <input wire:model="first_name" type="text" class="{{ $inputBase }}">
                                @error('first_name') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelBase }}">Last Name</label>
                                <input wire:model="last_name" type="text" class="{{ $inputBase }}">
                                @error('last_name') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelBase }}">Birth Date</label>
                                <input wire:model="birth_date" type="date" class="{{ $inputBase }}">
                                @error('birth_date') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelBase }}">Gender</label>
                                <select wire:model="gender" class="{{ $inputBase }}">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('gender') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="{{ $labelBase }}">Bio</label>
                                <textarea wire:model="bio" rows="5" class="{{ $inputBase }}"></textarea>
                                <div class="mt-1 text-xs font-semibold text-slate-400 dark:text-slate-500">Brief description for your profile.</div>
                                @error('bio') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6" x-show="tab==='contact'" x-cloak>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="{{ $labelBase }}">Email Address</label>
                                <input wire:model="email" type="email" class="{{ $inputBase }}">
                                @error('email') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="{{ $labelBase }}">Phone Number</label>
                                <input wire:model="phone_number" type="tel" class="{{ $inputBase }}">
                                @error('phone_number') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="{{ $labelBase }}">Street Address</label>
                                <input wire:model="street_address" type="text" class="{{ $inputBase }}">
                                @error('street_address') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelBase }}">City</label>
                                <input wire:model="city" type="text" class="{{ $inputBase }}">
                                @error('city') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelBase }}">State/Province</label>
                                <input wire:model="state_province" type="text" class="{{ $inputBase }}">
                                @error('state_province') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelBase }}">ZIP/Postal Code</label>
                                <input wire:model="zip_code" type="text" class="{{ $inputBase }}">
                                @error('zip_code') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelBase }}">Country</label>
                                <input wire:model="country" type="text" class="{{ $inputBase }}">
                                @error('country') <div class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Preference panel removed -->

                    <div class="mt-8 flex items-center justify-end gap-3">
                        <button type="button"
                                x-on:click="photoPreview = null; photoName = null; if ($refs.photo) { $refs.photo.value = null }"
                                wire:click="resetForm"
                                class="rounded-2xl bg-slate-200 px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-300 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            Cancel
                        </button>
                        <button type="submit"
                                class="rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-[0_18px_40px_-26px_rgba(37,99,235,0.95)] hover:bg-indigo-700">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
