<div>
    <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden mb-6">
        <div class="relative h-32 bg-indigo-600">
            <div class="absolute -bottom-12 left-8">
                <img class="rounded-full ring-4 ring-white dark:ring-gray-800 bg-white dark:bg-gray-800" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4f46e5&color=fff" alt="{{ $user->name }}" width="100" height="100" style="width: 100px; height: 100px; object-fit: cover;">
            </div>
        </div>
        <div class="pt-16 pb-6 px-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Member since {{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Personal Information Display -->
        <div class="lg:col-span-1 space-y-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact Info</h3>
                <dl class="mt-3 space-y-3">
                    <div>
                        <dt class="text-xs text-gray-400 dark:text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900 dark:text-white break-all">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 dark:text-gray-500">Phone</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $user->phone_number ?? 'Not provided' }}</dd>
                    </div>
                </dl>
            </div>
            
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Personal Details</h3>
                <dl class="mt-3 space-y-3">
                    <div>
                        <dt class="text-xs text-gray-400 dark:text-gray-500">Age</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $user->age ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 dark:text-gray-500">Gender</dt>
                        <dd class="text-sm text-gray-900 dark:text-white capitalize">{{ $user->gender ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 dark:text-gray-500">Status</dt>
                        <dd class="text-sm">
                            @if ($user->hasVerifiedEmail())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200">Verified</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200">Unverified</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Forms -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Edit Profile Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Profile</h2>
                
                @if (session('status'))
                    <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/50 border border-green-100 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                <form wire:submit="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                            <input wire:model="name" id="name" type="text" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                            <input wire:model="email" id="email" type="email" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                            <input wire:model="phone_number" id="phone_number" type="tel" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            @error('phone_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Age</label>
                            <input wire:model="age" id="age" type="number" min="1" max="120" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            @error('age') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                            <select wire:model="gender" id="gender" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Update Password Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Update Password</h2>

                @if (session('password_status'))
                    <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/50 border border-green-100 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-200">
                        {{ session('password_status') }}
                    </div>
                @endif

                <form wire:submit="updatePassword" class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                        <input wire:model="current_password" id="current_password" type="password" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input wire:model="password" id="password" type="password" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input wire:model="password_confirmation" id="password_confirmation" type="password" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                    </div>

                    <div class="pt-4 flex items-center justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
