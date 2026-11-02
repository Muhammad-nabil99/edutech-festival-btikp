<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EduTech Festival BTIKP — Pendaftaran Karya Digital' }}</title>

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
    </style>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
    <!-- Navbar -->
    <header class="sticky top-0 z-30 backdrop-blur-md bg-white/85 border-b border-slate-200/80 transition-all">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('registration.create') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-cyan-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
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
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs sm:text-sm font-medium text-rose-600 hover:text-rose-700 px-3 py-1.5 rounded-lg hover:bg-rose-50 transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('registration.create') }}" class="text-xs sm:text-sm font-medium text-slate-600 hover:text-blue-600 hidden sm:inline-block">
                            Formulir Pendaftaran
                        </a>
                        <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-slate-300 bg-white shadow-xs transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Panitia</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-8 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Notification -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-3 shadow-xs animate-fade-in">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center gap-3 shadow-xs animate-fade-in">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white/70 backdrop-blur-xs py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-800">Balai Teknologi Informasi dan Komunikasi Pendidikan (BTIKP)</p>
                <p class="text-xs text-slate-500">Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara &copy; {{ date('Y') }}. Seluruh hak cipta dilindungi.</p>
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
