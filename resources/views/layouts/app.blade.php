<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(request()->isSecure() || str_starts_with((string) config('app.url'), 'https://'))
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif

    <title>{{ $title ?? 'EduTech Festival BTIKP — Pendaftaran Karya Digital' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js Fallback if Vite is not running live -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        [x-cloak] { display: none !important; }
        @keyframes toastProgress {
            from { width: 100%; }
            to { width: 0%; }
        }
        html.overflow-hidden, body.overflow-hidden {
            overflow: hidden !important;
            touch-action: none !important;
            height: 100% !important;
        }
    </style>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
    <!-- Navbar -->
    <header class="sticky top-0 z-30 backdrop-blur-md bg-white/85 border-b border-slate-200/80 transition-all">
        <div class="{{ $maxWidth ?? 'max-w-6xl' }} mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('registration.create') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white p-1 border border-slate-200/80 flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform overflow-hidden">
                        <img src="{{ asset('logo.png') }}" alt="Logo EduTech BTIKP" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-base sm:text-lg font-bold tracking-tight text-slate-900 group-hover:text-blue-700 transition-colors">EduTech Festival</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">BTIKP</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium hidden sm:block">Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara</p>
                    </div>
                </a>

                <!-- Right Nav Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-slate-100 transition">
                            Dashboard Admin
                        </a>
                        <form method="POST" action="{{ route('admin.logout', [], false) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs sm:text-sm font-medium text-rose-600 hover:text-rose-700 px-3 py-1.5 rounded-lg hover:bg-rose-50 transition cursor-pointer">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('registration.create') }}" class="text-xs sm:text-sm font-medium text-slate-600 hover:text-blue-600 hidden sm:inline-block">
                            Formulir Pendaftaran
                        </a>
                        <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-slate-300 bg-white shadow-xs transition">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-4 w-auto object-contain shrink-0">
                            <span>Panitia</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-8 sm:py-12">
        <div class="{{ $maxWidth ?? 'max-w-6xl' }} mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Floating Toast Notification System (Auto-slides away after 5s) -->
            @if (session('status') || session('error'))
                <div 
                    class="fixed top-5 right-5 z-[99999] max-w-sm sm:max-w-md w-[calc(100vw-2.5rem)] pointer-events-none"
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                >
                    <div 
                        x-show="show"
                        x-cloak
                        x-transition:enter="transform ease-out duration-400 transition"
                        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-6 scale-95"
                        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0 scale-100"
                        x-transition:leave="transform ease-in duration-400 transition"
                        x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-x-12 scale-95"
                        class="pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-xl shadow-slate-900/10 border {{ session('status') ? 'border-emerald-200' : 'border-rose-200' }} p-4 relative overflow-hidden flex items-start gap-3.5 group"
                    >
                        @if (session('status'))
                            <!-- Success Icon -->
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5 shrink-0 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pr-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Berhasil</h4>
                                <p class="text-xs font-semibold text-slate-700 mt-0.5 leading-relaxed break-words">{{ session('status') }}</p>
                            </div>
                            <!-- 5-Second Animated Progress Bar -->
                            <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-400 rounded-b-2xl transition-all"
                                 style="animation: toastProgress 5s linear forwards;">
                            </div>
                        @else
                            <!-- Error Icon -->
                            <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/80 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5 shrink-0 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pr-4">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-800">Perhatian</h4>
                                <p class="text-xs font-semibold text-slate-700 mt-0.5 leading-relaxed break-words">{{ session('error') }}</p>
                            </div>
                            <!-- 5-Second Animated Progress Bar -->
                            <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-rose-500 to-red-400 rounded-b-2xl transition-all"
                                 style="animation: toastProgress 5s linear forwards;">
                            </div>
                        @endif

                        <!-- Close Button -->
                        <button 
                            type="button" 
                            @click="show = false"
                            class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition shrink-0 cursor-pointer"
                            title="Tutup pemberitahuan"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white/70 backdrop-blur-xs py-8">
        <div class="{{ $maxWidth ?? 'max-w-6xl' }} mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="Logo BTIKP" class="w-9 h-9 object-contain shrink-0 hidden sm:block">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Balai Teknologi Informasi dan Komunikasi Pendidikan (BTIKP)</p>
                    <p class="text-xs text-slate-500">Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara &copy; {{ date('Y') }}. Seluruh hak cipta dilindungi.</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-400">
                <span>Versi 1.0</span>
                <span>&bull;</span>
                <span>Edutech Sultra</span>
            </div>
        </div>
    </footer>
</body>
</html>
