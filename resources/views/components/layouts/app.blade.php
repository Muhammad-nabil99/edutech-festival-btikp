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
                        <img src="{{ asset('logoV2.png') }}" alt="Logo EduTech BTIKP" class="w-full h-full object-contain">
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
                            <img src="{{ asset('logoV2.png') }}" alt="Logo" class="h-4 w-auto object-contain shrink-0">
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
        <div class="{{ $maxWidth ?? 'max-w-6xl' }} mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logoV2.png') }}" alt="Logo BTIKP" class="w-9 h-9 object-contain shrink-0 hidden sm:block">
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
