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
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased text-slate-800 relative selection:bg-[#5e43f3] selection:text-white overflow-x-hidden">
    <!-- Ambient Decorative Background Blobs (Eduxon style) -->
    <div class="fixed top-0 left-0 w-96 h-96 ambient-glow-purple -translate-x-1/3 -translate-y-1/3 pointer-events-none z-0"></div>
    <div class="fixed top-1/4 right-0 w-[500px] h-[500px] ambient-glow-mint translate-x-1/3 pointer-events-none z-0"></div>
    <div class="fixed bottom-10 left-10 w-80 h-80 ambient-glow-purple opacity-70 pointer-events-none z-0"></div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/85 backdrop-blur-md border-b border-slate-100 transition-all">
        <div class="{{ $maxWidth ?? 'max-w-7xl' }} mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo & Agency Badge -->
                <a href="{{ route('registration.create') }}" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#5e43f3] via-[#8b5cf6] to-indigo-400 p-1 flex items-center justify-center shadow-lg shadow-[#5e43f3]/25 ring-4 ring-indigo-50 group-hover:scale-105 transition-transform overflow-hidden">
                        <img src="{{ asset('logo.png') }}" alt="Logo EduTech BTIKP" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg sm:text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-[#5e43f3] transition-colors">
                                EduTech<span class="text-[#5e43f3]">Festival</span>
                            </span>
                            <span class="px-2 py-0.5 text-[11px] font-bold tracking-wide uppercase bg-purple-50 text-[#5e43f3] rounded-md border border-purple-200/60">
                                BTIKP
                            </span>
                        </div>
                        <p class="text-xs font-medium text-slate-500 hidden sm:block">Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara</p>
                    </div>
                </a>

                <!-- Right Nav Actions -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    @auth
                        @if (auth()->user()->isJudge())
                            <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 text-[#5e43f3] border border-purple-200/80 text-xs font-bold hover:bg-purple-100 transition">
                                <span class="w-2 h-2 rounded-full bg-[#5e43f3] animate-pulse"></span>
                                <span>Dashboard Juri</span>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 text-[#5e43f3] border border-purple-200/80 text-xs font-bold hover:bg-purple-100 transition">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Dashboard Admin</span>
                            </a>
                        @endif

                        <!-- User Profile Pill -->
                        <div class="flex items-center gap-2.5 pl-1 hidden sm:flex">
                            <div class="text-right">
                                <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] font-semibold text-slate-400 font-mono">
                                    {{ auth()->user()->isJudge() ? 'Dewan Juri' : 'Panitia BTIKP' }}
                                </div>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-purple-100 border border-purple-200 flex items-center justify-center text-[#5e43f3] font-bold text-xs shadow-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('admin.logout', [], false) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center text-xs font-bold text-rose-500 hover:text-rose-600 px-3 py-2 rounded-xl hover:bg-rose-50 transition-colors cursor-pointer" title="Keluar dari sistem">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('registration.create') }}" class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-[#5e43f3] hidden sm:inline-block">
                            Formulir Pendaftaran
                        </a>
                        <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 px-3.5 py-2 rounded-xl border border-slate-200 hover:border-slate-300 bg-white shadow-xs transition">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-4 w-auto object-contain shrink-0">
                            <span>Portal Masuk</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        <!-- Flash Status Message -->
        @if (session('status'))
            <div class="mb-6 rounded-2xl bg-gradient-to-r from-emerald-50/90 via-emerald-50/50 to-teal-50/90 border border-emerald-200/80 p-4 shadow-xs flex items-center justify-between transition-all animate-fade-in">
                <div class="flex items-center space-x-3.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-emerald-950">{{ session('status') }}</h4>
                    </div>
                </div>
            </div>
        @endif

        <!-- Flash Error Message -->
        @if (session('error'))
            <div class="mb-6 rounded-2xl bg-gradient-to-r from-rose-50/90 via-rose-50/50 to-red-50/90 border border-rose-200/80 p-4 shadow-xs flex items-center justify-between transition-all animate-fade-in">
                <div class="flex items-center space-x-3.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-rose-950">{{ session('error') }}</h4>
                    </div>
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-12 bg-white border-t border-slate-100 py-8 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#5e43f3] flex items-center justify-center font-bold text-xs border border-purple-200/50">
                        BP
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800">Balai Teknologi Informasi dan Komunikasi Pendidikan (BTIKP)</div>
                        <div class="text-[11px] text-slate-500">Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara &copy; {{ date('Y') }}. Seluruh hak cipta dilindungi.</div>
                    </div>
                </div>
                <div class="flex items-center space-x-4 text-xs font-medium text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Server Sultra Cloud Aktif
                    </span>
                    <span>•</span>
                    <span>Versi 2.0</span>
                    <span>•</span>
                    <span class="text-[#5e43f3] font-semibold">Edutech Sultra</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
