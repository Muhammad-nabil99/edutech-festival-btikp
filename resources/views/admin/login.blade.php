<x-layouts.app title="Masuk Portal Panitia — EduTech Festival BTIKP">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xl shadow-slate-200/50">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center mx-auto mb-4 shadow-md shadow-blue-600/30">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Portal Panitia BTIKP</h1>
                <p class="text-xs text-slate-500 mt-1">Masuk untuk mengelola pendaftar EduTech Festival</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email Panitia
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', 'admin@btikp.sultra.go.id') }}"
                        placeholder="admin@btikp.sultra.go.id"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
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
