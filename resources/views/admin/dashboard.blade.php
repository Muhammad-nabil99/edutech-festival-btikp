<x-layouts.app title="Dashboard Panitia — EduTech Festival BTIKP" maxWidth="max-w-7xl">
    <div 
        class="space-y-6" 
        x-data="{ 
            viewMode: 'table', 
            previewImage: null, 
            selectedRegistration: null,
            deleteTarget: null,
            isDeleting: false,
            deleteSuccess: false,
            deletedInfo: null,
            openDetail(data) {
                this.selectedRegistration = data;
            },
            closeDetail() {
                this.selectedRegistration = null;
            },
            confirmDelete(data) {
                this.deleteSuccess = false;
                this.isDeleting = false;
                this.deletedInfo = null;
                this.deleteTarget = data;
            },
            closeDeleteModal() {
                if (this.isDeleting) return;
                if (this.deleteSuccess) {
                    window.location.reload();
                    return;
                }
                this.deleteTarget = null;
                this.deleteSuccess = false;
            },
            async executeDelete() {
                if (!this.deleteTarget || this.isDeleting) return;
                this.isDeleting = true;
                this.deletedInfo = { ...this.deleteTarget };
                try {
                    const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch(this.deleteTarget.delete_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    });
                    if (res.ok) {
                        this.deleteSuccess = true;
                        this.isDeleting = false;
                        setTimeout(() => {
                            window.location.reload();
                        }, 1800);
                    } else {
                        if (this.$refs.deleteForm) this.$refs.deleteForm.submit();
                    }
                } catch (e) {
                    if (this.$refs.deleteForm) this.$refs.deleteForm.submit();
                }
            },
            finishDelete() {
                window.location.reload();
            }
        }"
    >

        <!-- Top Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mb-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
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
            <form method="GET" action="{{ route('admin.dashboard', [], false) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

        <!-- View Controls Bar: Counter & Table/Grid Toggle -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-1">
            <div class="text-xs sm:text-sm text-slate-600">
                Menampilkan <strong class="text-slate-900 font-extrabold">{{ $registrations->count() }}</strong> dari total <strong class="text-slate-900 font-extrabold">{{ $registrations->total() }}</strong> pendaftar
                <span class="text-xs text-blue-600 font-medium block sm:inline sm:ml-2">
                    &bull; Klik baris/kartu pendaftar untuk melihat detail lengkap
                </span>
            </div>

            <!-- View Switcher (Table / Grid) -->
            <div class="inline-flex items-center p-1 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
                <button 
                    type="button" 
                    @click="viewMode = 'table'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                    :class="viewMode === 'table' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Tabel (List)</span>
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'grid'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                    :class="viewMode === 'grid' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Grid (Kartu)</span>
                </button>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- VIEW 1: TABLE VIEW (Fit, Polos, Bersih & Elegan)        -->
        <!-- ======================================================= -->
        <div x-show="viewMode === 'table'" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-slate-200/90 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Peserta</span>
                                </div>
                            </th>
                            <th class="py-3.5 px-5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>Instansi &amp; Daerah</span>
                                </div>
                            </th>
                            <th class="py-3.5 px-5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span>Kategori Karya</span>
                                </div>
                            </th>
                            <th class="py-3.5 px-5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>Kontak (WA / Email)</span>
                                </div>
                            </th>
                            <th class="py-3.5 px-5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <span>Link Karya</span>
                                </div>
                            </th>
                            <th class="py-3.5 px-5 text-center whitespace-nowrap">
                                <span>Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($registrations as $reg)
                            @php
                                $regPayload = [
                                    'id' => $reg->id,
                                    'registration_number' => $reg->registration_number,
                                    'full_name' => $reg->full_name,
                                    'school_name' => $reg->school_name,
                                    'regency_city' => $reg->regency_city,
                                    'email' => $reg->email,
                                    'whatsapp_number' => $reg->whatsapp_number,
                                    'whatsapp_chat_url' => $reg->whatsapp_chat_url,
                                    'learning_category' => $reg->learning_category,
                                    'category_label' => $reg->category_label,
                                    'profile_photo_url' => $reg->profile_photo_url,
                                    'media_link' => $reg->media_link,
                                    'created_at_formatted' => $reg->created_at ? $reg->created_at->translatedFormat('l, d F Y — H:i') . ' WITA' : '-',
                                    'delete_url' => route('admin.registrations.destroy', $reg, false),
                                ];
                            @endphp
                            <tr 
                                class="hover:bg-slate-50/70 transition-colors group cursor-pointer"
                                @click="openDetail(@js($regPayload))"
                            >
                                <!-- Peserta & Foto -->
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div 
                                            @click.stop="previewImage = '{{ $reg->profile_photo_url }}'"
                                            class="relative shrink-0 group/img focus:outline-hidden"
                                            title="Klik untuk memperbesar foto"
                                        >
                                            <img 
                                                src="{{ $reg->profile_photo_url }}" 
                                                alt="{{ $reg->full_name }}" 
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($reg->full_name) }}&background=1E40AF&color=FFFFFF&size=200';"
                                                class="w-10 h-10 rounded-xl object-cover border border-slate-200/80 shadow-2xs group-hover/img:scale-105 transition-all bg-slate-100"
                                            >
                                            <div class="absolute inset-0 rounded-xl bg-black/20 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="space-y-0.5">
                                            <div class="font-semibold text-slate-800 group-hover:text-blue-600 transition-colors whitespace-nowrap">
                                                {{ $reg->full_name }}
                                            </div>
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100 text-[11px] font-mono font-medium whitespace-nowrap">
                                                <span>{{ $reg->registration_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Instansi & Daerah -->
                                <td class="py-3.5 px-5">
                                    <div class="space-y-0.5">
                                        <div class="font-medium text-slate-800 text-xs tracking-wide uppercase whitespace-nowrap">
                                            {{ $reg->school_name }}
                                        </div>
                                        <div class="inline-flex items-center gap-1 text-[11px] text-slate-400 whitespace-nowrap">
                                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ $reg->regency_city }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    @if ($reg->learning_category === 'gim_edukasi')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Gim Edukasi</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-sky-50 text-sky-800 border border-sky-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            <span>Media Interaktif</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Kontak -->
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 text-xs text-slate-600 font-normal">
                                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $reg->email }}</span>
                                        </div>
                                        <div @click.stop>
                                            <a 
                                                href="{{ $reg->whatsapp_chat_url }}" 
                                                target="_blank" 
                                                rel="noopener noreferrer" 
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100/80 text-emerald-700 border border-emerald-200/70 font-medium text-[11px] transition-colors"
                                            >
                                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.449.741.963 1.2 1.014.904 1.868 1.185 2.134 1.315.265.13.42.115.578-.066.158-.181.677-.789.858-1.06.181-.271.36-.226.605-.136.245.09 1.545.728 1.811.86.265.132.441.198.505.311.063.113.063.655-.081 1.06z" />
                                                </svg>
                                                <span>Chat WA ({{ $reg->whatsapp_number }})</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <!-- Link Media -->
                                <td class="py-3.5 px-5 whitespace-nowrap" @click.stop>
                                    <a 
                                        href="{{ $reg->media_link }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-50 hover:bg-blue-50 border border-slate-200/80 hover:border-blue-200 text-slate-700 hover:text-blue-700 font-medium text-xs transition-colors"
                                        title="{{ $reg->media_link }}"
                                    >
                                        <span>Buka Karya</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-5 text-center whitespace-nowrap" @click.stop>
                                    <div class="inline-flex items-center gap-1">
                                        <!-- Tombol Lihat Detail -->
                                        <button 
                                            type="button" 
                                            @click="openDetail(@js($regPayload))"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer"
                                            title="Lihat Detail Pendaftaran"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Tombol Hapus (Pop-up Konfirmasi) -->
                                        <button 
                                            type="button" 
                                            @click.stop="confirmDelete(@js($regPayload))"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                            title="Hapus data pendaftaran"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center text-slate-500 bg-slate-50/50">
                                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                        <div class="w-14 h-14 rounded-3xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 shadow-sm">
                                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <span class="font-extrabold text-slate-800 text-sm">Tidak ada data pendaftar</span>
                                        <span class="text-xs text-slate-400">Tidak ada data yang cocok dengan kriteria filter atau pencarian Anda.</span>
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

        <!-- ======================================================= -->
        <!-- VIEW 2: GRID VIEW (Kartu-Kartu Interaktif & Modern)     -->
        <!-- ======================================================= -->
        <div x-show="viewMode === 'grid'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($registrations as $reg)
                    @php
                        $regPayload = [
                            'id' => $reg->id,
                            'registration_number' => $reg->registration_number,
                            'full_name' => $reg->full_name,
                            'school_name' => $reg->school_name,
                            'regency_city' => $reg->regency_city,
                            'email' => $reg->email,
                            'whatsapp_number' => $reg->whatsapp_number,
                            'whatsapp_chat_url' => $reg->whatsapp_chat_url,
                            'learning_category' => $reg->learning_category,
                            'category_label' => $reg->category_label,
                            'profile_photo_url' => $reg->profile_photo_url,
                            'media_link' => $reg->media_link,
                            'created_at_formatted' => $reg->created_at ? $reg->created_at->translatedFormat('l, d F Y — H:i') . ' WITA' : '-',
                            'delete_url' => route('admin.registrations.destroy', $reg, false),
                        ];
                    @endphp
                    <div 
                        class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-blue-300 transition-all cursor-pointer flex flex-col justify-between group"
                        @click="openDetail(@js($regPayload))"
                    >
                        <div>
                            <!-- Card Header: Category Badge & Registration Number -->
                            <div class="flex items-center justify-between gap-2 mb-4">
                                @if ($reg->learning_category === 'gim_edukasi')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-extrabold bg-gradient-to-r from-amber-50 to-orange-50 text-amber-900 border border-amber-300/80 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Gim Edukasi</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-extrabold bg-gradient-to-r from-cyan-50 to-blue-50 text-cyan-900 border border-cyan-300/80 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                                        <span>Media Interaktif</span>
                                    </span>
                                @endif

                                <span class="text-[11px] font-mono font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/80">
                                    {{ $reg->registration_number }}
                                </span>
                            </div>

                            <!-- Avatar & Participant Info -->
                            <div class="flex items-center gap-3.5 mb-4">
                                <div 
                                    @click.stop="previewImage = '{{ $reg->profile_photo_url }}'"
                                    class="relative shrink-0"
                                    title="Klik untuk memperbesar foto"
                                >
                                    <img 
                                        src="{{ $reg->profile_photo_url }}" 
                                        alt="{{ $reg->full_name }}" 
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($reg->full_name) }}&background=1E40AF&color=FFFFFF&size=200';"
                                        class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-md ring-2 ring-slate-100 group-hover:ring-blue-400 transition-all bg-slate-100"
                                    >
                                </div>
                                <div class="space-y-0.5 overflow-hidden">
                                    <h3 class="font-extrabold text-slate-900 group-hover:text-blue-700 transition-colors text-sm truncate" title="{{ $reg->full_name }}">
                                        {{ $reg->full_name }}
                                    </h3>
                                    <p class="text-xs font-bold text-slate-700 uppercase truncate" title="{{ $reg->school_name }}">
                                        {{ $reg->school_name }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 flex items-center gap-1 truncate">
                                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span class="truncate">{{ $reg->regency_city }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Contact Snippet -->
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 mb-4 space-y-1.5 text-xs text-slate-600">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="truncate" title="{{ $reg->email }}">{{ $reg->email }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2 pt-1" @click.stop>
                                    <a 
                                        href="{{ $reg->whatsapp_chat_url }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition"
                                    >
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.449.741.963 1.2 1.014.904 1.868 1.185 2.134 1.315.265.13.42.115.578-.066.158-.181.677-.789.858-1.06.181-.271.36-.226.605-.136.245.09 1.545.728 1.811.86.265.132.441.198.505.311.063.113.063.655-.081 1.06z" />
                                        </svg>
                                        <span>Chat WA</span>
                                    </a>
                                    <span class="text-[11px] font-mono text-slate-400">{{ $reg->whatsapp_number }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Actions -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2" @click.stop>
                            <a 
                                href="{{ $reg->media_link }}" 
                                target="_blank" 
                                rel="noopener noreferrer" 
                                class="inline-flex items-center gap-1.5 text-xs font-extrabold text-blue-600 hover:text-blue-800 transition"
                            >
                                <span>Buka Karya</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>

                            <div class="flex items-center gap-1">
                                <button 
                                    type="button" 
                                    @click="openDetail(@js($regPayload))"
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-xs transition cursor-pointer"
                                >
                                    Detail
                                </button>
                                <button 
                                    type="button" 
                                    @click.stop="confirmDelete(@js($regPayload))"
                                    class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                    title="Hapus data pendaftaran"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-200">
                        <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                            <span class="font-extrabold text-slate-800 text-sm">Tidak ada data pendaftar</span>
                            <span class="text-xs text-slate-400">Tidak ada data yang cocok dengan kriteria filter atau pencarian Anda.</span>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Grid -->
            @if ($registrations->hasPages())
                <div class="bg-white rounded-3xl p-4 border border-slate-200">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>

        <!-- ======================================================= -->
        <!-- MODAL POP-UP: DETAIL LENGKAP PENDAFTARAN                -->
        <!-- ======================================================= -->
        <div 
            x-show="selectedRegistration" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            @keydown.escape.window="closeDetail()"
        >
            <div 
                class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200/80 overflow-hidden transform transition-all max-h-[90vh] flex flex-col"
                @click.away="closeDetail()"
            >
                <!-- Modal Top Gradient Header -->
                <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 px-6 sm:px-8 py-5 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 p-1 backdrop-blur-xs border border-white/20 flex items-center justify-center shrink-0">
                            <img src="{{ asset('logo.png') }}" alt="Logo BTIKP" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="text-[10px] tracking-widest uppercase font-semibold text-cyan-300">Detail Pendaftaran Peserta</span>
                            <h2 class="text-base sm:text-lg font-bold tracking-tight" x-text="selectedRegistration?.registration_number"></h2>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="closeDetail()"
                        class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white transition cursor-pointer"
                        title="Tutup (Esc)"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 sm:p-8 overflow-y-auto space-y-6">
                    <!-- Profile Card -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 p-5 rounded-3xl bg-slate-50 border border-slate-200/70">
                        <img 
                            :src="selectedRegistration?.profile_photo_url" 
                            :alt="selectedRegistration?.full_name"
                            class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md border-2 border-white ring-2 ring-blue-100 shrink-0 bg-white"
                        >
                        <div class="flex-1 text-center sm:text-left space-y-1">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-800 mb-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                <span x-text="selectedRegistration?.category_label"></span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-extrabold text-slate-900" x-text="selectedRegistration?.full_name"></h3>
                            <p class="text-sm font-bold text-slate-700 uppercase" x-text="selectedRegistration?.school_name"></p>
                            <p class="text-xs text-slate-500 flex items-center justify-center sm:justify-start gap-1">
                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <span x-text="selectedRegistration?.regency_city"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <!-- Email -->
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Alamat Email</span>
                            <span class="font-bold text-slate-900 break-all" x-text="selectedRegistration?.email"></span>
                        </div>

                        <!-- WhatsApp -->
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Nomor WhatsApp</span>
                            <div class="flex items-center justify-between gap-2 pt-0.5">
                                <span class="font-bold text-slate-900" x-text="selectedRegistration?.whatsapp_number"></span>
                                <a 
                                    :href="selectedRegistration?.whatsapp_chat_url" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-xs"
                                >
                                    <span>Chat WA</span>
                                </a>
                            </div>
                        </div>

                        <!-- Kategori Pembelajaran -->
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Pilihan Pembelajaran</span>
                            <span class="font-extrabold text-blue-700 block" x-text="selectedRegistration?.category_label"></span>
                        </div>

                        <!-- Waktu Pendaftaran -->
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Waktu Terdaftar</span>
                            <span class="font-semibold text-slate-700 block" x-text="selectedRegistration?.created_at_formatted"></span>
                        </div>

                        <!-- Link Media Karya -->
                        <div class="sm:col-span-2 p-4 rounded-2xl bg-blue-50/70 border border-blue-200/80 shadow-2xs space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] uppercase tracking-wider text-blue-800 font-bold block">Tautan Media Karya Digital</span>
                                <a 
                                    :href="selectedRegistration?.media_link" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs"
                                >
                                    <span>Buka Tautan Karya</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                            <p class="text-xs text-blue-900 font-mono break-all" x-text="selectedRegistration?.media_link"></p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div>
                        <!-- Tombol Hapus di dalam modal (Buka Pop-up Konfirmasi) -->
                        <button 
                            type="button" 
                            @click="confirmDelete(selectedRegistration); closeDetail();"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-800 px-3 py-2 rounded-xl hover:bg-rose-50 transition cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Data</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="closeDetail()"
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition cursor-pointer"
                        >
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal Preview Foto Profil Saja -->
        <div 
            x-show="previewImage" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4"
            @click.self="previewImage = null"
        >
            <div class="relative bg-white rounded-3xl p-4 max-w-sm w-full text-center shadow-2xl">
                <img :src="previewImage" alt="Foto Profil" class="w-full h-80 object-cover rounded-2xl mb-4 bg-slate-100 shadow-inner">
                <button 
                    type="button" 
                    @click="previewImage = null"
                    class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition cursor-pointer"
                >
                    Tutup Pratinjau
                </button>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL POP-UP KONFIRMASI HAPUS PENDAFTAR                 -->
        <!-- ======================================================= -->
        <div 
            x-show="deleteTarget !== null" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="closeDeleteModal()"
        >
            <div 
                @click.outside="closeDeleteModal()"
                class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 text-center shadow-2xl border border-slate-200 relative overflow-hidden transition-all duration-300"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                <!-- ============================================== -->
                <!-- STATE 1: KONFIRMASI HAPUS (MERAH / ROSE)        -->
                <!-- ============================================== -->
                <div x-show="!deleteSuccess" class="space-y-4">
                    <!-- Ikon Peringatan & Avatar Peserta -->
                    <div class="relative w-20 h-20 mx-auto">
                        <img 
                            :src="deleteTarget?.profile_photo_url" 
                            :alt="deleteTarget?.full_name" 
                            class="w-20 h-20 rounded-3xl object-cover border-2 border-rose-200 shadow-md bg-slate-100"
                        >
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-md border-2 border-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1">
                            Hapus Data Pendaftar?
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Apakah Anda yakin ingin menghapus data peserta ini dari sistem?
                        </p>
                    </div>

                    <!-- Box Detail Singkat Peserta -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-left space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nomor Registrasi</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-blue-50 text-blue-700" x-text="deleteTarget?.registration_number"></span>
                        </div>
                        <div class="text-sm font-bold text-slate-800" x-text="deleteTarget?.full_name"></div>
                        <div class="text-xs text-slate-500" x-text="(deleteTarget?.school_name || '') + ' • ' + (deleteTarget?.regency_city || '')"></div>
                    </div>

                    <!-- Info Peringatan Permanen -->
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs text-left flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Tindakan ini bersifat permanen. Berkas foto dan data karya pendaftar akan terhapus.</span>
                    </div>

                    <!-- Tombol Aksi Batal / Hapus -->
                    <div class="flex items-center gap-3 pt-1">
                        <button 
                            type="button" 
                            :disabled="isDeleting"
                            @click="closeDeleteModal()"
                            class="flex-1 py-2.5 px-4 rounded-xl border border-slate-300 hover:bg-slate-100 disabled:opacity-50 text-slate-700 font-bold text-xs transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <form x-ref="deleteForm" :action="deleteTarget?.delete_url" method="POST" class="flex-1" @submit.prevent="executeDelete()">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                :disabled="isDeleting"
                                class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold text-xs shadow-md shadow-rose-600/30 transition cursor-pointer flex items-center justify-center gap-2"
                            >
                                <svg x-show="isDeleting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus Data'"></span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STATE 2: SUKSES BERHASIL DIHAPUS (HIJAU / EMERALD) -->
                <!-- ============================================== -->
                <div 
                    x-show="deleteSuccess" 
                    x-cloak
                    class="space-y-4 py-2"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                >
                    <!-- Ikon Centang Hijau Elegan -->
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center text-emerald-600 shadow-xl shadow-emerald-500/10">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 mb-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Terhapus dari Database
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-1">
                            Data Berhasil Dihapus!
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Data pendaftaran <span class="font-bold text-slate-800" x-text="deletedInfo?.full_name"></span> (<span class="font-mono text-emerald-700 font-semibold" x-text="deletedInfo?.registration_number"></span>) beserta berkas terkait telah berhasil dihapus.
                        </p>
                    </div>

                    <!-- Progress Info -->
                    <div class="p-3 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-emerald-800 text-xs flex items-center justify-center gap-2">
                        <svg class="animate-spin w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memperbarui daftar peserta secara otomatis...</span>
                    </div>

                    <button 
                        type="button" 
                        @click="finishDelete()"
                        class="w-full py-3 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span>Selesai</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
