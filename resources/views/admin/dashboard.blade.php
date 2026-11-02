<x-layouts.app title="Dashboard Panitia — EduTech Festival BTIKP">
    <div class="space-y-8" x-data="{ previewImage: null }">

        <!-- Top Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mb-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Panel Panitia BTIKP Sultra
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Data Pendaftar EduTech Festival
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Pantau statistik, kelola berkas pendaftaran, dan verifikasi karya peserta se-Sulawesi Tenggara.
                </p>
            </div>

            <!-- Export Button -->
            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('admin.registrations.export') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs sm:text-sm shadow-md shadow-emerald-600/20 hover:shadow-lg transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ekspor CSV / Excel</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Pendaftar -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pendaftar</span>
                    <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ number_format($stats['total']) }}</div>
                <p class="text-[11px] text-slate-400 mt-1">Peserta terdaftar</p>
            </div>

            <!-- Gim Edukasi -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gim Edukasi</span>
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ number_format($stats['gim_edukasi']) }}</div>
                <p class="text-[11px] text-slate-400 mt-1">Karya game interaktif</p>
            </div>

            <!-- Media Pembelajaran Interaktif -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Media Interaktif</span>
                    <div class="p-2 rounded-xl bg-cyan-50 text-cyan-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-cyan-600">{{ number_format($stats['media_interaktif']) }}</div>
                <p class="text-[11px] text-slate-400 mt-1">Multimedia modul / web</p>
            </div>

            <!-- Sebaran Daerah -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sebaran Wilayah</span>
                    <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-indigo-600">
                    {{ $stats['distinct_regions'] }} <span class="text-xs font-normal text-slate-400">/ 17 Daerah</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Kabupaten/Kota terwakili</p>
            </div>
        </div>

        <!-- Filter & Search Panel -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Box -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Pencarian Kata Kunci
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            value="{{ $filters['search'] }}" 
                            placeholder="Cari nama, sekolah, email, no. registrasi..." 
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none"
                        >
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Kategori Pembelajaran
                    </label>
                    <select 
                        id="category" 
                        name="category" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none bg-white cursor-pointer"
                    >
                        <option value="">Semua Kategori</option>
                        <option value="gim_edukasi" {{ $filters['category'] === 'gim_edukasi' ? 'selected' : '' }}>Gim Edukasi</option>
                        <option value="media_pembelajaran_interaktif" {{ $filters['category'] === 'media_pembelajaran_interaktif' ? 'selected' : '' }}>Media Pembelajaran Interaktif</option>
                    </select>
                </div>

                <!-- Regency Filter -->
                <div>
                    <label for="regency" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">
                        Kabupaten / Kota
                    </label>
                    <select 
                        id="regency" 
                        name="regency" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none bg-white cursor-pointer"
                    >
                        <option value="">Semua Wilayah</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region }}" {{ $filters['regency'] === $region ? 'selected' : '' }}>
                                {{ $region }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <a 
                        href="{{ route('admin.dashboard') }}" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition"
                    >
                        Reset Filter
                    </a>
                    <button 
                        type="submit" 
                        class="px-5 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold text-xs shadow-sm transition cursor-pointer"
                    >
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Participants Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Peserta</th>
                            <th class="py-3.5 px-4">Instansi &amp; Daerah</th>
                            <th class="py-3.5 px-4">Kategori Karya</th>
                            <th class="py-3.5 px-4">Kontak (WA / Email)</th>
                            <th class="py-3.5 px-4">Link Karya</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($registrations as $reg)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Peserta & Foto -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <button 
                                            type="button" 
                                            @click="previewImage = '{{ $reg->profile_photo_url }}'"
                                            class="relative group shrink-0"
                                            title="Klik untuk memperbesar foto"
                                        >
                                            <img 
                                                src="{{ $reg->profile_photo_url }}" 
                                                alt="{{ $reg->full_name }}" 
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($reg->full_name) }}&background=1E40AF&color=FFFFFF&size=200';"
                                                class="w-11 h-11 rounded-full object-cover border border-slate-200 shadow-xs group-hover:scale-105 transition bg-slate-100"
                                            >
                                        </button>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $reg->full_name }}</div>
                                            <div class="text-[11px] font-mono text-blue-700 font-semibold">{{ $reg->registration_number }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Instansi & Daerah -->
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-slate-800">{{ $reg->school_name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $reg->regency_city }}</div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-3.5 px-4">
                                    @if ($reg->learning_category === 'gim_edukasi')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Gim Edukasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-cyan-100 text-cyan-800 border border-cyan-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-600"></span>
                                            Media Interaktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Kontak -->
                                <td class="py-3.5 px-4">
                                    <div class="text-slate-600 text-xs">{{ $reg->email }}</div>
                                    <a 
                                        href="{{ $reg->whatsapp_chat_url }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 mt-0.5"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.449.741.963 1.2 1.014.904 1.868 1.185 2.134 1.315.265.13.42.115.578-.066.158-.181.677-.789.858-1.06.181-.271.36-.226.605-.136.245.09 1.545.728 1.811.86.265.132.441.198.505.311.063.113.063.655-.081 1.06z" />
                                        </svg>
                                        <span>Chat WA ({{ $reg->whatsapp_number }})</span>
                                    </a>
                                </td>

                                <!-- Link Media -->
                                <td class="py-3.5 px-4">
                                    <a 
                                        href="{{ $reg->media_link }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold text-xs transition"
                                        title="{{ $reg->media_link }}"
                                    >
                                        <span>Buka Link</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center">
                                    <form 
                                        method="POST" 
                                        action="{{ route('admin.registrations.destroy', $reg) }}" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran nomor {{ $reg->registration_number }}? Tindakan ini tidak dapat dibatalkan.')"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                            title="Hapus data pendaftaran"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <span class="font-semibold text-slate-700">Belum ada data pendaftar ditemukan</span>
                                        <span class="text-xs text-slate-400">Coba ubah filter pencarian Anda atau tunggu pendaftar baru masuk.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($registrations->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>

        <!-- Lightbox Modal Preview Foto Profil -->
        <div 
            x-show="previewImage" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4"
            @click.self="previewImage = null"
        >
            <div class="relative bg-white rounded-3xl p-4 max-w-sm w-full text-center shadow-2xl">
                <img :src="previewImage" alt="Foto Profil" class="w-full h-80 object-cover rounded-2xl mb-4">
                <button 
                    type="button" 
                    @click="previewImage = null"
                    class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition"
                >
                    Tutup Pratinjau
                </button>
            </div>
        </div>

    </div>
</x-layouts.app>

