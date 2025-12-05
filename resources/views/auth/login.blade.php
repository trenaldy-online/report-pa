<x-guest-layout>
    <div class="flex min-h-screen">
        
        {{-- BAGIAN KIRI: GAMBAR / BANNER --}}
        <div class="hidden lg:flex w-1/2 bg-blue-900 relative items-center justify-center overflow-hidden">
            {{-- Background Image (Ganti URL ini dengan foto AHCC asli jika ada) --}}
            <img src="https://images.unsplash.com/photo-1516549655169-df83a092dd14?q=80&w=2070&auto=format&fit=crop" 
                 class="absolute inset-0 w-full h-full object-cover opacity-40" alt="Medical Background">
            
            {{-- Overlay Text --}}
            <div class="relative z-10 px-12 text-white">
                <div class="mb-6">
                    {{-- Logo Placeholder (Ganti SVG/Img Logo AHCC) --}}
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <h2 class="text-4xl font-bold tracking-tight mb-4">Adhi Husada Cancer Center</h2>
                <p class="text-blue-100 text-lg leading-relaxed">
                    Sistem Informasi Manajemen Data Pasien & Marketing Tracking Terintegrasi.
                </p>
                <div class="mt-8 flex gap-2">
                    <div class="h-1 w-12 bg-white rounded-full"></div>
                    <div class="h-1 w-4 bg-blue-400 rounded-full"></div>
                    <div class="h-1 w-4 bg-blue-400 rounded-full"></div>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: FORM LOGIN --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 p-8">
            <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                
                <div class="mb-8 text-center lg:text-left">
                    <h3 class="text-2xl font-bold text-gray-900">Selamat Datang Kembali</h3>
                    <p class="text-sm text-gray-500 mt-2">Silakan masuk akun Anda untuk mengakses dashboard.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none" placeholder="nama@adhihusada.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none" placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-800 font-medium" href="{{ route('password.request') }}">
                                {{ __('Lupa Password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                        {{ __('Log in') }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} AHCC Data System. Internal Use Only.
                    </p>
                </div>

            </div>
        </div>

    </div>
</x-guest-layout>