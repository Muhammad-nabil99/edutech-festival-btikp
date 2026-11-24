<x-layouts.app title="Portal Dewan Juri — EduTech Festival BTIKP" maxWidth="max-w-7xl">
    <div x-data="judgeDashboard()" class="space-y-6">
        <!-- Welcome Juri Header Banner -->
        <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-indigo-900 to-[#5e43f3] text-white p-6 sm:p-8 shadow-soft relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold bg-white/10 backdrop-blur-md border border-white/20 text-purple-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Portal Resmi Dewan Juri — EduTech Festival BTIKP Sultra
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Evaluasi &amp; Rekomendasi Karya Digital
                    </h1>
                    <p class="text-xs sm:text-sm text-purple-100/90 leading-relaxed">
                        Selamat datang, <strong>{{ $judge->name }}</strong> ({{ $judge->username }}).
                        @if (!empty($judge->category))
                            Penugasan Anda: <strong class="text-white underline decoration-amber-300 decoration-2 underline-offset-2">{{ $judge->category === 'gim_edukasi' ? 'Kategori Gim Edukasi' : 'Kategori Media Pembelajaran Interaktif (MPI)' }}</strong>
                            sebagai <strong class="text-white">{{ $judge->judge_role_label }}</strong>.
                        @endif
                        Anda dapat merekomendasikan karya terbaik ke Panitia dengan mengisi checklist kelayakan, serta menilai karya Finalis Top 10 yang telah disetujui Admin.
                    </p>
                </div>

                <div class="flex items-center gap-3 self-start md:self-auto shrink-0">
                    <button 
                        type="button" 
                        @click="showProfileModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition cursor-pointer shadow-xs"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengaturan Akun Juri</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Metric Cards: Saran Rekomendasi & Penilaian -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @if (empty($judge->category))
                <!-- Saran Gim -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Saran Top 10: Gim</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1 font-mono">
                            <span x-text="myGimSuggestions"></span><span class="text-slate-400 text-xs font-semibold ml-1">Karya</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        Rekomendasi Anda untuk Gim Edukasi
                    </p>
                </div>

                <!-- Saran Media -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Saran Top 10: Media</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-sky-600 mt-1 font-mono">
                            <span x-text="myMediaSuggestions"></span><span class="text-slate-400 text-xs font-semibold ml-1">Karya</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        Rekomendasi Anda untuk Media Interaktif
                    </p>
                </div>
            @elseif ($judge->category === 'gim_edukasi')
                <!-- Saran Gim -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Saran Top 10: Gim</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1 font-mono">
                            <span x-text="myGimSuggestions"></span><span class="text-slate-400 text-xs font-semibold ml-1">Karya</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        Rekomendasi Anda untuk Gim Edukasi
                    </p>
                </div>

                <!-- Total Peserta Gim -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Peserta Gim</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1 font-mono">
                            {{ $allWorks->count() }}<span class="text-slate-400 text-xs font-semibold ml-1">Karya</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        Karya yang Anda evaluasi
                    </p>
                </div>
            @else
                <!-- Saran Media -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Saran Top 10: MPI</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-sky-600 mt-1 font-mono">
                            <span x-text="myMediaSuggestions"></span><span class="text-slate-400 text-xs font-semibold ml-1">Karya</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        Rekomendasi Media Interaktif
                    </p>
                </div>

                <!-- Total Peserta Media -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Peserta MPI</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-sky-600 mt-1 font-mono">
                            {{ $allWorks->count() }}<span class="text-slate-400 text-xs font-semibold ml-1">Karya</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">
                        Karya yang Anda evaluasi
                    </p>
                </div>
            @endif

            <!-- Total Finalis yang Disetujui Admin -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Finalis Resmi Top 10</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-[#5e43f3] mt-1 font-mono">
                        {{ $stats['finalists_count'] }}<span class="text-slate-300 text-lg font-normal"> / {{ !empty($judge->category) ? '10' : '20' }}</span>
                    </div>
                </div>
                <p class="text-[11px] text-[#5e43f3] mt-2 font-semibold">
                    {{ !empty($judge->category) ? ($judge->category === 'gim_edukasi' ? 'Top 10 Gim Edukasi' : 'Top 10 Media Interaktif') : 'Keputusan resmi Admin' }}
                </p>
            </div>

            <!-- Status Penilaian Juri Ini -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Status Nilai Anda</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1 font-mono">
                        {{ $stats['scored_finalists_count'] }}<span class="text-slate-300 text-lg font-normal"> / {{ $stats['finalists_count'] }}</span>
                    </div>
                </div>
                <p class="text-[11px] text-emerald-600 mt-2 font-semibold">
                    {{ $stats['remaining_finalists_to_score'] > 0 ? $stats['remaining_finalists_to_score'] . ' karya belum dinilai' : 'Semua finalis telah dinilai' }}
                </p>
            </div>
        </div>

        <!-- 3 TABS UTAMA DEWAN JURI -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-2 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center gap-1 sm:gap-2 overflow-x-auto p-1">
                <!-- Tab 1: Semua Peserta -->
                <button 
                    type="button" 
                    @click="activeTab = 'all'"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 cursor-pointer whitespace-nowrap"
                    :class="activeTab === 'all' ? 'bg-[#5e43f3] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>1. Semua Peserta</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
                        {{ $allWorks->count() }}
                    </span>
                </button>

                <!-- Tab 2: Saran Saya -->
                <button 
                    type="button" 
                    @click="activeTab = 'my_suggestions'"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 cursor-pointer whitespace-nowrap"
                    :class="activeTab === 'my_suggestions' ? 'bg-purple-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span>2. Saran Saya</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" :class="activeTab === 'my_suggestions' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800'">
                        <span x-text="myGimSuggestions + myMediaSuggestions"></span>
                    </span>
                </button>

                <!-- Tab 3: Finalis (Siap Dinilai) -->
                <button 
                    type="button" 
                    @click="activeTab = 'finalists'"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 cursor-pointer whitespace-nowrap"
                    :class="activeTab === 'finalists' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>3. Finalis Top 10 (Dinilai)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" :class="activeTab === 'finalists' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'">
                        {{ $finalists->count() }}
                    </span>
                </button>
            </div>

            <!-- Petunjuk Singkat Tab -->
            <div class="px-3 py-1.5 rounded-xl bg-slate-50 text-[11px] text-slate-500 font-medium">
                <template x-if="activeTab === 'all'">
                    <span>💡 Klik <strong>Sarankan Top 10</strong> untuk mengisi checklist kriteria kelayakan.</span>
                </template>
                <template x-if="activeTab === 'my_suggestions'">
                    <span>💡 Daftar karya yang Anda usulkan beserta checklist alasan. Anda dapat memperbarui/menarik saran.</span>
                </template>
                <template x-if="activeTab === 'finalists'">
                    <span>⭐ Hanya karya di tab ini yang dinilai menggunakan <strong>Rubrik Bab IV</strong>.</span>
                </template>
            </div>
        </div>

        <!-- Filter Kategori, Pencarian Form, dan View Switcher -->
        <div class="space-y-3">
            <form method="GET" action="{{ route('judge.dashboard') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <input type="hidden" name="tab" :value="activeTab">
                @if (!empty($judge->category))
                    <input type="hidden" name="category" value="{{ $judge->category }}">
                @endif

                <!-- Filter Kategori Button Group / Locked Category Badge -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    @if (empty($judge->category))
                        <a 
                            :href="'{{ route('judge.dashboard') }}?tab=' + activeTab + '&category=all'" 
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $category === 'all' ? 'bg-slate-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
                        >
                            Semua Kategori
                        </a>
                        <a 
                            :href="'{{ route('judge.dashboard') }}?tab=' + activeTab + '&category=gim_edukasi'" 
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $category === 'gim_edukasi' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
                        >
                            Gim Edukasi
                        </a>
                        <a 
                            :href="'{{ route('judge.dashboard') }}?tab=' + activeTab + '&category=media_pembelajaran_interaktif'" 
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $category === 'media_pembelajaran_interaktif' ? 'bg-sky-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
                        >
                            Media Interaktif (MPI)
                        </a>
                    @else
                        <!-- Badge Terkunci: Juri hanya melihat & menilai kategori penugasannya -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold shadow-2xs {{ $judge->category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-sky-50 text-sky-800 border border-sky-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $judge->category === 'gim_edukasi' ? 'bg-amber-500 animate-pulse' : 'bg-sky-500 animate-pulse' }}"></span>
                            <span>Kategori Tugas: {{ $judge->category === 'gim_edukasi' ? 'Gim Edukasi' : 'Media Pembelajaran Interaktif (MPI)' }}</span>
                            <span class="text-[10px] text-slate-500 font-medium">({{ $allWorks->count() }} Peserta / {{ $finalists->count() }} Finalis)</span>
                        </div>
                    @endif
                </div>

                <!-- Pencarian Input & View Switcher Container -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-64">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari peserta / sekolah..."
                            class="w-full pl-9 pr-4 py-2 rounded-xl text-xs bg-white border border-slate-200 focus:border-[#5e43f3] focus:ring-2 focus:ring-purple-100 outline-none"
                        >
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- View Switcher (Grid vs List) -->
                    <div class="inline-flex p-1 bg-white rounded-2xl border border-slate-200/80 shadow-2xs shrink-0">
                        <button 
                            type="button" 
                            @click="setViewMode('grid')"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                            :class="viewMode === 'grid' ? 'bg-[#5e43f3] text-white shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                            title="Tampilan Grid (Kartu)"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span class="hidden sm:inline">Grid</span>
                        </button>
                        <button 
                            type="button" 
                            @click="setViewMode('list')"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                            :class="(viewMode === 'list' || viewMode === 'table') ? 'bg-[#5e43f3] text-white shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                            title="Tampilan List (Tabel)"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            <span class="hidden sm:inline">List</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ======================================================= -->
        <!-- KONTEN TAB 1: SEMUA PESERTA (FULL LIST & SUGGEST)       -->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'all'" class="space-y-4">
            
            <!-- 1. Grid View (Kartu) -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($allWorks as $work)
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-soft transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Header: Kategori & Avatar Juri yang Menyarankan -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-sky-50 text-sky-800 border border-sky-200/60' }}">
                                    {{ $work->category_label }}
                                </span>

                                <!-- Avatar Lingkaran Juri yang Menyarankan Karya Ini -->
                                <div class="flex items-center gap-1" @click.stop>
                                    <template x-for="j in getSuggestions({{ $work->id }})" :key="j.userId">
                                        <div 
                                            class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shadow-2xs ring-1 ring-white cursor-pointer hover:scale-125 transition-transform"
                                            :class="j.colorClass"
                                            x-text="j.code"
                                            @mouseenter="showJudgeOverview(j, $event)"
                                            @mouseleave="hideJudgeOverview()"
                                            @click.stop="openJudgeModal(j)"
                                        ></div>
                                    </template>
                                    
                                    <!-- Badge jika disarankan oleh semua 3 Juri -->
                                    <template x-if="getSuggestions({{ $work->id }}).length >= 3">
                                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 ml-1" title="Disarankan oleh 3 Dewan Juri sekaligus!">
                                            ⭐ 3/3
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Info Peserta -->
                            <div class="flex items-center gap-3 mb-4">
                                <img 
                                    src="{{ $work->profile_photo_url }}" 
                                    alt="{{ $work->full_name }}" 
                                    class="w-12 h-12 rounded-2xl object-cover border border-slate-200 shadow-xs bg-slate-100 shrink-0"
                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($work->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                >
                                <div class="overflow-hidden">
                                    <h3 class="font-extrabold text-slate-900 text-sm truncate" title="{{ $work->full_name }}">
                                        {{ $work->full_name }}
                                    </h3>
                                    <p class="text-xs font-bold text-slate-700 uppercase truncate">{{ $work->school_name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ $work->regency_city }}</p>
                                </div>
                            </div>

                            <!-- Link Karya Digital -->
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 mb-4 flex items-center justify-between">
                                <span class="text-xs text-slate-600 font-medium">Karya Multimedia Digital:</span>
                                <a 
                                    href="{{ $work->media_link }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-[#5e43f3] hover:underline"
                                >
                                    <span>Buka Karya &rarr;</span>
                                </a>
                            </div>

                            <!-- Badge Status Finalis Jika Sudah Disetujui Admin -->
                            @if ($work->is_curated_top10)
                                <div class="mb-3 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Finalis Resmi (Dapat dinilai di tab Finalis)</span>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Action: Tombol Suggest Top 10 (Memicu Modal Checklist) -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                            <button 
                                type="button" 
                                @click="isFormOpen ? openSuggestionModal(@js($work)) : null"
                                :disabled="!isFormOpen"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs flex items-center gap-1.5"
                                :class="!isFormOpen 
                                    ? 'opacity-60 cursor-not-allowed bg-slate-100 text-slate-400 border border-slate-200' 
                                    : (isSuggested({{ $work->id }}) 
                                        ? 'bg-purple-600 hover:bg-purple-700 text-white cursor-pointer' 
                                        : 'bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 cursor-pointer')"
                                :title="!isFormOpen ? 'Formulir telah ditutup' : ''"
                            >
                                <span x-text="!isFormOpen ? '🔒 Formulir Ditutup' : (isSuggested({{ $work->id }}) ? '✓ Disarankan' : '+ Sarankan Top 10')"></span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                        <p class="font-bold text-sm text-slate-800">Tidak ada data pendaftar yang cocok dengan filter pencarian.</p>
                    </div>
                @endforelse
            </div>

            <!-- 2. List / Table View -->
            <div x-show="viewMode === 'list' || viewMode === 'table'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-4 pl-6 pr-4">Peserta &amp; No. Registrasi</th>
                                <th class="py-4 px-4">Instansi &amp; Daerah</th>
                                <th class="py-4 px-4">Kategori &amp; Status</th>
                                <th class="py-4 px-4">Berkas Karya</th>
                                <th class="py-4 px-4 text-center">Saran Juri</th>
                                <th class="py-4 pr-6 pl-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($allWorks as $work)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 pl-6 pr-4">
                                        <div class="flex items-center space-x-3">
                                            <img 
                                                src="{{ $work->profile_photo_url }}" 
                                                alt="{{ $work->full_name }}" 
                                                class="w-10 h-10 rounded-2xl object-cover border border-slate-200 bg-slate-100 shrink-0"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($work->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                            >
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm">{{ $work->full_name }}</div>
                                                <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-[#5e43f3] border border-purple-200">
                                                    {{ $work->registration_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800">{{ $work->school_name }}</div>
                                        <div class="text-slate-400 text-[11px]">{{ $work->regency_city }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-700 border border-amber-200/80' : 'bg-sky-50 text-sky-700 border border-sky-200/80' }}">
                                                {{ $work->category_label }}
                                            </span>
                                            @if ($work->is_curated_top10)
                                                <div>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        ✓ Finalis Resmi Top 10
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <a 
                                            href="{{ $work->media_link }}" 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-[#5e43f3] hover:underline"
                                        >
                                            <span>Buka Karya &rarr;</span>
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1" @click.stop>
                                            <template x-for="j in getSuggestions({{ $work->id }})" :key="j.userId">
                                                <div 
                                                    class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shadow-2xs ring-1 ring-white cursor-pointer hover:scale-125 transition-transform"
                                                    :class="j.colorClass"
                                                    x-text="j.code"
                                                    @mouseenter="showJudgeOverview(j, $event)"
                                                    @mouseleave="hideJudgeOverview()"
                                                    @click.stop="openJudgeModal(j)"
                                                ></div>
                                            </template>
                                            <template x-if="getSuggestions({{ $work->id }}).length >= 3">
                                                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 ml-1">
                                                    ⭐ 3/3
                                                </span>
                                            </template>
                                            <template x-if="getSuggestions({{ $work->id }}).length === 0">
                                                <span class="text-slate-300 text-[11px]">-</span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-3.5 pr-6 pl-4 text-right">
                                        <button 
                                            type="button" 
                                            @click="isFormOpen ? openSuggestionModal(@js($work)) : null"
                                            :disabled="!isFormOpen"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs inline-flex items-center gap-1.5"
                                            :class="!isFormOpen 
                                                ? 'opacity-60 cursor-not-allowed bg-slate-100 text-slate-400 border border-slate-200' 
                                                : (isSuggested({{ $work->id }}) 
                                                    ? 'bg-purple-600 hover:bg-purple-700 text-white cursor-pointer' 
                                                    : 'bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 cursor-pointer')"
                                            :title="!isFormOpen ? 'Formulir telah ditutup' : ''"
                                        >
                                            <span x-text="!isFormOpen ? '🔒 Formulir Ditutup' : (isSuggested({{ $work->id }}) ? '✓ Disarankan' : '+ Sarankan Top 10')"></span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500 font-medium">
                                        Tidak ada data pendaftar yang cocok dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- KONTEN TAB 2: SARAN SAYA                                -->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'my_suggestions'" class="space-y-4">
            <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-xs text-purple-900 font-medium flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">📌</span>
                    <span>Ini adalah daftar karya yang telah <strong>Anda sarankan</strong> ke Panitia. Anda dapat mengklik karya untuk meninjau checklist atau menarik saran.</span>
                </div>
                <div class="font-mono font-bold whitespace-nowrap text-purple-800">
                    Total Saran: <span x-text="myGimSuggestions + myMediaSuggestions"></span> / 20
                </div>
            </div>

            <!-- 1. Grid View (Kartu) -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($mySuggestions as $work)
                    <div 
                        x-show="isSuggested({{ $work->id }})" 
                        class="bg-white rounded-3xl p-5 border border-purple-200 shadow-xs hover:shadow-soft transition-all flex flex-col justify-between group"
                    >
                        <div>
                            <!-- Header: Kategori & Avatar Rekomendasi Juri Lain -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-sky-50 text-sky-800 border border-sky-200/60' }}">
                                    {{ $work->category_label }}
                                </span>

                                <div class="flex items-center gap-1" @click.stop>
                                    <template x-for="j in getSuggestions({{ $work->id }})" :key="j.userId">
                                        <div 
                                            class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shadow-2xs ring-1 ring-white cursor-pointer hover:scale-125 transition-transform"
                                            :class="j.colorClass"
                                            x-text="j.code"
                                            @mouseenter="showJudgeOverview(j, $event)"
                                            @mouseleave="hideJudgeOverview()"
                                            @click.stop="openJudgeModal(j)"
                                        ></div>
                                    </template>
                                </div>
                            </div>

                            <!-- Info Peserta -->
                            <div class="flex items-center gap-3 mb-4">
                                <img 
                                    src="{{ $work->profile_photo_url }}" 
                                    alt="{{ $work->full_name }}" 
                                    class="w-12 h-12 rounded-2xl object-cover border border-slate-200 shadow-xs bg-slate-100 shrink-0"
                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($work->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                >
                                <div class="overflow-hidden">
                                    <h3 class="font-extrabold text-slate-900 text-sm truncate" title="{{ $work->full_name }}">
                                        {{ $work->full_name }}
                                    </h3>
                                    <p class="text-xs font-bold text-slate-700 uppercase truncate">{{ $work->school_name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ $work->regency_city }}</p>
                                </div>
                            </div>

                            <!-- Link Karya Digital -->
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 mb-4 flex items-center justify-between">
                                <span class="text-xs text-slate-600 font-medium">Karya Multimedia Digital:</span>
                                <a 
                                    href="{{ $work->media_link }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-[#5e43f3] hover:underline"
                                >
                                    <span>Buka Karya &rarr;</span>
                                </a>
                            </div>

                            <!-- Status Finalis Admin -->
                            @if ($work->is_curated_top10)
                                <div class="mb-3 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Telah Disetujui Admin (Finalis Resmi)</span>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Action: Tombol Ubah Saran / Tarik Saran -->
                        <div class="pt-3 border-t border-purple-100 flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                            <button 
                                type="button" 
                                @click="isFormOpen ? openSuggestionModal(@js($work)) : null"
                                :disabled="!isFormOpen"
                                :class="!isFormOpen 
                                    ? 'opacity-60 cursor-not-allowed bg-slate-100 text-slate-400 border border-slate-200' 
                                    : 'bg-purple-100 hover:bg-purple-200 text-purple-800 border border-purple-300 cursor-pointer'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs flex items-center gap-1"
                                :title="!isFormOpen ? 'Formulir telah ditutup' : 'Ubah atau tarik saran'"
                            >
                                <span x-text="!isFormOpen ? '🔒 Formulir telah ditutup' : '✏️ Ubah / Tarik Saran'"></span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                        <p class="font-bold text-sm text-slate-800">Anda belum menyarankan karya peserta untuk nominasi Top 10.</p>
                        <p class="text-xs text-slate-400 mt-1">Buka tab "1. Semua Peserta" lalu klik tombol "Sarankan Top 10".</p>
                    </div>
                @endforelse
            </div>

            <!-- 2. List / Table View -->
            <div x-show="viewMode === 'list' || viewMode === 'table'" class="bg-white rounded-3xl border border-purple-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-purple-100 bg-purple-50/50 text-[11px] font-bold uppercase tracking-wider text-purple-900">
                                <th class="py-4 pl-6 pr-4">Peserta &amp; No. Registrasi</th>
                                <th class="py-4 px-4">Instansi &amp; Daerah</th>
                                <th class="py-4 px-4">Kategori</th>
                                <th class="py-4 px-4">Berkas Karya</th>
                                <th class="py-4 px-4 text-center">Saran Juri</th>
                                <th class="py-4 pr-6 pl-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100 text-xs">
                            @forelse ($mySuggestions as $work)
                                <tr x-show="isSuggested({{ $work->id }})" class="hover:bg-purple-50/40 transition-colors">
                                    <td class="py-3.5 pl-6 pr-4">
                                        <div class="flex items-center space-x-3">
                                            <img 
                                                src="{{ $work->profile_photo_url }}" 
                                                alt="{{ $work->full_name }}" 
                                                class="w-10 h-10 rounded-2xl object-cover border border-purple-200 bg-slate-100 shrink-0"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($work->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                            >
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm">{{ $work->full_name }}</div>
                                                <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-[#5e43f3] border border-purple-200">
                                                    {{ $work->registration_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800">{{ $work->school_name }}</div>
                                        <div class="text-slate-400 text-[11px]">{{ $work->regency_city }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-700 border border-amber-200/80' : 'bg-sky-50 text-sky-700 border border-sky-200/80' }}">
                                                {{ $work->category_label }}
                                            </span>
                                            @if ($work->is_curated_top10)
                                                <div>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        ✓ Finalis Resmi Top 10
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <a 
                                            href="{{ $work->media_link }}" 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-[#5e43f3] hover:underline"
                                        >
                                            <span>Buka Karya &rarr;</span>
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1" @click.stop>
                                            <template x-for="j in getSuggestions({{ $work->id }})" :key="j.userId">
                                                <div 
                                                    class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shadow-2xs ring-1 ring-white cursor-pointer hover:scale-125 transition-transform"
                                                    :class="j.colorClass"
                                                    x-text="j.code"
                                                    @mouseenter="showJudgeOverview(j, $event)"
                                                    @mouseleave="hideJudgeOverview()"
                                                    @click.stop="openJudgeModal(j)"
                                                ></div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-3.5 pr-6 pl-4 text-right">
                                        <button 
                                            type="button" 
                                            @click="isFormOpen ? openSuggestionModal(@js($work)) : null"
                                            :disabled="!isFormOpen"
                                            :class="!isFormOpen 
                                                ? 'opacity-60 cursor-not-allowed bg-slate-100 text-slate-400 border border-slate-200' 
                                                : 'bg-purple-100 hover:bg-purple-200 text-purple-800 border border-purple-300 cursor-pointer'"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs inline-flex items-center gap-1"
                                            :title="!isFormOpen ? 'Formulir telah ditutup' : 'Ubah atau tarik saran'"
                                        >
                                            <span x-text="!isFormOpen ? '🔒 Formulir telah ditutup' : '✏️ Ubah / Tarik'"></span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500 font-medium">
                                        Anda belum menyarankan karya peserta untuk nominasi Top 10.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- KONTEN TAB 3: FINALIS TOP 10 (SIAP DINILAI DENGAN RUBRIK)-->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'finalists'" class="space-y-4">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 font-medium flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">⭐</span>
                    <span>Karya di tab ini adalah <strong>Finalis Resmi Top 10</strong> yang disetujui Panitia/Admin. Beri penilaian objektif sesuai <strong>Rubrik Bab IV</strong>.</span>
                </div>
                <div class="font-mono font-bold whitespace-nowrap text-emerald-800">
                    Selesai Dinilai: {{ $stats['scored_finalists_count'] }} / {{ $stats['finalists_count'] }}
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2 px-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-extrabold text-slate-800">Lembar Penilaian Finalis Top 10</h2>
                    <span class="text-xs text-slate-400 hidden sm:inline">• Rekap Multi-Juri Standar Bab IV</span>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white border border-slate-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full" :class="rekapPollStatus === 'connected' ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500'"></span>
                    <span class="text-slate-600" x-text="rekapPollStatus === 'connected' ? 'Realtime Terhubung (Otomatis Diperbarui)' : 'Memperbarui data...'"></span>
                </div>
            </div>

            <!-- 1. Grid View (Kartu Finalis Top 10) -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($finalists as $work)
                    @php
                        $recap = $work->recap ?? $work->multi_judge_recap;
                        $myStatus = $work->my_status ?? 'Belum dinilai';
                        $hasMyScore = ($myStatus !== 'Belum dinilai');
                        $myJudgeRecap = $recap['judges'][$myRole] ?? null;
                    @endphp
                    <div class="bg-white rounded-3xl p-5 border {{ $myStatus === 'Sudah dinilai' ? 'border-emerald-300 shadow-xs' : ($myStatus === 'Sebagian' ? 'border-amber-300 shadow-xs' : 'border-slate-200 shadow-xs') }} hover:shadow-soft transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Header: Kategori, Peringkat & Status Penilaian Mandiri Juri Ini -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-1.5">
                                    <div 
                                        class="w-6 h-6 rounded-lg flex items-center justify-center font-extrabold text-[11px]"
                                        :class="ranksMap[{{ $work->id }}] === 1 ? 'bg-amber-400 text-white shadow-xs' : (ranksMap[{{ $work->id }}] === 2 ? 'bg-slate-300 text-slate-800' : (ranksMap[{{ $work->id }}] === 3 ? 'bg-amber-600 text-white' : (ranksMap[{{ $work->id }}] ? 'bg-slate-100 text-slate-700' : 'bg-slate-100 text-slate-400')))"
                                        x-text="ranksMap[{{ $work->id }}] ? ('#' + ranksMap[{{ $work->id }}]) : '-'"
                                        title="Peringkat Kategori"
                                    >
                                        {{ $work->category_rank ? '#' . $work->category_rank : '-' }}
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-sky-50 text-sky-800 border border-sky-200/60' }}">
                                        {{ $work->category_label }}
                                    </span>
                                </div>

                                @if ($myStatus === 'Sudah dinilai')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1 shadow-2xs">
                                        <span>✓ Anda:</span>
                                        <span class="font-mono">{{ number_format($myJudgeRecap['weighted_score'] ?? 0, 2) }}</span>
                                    </span>
                                @elseif ($myStatus === 'Sebagian')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1 shadow-2xs">
                                        <span>⏳ Anda:</span>
                                        <span class="font-mono">{{ $myJudgeRecap['progress_text'] ?? '' }}</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Belum Anda Nilai
                                    </span>
                                @endif
                            </div>

                            <!-- Info Peserta -->
                            <div class="flex items-center gap-3 mb-4">
                                <img 
                                    src="{{ $work->profile_photo_url }}" 
                                    alt="{{ $work->full_name }}" 
                                    class="w-12 h-12 rounded-2xl object-cover border border-slate-200 shadow-xs bg-slate-100 shrink-0"
                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($work->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                >
                                <div class="overflow-hidden">
                                    <h3 class="font-extrabold text-slate-900 text-sm truncate" title="{{ $work->full_name }}">
                                        {{ $work->full_name }}
                                    </h3>
                                    <p class="text-xs font-bold text-slate-700 uppercase truncate">{{ $work->school_name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ $work->regency_city }}</p>
                                </div>
                            </div>

                            <!-- Panel Realtime Ringkasan Nilai Akhir / Sementara (Tema Putih Bersih) -->
                            <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 text-slate-900 mb-3 shadow-2xs">
                                <div class="flex items-center justify-between text-[11px] mb-1">
                                    <span class="font-extrabold tracking-wider uppercase text-[10px]"
                                        :class="scoresMap[{{ $work->id }}]?.is_complete ? 'text-emerald-600' : 'text-amber-600'"
                                        x-text="scoresMap[{{ $work->id }}]?.status_label || '{{ $recap['status_label'] }}'">
                                        {{ $recap['status_label'] }}
                                    </span>
                                    <span class="text-slate-400 text-[10px] font-mono font-medium" x-text="(scoresMap[{{ $work->id }}]?.progress_all || '{{ $recap['progress_all'] ?? '0/12' }}') + ' Terisi'">{{ $recap['progress_all'] ?? '0/12' }} Terisi</span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <div class="text-2xl font-extrabold font-mono text-slate-900">
                                        <span x-text="scoresMap[{{ $work->id }}]?.display_score || '{{ $recap['display_score'] }}'">{{ $recap['display_score'] }}</span>
                                        <span class="text-[11px] font-sans font-normal text-slate-400">/ 100</span>
                                    </div>
                                    <template x-if="scoresMap[{{ $work->id }}]?.is_complete">
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-extrabold border border-emerald-200 flex items-center gap-1">
                                            <span>✓</span>
                                            <span>Lengkap (3 Juri)</span>
                                        </span>
                                    </template>
                                    <template x-if="!scoresMap[{{ $work->id }}]?.is_complete">
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-extrabold border border-amber-200 truncate max-w-[150px]" :title="scoresMap[{{ $work->id }}]?.waiting_text" x-text="scoresMap[{{ $work->id }}]?.waiting_text || '{{ $recap['waiting_text'] }}'">
                                            {{ $recap['waiting_text'] }}
                                        </span>
                                    </template>
                                </div>

                                <!-- 3 Chips Status Juri (Tema Putih Bersih) -->
                                <div class="grid grid-cols-3 gap-1.5 pt-2 mt-2 border-t border-slate-100 text-[10px]">
                                    <!-- J1 -->
                                    <div class="p-1.5 rounded-xl bg-slate-50 border border-slate-100 text-center {{ $myRole === 1 ? 'ring-2 ring-indigo-500 bg-indigo-50/40 border-indigo-200' : '' }}">
                                        <div class="text-slate-500 font-semibold text-[10px]">J1 (55%)</div>
                                        <div class="font-mono font-extrabold" :class="scoresMap[{{ $work->id }}]?.judges?.[1]?.is_complete ? 'text-emerald-600' : 'text-amber-600'" x-text="scoresMap[{{ $work->id }}]?.judges?.[1]?.is_complete ? Number(scoresMap[{{ $work->id }}]?.judges?.[1]?.weighted_score).toFixed(1) : (scoresMap[{{ $work->id }}]?.judges?.[1]?.progress_text || '0/5')">
                                            {{ ($recap['judges'][1]['is_complete'] ?? false) ? number_format($recap['judges'][1]['weighted_score'], 1) : ($recap['judges'][1]['progress_text'] ?? '0/5') }}
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-medium truncate mt-0.5 max-w-[85px] mx-auto" x-text="scoresMap[{{ $work->id }}]?.judges?.[1]?.judge_name || 'Juri 1'">
                                            {{ $recap['judges'][1]['judge_name'] ?? 'Juri 1' }}
                                        </div>
                                    </div>
                                    <!-- J2 -->
                                    <div class="p-1.5 rounded-xl bg-slate-50 border border-slate-100 text-center {{ $myRole === 2 ? 'ring-2 ring-indigo-500 bg-indigo-50/40 border-indigo-200' : '' }}">
                                        <div class="text-slate-500 font-semibold text-[10px]">J2 (30%)</div>
                                        <div class="font-mono font-extrabold" :class="scoresMap[{{ $work->id }}]?.judges?.[2]?.is_complete ? 'text-emerald-600' : 'text-amber-600'" x-text="scoresMap[{{ $work->id }}]?.judges?.[2]?.is_complete ? Number(scoresMap[{{ $work->id }}]?.judges?.[2]?.weighted_score).toFixed(1) : (scoresMap[{{ $work->id }}]?.judges?.[2]?.progress_text || '0/4')">
                                            {{ ($recap['judges'][2]['is_complete'] ?? false) ? number_format($recap['judges'][2]['weighted_score'], 1) : ($recap['judges'][2]['progress_text'] ?? '0/4') }}
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-medium truncate mt-0.5 max-w-[85px] mx-auto" x-text="scoresMap[{{ $work->id }}]?.judges?.[2]?.judge_name || 'Juri 2'">
                                            {{ $recap['judges'][2]['judge_name'] ?? 'Juri 2' }}
                                        </div>
                                    </div>
                                    <!-- J3 -->
                                    <div class="p-1.5 rounded-xl bg-slate-50 border border-slate-100 text-center {{ $myRole === 3 ? 'ring-2 ring-indigo-500 bg-indigo-50/40 border-indigo-200' : '' }}">
                                        <div class="text-slate-500 font-semibold text-[10px]">J3 (15%)</div>
                                        <div class="font-mono font-extrabold" :class="scoresMap[{{ $work->id }}]?.judges?.[3]?.is_complete ? 'text-emerald-600' : 'text-amber-600'" x-text="scoresMap[{{ $work->id }}]?.judges?.[3]?.is_complete ? Number(scoresMap[{{ $work->id }}]?.judges?.[3]?.weighted_score).toFixed(1) : (scoresMap[{{ $work->id }}]?.judges?.[3]?.progress_text || '0/3')">
                                            {{ ($recap['judges'][3]['is_complete'] ?? false) ? number_format($recap['judges'][3]['weighted_score'], 1) : ($recap['judges'][3]['progress_text'] ?? '0/3') }}
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-medium truncate mt-0.5 max-w-[85px] mx-auto" x-text="scoresMap[{{ $work->id }}]?.judges?.[3]?.judge_name || 'Juri 3'">
                                            {{ $recap['judges'][3]['judge_name'] ?? 'Juri 3' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Link Karya Digital -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium text-[11px]">Media Karya:</span>
                                <a 
                                    href="{{ $work->media_link }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 font-bold text-[#5e43f3] hover:underline text-[11px]"
                                >
                                    <span>Buka Karya &rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- Footer Action: Tombol Input / Edit Nilai & Reset -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                            <div class="flex items-center gap-1.5">
                                @if ($hasMyScore)
                                    <button 
                                        type="button" 
                                        @click="confirmResetScore(@js($work))"
                                        class="px-2.5 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 border border-rose-200 transition shadow-2xs cursor-pointer flex items-center gap-1"
                                        title="Reset / Hapus Nilai Anda untuk karya ini"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="hidden sm:inline">Reset</span>
                                    </button>
                                @endif
                                <button 
                                    type="button" 
                                    @click="openScoring(@js($work))"
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-1.5 {{ $hasMyScore ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-[#5e43f3] hover:bg-indigo-700 text-white' }}"
                                >
                                    <span>{{ $hasMyScore ? '✏️ Ubah Nilai' : '📝 Beri Nilai' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                        <p class="font-bold text-sm text-slate-800">Belum ada karya yang disetujui Admin masuk Top 10.</p>
                        <p class="text-xs text-slate-400 mt-1">Gunakan tab "Semua Peserta" untuk memberikan rekomendasi karya ke Admin.</p>
                    </div>
                @endforelse
            </div>

            <!-- 2. List / Table View (Finalis Top 10 - Tampilan Lengkap Multi-Juri Seperti Admin) -->
            <div x-show="viewMode === 'list' || viewMode === 'table'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-4 pl-5 pr-2">Rank</th>
                                <th class="py-4 px-4">Finalis &amp; Asal Instansi</th>
                                <th class="py-4 px-3">Kategori</th>
                                <th class="py-4 px-3">Juri 1: Pedagogis &amp; Teknis (55%)</th>
                                <th class="py-4 px-3">Juri 2: Kreativitas &amp; Desain (30%)</th>
                                <th class="py-4 px-3">Juri 3: Presentasi &amp; Dampak (15%)</th>
                                <th class="py-4 px-4 text-center">Status &amp; Nilai Akhir</th>
                                <th class="py-4 pr-6 pl-4 text-right">Aksi Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($finalists as $work)
                                @php
                                    $recap = $work->recap ?? $work->multi_judge_recap;
                                    $myStatus = $work->my_status ?? 'Belum dinilai';
                                    $hasMyScore = ($myStatus !== 'Belum dinilai');
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <!-- Rank Peringkat Realtime -->
                                    <td class="py-3.5 pl-5 pr-2 align-middle">
                                        <div 
                                            class="w-7 h-7 rounded-xl flex items-center justify-center font-extrabold text-xs"
                                            :class="ranksMap[{{ $work->id }}] === 1 ? 'bg-amber-400 text-white shadow-xs' : (ranksMap[{{ $work->id }}] === 2 ? 'bg-slate-300 text-slate-800' : (ranksMap[{{ $work->id }}] === 3 ? 'bg-amber-600 text-white' : (ranksMap[{{ $work->id }}] ? 'bg-slate-100 text-slate-700' : 'bg-slate-100 text-slate-400')))"
                                            x-text="ranksMap[{{ $work->id }}] ? ('#' + ranksMap[{{ $work->id }}]) : '-'"
                                        >
                                            {{ $work->category_rank ? '#' . $work->category_rank : '-' }}
                                        </div>
                                    </td>

                                    <!-- Finalis & Info Sekolah -->
                                    <td class="py-3.5 px-4 align-middle">
                                        <div class="flex items-center space-x-3">
                                            <img 
                                                src="{{ $work->profile_photo_url }}" 
                                                alt="{{ $work->full_name }}" 
                                                class="w-10 h-10 rounded-2xl object-cover border border-slate-200 bg-slate-100 shrink-0"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($work->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                            >
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm">{{ $work->full_name }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $work->school_name }} • {{ $work->regency_city }}</div>
                                                <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded-md text-[10px] font-mono font-bold bg-purple-50 text-[#5e43f3] border border-purple-200">
                                                    {{ $work->registration_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="py-3.5 px-3 align-middle whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-sky-50 text-sky-800 border border-sky-200/60' }}">
                                            {{ $work->category_label }}
                                        </span>
                                    </td>

                                    <!-- Juri 1: Pedagogis & Teknis (55%) -->
                                    <td class="py-3.5 px-3 whitespace-nowrap align-middle">
                                        <div class="{{ $myRole === 1 ? 'p-1.5 -m-1.5 rounded-xl bg-indigo-50/60 border border-indigo-200/80' : '' }}">
                                            <template x-if="scoresMap[{{ $work->id }}]?.judges?.[1]?.is_complete">
                                                <div class="flex items-baseline gap-1">
                                                    <span class="font-mono font-extrabold text-slate-900 text-xs" x-text="Number(scoresMap[{{ $work->id }}]?.judges?.[1]?.weighted_score).toFixed(2)"></span>
                                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'(' + scoresMap[{{ $work->id }}]?.judges?.[1]?.total_score + '/25 pt)'"></span>
                                                </div>
                                            </template>
                                            <template x-if="!scoresMap[{{ $work->id }}]?.judges?.[1]?.is_complete">
                                                <div>
                                                    <span 
                                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold"
                                                        :class="(scoresMap[{{ $work->id }}]?.judges?.[1]?.filled_count > 0) ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-400'"
                                                        x-text="scoresMap[{{ $work->id }}]?.judges?.[1]?.progress_text || 'Belum dinilai'"
                                                    >
                                                        {{ $recap['judges'][1]['progress_text'] ?? 'Belum dinilai' }}
                                                    </span>
                                                </div>
                                            </template>
                                            <div class="text-[10px] text-slate-400 font-medium truncate max-w-[130px] flex items-center gap-1 mt-0.5">
                                                <span x-text="scoresMap[{{ $work->id }}]?.judges?.[1]?.judge_name">{{ $recap['judges'][1]['judge_name'] ?? 'Juri 1' }}</span>
                                                @if ($myRole === 1)
                                                    <span class="px-1 py-0.2 rounded text-[9px] font-extrabold bg-[#5e43f3] text-white">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Juri 2: Kreativitas & Desain (30%) -->
                                    <td class="py-3.5 px-3 whitespace-nowrap align-middle">
                                        <div class="{{ $myRole === 2 ? 'p-1.5 -m-1.5 rounded-xl bg-indigo-50/60 border border-indigo-200/80' : '' }}">
                                            <template x-if="scoresMap[{{ $work->id }}]?.judges?.[2]?.is_complete">
                                                <div class="flex items-baseline gap-1">
                                                    <span class="font-mono font-extrabold text-slate-900 text-xs" x-text="Number(scoresMap[{{ $work->id }}]?.judges?.[2]?.weighted_score).toFixed(2)"></span>
                                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'(' + scoresMap[{{ $work->id }}]?.judges?.[2]?.total_score + '/20 pt)'"></span>
                                                </div>
                                            </template>
                                            <template x-if="!scoresMap[{{ $work->id }}]?.judges?.[2]?.is_complete">
                                                <div>
                                                    <span 
                                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold"
                                                        :class="(scoresMap[{{ $work->id }}]?.judges?.[2]?.filled_count > 0) ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-400'"
                                                        x-text="scoresMap[{{ $work->id }}]?.judges?.[2]?.progress_text || 'Belum dinilai'"
                                                    >
                                                        {{ $recap['judges'][2]['progress_text'] ?? 'Belum dinilai' }}
                                                    </span>
                                                </div>
                                            </template>
                                            <div class="text-[10px] text-slate-400 font-medium truncate max-w-[130px] flex items-center gap-1 mt-0.5">
                                                <span x-text="scoresMap[{{ $work->id }}]?.judges?.[2]?.judge_name">{{ $recap['judges'][2]['judge_name'] ?? 'Juri 2' }}</span>
                                                @if ($myRole === 2)
                                                    <span class="px-1 py-0.2 rounded text-[9px] font-extrabold bg-[#5e43f3] text-white">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Juri 3: Presentasi & Dampak (15%) -->
                                    <td class="py-3.5 px-3 whitespace-nowrap align-middle">
                                        <div class="{{ $myRole === 3 ? 'p-1.5 -m-1.5 rounded-xl bg-indigo-50/60 border border-indigo-200/80' : '' }}">
                                            <template x-if="scoresMap[{{ $work->id }}]?.judges?.[3]?.is_complete">
                                                <div class="flex items-baseline gap-1">
                                                    <span class="font-mono font-extrabold text-slate-900 text-xs" x-text="Number(scoresMap[{{ $work->id }}]?.judges?.[3]?.weighted_score).toFixed(2)"></span>
                                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'(' + scoresMap[{{ $work->id }}]?.judges?.[3]?.total_score + '/15 pt)'"></span>
                                                </div>
                                            </template>
                                            <template x-if="!scoresMap[{{ $work->id }}]?.judges?.[3]?.is_complete">
                                                <div>
                                                    <span 
                                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold"
                                                        :class="(scoresMap[{{ $work->id }}]?.judges?.[3]?.filled_count > 0) ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-400'"
                                                        x-text="scoresMap[{{ $work->id }}]?.judges?.[3]?.progress_text || 'Belum dinilai'"
                                                    >
                                                        {{ $recap['judges'][3]['progress_text'] ?? 'Belum dinilai' }}
                                                    </span>
                                                </div>
                                            </template>
                                            <div class="text-[10px] text-slate-400 font-medium truncate max-w-[130px] flex items-center gap-1 mt-0.5">
                                                <span x-text="scoresMap[{{ $work->id }}]?.judges?.[3]?.judge_name">{{ $recap['judges'][3]['judge_name'] ?? 'Juri 3' }}</span>
                                                @if ($myRole === 3)
                                                    <span class="px-1 py-0.2 rounded text-[9px] font-extrabold bg-[#5e43f3] text-white">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status & Nilai Akhir -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap align-middle">
                                        <template x-if="scoresMap[{{ $work->id }}]?.is_complete">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-sm font-extrabold text-[#5e43f3] bg-purple-50 px-2.5 py-1 rounded-xl border border-purple-200 font-mono shadow-2xs" x-text="Number(scoresMap[{{ $work->id }}]?.final_score).toFixed(2)">
                                                    {{ number_format($recap['final_score'] ?? 0, 2) }}
                                                </span>
                                                <span class="text-[10px] text-emerald-600 font-bold mt-0.5 flex items-center gap-0.5">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Nilai Akhir
                                                </span>
                                            </div>
                                        </template>
                                        <template x-if="!scoresMap[{{ $work->id }}]?.is_complete">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-200 font-mono" x-text="Number(scoresMap[{{ $work->id }}]?.interim_score || 0).toFixed(2)">
                                                    {{ number_format($recap['interim_score'] ?? 0, 2) }}
                                                </span>
                                                <span class="text-[10px] text-amber-600 font-semibold mt-0.5">
                                                    ⏳ Nilai Sementara
                                                </span>
                                                <span class="text-[9px] text-slate-400 font-medium max-w-[120px] truncate mt-0.5" x-text="scoresMap[{{ $work->id }}]?.waiting_text">
                                                    {{ $recap['waiting_text'] ?? '' }}
                                                </span>
                                            </div>
                                        </template>
                                    </td>

                                    <!-- Aksi Penilaian -->
                                    <td class="py-3.5 pr-6 pl-4 align-middle text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a 
                                                href="{{ $work->media_link }}" 
                                                target="_blank" 
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 text-[11px] font-bold text-[#5e43f3] hover:underline bg-purple-50/70 hover:bg-purple-100 px-2.5 py-1.5 rounded-xl border border-purple-200 transition"
                                                title="Buka Karya Digital Peserta"
                                            >
                                                <span>Buka</span>
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                            @if ($hasMyScore)
                                                <button 
                                                    type="button" 
                                                    @click="confirmResetScore(@js($work))"
                                                    class="p-1.5 rounded-xl text-rose-600 hover:bg-rose-50 border border-rose-200 transition shadow-2xs cursor-pointer"
                                                    title="Reset Nilai Anda untuk karya ini"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                            <button 
                                                type="button" 
                                                @click="openScoring(@js($work))"
                                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-1 {{ $hasMyScore ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-[#5e43f3] hover:bg-indigo-700 text-white' }}"
                                            >
                                                <span>{{ $hasMyScore ? '✏️ Ubah Nilai' : '📝 Beri Nilai' }}</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        Belum ada karya yang disetujui Admin masuk Top 10.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL FORM PENILAIAN RUBRIK BAB IV                      -->
        <!-- ======================================================= -->
        <div 
            x-show="scoringWork !== null" 
            x-cloak
            class="fixed inset-0 z-[999999] flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
            @click.self="closeScoring()"
        >
            <div 
                x-show="scoringWork !== null"
                class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col my-auto relative text-slate-800 max-h-[92vh]"
            >
                <!-- Modal Header -->
                <div class="px-6 py-4.5 bg-gradient-to-r from-emerald-700 via-teal-800 to-emerald-900 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-lg font-bold border border-white/30">
                            📝
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-white">Lembar Penilaian Finalis</h3>
                            <p class="text-xs text-emerald-200">Rubrik Standar Bab IV — EduTech Festival BTIKP</p>
                        </div>
                    </div>
                    <button type="button" @click="closeScoring()" class="w-8 h-8 rounded-xl bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
                    <!-- Alert Success -->
                    <template x-if="scoreSuccessMsg">
                        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                            <span>✓</span>
                            <span x-text="scoreSuccessMsg"></span>
                        </div>
                    </template>

                    <!-- Banner Kewenangan Juri -->
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50 via-purple-50 to-indigo-50 border border-indigo-200/80 text-indigo-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-xs shrink-0">
                                <span x-text="'J' + myRole"></span>
                            </div>
                            <div>
                                <div class="text-xs font-extrabold flex items-center gap-2">
                                    <span class="text-slate-900" x-text="myRoleConfig?.name"></span>
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-600 text-white text-[10px] font-bold" x-text="'Bobot ' + (myRoleConfig?.weight || 0) + '%'"></span>
                                </div>
                                <p class="text-xs font-bold text-indigo-900 mt-0.5" x-text="myRoleConfig?.aspect"></p>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    <span>Tanggung jawab: <strong x-text="myTotalItems + ' Subindikator'"></strong> • Skor Maksimal: <strong x-text="myMaxScore"></strong></span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:self-center shrink-0">
                            <div class="text-left sm:text-right px-3 py-1.5 rounded-xl bg-white border border-indigo-100 shadow-2xs">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Skala Penilaian</span>
                                <div class="text-xs font-extrabold text-indigo-900">Tombol Likert 1 – 5</div>
                            </div>
                        </div>
                    </div>

                    <!-- Identitas Peserta -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <img 
                                :src="scoringWork?.profile_photo_url || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(scoringWork?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200')" 
                                :alt="scoringWork?.full_name"
                                class="w-12 h-12 rounded-2xl object-cover border border-slate-200 bg-white shrink-0 shadow-2xs"
                                x-on:error="$event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(scoringWork?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200'"
                            >
                            <div class="truncate">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-slate-900 text-sm truncate" x-text="scoringWork?.full_name"></h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="scoringWork?.learning_category === 'gim_edukasi' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800'"
                                        x-text="scoringWork?.learning_category === 'gim_edukasi' ? 'Gim Edukasi' : 'Media Interaktif (MPI)'">
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 truncate mt-0.5" x-text="scoringWork?.school_name + ' • ' + scoringWork?.regency_city"></p>
                            </div>
                        </div>
                        <a :href="scoringWork?.media_link" target="_blank" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-[#5e43f3] hover:bg-indigo-50 font-bold text-xs transition whitespace-nowrap shadow-2xs flex items-center gap-1.5 shrink-0">
                            <span>Buka Karya</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                    <!-- Panel Nilai Akhir / Sementara Realtime (Tema Putih Bersih) -->
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 text-slate-800 space-y-3 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs uppercase font-extrabold tracking-wider" 
                                    :class="liveRecap?.is_complete ? 'text-emerald-600' : 'text-amber-600'" 
                                    x-text="liveRecap?.status_label || 'Nilai Sementara'">
                                </span>
                                <template x-if="liveRecap?.waiting_text">
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200" x-text="liveRecap.waiting_text"></span>
                                </template>
                                <template x-if="liveRecap?.is_complete">
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">✓ Lengkap (3 Juri)</span>
                                </template>
                            </div>

                            <!-- Sync Connection Status -->
                            <div class="flex items-center gap-1.5 text-[10px]" title="Sinkronisasi otomatis realtime seiring juri menilai">
                                <span class="w-2 h-2 rounded-full"
                                    :class="syncStatus === 'connected' ? 'bg-emerald-500 animate-pulse' : (syncStatus === 'updating' ? 'bg-amber-500 animate-ping' : 'bg-rose-500')"></span>
                                <span class="text-slate-500 font-medium" x-text="syncStatus === 'connected' ? '● Terhubung' : (syncStatus === 'updating' ? 'Memperbarui...' : '⚠️ Sambungan terputus')"></span>
                            </div>
                        </div>

                        <!-- Skor Utama & Kontribusi Juri -->
                        <div class="flex items-baseline justify-between pt-1 border-t border-slate-100">
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400">Total Nilai (Maks. 100)</div>
                                <div class="text-3xl font-extrabold font-mono tracking-tight text-slate-900 transition-all duration-300 flex items-baseline gap-2">
                                    <span x-text="liveRecap?.display_score || '0.00'"></span>
                                    <span class="text-xs font-semibold text-slate-400" x-text="liveRecap?.is_complete ? '(Nilai Akhir Resmi)' : '(Sementara)'"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Skor Anda (Juri <span x-text="myRole"></span>)</div>
                                <div class="text-xl font-extrabold font-mono text-emerald-600">
                                    <span x-text="myTotalScore + '/' + myMaxScore"></span>
                                    <span class="text-xs font-normal text-slate-400 font-sans">(<span x-text="myWeightedScore.toFixed(2)"></span>)</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3 Kolom Rincian Dewan Juri -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 border-t border-slate-100 text-[11px]">
                            <!-- Juri 1 -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 transition" :class="myRole === 1 ? 'ring-2 ring-indigo-500 bg-indigo-50/40 border-indigo-200' : ''">
                                <div class="flex items-center justify-between">
                                    <div class="font-bold text-slate-800 truncate" x-text="(liveRecap?.judges?.[1]?.name || 'Juri 1') + (myRole === 1 ? ' (Anda)' : '')"></div>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800 font-bold">55%</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Maksimal: 25 poin (5 subindikator)</div>
                                <div class="mt-1 font-mono font-bold" :class="liveRecap?.judges?.[1]?.is_complete ? 'text-emerald-600' : 'text-amber-600'">
                                    <template x-if="liveRecap?.judges?.[1]?.is_complete">
                                        <span>Skor: <span x-text="liveRecap?.judges?.[1]?.total_score"></span> &rarr; <span class="text-xs text-slate-900 font-extrabold" x-text="Number(liveRecap?.judges?.[1]?.weighted_score).toFixed(2)"></span></span>
                                    </template>
                                    <template x-if="!liveRecap?.judges?.[1]?.is_complete">
                                        <span x-text="liveRecap?.judges?.[1]?.progress_text || '0/5 terisi'"></span>
                                    </template>
                                </div>
                            </div>

                            <!-- Juri 2 -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 transition" :class="myRole === 2 ? 'ring-2 ring-indigo-500 bg-indigo-50/40 border-indigo-200' : ''">
                                <div class="flex items-center justify-between">
                                    <div class="font-bold text-slate-800 truncate" x-text="(liveRecap?.judges?.[2]?.name || 'Juri 2') + (myRole === 2 ? ' (Anda)' : '')"></div>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800 font-bold">30%</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Maksimal: 20 poin (4 subindikator)</div>
                                <div class="mt-1 font-mono font-bold" :class="liveRecap?.judges?.[2]?.is_complete ? 'text-emerald-600' : 'text-amber-600'">
                                    <template x-if="liveRecap?.judges?.[2]?.is_complete">
                                        <span>Skor: <span x-text="liveRecap?.judges?.[2]?.total_score"></span> &rarr; <span class="text-xs text-slate-900 font-extrabold" x-text="Number(liveRecap?.judges?.[2]?.weighted_score).toFixed(2)"></span></span>
                                    </template>
                                    <template x-if="!liveRecap?.judges?.[2]?.is_complete">
                                        <span x-text="liveRecap?.judges?.[2]?.progress_text || '0/4 terisi'"></span>
                                    </template>
                                </div>
                            </div>

                            <!-- Juri 3 -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 transition" :class="myRole === 3 ? 'ring-2 ring-indigo-500 bg-indigo-50/40 border-indigo-200' : ''">
                                <div class="flex items-center justify-between">
                                    <div class="font-bold text-slate-800 truncate" x-text="(liveRecap?.judges?.[3]?.name || 'Juri 3') + (myRole === 3 ? ' (Anda)' : '')"></div>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800 font-bold">15%</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Maksimal: 15 poin (3 subindikator)</div>
                                <div class="mt-1 font-mono font-bold" :class="liveRecap?.judges?.[3]?.is_complete ? 'text-emerald-600' : 'text-amber-600'">
                                    <template x-if="liveRecap?.judges?.[3]?.is_complete">
                                        <span>Skor: <span x-text="liveRecap?.judges?.[3]?.total_score"></span> &rarr; <span class="text-xs text-slate-900 font-extrabold" x-text="Number(liveRecap?.judges?.[3]?.weighted_score).toFixed(2)"></span></span>
                                    </template>
                                    <template x-if="!liveRecap?.judges?.[3]?.is_complete">
                                        <span x-text="liveRecap?.judges?.[3]?.progress_text || '0/3 terisi'"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulir 12 Subindikator Penilaian Juri -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-200">
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider" x-text="myRoleConfig?.aspect"></h4>
                                <p class="text-[11px] text-slate-500">Pilih skala 1 sampai 5 untuk setiap subindikator berikut (nilai otomatis dihitung):</p>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] font-extrabold px-3 py-1 rounded-xl shadow-2xs"
                                    :class="isMyScoringComplete ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300'">
                                    <span x-text="myFilledCount + '/' + myTotalItems + ' Terisi'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Skala Keterangan Likert 1 - 5 -->
                        <div class="grid grid-cols-5 gap-1.5 text-[11px] text-center p-2.5 rounded-2xl bg-slate-100/90 border border-slate-200/80 font-semibold text-slate-700 shadow-2xs">
                            <div class="p-1 rounded-lg bg-white/70 border border-slate-200/60"><strong class="text-slate-900">1:</strong> Sangat Kurang</div>
                            <div class="p-1 rounded-lg bg-white/70 border border-slate-200/60"><strong class="text-slate-900">2:</strong> Kurang</div>
                            <div class="p-1 rounded-lg bg-white/70 border border-slate-200/60"><strong class="text-slate-900">3:</strong> Cukup</div>
                            <div class="p-1 rounded-lg bg-white/70 border border-slate-200/60"><strong class="text-slate-900">4:</strong> Baik</div>
                            <div class="p-1 rounded-lg bg-white/70 border border-slate-200/60"><strong class="text-slate-900">5:</strong> Sangat Baik</div>
                        </div>

                        <!-- Daftar Subindikator Sesuai Peran Juri -->
                        <div class="space-y-3.5">
                            <template x-for="(text, idx) in (myRoleConfig?.subindicators || {})" :key="idx">
                                <div class="p-4 rounded-2xl border transition-all"
                                    :class="subindicators[idx] ? 'border-emerald-300 bg-white shadow-xs ring-1 ring-emerald-500/20' : 'border-slate-200 bg-white hover:border-slate-300'">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5" x-text="idx"></span>
                                            <p class="text-xs font-bold text-slate-800 leading-snug" x-text="text"></p>
                                        </div>
                                        <template x-if="subindicators[idx]">
                                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-mono font-bold text-xs border border-emerald-200 shrink-0 shadow-2xs">
                                                Skor: <span class="text-emerald-700 font-extrabold" x-text="subindicators[idx]"></span>
                                            </span>
                                        </template>
                                    </div>

                                    <!-- Tombol Pilihan 1-5 (Ukuran Touch-Friendly >= 44px) -->
                                    <div class="flex items-center justify-between gap-2 pt-1" role="radiogroup" :aria-label="'Nilai subindikator ' + idx">
                                        <template x-for="val in [1, 2, 3, 4, 5]" :key="val">
                                            <button 
                                                type="button"
                                                @click="setSubindicatorScore(idx, val)"
                                                tabindex="0"
                                                class="flex-1 min-w-[44px] min-h-[44px] h-12 rounded-xl font-extrabold text-sm flex flex-col items-center justify-center transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#5e43f3] focus:ring-offset-1 select-none"
                                                :class="subindicators[idx] === val 
                                                    ? 'bg-gradient-to-br from-indigo-600 to-[#5e43f3] text-white shadow-md ring-2 ring-indigo-400 scale-[1.03]' 
                                                    : 'bg-slate-100 hover:bg-slate-200/90 text-slate-700 border border-slate-200/80 hover:border-slate-300 active:scale-95'"
                                                :aria-checked="subindicators[idx] === val ? 'true' : 'false'"
                                                role="radio"
                                            >
                                                <span class="text-sm font-extrabold font-mono" x-text="val"></span>
                                                <span class="text-[9px] font-medium opacity-85 hidden sm:inline" x-text="val === 1 ? 'S.Kurang' : (val === 2 ? 'Kurang' : (val === 3 ? 'Cukup' : (val === 4 ? 'Baik' : 'S.Baik')))"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Catatan Evaluasi Juri -->
                        <div class="space-y-1.5 pt-2">
                            <label class="block text-xs font-bold text-slate-700">Catatan Masukan / Umpan Balik (Opsional):</label>
                            <textarea x-model="notes" rows="3" placeholder="Tuliskan apresiasi, masukan perbaikan, atau catatan khusus dewan juri..." class="w-full p-3 text-xs rounded-xl border border-slate-200 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shrink-0">
                    <div>
                        <span class="text-xs text-slate-500 font-medium">Progres Penilaian Juri <span x-text="myRole"></span>:</span>
                        <div class="text-sm font-extrabold font-mono"
                            :class="isMyScoringComplete ? 'text-emerald-700' : 'text-amber-700'">
                            <span x-text="myFilledCount + ' dari ' + myTotalItems + ' Subindikator Dinilai (' + myTotalScore + '/' + myMaxScore + ' Poin)'"></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <template x-if="hasExistingScore">
                            <button type="button" @click="resetCurrentScore()" class="px-4 py-2.5 rounded-xl border border-rose-300 text-rose-600 font-bold text-xs hover:bg-rose-50 transition cursor-pointer flex items-center gap-1.5">
                                <span>🗑️</span>
                                <span>Reset Penilaian</span>
                            </button>
                        </template>
                        <button type="button" @click="closeScoring()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer">
                            Batal
                        </button>
                        <button 
                            type="button" 
                            @click="submitScore()" 
                            :disabled="!isMyScoringComplete || isSubmittingScore" 
                            class="px-6 py-2.5 rounded-xl text-white font-bold text-xs shadow-md transition cursor-pointer flex items-center gap-2"
                            :class="isMyScoringComplete 
                                ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20' 
                                : 'bg-slate-400 cursor-not-allowed opacity-75'"
                        >
                            <span x-text="isSubmittingScore ? 'Menyimpan...' : (isMyScoringComplete ? 'Simpan Nilai Resmi' : ('Lengkapi ' + myFilledCount + '/' + myTotalItems))"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL PENGATURAN PROFIL JURI                            -->
        <!-- ======================================================= -->
        <div 
            x-show="showProfileModal" 
            x-cloak
            class="fixed inset-0 z-[999999] flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs"
            @click.self="showProfileModal = false"
        >
            <div class="w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm">Pengaturan Akun Juri</h3>
                    <button type="button" @click="showProfileModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
                </div>

                <form method="POST" action="{{ route('judge.profile.update') }}" class="space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="profileForm.name" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 outline-none focus:border-[#5e43f3]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Username</label>
                        <input type="text" name="username" x-model="profileForm.username" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 outline-none focus:border-[#5e43f3]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="profileForm.email" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 outline-none focus:border-[#5e43f3]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi Baru (Opsional)</label>
                        <input type="password" name="password" x-model="profileForm.password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 outline-none focus:border-[#5e43f3]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" x-model="profileForm.password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 outline-none focus:border-[#5e43f3]">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showProfileModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#5e43f3] hover:bg-indigo-700 rounded-xl shadow-xs cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- 1. FLOATING HOVER POPOVER (DESKTOP MOUSE HOVER)        -->
        <!-- ======================================================= -->
        <div 
            x-show="!isJudgeModalOpen && hoveredJudge !== null" 
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-98"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-98"
            class="hidden md:block fixed z-[999998] pointer-events-auto select-none"
            :style="`left: ${hoverPos.x}px; top: ${hoverPos.y}px; width: 487px; height: 280px;`"
            @mouseenter="keepJudgeOverview()"
            @mouseleave="hideJudgeOverview()"
        >
            <div class="w-[487px] h-[280px] bg-white rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col relative text-slate-800">
                <!-- Header Banner with Gradient -->
                <div 
                    class="h-[84px] px-5 py-4 text-white relative flex items-center justify-between bg-gradient-to-r"
                    :class="hoveredJudge?.color || 'from-indigo-600 via-indigo-700 to-indigo-900'"
                >
                    <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>

                    <div class="flex items-center gap-3 relative z-10 min-w-0 flex-1 pr-2">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center font-extrabold text-lg shadow-md text-white tracking-wider shrink-0" x-text="hoveredJudge?.code"></div>
                        
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="font-bold text-sm text-white truncate max-w-[200px]" :title="hoveredJudge?.name" x-text="hoveredJudge?.name"></h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-white/25 border border-white/30 text-white uppercase tracking-wider backdrop-blur-xs whitespace-nowrap shrink-0">
                                    Dewan Juri
                                </span>
                            </div>
                            <div class="text-[11px] text-white/90 font-medium flex items-center gap-1 mt-0.5">
                                <span class="font-mono text-white/90">@<span x-text="hoveredJudge?.username"></span></span>
                                <span class="text-white/60">•</span>
                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-200 font-medium whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                    Aktif Menilai
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right relative z-10 shrink-0">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl font-mono font-black text-xs shadow-sm whitespace-nowrap shrink-0" :class="hoveredJudge?.badgeColor || 'bg-white text-indigo-700'" x-text="'KODE: ' + (hoveredJudge?.code || '-')"></span>
                    </div>
                </div>

                <!-- Body Info Grid -->
                <div class="p-3.5 flex-1 flex flex-col justify-between text-xs bg-slate-50/70 overflow-hidden">
                    <div class="grid grid-cols-2 gap-2 shrink-0">
                        <div class="p-2 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                            <div class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mb-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email Juri
                            </div>
                            <div class="font-bold text-slate-800 text-[10px] truncate" :title="hoveredJudge?.email" x-text="hoveredJudge?.email || '-'"></div>
                        </div>

                        <div class="p-2 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                            <div class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mb-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Waktu
                            </div>
                            <div class="font-bold text-slate-800 text-[10px] truncate" x-text="hoveredJudge?.suggested_at || '-'"></div>
                        </div>
                    </div>

                    <!-- Checklist Alasan Rekomendasi Juri -->
                    <div class="p-2.5 rounded-xl bg-purple-50/70 border border-purple-200/70 text-purple-950 flex-1 flex flex-col min-h-0 my-1.5">
                        <div class="text-[10px] uppercase font-bold text-purple-700 tracking-wider mb-1 flex items-center justify-between shrink-0 gap-1.5">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-purple-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Alasan Rekomendasi Top 10</span>
                            </span>
                            <span class="inline-flex items-center font-mono text-[9px] px-1.5 py-0.5 rounded-md bg-purple-200/70 text-purple-800 font-extrabold whitespace-nowrap shrink-0" x-text="(hoveredJudge?.reasons?.length || 0) + ' Kriteria'"></span>
                        </div>
                        <div class="overflow-y-auto space-y-1 pr-1 flex-1 text-[10.5px]">
                            <template x-if="!hoveredJudge?.reasons || hoveredJudge?.reasons.length === 0">
                                <p class="text-slate-400 italic text-[10px] py-1 text-center">Belum ada rincian checklist alasan.</p>
                            </template>
                            <template x-for="(reason, rIdx) in (hoveredJudge?.reasons || [])" :key="rIdx">
                                <div class="flex items-start gap-1.5 text-purple-950 leading-snug">
                                    <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[8px] font-black shrink-0 mt-0.5">✓</span>
                                    <span class="font-medium" x-text="reason"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-slate-200/60 text-[10px] text-slate-500 shrink-0">
                        <span class="flex items-center gap-1 font-semibold text-emerald-700">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Dewan Juri BTIKP Sultra</span>
                        </span>
                        <span class="text-slate-400 font-mono text-[9px]">Portal EduTech 2026</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- 2. MODAL DIALOG JURI (RESPONSIVE KESEMUA JENIS LAYAR)   -->
        <!-- ======================================================= -->
        <div 
            x-show="isJudgeModalOpen && hoveredJudge !== null" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999999] flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
            @click.self="closeJudgeModal()"
            @keydown.escape.window="closeJudgeModal()"
        >
            <div 
                x-show="isJudgeModalOpen && hoveredJudge !== null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="w-full max-w-[520px] bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col my-auto relative text-slate-800 transform max-h-[90vh]"
            >
                <div 
                    class="px-4 sm:px-5 py-3.5 sm:py-4 text-white relative flex items-center justify-between gap-2.5 bg-gradient-to-r shrink-0"
                    :class="hoveredJudge?.color || 'from-indigo-600 via-indigo-700 to-indigo-900'"
                >
                    <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>

                    <div class="flex items-center gap-2.5 sm:gap-3.5 relative z-10 min-w-0 flex-1 pr-1 sm:pr-2">
                        <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center font-extrabold text-lg sm:text-xl shadow-md text-white tracking-wider shrink-0" x-text="hoveredJudge?.code"></div>
                        
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                <h4 class="font-bold text-sm sm:text-base text-white truncate max-w-[150px] xs:max-w-[200px] sm:max-w-[260px]" :title="hoveredJudge?.name" x-text="hoveredJudge?.name"></h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-white/25 border border-white/30 text-white uppercase tracking-wider backdrop-blur-xs whitespace-nowrap shrink-0">
                                    Dewan Juri
                                </span>
                            </div>
                            <div class="text-[11px] sm:text-xs text-white/90 font-medium flex items-center gap-1.5 mt-0.5 flex-wrap">
                                <span class="font-mono text-white/90">@<span x-text="hoveredJudge?.username"></span></span>
                                <span class="text-white/60">•</span>
                                <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] text-emerald-200 font-medium whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                    Aktif Menilai
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2 relative z-10 shrink-0">
                        <span class="inline-flex items-center px-2 sm:px-2.5 py-1 rounded-xl font-mono font-black text-[11px] sm:text-xs shadow-sm whitespace-nowrap shrink-0" :class="hoveredJudge?.badgeColor || 'bg-white text-indigo-700'" x-text="'KODE: ' + (hoveredJudge?.code || '-')"></span>
                        <button 
                            type="button" 
                            @click="closeJudgeModal()"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition cursor-pointer shrink-0"
                            title="Tutup"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between text-xs bg-slate-50/70 gap-3 overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 shrink-0">
                        <div class="p-3 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Email Akun Juri</span>
                            </div>
                            <div class="font-bold text-slate-800 text-xs truncate" :title="hoveredJudge?.email" x-text="hoveredJudge?.email || '-'"></div>
                        </div>

                        <div class="p-3 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Waktu Rekomendasi</span>
                            </div>
                            <div class="font-bold text-slate-800 text-xs truncate" x-text="hoveredJudge?.suggested_at || '-'"></div>
                        </div>
                    </div>

                    <!-- Checklist Alasan Rekomendasi -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 text-emerald-950 shrink-0">
                        <div class="text-[10px] sm:text-[11px] uppercase font-bold text-emerald-800 tracking-wider mb-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-2">
                            <span class="flex items-center gap-1.5 leading-snug">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-extrabold">Kriteria &amp; Alasan Rekomendasi yang Dicentang Juri</span>
                            </span>
                            <span class="inline-flex items-center self-start sm:self-auto font-mono text-[10px] sm:text-xs px-2.5 py-1 rounded-full bg-emerald-200 text-emerald-900 font-extrabold whitespace-nowrap shrink-0 shadow-2xs" x-text="(hoveredJudge?.reasons?.length || 0) + ' Kriteria Terpenuhi'"></span>
                        </div>
                        
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            <template x-if="!hoveredJudge?.reasons || hoveredJudge?.reasons.length === 0">
                                <p class="text-slate-400 italic text-xs py-3 text-center bg-white/60 rounded-xl">Juri belum mencantumkan rincian checklist alasan rekomendasi.</p>
                            </template>
                            <template x-for="(reason, rIdx) in (hoveredJudge?.reasons || [])" :key="rIdx">
                                <div class="flex items-start gap-2.5 text-xs leading-relaxed text-emerald-950 font-medium bg-white/90 p-2.5 rounded-xl border border-emerald-100 shadow-2xs">
                                    <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5 shadow-2xs">✓</span>
                                    <span class="flex-1" x-text="reason"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-3 rounded-2xl bg-purple-50/80 border border-purple-200/70 text-purple-950 shrink-0">
                        <div class="text-[10px] uppercase font-bold text-purple-700 tracking-wider mb-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-purple-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                            </svg>
                            <span>Fokus Bidang Penilaian</span>
                        </div>
                        <div class="font-semibold text-xs leading-relaxed text-purple-900" x-text="hoveredJudge?.focus || 'Evaluasi Inovasi Pembelajaran Digital BTIKP'"></div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-200/70 text-xs text-slate-500 shrink-0">
                        <span class="flex items-center gap-1.5 font-bold text-emerald-700">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Dewan Juri Terverifikasi BTIKP Sultra</span>
                        </span>
                        <button 
                            type="button" 
                            @click="closeJudgeModal()"
                            class="px-4 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs transition cursor-pointer"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- 3. MODAL SARANKAN TOP 10 DENGAN CHECKLIST ALASAN       -->
        <!-- ======================================================= -->
        <div 
            x-show="suggestionWork !== null" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999999] flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
            @click.self="closeSuggestionModal()"
            @keydown.escape.window="closeSuggestionModal()"
        >
            <div 
                x-show="suggestionWork !== null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col my-auto relative text-slate-800 transform max-h-[92vh]"
            >
                <!-- Modal Header -->
                <div class="px-6 py-4.5 bg-gradient-to-r from-purple-800 via-indigo-800 to-[#5e43f3] text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-lg font-bold border border-white/30">
                            ⭐
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-white">
                                <span x-text="isSuggested(suggestionWork?.id) ? 'Ubah Saran Top 10' : 'Sarankan Karya untuk Top 10'"></span>
                            </h3>
                            <p class="text-xs text-purple-200">
                                Rekomendasi Dewan Juri untuk Babak Finalis
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="closeSuggestionModal()"
                        :disabled="isSubmittingSuggestion"
                        class="w-8 h-8 rounded-xl bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition cursor-pointer"
                        title="Tutup"
                    >
                        ✕
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-5 sm:p-6 overflow-y-auto space-y-5 flex-1 text-xs">
                    
                    <!-- Alert Error / Success Notification -->
                    <template x-if="suggestionError">
                        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                            <span>⚠️</span>
                            <span x-text="suggestionError"></span>
                        </div>
                    </template>
                    <template x-if="suggestionSuccessMsg">
                        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                            <span>✓</span>
                            <span x-text="suggestionSuccessMsg"></span>
                        </div>
                    </template>

                    <!-- Status Formulir Tertutup Alert (Poin 4) -->
                    <div x-show="!isFormOpen" class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2.5">
                        <span class="text-base">🔒</span>
                        <div>
                            <p class="font-bold">Formulir telah ditutup</p>
                            <p class="text-[11px] text-amber-700">Periode pengusulan atau penarikan saran Top 10 telah berakhir. Data saran telah terkunci.</p>
                        </div>
                    </div>

                    <!-- A. Detail Karya & Peserta (Read-only) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">A. Informasi Peserta &amp; Karya</span>
                            <span 
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap shrink-0"
                                :class="suggestionWork?.learning_category === 'gim_edukasi' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-sky-100 text-sky-800 border border-sky-300'"
                                x-text="suggestionWork?.category_label || (suggestionWork?.learning_category === 'gim_edukasi' ? 'Gim Edukasi' : 'Media Interaktif')"
                            ></span>
                        </div>

                        <div class="flex items-center gap-3.5">
                            <img 
                                :src="suggestionWork?.profile_photo_url || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(suggestionWork?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200')" 
                                :alt="suggestionWork?.full_name" 
                                class="w-13 h-13 rounded-2xl object-cover border border-slate-200 shadow-2xs bg-slate-100 shrink-0"
                                x-on:error="$event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(suggestionWork?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200'"
                            >
                            <div class="min-w-0 flex-1">
                                <h4 class="font-extrabold text-slate-900 text-sm truncate" x-text="suggestionWork?.full_name"></h4>
                                <p class="text-xs font-bold text-slate-700 uppercase truncate" x-text="suggestionWork?.school_name"></p>
                                <p class="text-[11px] text-slate-400 truncate" x-text="suggestionWork?.regency_city"></p>
                            </div>
                        </div>

                        <!-- Info Kontak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 text-[11px]">
                            <div class="text-slate-600 truncate">
                                📧 <span class="font-semibold" x-text="suggestionWork?.email"></span>
                            </div>
                            <div class="text-slate-600 truncate">
                                📱 <span class="font-semibold" x-text="suggestionWork?.whatsapp_number"></span>
                            </div>
                        </div>

                        <!-- Link Berkas Karya & Video Demonstrasi -->
                        <div class="pt-2 border-t border-slate-200/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 bg-white p-3 rounded-xl border border-slate-200/80">
                            <div>
                                <div class="text-[10px] font-bold uppercase text-slate-400">Tautan Berkas Karya &amp; Video Demo (Maks. 1 Menit)</div>
                                <div class="text-xs font-semibold text-slate-800 mt-0.5">Tautan unggahan resmi peserta</div>
                            </div>
                            <a 
                                :href="suggestionWork?.media_link" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-[#5e43f3] font-bold text-xs border border-purple-200 transition cursor-pointer shrink-0"
                            >
                                <span>Buka File &amp; Video Demo ↗</span>
                            </a>
                        </div>
                    </div>

                    <!-- B. Reason Checklist (Checkboxes, minimal 1 kriteria) -->
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-2">
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">B. Checklist Alasan Saran Top 10</span>
                                <p class="text-[11px] text-slate-500">Centang seluruh kriteria kelayakan yang telah Anda review (minimal 1 dipilih):</p>
                            </div>
                            <span 
                                class="inline-flex items-center self-start sm:self-auto font-mono text-[11px] font-bold px-2.5 py-0.5 rounded-full whitespace-nowrap shrink-0 shadow-2xs"
                                :class="selectedReasons.length > 0 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'"
                                x-text="selectedReasons.length + ' / 5 dipilih'"
                            ></span>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(r, idx) in reasonsList" :key="idx">
                                <label 
                                    class="flex items-start gap-3 p-3 rounded-2xl border transition-all cursor-pointer select-none"
                                    :class="selectedReasons.includes(r) ? 'bg-purple-50/70 border-purple-300 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200'"
                                >
                                    <input 
                                        type="checkbox" 
                                        :value="r"
                                        :checked="selectedReasons.includes(r)"
                                        @change="toggleReason(r)"
                                        class="w-4 h-4 mt-0.5 rounded text-purple-600 focus:ring-purple-400 border-slate-300 shrink-0 cursor-pointer"
                                    >
                                    <div class="text-xs leading-relaxed" :class="selectedReasons.includes(r) ? 'font-bold text-purple-950' : 'text-slate-700 font-medium'" x-text="r"></div>
                                </label>
                            </template>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer Actions -->
                <div class="p-4 sm:p-5 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3 shrink-0">
                    <div>
                        <template x-if="isSuggested(suggestionWork?.id)">
                            <button 
                                type="button" 
                                @click="withdrawSuggestion()"
                                :disabled="!isFormOpen || isSubmittingSuggestion"
                                :class="!isFormOpen ? 'opacity-50 cursor-not-allowed bg-slate-100 text-slate-400 border-slate-200' : 'border-rose-300 hover:bg-rose-50 text-rose-700 cursor-pointer'"
                                class="px-4 py-2.5 rounded-xl border font-bold text-xs transition"
                            >
                                <span x-text="!isFormOpen ? '🔒 Formulir Ditutup' : (isSubmittingSuggestion ? 'Memproses...' : '✕ Tarik Saran')"></span>
                            </button>
                        </template>
                        <template x-if="!isSuggested(suggestionWork?.id)">
                            <button 
                                type="button" 
                                @click="closeSuggestionModal()"
                                :disabled="isSubmittingSuggestion"
                                class="px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
                            >
                                Batal
                            </button>
                        </template>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="submitSuggestion()"
                            :disabled="!isFormOpen || isSubmittingSuggestion || selectedReasons.length === 0"
                            class="px-5 py-2.5 rounded-xl bg-[#5e43f3] hover:bg-indigo-700 disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition cursor-pointer flex items-center gap-2"
                        >
                            <svg x-show="isSubmittingSuggestion" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="!isFormOpen ? '🔒 Formulir Ditutup' : (isSubmittingSuggestion ? 'Menyimpan...' : (isSuggested(suggestionWork?.id) ? 'Perbarui Saran Top 10' : 'Simpan & Sarankan Top 10'))"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- 4. MODAL KONFIRMASI RESET PENILAIAN KARYA (POPOVER)    -->
        <!-- ======================================================= -->
        <div 
            x-show="resetTarget !== null" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[999999] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs"
            @keydown.escape.window="closeResetModal()"
        >
            <div 
                @click.outside="closeResetModal()"
                x-show="resetTarget !== null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 text-center shadow-2xl border border-slate-200 relative overflow-hidden transition-all duration-300"
            >
                <!-- State 1: Konfirmasi Reset Nilai -->
                <div x-show="!resetSuccess" class="space-y-4">
                    <div class="w-20 h-20 mx-auto mb-2 relative">
                        <img 
                            :src="resetTarget?.profile_photo_url || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(resetTarget?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200')" 
                            :alt="resetTarget?.full_name" 
                            class="w-20 h-20 rounded-3xl object-cover border border-slate-200 shadow-sm bg-slate-100"
                            x-on:error="$event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(resetTarget?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200'"
                        >
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-md ring-2 ring-white text-xs">
                            🗑️
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1">
                            Reset Penilaian Karya?
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Apakah Anda yakin ingin mereset dan mengosongkan nilai Anda untuk karya ini?
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-left space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nomor Registrasi</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-purple-50 text-[#5e43f3]" x-text="resetTarget?.registration_number"></span>
                        </div>
                        <div class="text-sm font-bold text-slate-800" x-text="resetTarget?.full_name"></div>
                        <div class="text-xs text-slate-500" x-text="(resetTarget?.school_name || '') + ' • ' + (resetTarget?.regency_city || '')"></div>
                    </div>

                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs text-left flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Nilai subindikator Anda pada karya ini akan dihapus. Rekapitulasi nilai akhir karya akan kembali menjadi <strong>Nilai Sementara</strong>.</span>
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <button 
                            type="button" 
                            :disabled="isResettingScore"
                            @click="closeResetModal()"
                            class="flex-1 py-2.5 px-4 rounded-xl border border-slate-300 hover:bg-slate-100 disabled:opacity-50 text-slate-700 font-bold text-xs transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="button" 
                            :disabled="isResettingScore"
                            @click="executeResetScore()"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold text-xs shadow-md shadow-rose-600/30 transition cursor-pointer flex items-center justify-center gap-2"
                        >
                            <svg x-show="isResettingScore" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isResettingScore ? 'Mereset...' : 'Ya, Reset Nilai'"></span>
                        </button>
                    </div>
                </div>

                <!-- State 2: Sukses Reset (Hijau / Emerald) -->
                <div x-show="resetSuccess" class="space-y-4 py-2">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center text-emerald-600 shadow-xl shadow-emerald-500/10">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1">
                            Nilai Berhasil Direset!
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500" x-text="resetSuccessMsg || 'Nilai Anda telah dihapus dan karya kembali berstatus Belum dinilai.'">
                        </p>
                    </div>

                    <div class="pt-2">
                        <button 
                            type="button" 
                            @click="closeResetModal(true)"
                            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition cursor-pointer"
                        >
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Data Awal Rekap Penilaian & Peringkat Multi-Juri Realtime -->
    <script id="judge-recap-initial-data" type="application/json">
        {!! json_encode([
            'scores' => $initialScoresMap ?? [],
            'ranks' => $initialRanksMap ?? [],
        ]) !!}
    </script>

    <script>
        function judgeDashboard() {
            return {
                activeTab: @json($activeTab ?? 'finalists'),
                selectedCategory: @json(!empty($judge->category) ? $judge->category : ($category ?? 'all')),
                searchQuery: @json($search ?? ''),
                viewMode: localStorage.getItem('judge_view_mode') || 'grid',
                isFormOpen: {{ $isFormOpen ? 'true' : 'false' }},
                
                setViewMode(mode) {
                    this.viewMode = mode;
                    localStorage.setItem('judge_view_mode', mode);
                },

                // Realtime Scoring & Rankings Map (Sama seperti Panel Admin)
                scoresMap: {},
                ranksMap: {},
                rekapPollTimer: null,
                rekapPollStatus: 'connected',

                // Reset Score Modal State
                resetTarget: null,
                isResettingScore: false,
                resetSuccess: false,
                resetSuccessMsg: '',

                // Suggestion Quota State & Map
                myGimSuggestions: {{ (int) ($stats['my_gim_suggestions'] ?? 0) }},
                myMediaSuggestions: {{ (int) ($stats['my_media_suggestions'] ?? 0) }},
                togglingSuggestId: null,

                // Reactive Suggestions Map (Loaded directly from backend)
                suggestionsMap: @json($suggestionsMap ?? []),

                isSuggested(id) {
                    return this.suggestionsMap[id] ? this.suggestionsMap[id].isSuggestedByMe : false;
                },

                getSuggestions(id) {
                    return this.suggestionsMap[id] ? this.suggestionsMap[id].suggestions : [];
                },

                // 5 Kriteria Checklist Alasan Saran Top 10
                reasonsList: [
                    'Karya layak secara teknis dan berfungsi dengan baik (tanpa bug/crash)',
                    'Ide karya orisinal',
                    'Materi pembelajaran benar dan akurat',
                    'Video demonstrasi (maks. 1 menit) sudah disimak dan sesuai dengan karya',
                    'File/tautan karya dapat diakses dan dibuka dengan baik'
                ],

                // State Modal Saran Top 10
                suggestionWork: null,
                selectedReasons: [],
                isSubmittingSuggestion: false,
                suggestionError: null,
                suggestionSuccessMsg: null,

                openSuggestionModal(work) {
                    if (!work.profile_photo_url) {
                        work.profile_photo_url = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(work.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200';
                    }
                    this.suggestionWork = work;
                    this.suggestionError = null;
                    this.suggestionSuccessMsg = null;
                    const existingReasons = this.suggestionsMap[work.id]?.myReasons;
                    if (existingReasons && Array.isArray(existingReasons) && existingReasons.length > 0) {
                        this.selectedReasons = [...existingReasons];
                    } else {
                        this.selectedReasons = [];
                    }
                },

                closeSuggestionModal() {
                    if (this.isSubmittingSuggestion) return;
                    this.suggestionWork = null;
                    this.selectedReasons = [];
                    this.suggestionError = null;
                    this.suggestionSuccessMsg = null;
                },

                toggleReason(reason) {
                    if (this.selectedReasons.includes(reason)) {
                        this.selectedReasons = this.selectedReasons.filter(r => r !== reason);
                    } else {
                        this.selectedReasons.push(reason);
                    }
                },

                async submitSuggestion() {
                    if (!this.suggestionWork || this.isSubmittingSuggestion) return;
                    if (!this.isFormOpen) {
                        this.suggestionError = 'Formulir telah ditutup. Anda tidak dapat lagi memberikan, mengubah, atau menarik saran Top 10.';
                        return;
                    }
                    if (this.selectedReasons.length === 0) {
                        this.suggestionError = 'Pilih minimal 1 alasan rekomendasi sebelum menyimpan saran Top 10.';
                        return;
                    }
                    this.isSubmittingSuggestion = true;
                    this.suggestionError = null;

                    const work = this.suggestionWork;

                    try {
                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('/judge/works/' + work.id + '/toggle-suggest', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                reasons: this.selectedReasons
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            if (!this.suggestionsMap[work.id]) {
                                this.suggestionsMap[work.id] = { isSuggestedByMe: false, count: 0, myReasons: [], suggestions: [] };
                            }
                            this.suggestionsMap[work.id].isSuggestedByMe = data.is_suggested;
                            this.suggestionsMap[work.id].count = data.suggestions_count;
                            this.suggestionsMap[work.id].myReasons = data.my_reasons || this.selectedReasons;
                            this.suggestionsMap[work.id].suggestions = (data.suggestions || []).map(s => ({
                                userId: s.user_id,
                                name: s.judge_name,
                                username: s.judge_username || ('juri' + s.user_id),
                                email: s.judge_email || '-',
                                code: s.judge_code,
                                reasons: s.reasons || [],
                                focus: s.focus || 'Evaluasi Inovasi Pembelajaran Digital BTIKP',
                                suggested_at: s.suggested_at || 'Tercatat di sistem',
                                participant_name: s.participant_name || work.full_name,
                                colorClass: s.colorClass || (s.judge_code === 'J1' ? 'bg-indigo-600 text-white' : (s.judge_code === 'J2' ? 'bg-emerald-600 text-white' : (s.judge_code === 'J3' ? 'bg-amber-600 text-white' : 'bg-purple-600 text-white'))),
                                color: s.color || 'from-indigo-600 via-indigo-700 to-indigo-900',
                                badgeColor: s.badgeColor || 'bg-white text-indigo-700'
                            }));

                            if (data.stats) {
                                this.myGimSuggestions = data.stats.gim_suggestions;
                                this.myMediaSuggestions = data.stats.media_suggestions;
                            }
                            this.suggestionSuccessMsg = data.message;
                            setTimeout(() => {
                                this.closeSuggestionModal();
                            }, 700);
                        } else {
                            this.suggestionError = data.message || 'Gagal menyimpan saran.';
                        }
                    } catch (e) {
                        this.suggestionError = 'Gagal menghubungi server.';
                    } finally {
                        this.isSubmittingSuggestion = false;
                    }
                },

                async withdrawSuggestion() {
                    if (!this.suggestionWork || this.isSubmittingSuggestion) return;
                    if (!this.isFormOpen) {
                        this.suggestionError = 'Formulir telah ditutup. Anda tidak dapat lagi memberikan, mengubah, atau menarik saran Top 10.';
                        return;
                    }
                    if (!confirm('Apakah Anda yakin ingin membatalkan saran Top 10 untuk karya ini?')) return;
                    this.isSubmittingSuggestion = true;
                    this.suggestionError = null;

                    const work = this.suggestionWork;
                    try {
                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('/judge/works/' + work.id + '/toggle-suggest', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                action: 'withdraw'
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            if (!this.suggestionsMap[work.id]) {
                                this.suggestionsMap[work.id] = { isSuggestedByMe: false, count: 0, myReasons: [], suggestions: [] };
                            }
                            this.suggestionsMap[work.id].isSuggestedByMe = false;
                            this.suggestionsMap[work.id].count = data.suggestions_count;
                            this.suggestionsMap[work.id].myReasons = [];
                            this.suggestionsMap[work.id].suggestions = (data.suggestions || []).map(s => ({
                                userId: s.user_id,
                                name: s.judge_name,
                                username: s.judge_username || ('juri' + s.user_id),
                                email: s.judge_email || '-',
                                code: s.judge_code,
                                reasons: s.reasons || [],
                                focus: s.focus || 'Evaluasi Inovasi Pembelajaran Digital BTIKP',
                                suggested_at: s.suggested_at || 'Tercatat di sistem',
                                participant_name: s.participant_name || work.full_name,
                                colorClass: s.colorClass || (s.judge_code === 'J1' ? 'bg-indigo-600 text-white' : (s.judge_code === 'J2' ? 'bg-emerald-600 text-white' : (s.judge_code === 'J3' ? 'bg-amber-600 text-white' : 'bg-purple-600 text-white'))),
                                color: s.color || 'from-indigo-600 via-indigo-700 to-indigo-900',
                                badgeColor: s.badgeColor || 'bg-white text-indigo-700'
                            }));

                            if (data.stats) {
                                this.myGimSuggestions = data.stats.gim_suggestions;
                                this.myMediaSuggestions = data.stats.media_suggestions;
                            }
                            this.closeSuggestionModal();
                        } else {
                            this.suggestionError = data.message || 'Gagal membatalkan saran.';
                        }
                    } catch (e) {
                        this.suggestionError = 'Gagal menghubungi server.';
                    } finally {
                        this.isSubmittingSuggestion = false;
                    }
                },

                // Scoring & Realtime Rubrik State
                scoringWork: null,
                hasExistingScore: false,
                subindicators: {},
                notes: '',
                isSubmittingScore: false,
                scoreSuccessMsg: null,
                syncStatus: 'connected',
                recapData: null,
                recapPollInterval: null,

                rubricRoles: @json($rubricRoles ?? \App\Services\RubricService::getRoles()),
                rubricScale: @json($rubricScale ?? \App\Services\RubricService::getScale()),
                myRole: @json($myRole ?? ($judge->judge_role ?? ($judge->judge_code === 'J1' ? 1 : ($judge->judge_code === 'J2' ? 2 : ($judge->judge_code === 'J3' ? 3 : 1))))),
                myRoleConfig: @json($myRoleConfig ?? \App\Services\RubricService::getRoleConfig($judge->judge_role ?? 1)),

                setSubindicatorScore(idx, val) {
                    this.subindicators[idx] = parseInt(val);
                },

                get myFilledCount() {
                    if (!this.myRoleConfig || !this.myRoleConfig.subindicators) return 0;
                    let count = 0;
                    for (let k in this.myRoleConfig.subindicators) {
                        let v = parseInt(this.subindicators[k]);
                        if (v >= 1 && v <= 5) count++;
                    }
                    return count;
                },

                get myTotalItems() {
                    if (!this.myRoleConfig || !this.myRoleConfig.subindicators) return 0;
                    return Object.keys(this.myRoleConfig.subindicators).length;
                },

                get myTotalScore() {
                    let total = 0;
                    for (let k in this.subindicators) {
                        let v = parseInt(this.subindicators[k]);
                        if (v >= 1 && v <= 5) total += v;
                    }
                    return total;
                },

                get myMaxScore() {
                    return this.myRoleConfig ? this.myRoleConfig.max_score : 25;
                },

                get myWeight() {
                    return this.myRoleConfig ? this.myRoleConfig.weight : 55;
                },

                get myWeightedScore() {
                    if (this.myFilledCount < this.myTotalItems || this.myMaxScore <= 0) return 0.0;
                    let score = (this.myTotalScore / this.myMaxScore) * this.myWeight;
                    return parseFloat(score.toFixed(2));
                },

                get isMyScoringComplete() {
                    return this.myFilledCount === this.myTotalItems && this.myTotalItems > 0;
                },

                get liveRecap() {
                    // Kloning objek rekap dari server atau fallback kosong
                    let base = this.recapData || (this.scoringWork ? (this.scoringWork.recap || null) : null);
                    let copy = base ? JSON.parse(JSON.stringify(base)) : {
                        is_complete: false,
                        status_label: 'Nilai Sementara',
                        display_score: '0.00',
                        interim_score: 0.0,
                        final_score: null,
                        waiting_judges: ['Juri 1', 'Juri 2', 'Juri 3'],
                        waiting_text: 'Menunggu: Juri 1, Juri 2, Juri 3',
                        judges: {}
                    };

                    if (!copy.judges) copy.judges = {};

                    // Suntikkan pratinjau realtime nilai juri yang sedang aktif mengisi
                    let r = this.myRole;
                    if (!copy.judges[r]) {
                        copy.judges[r] = {
                            role: r,
                            name: this.myRoleConfig?.name || ('Juri ' + r),
                            weight: this.myWeight,
                            max_score: this.myMaxScore,
                        };
                    }

                    copy.judges[r].total_score = this.myTotalScore;
                    copy.judges[r].filled_count = this.myFilledCount;
                    copy.judges[r].items_count = this.myTotalItems;
                    copy.judges[r].progress_text = this.myFilledCount + '/' + this.myTotalItems + ' terisi';
                    copy.judges[r].is_complete = this.isMyScoringComplete;
                    copy.judges[r].weighted_score = this.isMyScoringComplete ? this.myWeightedScore : 0.0;

                    // Hitung ulang Nilai Akhir / Sementara secara live
                    let completedCount = 0;
                    let sumWeighted = 0;
                    let waiting = [];

                    for (let j = 1; j <= 3; j++) {
                        let jData = copy.judges[j];
                        if (jData && jData.is_complete) {
                            completedCount++;
                            sumWeighted += parseFloat(jData.weighted_score || 0);
                        } else {
                            waiting.push('Juri ' + j);
                        }
                    }

                    copy.completed_judges_count = completedCount;
                    copy.is_complete = (completedCount === 3);
                    copy.status_label = copy.is_complete ? 'Nilai Akhir' : 'Nilai Sementara';
                    copy.interim_score = parseFloat(sumWeighted.toFixed(2));
                    copy.final_score = copy.is_complete ? copy.interim_score : null;
                    copy.display_score = copy.interim_score.toFixed(2);
                    copy.waiting_judges = waiting;
                    copy.waiting_text = waiting.length > 0 ? ('Menunggu: ' + waiting.join(', ')) : null;

                    return copy;
                },

                // Profile State
                showProfileModal: false,
                profileForm: {
                    name: @json($judge->name),
                    username: @json($judge->username),
                    email: @json($judge->email),
                    password: '',
                    password_confirmation: ''
                },

                openScoring(work) {
                    if (!work.profile_photo_url) {
                        work.profile_photo_url = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(work.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200';
                    }
                    this.scoringWork = work;
                    this.subindicators = {};

                    // Muat subindikator yang tersimpan sebelumnya jika ada
                    if (work.my_subindicators && typeof work.my_subindicators === 'object') {
                        for (let k in work.my_subindicators) {
                            this.subindicators[k] = parseInt(work.my_subindicators[k]);
                        }
                    }

                    this.hasExistingScore = (work.my_status && work.my_status !== 'Belum dinilai');
                    this.notes = (work.judge_scores && work.judge_scores.length > 0) ? (work.judge_scores[0].notes || '') : '';
                    this.scoreSuccessMsg = null;
                    this.recapData = work.recap || null;

                    this.startRecapPolling();
                },

                closeScoring() {
                    this.scoringWork = null;
                    this.subindicators = {};
                    this.stopRecapPolling();
                },

                async pollCurrentWorkRecap() {
                    if (!this.scoringWork) return;
                    try {
                        this.syncStatus = 'updating';
                        const res = await fetch('/realtime/works/' + this.scoringWork.id + '/score-recap', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.recapData = data;
                            this.syncStatus = 'connected';
                        } else {
                            this.syncStatus = 'error';
                        }
                    } catch (e) {
                        this.syncStatus = 'error';
                    }
                },

                startRecapPolling() {
                    this.stopRecapPolling();
                    this.pollCurrentWorkRecap();
                    this.recapPollInterval = setInterval(() => {
                        this.pollCurrentWorkRecap();
                    }, 3000);
                },

                stopRecapPolling() {
                    if (this.recapPollInterval) {
                        clearInterval(this.recapPollInterval);
                        this.recapPollInterval = null;
                    }
                },

                async submitScore() {
                    if (!this.scoringWork || this.isSubmittingScore) return;

                    if (!this.isMyScoringComplete) {
                        alert('Seluruh ' + this.myTotalItems + ' subindikator wajib dinilai sebelum menyimpan (saat ini: ' + this.myFilledCount + '/' + this.myTotalItems + ' terisi).');
                        return;
                    }

                    this.isSubmittingScore = true;
                    this.scoreSuccessMsg = null;

                    try {
                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('/judge/works/' + this.scoringWork.id + '/score', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                subindicators: this.subindicators,
                                notes: this.notes
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.scoreSuccessMsg = data.message;
                            if (data.recap) {
                                this.recapData = data.recap;
                                this.scoringWork.recap = data.recap;
                                this.scoringWork.my_status = 'Sudah dinilai';
                                this.scoresMap[this.scoringWork.id] = data.recap;
                            }
                            this.hasExistingScore = true;
                            this.pollFinalistsRecap();
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } else {
                            alert(data.message || 'Gagal menyimpan nilai.');
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server.');
                    } finally {
                        this.isSubmittingScore = false;
                    }
                },

                confirmResetScore(work) {
                    if (!work) return;
                    this.resetTarget = work;
                    this.resetSuccess = false;
                    this.isResettingScore = false;
                    this.resetSuccessMsg = '';
                },

                closeResetModal(reload = false) {
                    if (this.isResettingScore) return;
                    const shouldReload = this.resetSuccess || reload;
                    this.resetTarget = null;
                    this.resetSuccess = false;
                    this.isResettingScore = false;
                    if (shouldReload) {
                        window.location.reload();
                    }
                },

                async executeResetScore() {
                    if (!this.resetTarget || this.isResettingScore) return;
                    this.isResettingScore = true;
                    const work = this.resetTarget;
                    const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                    try {
                        const res = await fetch('/judge/works/' + work.id + '/score', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.resetSuccess = true;
                            this.resetSuccessMsg = data.message || 'Nilai Anda untuk karya ini berhasil direset.';
                            this.subindicators = {};
                            this.hasExistingScore = false;
                            if (data.recap) {
                                this.scoresMap[work.id] = data.recap;
                            }
                            this.pollFinalistsRecap();
                            if (this.scoringWork && this.scoringWork.id === work.id) {
                                this.scoringWork.my_status = 'Belum dinilai';
                                if (data.recap) {
                                    this.recapData = data.recap;
                                    this.scoringWork.recap = data.recap;
                                }
                                this.closeScoring();
                            }
                        } else {
                            alert(data.message || 'Gagal mereset nilai.');
                            this.isResettingScore = false;
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server.');
                        this.isResettingScore = false;
                    }
                },

                async resetCurrentScore() {
                    if (!this.scoringWork) return;
                    this.confirmResetScore(this.scoringWork);
                },

                // Judge Popover & Modal State
                hoveredJudge: null,
                hoverPos: { x: 0, y: 0 },
                hoverTimeout: null,
                isJudgeModalOpen: false,

                showJudgeOverview(judgeData, event) {
                    if (window.innerWidth < 768) return;
                    clearTimeout(this.hoverTimeout);
                    if (this.isJudgeModalOpen) return;

                    const rect = event ? event.currentTarget.getBoundingClientRect() : null;
                    const cardW = 487;
                    const cardH = 280;

                    let x = rect ? rect.left + (rect.width / 2) - (cardW / 2) : 20;
                    if (x < 15) x = 15;
                    if (x + cardW > window.innerWidth - 15) {
                        x = window.innerWidth - cardW - 15;
                    }

                    let y = rect ? rect.bottom + 8 : 20;
                    if (y + cardH > window.innerHeight - 15) {
                        y = rect.top - cardH - 8;
                    }
                    if (y < 10) y = 10;

                    this.hoverPos = { x, y };
                    this.hoveredJudge = judgeData;
                },

                hideJudgeOverview() {
                    if (this.isJudgeModalOpen) return;
                    this.hoverTimeout = setTimeout(() => {
                        this.hoveredJudge = null;
                    }, 180);
                },

                keepJudgeOverview() {
                    clearTimeout(this.hoverTimeout);
                },

                openJudgeModal(judgeData) {
                    clearTimeout(this.hoverTimeout);
                    this.hoveredJudge = judgeData;
                    this.isJudgeModalOpen = true;
                },

                closeJudgeModal() {
                    this.isJudgeModalOpen = false;
                    this.hoveredJudge = null;
                },

                async pollFinalistsRecap() {
                    try {
                        const res = await fetch('{{ route('realtime.category-recap') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            if (data && data.works) {
                                data.works.forEach(w => {
                                    this.scoresMap[w.id] = w.multi_recap;
                                    this.ranksMap[w.id] = w.category_rank;
                                });
                                this.rekapPollStatus = 'connected';
                            }
                        } else {
                            this.rekapPollStatus = 'error';
                        }
                    } catch (e) {
                        this.rekapPollStatus = 'error';
                    }
                },

                startFinalistsRecapPolling() {
                    this.stopFinalistsRecapPolling();
                    this.pollFinalistsRecap();
                    this.rekapPollTimer = setInterval(() => {
                        this.pollFinalistsRecap();
                    }, 3500);
                },

                stopFinalistsRecapPolling() {
                    if (this.rekapPollTimer) {
                        clearInterval(this.rekapPollTimer);
                        this.rekapPollTimer = null;
                    }
                },

                get isAnyModalOpen() {
                    return !!(this.scoringWork !== null || this.showProfileModal || this.isJudgeModalOpen || this.suggestionWork !== null || this.resetTarget !== null);
                },

                init() {
                    try {
                        const initEl = document.getElementById('judge-recap-initial-data');
                        if (initEl && initEl.textContent) {
                            const parsed = JSON.parse(initEl.textContent);
                            this.scoresMap = parsed.scores || {};
                            this.ranksMap = parsed.ranks || {};
                        }
                    } catch (e) {
                        console.error('Failed to parse judge initial recap data', e);
                    }

                    this.$watch('isAnyModalOpen', (isOpen) => {
                        if (isOpen) {
                            document.body.classList.add('overflow-hidden');
                            document.documentElement.classList.add('overflow-hidden');
                        } else {
                            document.body.classList.remove('overflow-hidden');
                            document.documentElement.classList.remove('overflow-hidden');
                        }
                    });

                    this.$watch('activeTab', (val) => {
                        if (val === 'finalists') {
                            this.startFinalistsRecapPolling();
                        } else {
                            this.stopFinalistsRecapPolling();
                        }
                    });

                    if (this.activeTab === 'finalists') {
                        this.startFinalistsRecapPolling();
                    }
                }
            };
        }
        document.addEventListener('alpine:init', () => {
            Alpine.data('judgeDashboard', judgeDashboard);
        });
    </script>
</x-layouts.app>
