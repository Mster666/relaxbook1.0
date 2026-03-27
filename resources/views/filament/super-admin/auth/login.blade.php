    <!-- Card Container -->
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left Side (Logo & Branding) -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-rose-600 to-pink-700 p-8 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute top-10 left-10 w-32 h-32 rounded-full bg-white blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-40 h-40 rounded-full bg-orange-400 blur-3xl"></div>
            </div>

            <div class="z-10 flex flex-col items-center text-center">
                <!-- LOGO PLACEHOLDER -->
                <div class="mb-6 w-40 h-40 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border-4 border-white/30 shadow-lg overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="RelaxBook Logo" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="text-4xl font-bold hidden">SA</span>
                </div>

                <h1 class="text-3xl font-bold mb-2 tracking-wide">RelaxBook Super Admin</h1>
                
                <a href="{{ url('/') }}" class="mt-8 px-8 py-2 bg-white text-rose-700 font-bold rounded-full shadow-lg hover:bg-gray-100 transition transform hover:scale-105">
                    Go to Homepage >
                </a>
            </div>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="w-full md:w-1/2 bg-white p-8 md:p-12">
            <div class="max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-rose-700 mb-2">Super Admin Access</h2>
                <p class="text-gray-500 mb-8 text-sm">Sign in to manage the entire system.</p>

                <form wire:submit="authenticate" class="space-y-6">
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" wire:model="data.email" required autofocus
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-transparent transition outline-none"
                            placeholder="superadmin@domain.com">
                        @error('data.email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" wire:model="data.password" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-transparent transition outline-none pr-10"
                                placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-rose-600 focus:outline-none">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('data.password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" wire:model="data.remember" class="w-4 h-4 text-rose-600 border-gray-300 rounded focus:ring-rose-500">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-500">
                            Remember me
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" 
                        wire:loading.attr="disabled" 
                        wire:loading.class="opacity-75 cursor-wait"
                        class="w-full py-3 bg-gradient-to-r from-rose-700 to-pink-600 hover:from-rose-800 hover:to-pink-700 text-white font-bold rounded-lg shadow-md transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 disabled:opacity-75 disabled:cursor-wait">
                        <span wire:loading.remove>Login</span>
                        <span wire:loading>Logging in...</span>
                    </button>
                    
                </form>
            </div>
        </div>
    </div>
