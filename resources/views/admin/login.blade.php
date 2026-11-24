<x-layouts.app title="Masuk Portal Panitia & Dewan Juri — EduTech Festival BTIKP">
    <div 
        class="max-w-md w-full mx-auto"
        x-data="{ showPassword: false }"
    >
        <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-8 border border-slate-200/80 shadow-xl shadow-slate-200/50">
            <!-- Header -->
            <div class="text-center mb-5 sm:mb-8">
                <div class="w-14 h-14 sm:w-20 sm:h-20 mx-auto mb-2.5 sm:mb-4 p-2 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-xs flex items-center justify-center">
                    <img src="{{ asset('logo.png') }}" alt="Logo BTIKP" class="w-full h-full object-contain">
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-purple-50 text-[#5e43f3] border border-purple-200/60 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#5e43f3] animate-pulse"></span>
                    <span>Portal Resmi BTIKP Sultra</span>
                </div>
                <h1 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">Portal Panitia &amp; Dewan Juri</h1>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed">
                    Masuk untuk mengelola pendaftar, kurasi Top 10, atau melakukan penilaian karya EduTech Festival
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-3.5 sm:space-y-5">
                @csrf

                <!-- Username or Email Input -->
                <div>
                    <label for="login" class="block text-[11px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email atau Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            value="{{ old('login', old('email', '')) }}"
                            placeholder="Contoh: admin atau juri1"
                            class="w-full pl-9 pr-3.5 py-2.5 sm:pl-10 sm:pr-4 sm:py-3 rounded-xl border {{ $errors->has('email') || $errors->has('login') || $errors->has('login_identifier') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-[#5e43f3]' }} focus:ring-4 focus:ring-purple-100 outline-none text-slate-800 text-base sm:text-sm transition"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    @if ($errors->has('email') || $errors->has('login') || $errors->has('login_identifier'))
                        <p class="mt-1.5 text-xs text-rose-600 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $errors->first('email') ?: ($errors->first('login') ?: $errors->first('login_identifier')) }}</span>
                        </p>
                    @endif
                </div>

                <!-- Password Input with Show/Hide Toggle -->
                <div>
                    <label for="password" class="block text-[11px] sm:text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••••••"
                            class="w-full pl-9 pr-11 py-2.5 sm:pl-10 sm:pr-12 sm:py-3 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-[#5e43f3]' }} focus:ring-4 focus:ring-purple-100 outline-none text-slate-800 text-base sm:text-sm transition font-mono"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 pl-2 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer min-w-[40px] sm:min-w-[44px] justify-center"
                            :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                            tabindex="-1"
                        >
                            <template x-if="!showPassword">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </template>
                            <template x-if="showPassword">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-[11px] sm:text-xs text-slate-600 pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#5e43f3] focus:ring-[#5e43f3]">
                        <span class="font-medium text-slate-700">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button (Touch-Friendly min 44px-48px) -->
                <button 
                    type="submit" 
                    class="w-full min-h-[44px] sm:min-h-[48px] py-3 sm:py-3.5 px-4 rounded-xl bg-gradient-to-r from-[#5e43f3] via-indigo-600 to-purple-700 hover:from-indigo-600 hover:to-[#5e43f3] text-white font-extrabold text-sm sm:text-base shadow-md shadow-indigo-600/20 active:scale-[0.99] transition cursor-pointer flex items-center justify-center gap-2"
                >
                    <span>Masuk ke Dashboard</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
