<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAFAFC]">
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
            background-color: #FAFAFC;
        }
        [x-cloak] { display: none !important; }
        .ambient-glow-purple {
            background: radial-gradient(circle, rgba(94, 67, 243, 0.10) 0%, rgba(245, 243, 255, 0) 70%);
        }
        .ambient-glow-mint {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, rgba(240, 253, 244, 0) 70%);
        }
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
<body class="min-h-screen flex flex-col font-sans antialiased text-slate-800 relative selection:bg-[#5e43f3] selection:text-white overflow-x-hidden">
    <!-- Ambient Decorative Background Blobs (Eduxon style) -->
    <div class="fixed top-0 left-0 w-96 h-96 ambient-glow-purple -translate-x-1/3 -translate-y-1/3 pointer-events-none z-0"></div>
    <div class="fixed top-1/4 right-0 w-[500px] h-[500px] ambient-glow-mint translate-x-1/3 pointer-events-none z-0"></div>
    <div class="fixed bottom-10 left-10 w-80 h-80 ambient-glow-purple opacity-70 pointer-events-none z-0"></div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-100 transition-all">
        <div class="{{ $maxWidth ?? 'max-w-7xl' }} mx-auto px-2.5 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-20 gap-2">
                <!-- Brand / Logo & Agency Badge -->
                <a href="{{ route('registration.create') }}" class="flex items-center gap-1.5 sm:gap-3.5 group shrink-0 min-w-0">
                    <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-white border border-slate-200/80 p-0.5 sm:p-1 flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform overflow-hidden shrink-0">
                        <img src="{{ asset('logo.png') }}" alt="Logo EduTech BTIKP" class="w-full h-full object-contain">
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1 sm:gap-2">
                            <span class="text-sm sm:text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-[#5e43f3] transition-colors whitespace-nowrap">
                                EduTech<span class="text-[#5e43f3]">Festival</span>
                            </span>
                            <span class="px-1.5 py-0.5 text-[9px] sm:text-[11px] font-bold tracking-wide uppercase bg-purple-50 text-[#5e43f3] rounded-md border border-purple-200/60 hidden min-[360px]:inline-block shrink-0">
                                BTIKP
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-500 hidden md:block">Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara</p>
                    </div>
                </a>

                <!-- Right Nav Actions -->
                <div class="flex items-center space-x-1.5 sm:space-x-4 shrink-0">
                    @auth
                        @if (auth()->user()->isJudge())
                            <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1.5 rounded-xl bg-purple-50 text-[#5e43f3] border border-purple-200/80 text-xs font-bold hover:bg-purple-100 transition whitespace-nowrap">
                                <span class="w-2 h-2 rounded-full bg-[#5e43f3] animate-pulse shrink-0"></span>
                                <span class="hidden sm:inline">Dashboard Juri</span>
                                <span class="sm:hidden">Juri</span>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1.5 rounded-xl bg-purple-50 text-[#5e43f3] border border-purple-200/80 text-xs font-bold hover:bg-purple-100 transition whitespace-nowrap">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                <span class="hidden sm:inline">Dashboard Admin</span>
                                <span class="sm:hidden">Admin</span>
                            </a>
                        @endif

                        <!-- User Profile Pill -->
                        <div class="flex items-center gap-2.5 pl-1 hidden sm:flex">
                            <div class="text-right">
                                <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] font-semibold text-slate-400 font-mono">
                                    {{ auth()->user()->isJudge() ? (auth()->user()->judge_code . ' • Dewan Juri') : 'Panitia BTIKP' }}
                                </div>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-purple-100 border border-purple-200 flex items-center justify-center text-[#5e43f3] font-bold text-xs shadow-xs">
                                {{ auth()->user()->isJudge() ? auth()->user()->judge_code : strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="h-5 sm:h-6 w-px bg-slate-200 hidden sm:block"></div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('admin.logout', [], false) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center text-xs font-bold text-rose-500 hover:text-rose-600 px-2 sm:px-3 py-1.5 sm:py-2 rounded-xl hover:bg-rose-50 transition-colors cursor-pointer" title="Keluar dari sistem">
                                <svg class="w-4 h-4 sm:mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('registration.create') }}" class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-[#5e43f3] hidden sm:inline-block">
                            Formulir Pendaftaran
                        </a>
                        <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1 sm:gap-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 px-2 sm:px-3.5 py-1.5 sm:py-2 rounded-xl border border-slate-200 hover:border-slate-300 bg-white shadow-xs transition shrink-0 whitespace-nowrap">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-3.5 sm:h-4 w-auto object-contain shrink-0">
                            <span class="hidden min-[360px]:inline">Portal </span><span>Masuk</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-2.5 sm:px-6 lg:px-8 py-3.5 sm:py-8 relative min-w-0">
        <!-- Floating Toast Notification System (Auto-slides away after 5s) -->
        @if (session('status') || session('error') || $errors->any())
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
                            <p class="text-xs font-semibold text-slate-700 mt-0.5 leading-relaxed break-words">{{ session('error') ?? $errors->first() }}</p>
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
    </main>

    <!-- Footer -->
    <footer class="mt-12 bg-white border-t border-slate-100 py-6 sm:py-8 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#5e43f3] flex items-center justify-center font-bold text-xs border border-purple-200/50">
                        BP
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800">Balai Teknologi Informasi dan Komunikasi Pendidikan (BTIKP)</div>
                        <div class="text-[11px] text-slate-500">Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara &copy; {{ date('Y') }}. Seluruh hak cipta dilindungi.</div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
