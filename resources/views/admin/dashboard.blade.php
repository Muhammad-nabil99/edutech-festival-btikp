<x-layouts.app title="Dashboard Panitia — EduTech Festival BTIKP" maxWidth="max-w-7xl">
    <div 
        class="space-y-6" 
        x-data="{ 
            activeTab: '{{ $filters['tab'] ?? 'all' }}',
            viewMode: 'table', 
            previewImage: null, 
            selectedRegistration: null,
            deleteTarget: null,
            isDeleting: false,
            deleteSuccess: false,
            deletedInfo: null,
            
            // Top 10 Slots State
            top10Slots: {
                gim: {{ $stats['top10_gim'] }},
                media: {{ $stats['top10_media'] }},
                total: {{ $stats['top10_total'] }}
            },
            togglingTop10Id: null,

            // Setting Modal State
            showSettingModal: false,
            settingMode: '{{ $registrationSetting->mode }}',
            settingIsOpen: {{ $registrationSetting->is_open ? 'true' : 'false' }},

            // Judge Edit Modal State
            editingJudge: null,

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
            },

            // Interactive Toggle Top 10
            async toggleTop10(reg) {
                if (this.togglingTop10Id) return;

                // Check quota limit locally before sending
                const isAdding = !reg.is_curated_top10;
                const isGim = reg.learning_category === 'gim_edukasi';
                const currentCount = isGim ? this.top10Slots.gim : this.top10Slots.media;

                if (isAdding && currentCount >= 10) {
                    alert('Slot Top 10 untuk kategori ' + reg.category_label + ' sudah penuh (maksimal 10 karya). Batalkan karya lain terlebih dahulu jika ingin menggantinya.');
                    return;
                }

                this.togglingTop10Id = reg.id;
                try {
                    const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch('/admin/registrations/' + reg.id + '/toggle-top10', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        reg.is_curated_top10 = data.is_curated;
                        if (this.selectedRegistration && this.selectedRegistration.id === reg.id) {
                            this.selectedRegistration.is_curated_top10 = data.is_curated;
                        }
                        if (data.slots) {
                            this.top10Slots.gim = data.slots.gim_count;
                            this.top10Slots.media = data.slots.media_count;
                            this.top10Slots.total = data.slots.total_count;
                        }
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat mengubah kurasi Top 10.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Gagal menghubungi server.');
                } finally {
                    this.togglingTop10Id = null;
                }
            },

            openJudgeEdit(judge) {
                this.editingJudge = { ...judge, password: '' };
            }
        }"
    >
        <!-- Top Alert Notification Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-emerald-50/90 via-emerald-50/50 to-teal-50/90 border border-emerald-200/80 p-4 shadow-xs flex items-center justify-between transition-all">
            <div class="flex items-center space-x-3.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-emerald-950">Selamat datang kembali di Dashboard Panitia BTIKP!</h4>
                    <p class="text-[11px] sm:text-xs text-emerald-700/90 mt-0.5">
                        Statistik karya, kurasi Top 10, dan rekap penilaian Dewan Juri disinkronkan secara real-time.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    @click="showSettingModal = true"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-200 text-emerald-800 text-xs font-bold hover:bg-emerald-50 transition shadow-2xs cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Status Pendaftaran: <strong>{{ $registrationSetting->isOpen() ? 'Dibuka' : 'Ditutup' }}</strong></span>
                </button>
            </div>
        </div>

        <!-- Dashboard Header & Top Actions -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-[#5e43f3] border border-purple-200/70 mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#5e43f3]"></span>
                    Panel Kurasi &amp; Manajemen Panitia BTIKP Sultra
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Data Pendaftar EduTech Festival
                </h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-1.5 max-w-2xl font-normal leading-relaxed">
                    Pantau statistik karya guru &amp; siswa, kurasi 10 karya terbaik per kategori, dan rekap penilaian dewan juri se-Sulawesi Tenggara.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <button 
                    type="button" 
                    @click="showSettingModal = true"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:border-slate-300 rounded-xl shadow-xs hover:bg-slate-50 transition cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span>Jadwal &amp; Form</span>
                </button>

                <a 
                    href="{{ route('admin.registrations.export', ['type' => 'top10']) }}" 
                    class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl shadow-xs transition"
                    title="Ekspor rekap 20 karya nominasi Top 10 beserta nilai juri"
                >
                    <svg class="w-4 h-4 mr-1.5 text-amber-700" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>Unduh Top 10</span>
                </a>

                <a 
                    href="{{ route('admin.registrations.export') }}" 
                    class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/25 transition cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ekspor CSV / Excel</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards (Eduxon Style from code.html) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Metric 1: Total Pendaftar -->
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs hover:shadow-soft transition-all duration-300 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pendaftar</span>
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-[#5e43f3] flex items-center justify-center ring-4 ring-purple-50/50 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total']) }}</span>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Terkonfirmasi</span>
                </div>
                <p class="text-xs text-slate-400 mt-2 font-medium">Peserta terdaftar di sistem</p>
            </div>

            <!-- Metric 2: Gim Edukasi -->
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs hover:shadow-soft transition-all duration-300 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Gim Edukasi</span>
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center ring-4 ring-amber-50/50 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-extrabold text-amber-500 tracking-tight">{{ number_format($stats['gim_edukasi']) }}</span>
                    <span class="text-xs font-medium text-slate-400">Karya Game</span>
                </div>
                <p class="text-xs text-slate-400 mt-2 font-medium">Karya game interaktif terkirim</p>
            </div>

            <!-- Metric 3: Media Pembelajaran Interaktif -->
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs hover:shadow-soft transition-all duration-300 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Media Interaktif</span>
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center ring-4 ring-sky-50/50 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-extrabold text-sky-600 tracking-tight">{{ number_format($stats['media_interaktif']) }}</span>
                    <span class="text-xs font-medium text-slate-400">Multimedia</span>
                </div>
                <p class="text-xs text-slate-400 mt-2 font-medium">Modul &amp; web interaktif</p>
            </div>

            <!-- Metric 4: Sebaran Wilayah -->
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs hover:shadow-soft transition-all duration-300 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sebaran Wilayah</span>
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-[#5e43f3] flex items-center justify-center ring-4 ring-purple-50/50 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-extrabold text-[#5e43f3] tracking-tight">{{ $stats['distinct_regions'] }}</span>
                    <span class="text-xs font-medium text-slate-400">/ 17 Daerah</span>
                </div>
                <p class="text-xs text-slate-400 mt-2 font-medium">Kabupaten &amp; Kota se-Sultra</p>
            </div>
        </section>

        <!-- TOP 10 CURATED SLOTS TRACKER CARD (New Feature Requirement) -->
        <section class="bg-gradient-to-br from-white via-purple-50/20 to-indigo-50/30 rounded-3xl p-6 sm:p-7 border border-purple-100 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <!-- Info Header -->
                <div class="space-y-1.5 max-w-md">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span>Kuota Nominasi Top 10 Terbaik</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">
                        Status Slot Kurasi Karya Terbaik (20 Slot Total)
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Setiap kategori memiliki <strong>kuota maksimal 10 karya</strong> (total 20 karya). Karya terpilih otomatis diteruskan ke portal dewan juri untuk dinilai.
                    </p>
                </div>

                <!-- Slot Trackers -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1 max-w-xl">
                    <!-- Gim Edukasi Slot -->
                    <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Gim Edukasi
                            </span>
                            <span class="font-mono font-extrabold text-amber-700">
                                <span x-text="top10Slots.gim">{{ $stats['top10_gim'] }}</span> / 10 Slot
                            </span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div 
                                class="bg-amber-500 h-2.5 rounded-full transition-all duration-500" 
                                :style="'width: ' + (top10Slots.gim * 10) + '%'"
                            ></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span x-text="top10Slots.gim >= 10 ? 'Slot Kategori Penuh' : (10 - top10Slots.gim) + ' slot tersisa'"></span>
                            <a href="{{ route('admin.dashboard', ['tab' => 'top10_gim']) }}" class="text-[#5e43f3] font-bold hover:underline">Lihat Karya &rarr;</a>
                        </div>
                    </div>

                    <!-- Media Interaktif Slot -->
                    <div class="bg-white p-4 rounded-2xl border border-sky-200/80 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                Media Interaktif
                            </span>
                            <span class="font-mono font-extrabold text-sky-700">
                                <span x-text="top10Slots.media">{{ $stats['top10_media'] }}</span> / 10 Slot
                            </span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div 
                                class="bg-sky-500 h-2.5 rounded-full transition-all duration-500" 
                                :style="'width: ' + (top10Slots.media * 10) + '%'"
                            ></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span x-text="top10Slots.media >= 10 ? 'Slot Kategori Penuh' : (10 - top10Slots.media) + ' slot tersisa'"></span>
                            <a href="{{ route('admin.dashboard', ['tab' => 'top10_media']) }}" class="text-[#5e43f3] font-bold hover:underline">Lihat Karya &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DASHBOARD NAVIGATION TABS -->
        <div class="border-b border-slate-200/80 flex items-center gap-2 overflow-x-auto pb-1 text-xs sm:text-sm font-bold">
            <a 
                href="{{ route('admin.dashboard', ['tab' => 'all']) }}" 
                class="px-4 py-2.5 rounded-xl transition cursor-pointer whitespace-nowrap {{ $filters['tab'] === 'all' ? 'bg-[#5e43f3] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
            >
                Semua Pendaftar ({{ $stats['total'] }})
            </a>
            <a 
                href="{{ route('admin.dashboard', ['tab' => 'top10']) }}" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl transition cursor-pointer whitespace-nowrap {{ $filters['tab'] === 'top10' ? 'bg-amber-500 text-white shadow-xs' : 'text-amber-800 bg-amber-50 hover:bg-amber-100' }}"
            >
                <span>⭐ Top 10 Terpilih ({{ $stats['top10_total'] }}/20)</span>
            </a>
            <a 
                href="{{ route('admin.dashboard', ['tab' => 'top10_gim']) }}" 
                class="px-4 py-2.5 rounded-xl transition cursor-pointer whitespace-nowrap {{ $filters['tab'] === 'top10_gim' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
            >
                Top 10 Gim ({{ $stats['top10_gim'] }}/10)
            </a>
            <a 
                href="{{ route('admin.dashboard', ['tab' => 'top10_media']) }}" 
                class="px-4 py-2.5 rounded-xl transition cursor-pointer whitespace-nowrap {{ $filters['tab'] === 'top10_media' ? 'bg-sky-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
            >
                Top 10 Media ({{ $stats['top10_media'] }}/10)
            </a>
            <button 
                type="button"
                @click="activeTab = 'scores'"
                class="px-4 py-2.5 rounded-xl transition cursor-pointer whitespace-nowrap"
                :class="activeTab === 'scores' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
                📊 Rekap Nilai Juri
            </button>
            <button 
                type="button"
                @click="activeTab = 'judges'"
                class="px-4 py-2.5 rounded-xl transition cursor-pointer whitespace-nowrap"
                :class="activeTab === 'judges' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
                👥 Akun Dewan Juri
            </button>
        </div>

        <!-- ======================================================= -->
        <!-- TAB CONTENT: DATA PENDAFTAR & KURASI TOP 10             -->
        <!-- ======================================================= -->
        <div x-show="activeTab !== 'scores' && activeTab !== 'judges'" class="space-y-6">
            <!-- Filter & Search Panel (Eduxon Style from code.html) -->
            <section class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-xs">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-4">
                    <input type="hidden" name="tab" value="{{ $filters['tab'] }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Field 1: Pencarian Kata Kunci -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Pencarian Kata Kunci</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ $filters['search'] }}" 
                                    placeholder="Cari nama, sekolah, email..." 
                                    class="w-full pl-10 pr-4 py-2.5 text-xs rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-[#5e43f3] transition-all"
                                >
                            </div>
                        </div>

                        <!-- Field 2: Kategori Pembelajaran -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Kategori Pembelajaran</label>
                            <select 
                                name="category" 
                                class="w-full px-3.5 py-2.5 text-xs rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-[#5e43f3] transition-all cursor-pointer"
                            >
                                <option value="">Semua Kategori</option>
                                <option value="gim_edukasi" {{ $filters['category'] === 'gim_edukasi' ? 'selected' : '' }}>Gim Edukasi</option>
                                <option value="media_pembelajaran_interaktif" {{ $filters['category'] === 'media_pembelajaran_interaktif' ? 'selected' : '' }}>Media Pembelajaran Interaktif</option>
                            </select>
                        </div>

                        <!-- Field 3: Kabupaten / Kota -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Kabupaten / Kota</label>
                            <select 
                                name="regency" 
                                class="w-full px-3.5 py-2.5 text-xs rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-[#5e43f3] transition-all cursor-pointer"
                            >
                                <option value="">Semua Wilayah Sultra</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}" {{ $filters['regency'] === $region ? 'selected' : '' }}>
                                        {{ $region }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Field 4: Status Nominasi Top 10 -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Status Nominasi</label>
                            <select 
                                name="curated" 
                                class="w-full px-3.5 py-2.5 text-xs rounded-2xl border border-slate-200 bg-slate-50/50 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-[#5e43f3] transition-all cursor-pointer"
                            >
                                <option value="">Semua Status Nominasi</option>
                                <option value="top10_only" {{ $filters['curated'] === 'top10_only' ? 'selected' : '' }}>⭐ Top 10 Terpilih Saja</option>
                                <option value="regular_only" {{ $filters['curated'] === 'regular_only' ? 'selected' : '' }}>Belum Masuk Top 10</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Actions Footer -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                        <a 
                            href="{{ route('admin.dashboard', ['tab' => $filters['tab']]) }}" 
                            class="px-4 py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all"
                        >
                            Reset Filter
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 text-xs font-bold text-white bg-[#5e43f3] hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md shadow-[#5e43f3]/30 transition-all cursor-pointer"
                        >
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </section>

            <!-- Table Header & View Controls -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-1">
                <p class="text-xs sm:text-sm text-slate-500">
                    Menampilkan <strong class="text-slate-800 font-bold">{{ $registrations->count() }}</strong> dari total <strong class="text-slate-800 font-bold">{{ $registrations->total() }}</strong> pendaftar
                    <span class="inline-block mx-1.5 text-purple-400">•</span>
                    <span class="text-[#5e43f3] font-medium">Klik baris/kartu pendaftar untuk melihat detail lengkap</span>
                </p>

                <!-- View Switcher (Eduxon pill style) -->
                <div class="inline-flex p-1 bg-slate-100 rounded-2xl self-start sm:self-auto border border-slate-200/60">
                    <button 
                        type="button" 
                        @click="viewMode = 'table'"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                        :class="viewMode === 'table' ? 'bg-white text-[#5e43f3] shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <span>Tabel (List)</span>
                    </button>
                    <button 
                        type="button" 
                        @click="viewMode = 'grid'"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                        :class="viewMode === 'grid' ? 'bg-white text-[#5e43f3] shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Grid (Kartu)</span>
                    </button>
                </div>
            </div>

            <!-- VIEW 1: DATA TABLE VIEW (Slicing Code.html + New Feature Top 10 Toggle) -->
            <div x-show="viewMode === 'table'" class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-4 pl-6 pr-4" scope="col">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Peserta</span>
                                    </div>
                                </th>
                                <th class="py-4 px-4" scope="col">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Instansi &amp; Daerah</span>
                                    </div>
                                </th>
                                <th class="py-4 px-4" scope="col">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span>Kategori &amp; Status Nominasi</span>
                                    </div>
                                </th>
                                <th class="py-4 px-4" scope="col">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span>Kontak (WA / Email)</span>
                                    </div>
                                </th>
                                <th class="py-4 px-4" scope="col">Link Karya</th>
                                <th class="py-4 px-4 text-center" scope="col">Kurasi Top 10</th>
                                <th class="py-4 pr-6 pl-4 text-right" scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
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
                                        'is_curated_top10' => (bool) $reg->is_curated_top10,
                                        'created_at_formatted' => $reg->created_at ? $reg->created_at->translatedFormat('l, d F Y — H:i') . ' WITA' : '-',
                                        'delete_url' => route('admin.registrations.destroy', $reg, false),
                                    ];
                                @endphp
                                <tr 
                                    class="hover:bg-slate-50/80 transition-colors group cursor-pointer"
                                    @click="openDetail(@js($regPayload))"
                                >
                                    <!-- Peserta -->
                                    <td class="py-4 pl-6 pr-4">
                                        <div class="flex items-center space-x-3">
                                            <div 
                                                @click.stop="previewImage = '{{ $reg->profile_photo_url }}'"
                                                class="relative shrink-0" 
                                                title="Klik untuk memperbesar foto"
                                            >
                                                <img 
                                                    src="{{ $reg->profile_photo_url }}" 
                                                    alt="{{ $reg->full_name }}" 
                                                    class="w-10 h-10 rounded-2xl object-cover border border-slate-200/80 shadow-2xs group-hover:scale-105 transition-all bg-slate-100"
                                                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($reg->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                                >
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 group-hover:text-[#5e43f3] transition-colors text-sm">
                                                    {{ $reg->full_name }}
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-[#5e43f3] border border-purple-200">
                                                    {{ $reg->registration_number }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Instansi & Daerah -->
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-800">{{ $reg->school_name }}</div>
                                        <div class="text-slate-400 flex items-center gap-1 mt-0.5 text-[11px]">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ $reg->regency_city }}</span>
                                        </div>
                                    </td>

                                    <!-- Kategori & Badge Top 10 -->
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            @if ($reg->learning_category === 'gim_edukasi')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2"></span>
                                                    Gim Edukasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200/80">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 mr-2"></span>
                                                    Media Interaktif
                                                </span>
                                            @endif

                                            <!-- Prominent Top 10 Badge if Selected -->
                                            @if ($reg->is_curated_top10)
                                                <div>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-2xs">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                        <span>Top 10 Terbaik</span>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Kontak -->
                                    <td class="py-4 px-4" @click.stop>
                                        <div class="space-y-1">
                                            <div class="text-slate-600 font-medium flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <span>{{ $reg->email }}</span>
                                            </div>
                                            <a 
                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors border border-emerald-200" 
                                                href="{{ $reg->whatsapp_chat_url }}" 
                                                target="_blank"
                                            >
                                                <svg class="w-3 h-3 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.449.741.963 1.2 1.014.904 1.868 1.185 2.134 1.315.265.13.42.115.578-.066.158-.181.677-.789.858-1.06.181-.271.36-.226.605-.136.245.09 1.545.728 1.811.86.265.132.441.198.505.311.063.113.063.655-.081 1.06z" />
                                                </svg>
                                                <span>Chat WA ({{ $reg->whatsapp_number }})</span>
                                            </a>
                                        </div>
                                    </td>

                                    <!-- Link Karya -->
                                    <td class="py-4 px-4" @click.stop>
                                        <a 
                                            class="inline-flex items-center px-3.5 py-1.5 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:border-[#5e43f3] hover:text-[#5e43f3] hover:bg-purple-50/50 transition-all shadow-2xs" 
                                            href="{{ $reg->media_link }}" 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                        >
                                            <span>Buka Karya</span>
                                            <svg class="w-3.5 h-3.5 ml-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </td>

                                    <!-- Tombol Toggle Kurasi Top 10 (Interactive Action) -->
                                    <td class="py-4 px-4 text-center" @click.stop>
                                        @if ($reg->is_curated_top10)
                                            <button 
                                                type="button" 
                                                @click="toggleTop10(@js($regPayload))"
                                                :disabled="togglingTop10Id === {{ $reg->id }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-100 hover:bg-rose-100 text-amber-900 hover:text-rose-700 border border-amber-300 hover:border-rose-300 transition-all cursor-pointer shadow-2xs"
                                                title="Klik untuk membatalkan status Top 10"
                                            >
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span>Batal Top 10</span>
                                            </button>
                                        @else
                                            @php
                                                $isGimFull = $reg->learning_category === 'gim_edukasi' && $stats['top10_gim'] >= 10;
                                                $isMediaFull = $reg->learning_category === 'media_pembelajaran_interaktif' && $stats['top10_media'] >= 10;
                                                $isSlotFull = $isGimFull || $isMediaFull;
                                            @endphp
                                            <button 
                                                type="button" 
                                                @click="toggleTop10(@js($regPayload))"
                                                :disabled="togglingTop10Id === {{ $reg->id }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $isSlotFull ? 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed' : 'bg-white hover:bg-purple-50 text-[#5e43f3] hover:text-indigo-700 border border-purple-200 hover:border-purple-300 shadow-2xs' }} transition-all cursor-pointer"
                                                title="{{ $isSlotFull ? 'Slot Top 10 kategori ini sudah penuh (10/10)' : 'Pilih karya ini masuk nominasi Top 10' }}"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                                <span>{{ $isSlotFull ? 'Slot Penuh' : 'Pilih Top 10' }}</span>
                                            </button>
                                        @endif
                                    </td>

                                    <!-- Aksi Detail & Hapus -->
                                    <td class="py-4 pr-6 pl-4 text-right" @click.stop>
                                        <div class="inline-flex items-center space-x-1">
                                            <button 
                                                type="button" 
                                                @click="openDetail(@js($regPayload))"
                                                class="p-1.5 text-slate-400 hover:text-[#5e43f3] hover:bg-purple-50 rounded-lg transition-colors cursor-pointer" 
                                                title="Lihat Detail Peserta"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="confirmDelete(@js($regPayload))"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" 
                                                title="Hapus Berkas Data"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-16 text-center text-slate-500 bg-slate-50/50">
                                        <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                            <div class="w-14 h-14 rounded-3xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 shadow-xs">
                                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <span class="font-extrabold text-slate-800 text-sm">Tidak ada data pendaftar</span>
                                            <span class="text-xs text-slate-400">Tidak ada karya yang cocok dengan kriteria filter saat ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Pagination Footer -->
                @if ($registrations->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
                        {{ $registrations->links() }}
                    </div>
                @endif
            </div>

            <!-- VIEW 2: GRID VIEW (Kartu-Kartu Interaktif) -->
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
                                'is_curated_top10' => (bool) $reg->is_curated_top10,
                                'created_at_formatted' => $reg->created_at ? $reg->created_at->translatedFormat('l, d F Y — H:i') . ' WITA' : '-',
                                'delete_url' => route('admin.registrations.destroy', $reg, false),
                            ];
                        @endphp
                        <div 
                            class="bg-white rounded-3xl p-5 border {{ $reg->is_curated_top10 ? 'border-amber-300 ring-2 ring-amber-100' : 'border-slate-100' }} shadow-xs hover:shadow-soft transition-all cursor-pointer flex flex-col justify-between group"
                            @click="openDetail(@js($regPayload))"
                        >
                            <div>
                                <!-- Header Badge & Registration Number -->
                                <div class="flex items-center justify-between gap-2 mb-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if ($reg->learning_category === 'gim_edukasi')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                Gim Edukasi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                                Media Interaktif
                                            </span>
                                        @endif

                                        @if ($reg->is_curated_top10)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-2xs">
                                                ⭐ Top 10
                                            </span>
                                        @endif
                                    </div>

                                    <span class="text-[11px] font-mono font-bold text-[#5e43f3] bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200">
                                        {{ $reg->registration_number }}
                                    </span>
                                </div>

                                <!-- Avatar & Participant Info -->
                                <div class="flex items-center gap-3.5 mb-4">
                                    <img 
                                        src="{{ $reg->profile_photo_url }}" 
                                        alt="{{ $reg->full_name }}" 
                                        class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-md ring-2 ring-slate-100 group-hover:ring-purple-300 transition-all bg-slate-100 shrink-0"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($reg->full_name) }}&background=5E43F3&color=FFFFFF&size=200';"
                                    >
                                    <div class="space-y-0.5 overflow-hidden">
                                        <h3 class="font-extrabold text-slate-900 group-hover:text-[#5e43f3] transition-colors text-sm truncate" title="{{ $reg->full_name }}">
                                            {{ $reg->full_name }}
                                        </h3>
                                        <p class="text-xs font-bold text-slate-700 uppercase truncate" title="{{ $reg->school_name }}">
                                            {{ $reg->school_name }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 flex items-center gap-1 truncate">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                        <span class="truncate">{{ $reg->email }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 pt-1" @click.stop>
                                        <a 
                                            href="{{ $reg->whatsapp_chat_url }}" 
                                            target="_blank" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-[11px] font-bold border border-emerald-200 transition"
                                        >
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.288.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.449.741.963 1.2 1.014.904 1.868 1.185 2.134 1.315.265.13.42.115.578-.066.158-.181.677-.789.858-1.06.181-.271.36-.226.605-.136.245.09 1.545.728 1.811.86.265.132.441.198.505.311.063.113.063.655-.081 1.06z" />
                                            </svg>
                                            <span>Chat WA</span>
                                        </a>
                                        <a 
                                            href="{{ $reg->media_link }}" 
                                            target="_blank" 
                                            class="inline-flex items-center gap-1 text-[11px] font-bold text-[#5e43f3] hover:underline"
                                        >
                                            <span>Buka Karya &rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer Action Buttons -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2" @click.stop>
                                <!-- Toggle Top 10 Button -->
                                @if ($reg->is_curated_top10)
                                    <button 
                                        type="button" 
                                        @click="toggleTop10(@js($regPayload))"
                                        class="px-3 py-1.5 rounded-xl bg-amber-100 hover:bg-rose-100 text-amber-900 hover:text-rose-700 text-xs font-bold transition cursor-pointer"
                                    >
                                        Batal Top 10
                                    </button>
                                @else
                                    <button 
                                        type="button" 
                                        @click="toggleTop10(@js($regPayload))"
                                        class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-[#5e43f3] text-xs font-bold transition cursor-pointer"
                                    >
                                        + Pilih Top 10
                                    </button>
                                @endif

                                <div class="flex items-center gap-1">
                                    <button 
                                        type="button" 
                                        @click="openDetail(@js($regPayload))"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer"
                                    >
                                        Detail
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="confirmDelete(@js($regPayload))"
                                        class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                        title="Hapus berkas"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                            <span class="font-bold text-sm text-slate-800">Tidak ada data yang cocok dengan kriteria filter.</span>
                        </div>
                    @endforelse
                </div>

                @if ($registrations->hasPages())
                    <div class="bg-white rounded-3xl p-4 border border-slate-100">
                        {{ $registrations->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- TAB CONTENT 2: REKAP NILAI DEWAN JURI (LEADERBOARD)     -->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'scores'" class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-xs">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">
                            Rekapitulasi Penilaian Dewan Juri — Nominasi Top 10
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Peringkat sementara karya digital berdasarkan rata-rata 4 aspek rubrik penilaian (Skala 0 - 100).
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('admin.registrations.export', ['type' => 'top10']) }}" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#5e43f3] text-white text-xs font-bold hover:bg-indigo-700 transition shadow-xs"
                        >
                            <span>Unduh Rekap Nilai CSV</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 pl-5 pr-2">Rank</th>
                                <th class="py-3.5 px-4">Karya &amp; Peserta</th>
                                <th class="py-3.5 px-3">Kategori</th>
                                @foreach ($judges as $idx => $j)
                                    <th class="py-3.5 px-3 text-center">Juri {{ $idx + 1 }}</th>
                                @endforeach
                                <th class="py-3.5 px-4 text-center">Skor Rata-rata</th>
                                <th class="py-3.5 pr-5 pl-3 text-right">Tautan Karya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($top10Works as $rank => $work)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 pl-5 pr-2">
                                        <div class="w-7 h-7 rounded-xl flex items-center justify-center font-extrabold text-xs {{ $rank === 0 ? 'bg-amber-400 text-white shadow-xs' : ($rank === 1 ? 'bg-slate-300 text-slate-800' : ($rank === 2 ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                            {{ $rank + 1 }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 text-sm">{{ $work->full_name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $work->school_name }} • {{ $work->regency_city }}</div>
                                    </td>
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800' : 'bg-sky-50 text-sky-800' }}">
                                            {{ $work->category_label }}
                                        </span>
                                    </td>
                                    @foreach ($judges as $j)
                                        @php
                                            $judgeScore = $work->judgeScores->firstWhere('user_id', $j->id);
                                        @endphp
                                        <td class="py-3.5 px-3 text-center whitespace-nowrap font-mono font-bold">
                                            @if ($judgeScore)
                                                <span class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs">
                                                    {{ number_format($judgeScore->total_score, 1) }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 font-normal">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="text-sm font-extrabold text-[#5e43f3] bg-purple-50 px-2.5 py-1 rounded-xl border border-purple-200">
                                            {{ number_format($work->calculated_avg_score, 2) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 pr-5 pl-3 text-right whitespace-nowrap">
                                        <a 
                                            href="{{ $work->media_link }}" 
                                            target="_blank" 
                                            class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 hover:text-[#5e43f3] bg-slate-50 hover:bg-purple-50 px-3 py-1.5 rounded-xl border border-slate-200 transition"
                                        >
                                            <span>Buka Karya</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 5 + $judges->count() }}" class="py-12 text-center text-slate-400">
                                        Belum ada karya yang dipilih ke dalam Top 10. Pilih karya melalui tab pendaftar terlebih dahulu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- TAB CONTENT 3: AKUN DEWAN JURI & KREDENSIAL             -->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'judges'" class="space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-xs">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">
                            Kelola Akun Dewan Juri
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Administrator dapat mengubah username, email, dan mengatur ulang kata sandi seluruh juri.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach ($judges as $judge)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-[#5e43f3]">
                                        Role: Judge
                                    </span>
                                    <span class="font-mono text-xs font-bold text-slate-400">ID: #{{ $judge->id }}</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $judge->name }}</h4>
                                <div class="text-xs text-slate-600 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 font-medium">Username:</span>
                                        <span class="font-mono font-bold text-slate-800">{{ $judge->username ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 font-medium">Email:</span>
                                        <span class="font-bold text-slate-800 break-all">{{ $judge->email }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 font-medium">Karya Dinilai:</span>
                                        <span class="font-mono font-bold text-emerald-700">
                                            {{ $judge->scores()->count() }} Karya
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button 
                                type="button" 
                                @click="openJudgeEdit(@js($judge))"
                                class="w-full py-2 px-3 rounded-xl bg-white border border-slate-300 hover:border-[#5e43f3] text-slate-700 hover:text-[#5e43f3] text-xs font-bold transition shadow-2xs cursor-pointer"
                            >
                                Edit Akun / Reset Password
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL 1: DETAIL LENGKAP PENDAFTAR                       -->
        <!-- ======================================================= -->
        <div 
            x-show="selectedRegistration" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            @keydown.escape.window="closeDetail()"
        >
            <div 
                class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all max-h-[90vh] flex flex-col"
                @click.away="closeDetail()"
            >
                <!-- Top Header -->
                <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-[#5e43f3] px-6 sm:px-8 py-5 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/10 p-1 backdrop-blur-xs border border-white/20 flex items-center justify-center shrink-0">
                            <img src="{{ asset('logo.png') }}" alt="Logo BTIKP" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="text-[10px] tracking-widest uppercase font-bold text-purple-200">Detail Pendaftaran Peserta</span>
                            <h2 class="text-base sm:text-lg font-bold tracking-tight" x-text="selectedRegistration?.registration_number"></h2>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="closeDetail()"
                        class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white transition cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 sm:p-8 overflow-y-auto space-y-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 p-5 rounded-3xl bg-slate-50 border border-slate-100">
                        <img 
                            :src="selectedRegistration?.profile_photo_url" 
                            :alt="selectedRegistration?.full_name"
                            class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md border-2 border-white ring-2 ring-purple-100 shrink-0 bg-white"
                        >
                        <div class="flex-1 text-center sm:text-left space-y-1">
                            <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap mb-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-purple-100 text-[#5e43f3]" x-text="selectedRegistration?.category_label"></span>
                                <template x-if="selectedRegistration?.is_curated_top10">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                                        ⭐ Top 10 Terpilih
                                    </span>
                                </template>
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Alamat Email</span>
                            <span class="font-bold text-slate-900 break-all" x-text="selectedRegistration?.email"></span>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Nomor WhatsApp</span>
                            <div class="flex items-center justify-between gap-2 pt-0.5">
                                <span class="font-bold text-slate-900" x-text="selectedRegistration?.whatsapp_number"></span>
                                <a 
                                    :href="selectedRegistration?.whatsapp_chat_url" 
                                    target="_blank" 
                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition"
                                >
                                    Chat WA
                                </a>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Kategori Lomba</span>
                            <span class="font-extrabold text-[#5e43f3] block" x-text="selectedRegistration?.category_label"></span>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Waktu Terdaftar</span>
                            <span class="font-semibold text-slate-700 block" x-text="selectedRegistration?.created_at_formatted"></span>
                        </div>

                        <!-- Link Media Karya -->
                        <div class="sm:col-span-2 p-4 rounded-2xl bg-purple-50/60 border border-purple-200 shadow-2xs space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] uppercase tracking-wider text-[#5e43f3] font-bold block">Tautan Media Karya Digital</span>
                                <a 
                                    :href="selectedRegistration?.media_link" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#5e43f3] hover:bg-indigo-700 text-white font-bold text-xs transition shadow-xs"
                                >
                                    <span>Buka Tautan Karya</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                            <p class="text-xs text-purple-900 font-mono break-all" x-text="selectedRegistration?.media_link"></p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer with Top 10 Toggle and Actions -->
                <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2">
                        <!-- Toggle Top 10 Action inside Detail Modal -->
                        <button 
                            type="button" 
                            @click="toggleTop10(selectedRegistration)"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer"
                            :class="selectedRegistration?.is_curated_top10 ? 'bg-amber-100 hover:bg-rose-100 text-amber-900 hover:text-rose-700 border border-amber-300' : 'bg-[#5e43f3] hover:bg-indigo-700 text-white'"
                        >
                            <span x-text="selectedRegistration?.is_curated_top10 ? '⭐ Batalkan dari Top 10' : '⭐ Masukkan ke Top 10 Terbaik'"></span>
                        </button>

                        <button 
                            type="button" 
                            @click="confirmDelete(selectedRegistration); closeDetail();"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-800 px-3 py-2 rounded-xl hover:bg-rose-50 transition cursor-pointer"
                        >
                            <span>Hapus Data</span>
                        </button>
                    </div>

                    <button 
                        type="button" 
                        @click="closeDetail()"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL 2: PENGATURAN STATUS REGISTRASI (BUKA / TUTUP)    -->
        <!-- ======================================================= -->
        <div 
            x-show="showSettingModal" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            @keydown.escape.window="showSettingModal = false"
        >
            <div 
                class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all"
                @click.away="showSettingModal = false"
            >
                <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-[#5e43f3] text-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-purple-200">Konfigurasi Sistem</span>
                        <h3 class="text-base font-bold">Jadwal &amp; Status Pendaftaran</h3>
                    </div>
                    <button type="button" @click="showSettingModal = false" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.settings.registration') }}" class="p-6 space-y-5">
                    @csrf
                    <!-- Mode Selection -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Mode Buka/Tutup Pendaftaran
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label 
                                class="p-3 rounded-2xl border cursor-pointer flex items-center gap-2 text-xs font-bold transition"
                                :class="settingMode === 'manual' ? 'border-[#5e43f3] bg-purple-50 text-[#5e43f3]' : 'border-slate-200 text-slate-600'"
                            >
                                <input type="radio" name="mode" value="manual" x-model="settingMode" class="text-[#5e43f3]">
                                <span>Manual (Saklar On/Off)</span>
                            </label>
                            <label 
                                class="p-3 rounded-2xl border cursor-pointer flex items-center gap-2 text-xs font-bold transition"
                                :class="settingMode === 'scheduled' ? 'border-[#5e43f3] bg-purple-50 text-[#5e43f3]' : 'border-slate-200 text-slate-600'"
                            >
                                <input type="radio" name="mode" value="scheduled" x-model="settingMode" class="text-[#5e43f3]">
                                <span>Terjadwal (Otomatis)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Mode Manual: Switch On / Off -->
                    <div x-show="settingMode === 'manual'" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-xs font-bold text-slate-800">Status Pendaftaran Saat Ini</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_open" value="1" x-model="settingIsOpen" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </div>
                        </label>
                        <p class="text-[11px] text-slate-500" x-text="settingIsOpen ? 'Pendaftaran sedang DIBUKA untuk umum.' : 'Pendaftaran DITUTUP. Formulir disembunyikan dari peserta.'"></p>
                    </div>

                    <!-- Mode Scheduled: Tanggal & Jam Mulai - Selesai -->
                    <div x-show="settingMode === 'scheduled'" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Mulai Buka</label>
                                <input 
                                    type="datetime-local" 
                                    name="start_time" 
                                    value="{{ $registrationSetting->start_time ? $registrationSetting->start_time->format('Y-m-d\TH:i') : '' }}"
                                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white"
                                >
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Batas Akhir (Tutup)</label>
                                <input 
                                    type="datetime-local" 
                                    name="end_time" 
                                    value="{{ $registrationSetting->end_time ? $registrationSetting->end_time->format('Y-m-d\TH:i') : '' }}"
                                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white"
                                >
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Countdown timer di halaman pendaftaran otomatis aktif menghitung mundur menuju tanggal yang ditetapkan.
                        </p>
                    </div>

                    <!-- Pesan saat Pendaftaran Ditutup -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Pesan Kustom Saat Ditutup
                        </label>
                        <input 
                            type="text" 
                            name="closed_message" 
                            value="{{ $registrationSetting->closed_message ?? 'Pendaftaran ditutup' }}"
                            placeholder="Pendaftaran ditutup"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showSettingModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#5e43f3] hover:bg-indigo-700 rounded-xl shadow-xs">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL 3: EDIT AKUN DEWAN JURI (ADMIN)                   -->
        <!-- ======================================================= -->
        <div 
            x-show="editingJudge !== null" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            @keydown.escape.window="editingJudge = null"
        >
            <div 
                class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all"
                @click.away="editingJudge = null"
            >
                <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-[#5e43f3] text-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-purple-200">Manajemen Juri</span>
                        <h3 class="text-base font-bold" x-text="'Edit Akun: ' + (editingJudge?.name || '')"></h3>
                    </div>
                    <button type="button" @click="editingJudge = null" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/judges/' + (editingJudge?.id || '')" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Dewan Juri</label>
                        <input type="text" name="name" x-model="editingJudge.name" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Username Login</label>
                        <input type="text" name="username" x-model="editingJudge.username" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="editingJudge.email" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Reset Kata Sandi (Opsional)</label>
                        <input type="password" name="password" x-model="editingJudge.password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="editingJudge = null" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#5e43f3] hover:bg-indigo-700 rounded-xl shadow-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL 4: KONFIRMASI HAPUS PESERTA                       -->
        <!-- ======================================================= -->
        <div 
            x-show="deleteTarget !== null" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            @keydown.escape.window="closeDeleteModal()"
        >
            <div 
                @click.outside="closeDeleteModal()"
                class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 text-center shadow-2xl border border-slate-200 relative overflow-hidden transition-all duration-300"
            >
                <!-- State 1: Konfirmasi Hapus -->
                <div x-show="!deleteSuccess" class="space-y-4">
                    <div class="w-20 h-20 mx-auto mb-2">
                        <img 
                            :src="deleteTarget?.profile_photo_url" 
                            :alt="deleteTarget?.full_name" 
                            class="w-20 h-20 rounded-3xl object-cover border border-slate-200 shadow-sm bg-slate-100"
                        >
                    </div>

                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1">
                            Hapus Data Pendaftar?
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Apakah Anda yakin ingin menghapus data peserta ini dari sistem?
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-left space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nomor Registrasi</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-purple-50 text-[#5e43f3]" x-text="deleteTarget?.registration_number"></span>
                        </div>
                        <div class="text-sm font-bold text-slate-800" x-text="deleteTarget?.full_name"></div>
                        <div class="text-xs text-slate-500" x-text="(deleteTarget?.school_name || '') + ' • ' + (deleteTarget?.regency_city || '')"></div>
                    </div>

                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs text-left flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Tindakan ini bersifat permanen. Berkas foto dan data karya pendaftar akan terhapus.</span>
                    </div>

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

                <!-- State 2: Sukses Hapus (Hijau / Emerald) -->
                <div x-show="deleteSuccess" class="space-y-4 py-2">
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
                            Data pendaftaran <span class="font-bold text-slate-800" x-text="deletedInfo?.full_name"></span> (<span class="font-mono text-emerald-700 font-semibold" x-text="deletedInfo?.registration_number"></span>) beserta berkas telah dihapus.
                        </p>
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

        <!-- Lightbox Modal Preview Foto Profil -->
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

    </div>
</x-layouts.app>
