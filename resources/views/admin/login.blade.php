<x-layouts.app title="Masuk Portal Panitia & Dewan Juri — EduTech Festival BTIKP">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xl shadow-slate-200/50">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-4 p-2 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-sm flex items-center justify-center">
                    <img src="{{ asset('logo.png') }}" alt="Logo BTIKP" class="w-full h-full object-contain">
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Portal Panitia & Dewan Juri</h1>
                <p class="text-xs text-slate-500 mt-1">Masuk untuk mengelola pendaftar atau menilai karya EduTech Festival</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email atau Username
                    </label>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        value="{{ old('login', old('email', 'admin@btikp.sultra.go.id')) }}"
                        placeholder="Contoh: admin atau juri1"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') || $errors->has('login') || $errors->has('login_identifier') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                        required
                        autofocus
                    >
                    @if ($errors->has('email') || $errors->has('login') || $errors->has('login_identifier'))
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">
                            {{ $errors->first('email') ?: ($errors->first('login') ?: $errors->first('login_identifier')) }}
                        </p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••••••"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                        required
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white font-bold text-sm shadow-md shadow-blue-700/20 hover:shadow-lg transition cursor-pointer"
                >
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <a href="{{ route('registration.create') }}" class="text-xs font-medium text-slate-500 hover:text-blue-600 transition flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Halaman Pendaftaran</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
