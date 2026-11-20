<x-layouts.app title="Portal Dewan Juri — EduTech Festival BTIKP" maxWidth="max-w-7xl">
    <div 
        class="space-y-6"
        x-data="{
            selectedCategory: '{{ $category ?? 'all' }}',
            scoringWork: null,
            pedagogical: 0,
            creativity: 0,
            technical: 0,
            presentation: 0,
            notes: '',
            isSubmittingScore: false,
            scoreSuccessMsg: null,
            showProfileModal: false,
            profileForm: {
                name: '{{ $judge->name }}',
                username: '{{ $judge->username }}',
                email: '{{ $judge->email }}',
                password: '',
                password_confirmation: ''
            },

            openScoring(work) {
                this.scoringWork = work;
                const existingScore = work.judge_scores && work.judge_scores.length > 0 ? work.judge_scores[0] : null;
                this.pedagogical = existingScore ? existingScore.pedagogical_score : 80;
                this.creativity = existingScore ? existingScore.creativity_score : 80;
                this.technical = existingScore ? existingScore.technical_score : 80;
                this.presentation = existingScore ? existingScore.presentation_score : 80;
                this.notes = existingScore ? (existingScore.notes || '') : '';
                this.scoreSuccessMsg = null;
            },

            closeScoring() {
                this.scoringWork = null;
            },

            get calculatedTotal() {
                const p = parseFloat(this.pedagogical) || 0;
                const c = parseFloat(this.creativity) || 0;
                const t = parseFloat(this.technical) || 0;
                const pr = parseFloat(this.presentation) || 0;
                const total = (p * 0.30) + (c * 0.30) + (t * 0.25) + (pr * 0.15);
                return total.toFixed(2);
            },

            async submitScore() {
                if (!this.scoringWork || this.isSubmittingScore) return;
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
                            pedagogical_score: this.pedagogical,
                            creativity_score: this.creativity,
                            technical_score: this.technical,
                            presentation_score: this.presentation,
                            notes: this.notes
                        })
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.scoreSuccessMsg = data.message;
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
            }
        }"
    >
        <!-- Welcome Juri Header Banner -->
        <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-indigo-900 to-[#5e43f3] text-white p-6 sm:p-8 shadow-soft relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold bg-white/10 backdrop-blur-md border border-white/20 text-purple-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Portal Resmi Penilaian Dewan Juri
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Lembar Penilaian Top 10 Karya Digital
                    </h1>
                    <p class="text-xs sm:text-sm text-purple-100/90 leading-relaxed">
                        Selamat datang, <strong>{{ $judge->name }}</strong>. Silakan evaluasi 20 karya nominasi terbaik berdasarkan 4 aspek rubrik penilaian Bab IV.
                    </p>
                </div>

                <div class="flex items-center gap-3 self-start md:self-auto">
                    <button 
                        type="button" 
                        @click="showProfileModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengaturan Akun Juri</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Metric Cards Penilaian Juri -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Nominasi</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total_curated'] }}</div>
                <p class="text-[11px] text-slate-400 mt-0.5">Maks. 20 karya terbaik</p>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sudah Anda Nilai</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['scored_count'] }}</div>
                <p class="text-[11px] text-emerald-600/80 mt-0.5 font-semibold">Tersimpan di sistem</p>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Belum Dinilai</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-500 mt-1">{{ $stats['remaining_count'] }}</div>
                <p class="text-[11px] text-amber-500/80 mt-0.5 font-semibold">Menunggu evaluasi</p>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kategori Lomba</span>
                <div class="text-sm font-extrabold text-slate-800 mt-2 space-y-0.5">
                    <div class="text-amber-700">Gim: {{ $stats['gim_count'] }} / 10</div>
                    <div class="text-sky-700">Media: {{ $stats['media_count'] }} / 10</div>
                </div>
            </div>
        </div>

        <!-- Filter Kategori Lomba -->
        <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 text-xs font-bold">
            <a 
                href="{{ route('judge.dashboard', ['category' => 'all']) }}" 
                class="px-4 py-2 rounded-xl transition {{ $category === 'all' ? 'bg-[#5e43f3] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                Semua Karya ({{ $works->count() }})
            </a>
            <a 
                href="{{ route('judge.dashboard', ['category' => 'gim_edukasi']) }}" 
                class="px-4 py-2 rounded-xl transition {{ $category === 'gim_edukasi' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                Gim Edukasi ({{ $stats['gim_count'] }})
            </a>
            <a 
                href="{{ route('judge.dashboard', ['category' => 'media_pembelajaran_interaktif']) }}" 
                class="px-4 py-2 rounded-xl transition {{ $category === 'media_pembelajaran_interaktif' ? 'bg-sky-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                Media Interaktif ({{ $stats['media_count'] }})
            </a>
        </div>

        <!-- Grid Karya Top 10 untuk Dinilai -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($works as $work)
                @php
                    $myScore = $work->judgeScores->first();
                @endphp
                <div class="bg-white rounded-3xl p-5 border {{ $myScore ? 'border-emerald-200 bg-emerald-50/10' : 'border-slate-100' }} shadow-xs hover:shadow-soft transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Header Kategori & Status Nilai -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800' : 'bg-sky-50 text-sky-800' }}">
                                {{ $work->category_label }}
                            </span>

                            @if ($myScore)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Skor: {{ number_format($myScore->total_score, 1) }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Belum Dinilai
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
                    </div>

                    <!-- Footer Action: Tombol Beri / Edit Nilai -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                        <button 
                            type="button" 
                            @click="openScoring(@js($work))"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer {{ $myScore ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-[#5e43f3] hover:bg-indigo-700 text-white' }}"
                        >
                            {{ $myScore ? 'Edit Nilai (' . number_format($myScore->total_score, 1) . ')' : 'Input Nilai Karya' }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                    <p class="font-bold text-sm text-slate-800">Belum ada karya yang dipilih oleh panitia ke dalam nominasi Top 10.</p>
                </div>
            @endforelse
        </div>

        <!-- ======================================================= -->
        <!-- MODAL PENILAIAN JURI (SCORING CARD DENGAN RUBRIK)       -->
        <!-- ======================================================= -->
        <div 
            x-show="scoringWork !== null" 
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center p-3 sm:p-6 bg-slate-900/70 backdrop-blur-xs overflow-hidden"
            @keydown.escape.window="closeScoring()"
        >
            <div 
                class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-auto max-h-[92vh] sm:max-h-[88vh] flex flex-col"
                @click.away="closeScoring()"
            >
                <!-- Top Header (Always Visible & Pinned) -->
                <div class="px-5 py-3.5 sm:px-6 sm:py-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-[#5e43f3] text-white flex items-center justify-between shrink-0">
                    <div class="min-w-0 pr-3">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-500/30 text-purple-200 border border-purple-300/30 mb-0.5">
                            <span>Rubrik Penilaian Bab IV</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold truncate" x-text="'Lembar Nilai: ' + (scoringWork?.full_name || '')"></h3>
                        <p class="text-[11px] sm:text-xs text-purple-200 truncate" x-text="(scoringWork?.school_name || '') + ' • ' + (scoringWork?.category_label || '')"></p>
                    </div>
                    <button type="button" @click="closeScoring()" class="p-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white shrink-0 transition cursor-pointer" title="Tutup lembar nilai">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Scrollable Rubric Form (Fits and Scrolls Smoothly Inside Modal) -->
                <div class="p-4 sm:p-6 overflow-y-auto space-y-4 sm:space-y-5 flex-1 min-h-0">
                    <!-- Tautan Cek Karya -->
                    <div class="p-3 sm:p-3.5 rounded-2xl bg-purple-50 border border-purple-200 flex items-center justify-between text-xs gap-3">
                        <span class="font-bold text-[#5e43f3] whitespace-nowrap">Tautan Karya Peserta:</span>
                        <a 
                            :href="scoringWork?.media_link" 
                            target="_blank" 
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#5e43f3] text-white text-xs font-bold hover:bg-indigo-700 transition whitespace-nowrap shrink-0 shadow-2xs"
                        >
                            <span>Buka Media Karya Digital</span>
                            <svg class="w-3.5 h-3.5 shrink-0 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>

                    <!-- ASPEK 1: Pedagogis & Subjek Materi (Bobot 30%) -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-extrabold text-slate-900 block">1. Aspek Pedagogis &amp; Subjek Materi</span>
                                <span class="text-[11px] font-bold text-[#5e43f3]">Bobot: 30%</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <input 
                                    type="number" 
                                    min="0" 
                                    max="100" 
                                    step="0.5" 
                                    x-model.number="pedagogical" 
                                    class="w-20 px-3 py-1.5 rounded-xl border border-slate-300 font-mono font-bold text-center text-sm focus:border-[#5e43f3] bg-white outline-none focus:ring-2 focus:ring-purple-100"
                                >
                                <span class="text-xs font-bold text-slate-500 whitespace-nowrap">/ 100</span>
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-500 bg-white p-2.5 rounded-xl border border-slate-100 leading-relaxed">
                            <strong class="text-slate-700 block mb-0.5">Indikator Penilaian:</strong>
                            • Kebenaran dan keakuratan substansi materi pelajaran SMA/SMK<br>
                            • Kesesuaian dengan Capaian Pembelajaran Kurikulum<br>
                            • Kejelasan alur penyampaian konsep dan efektivitas umpan balik (feedback) belajar
                        </div>
                    </div>

                    <!-- ASPEK 2: Kreativitas & Desain Visual (Bobot 30%) -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-extrabold text-slate-900 block">2. Aspek Kreativitas &amp; Desain Visual</span>
                                <span class="text-[11px] font-bold text-[#5e43f3]">Bobot: 30%</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <input 
                                    type="number" 
                                    min="0" 
                                    max="100" 
                                    step="0.5" 
                                    x-model.number="creativity" 
                                    class="w-20 px-3 py-1.5 rounded-xl border border-slate-300 font-mono font-bold text-center text-sm focus:border-[#5e43f3] bg-white outline-none focus:ring-2 focus:ring-purple-100"
                                >
                                <span class="text-xs font-bold text-slate-500 whitespace-nowrap">/ 100</span>
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-500 bg-white p-2.5 rounded-xl border border-slate-100 leading-relaxed">
                            <strong class="text-slate-700 block mb-0.5">Indikator Penilaian:</strong>
                            • Keaslian/orisinalitas ide dan keunikan konsep media/gim<br>
                            • Kualitas tata letak (layout), pilihan warna, tipografi, dan estetika visual<br>
                            • Desain karakter/elemen permainan (Gim) atau kerapian UI/UX (MPI)
                        </div>
                    </div>

                    <!-- ASPEK 3: Kualitas Teknis & Interaktivitas (Bobot 25%) -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-extrabold text-slate-900 block">3. Aspek Kualitas Teknis &amp; Interaktivitas</span>
                                <span class="text-[11px] font-bold text-[#5e43f3]">Bobot: 25%</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <input 
                                    type="number" 
                                    min="0" 
                                    max="100" 
                                    step="0.5" 
                                    x-model.number="technical" 
                                    class="w-20 px-3 py-1.5 rounded-xl border border-slate-300 font-mono font-bold text-center text-sm focus:border-[#5e43f3] bg-white outline-none focus:ring-2 focus:ring-purple-100"
                                >
                                <span class="text-xs font-bold text-slate-500 whitespace-nowrap">/ 100</span>
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-500 bg-white p-2.5 rounded-xl border border-slate-100 leading-relaxed">
                            <strong class="text-slate-700 block mb-0.5">Indikator Penilaian:</strong>
                            • Kemudahan navigasi, responsivitas kontrol, dan kebebasan dari bug/crash<br>
                            • Kecepatan pemuatan (loading time) dan kemudahan pengoperasian<br>
                            • Kedalaman tingkat interaktivitas antara pengguna dengan media/gim
                        </div>
                    </div>

                    <!-- ASPEK 4: Presentasi & Dampak Penerapan (Bobot 15%) -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-extrabold text-slate-900 block">4. Aspek Presentasi &amp; Dampak Penerapan</span>
                                <span class="text-[11px] font-bold text-[#5e43f3]">Bobot: 15%</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <input 
                                    type="number" 
                                    min="0" 
                                    max="100" 
                                    step="0.5" 
                                    x-model.number="presentation" 
                                    class="w-20 px-3 py-1.5 rounded-xl border border-slate-300 font-mono font-bold text-center text-sm focus:border-[#5e43f3] bg-white outline-none focus:ring-2 focus:ring-purple-100"
                                >
                                <span class="text-xs font-bold text-slate-500 whitespace-nowrap">/ 100</span>
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-500 bg-white p-2.5 rounded-xl border border-slate-100 leading-relaxed">
                            <strong class="text-slate-700 block mb-0.5">Indikator Penilaian:</strong>
                            • Penguasaan materi dan kemampuan mempertahankan argumen di depan Dewan Juri<br>
                            • Kejujuran demonstrasi produk secara langsung<br>
                            • Potensi dan kepraktisan implementasi karya di dalam kelas secara berkelanjutan
                        </div>
                    </div>

                    <!-- Catatan Masukan Juri -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Catatan Masukan / Ulasan Dewan Juri (Opsional)
                        </label>
                        <textarea 
                            rows="2" 
                            x-model="notes" 
                            placeholder="Tuliskan catatan evaluasi, keunggulan karya, atau masukan pengembangan..."
                            class="w-full px-3.5 py-2 text-xs rounded-2xl border border-slate-300 focus:border-[#5e43f3] focus:ring-2 focus:ring-purple-100 outline-none"
                        ></textarea>
                    </div>

                    <!-- Total Nilai Terhitung Otomatis -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Nilai Akhir Berbobot (100%):</span>
                            <span class="text-xs text-purple-700">(30% P + 30% K + 25% T + 15% Pr)</span>
                        </div>
                        <div class="text-right flex items-baseline gap-1">
                            <span class="text-2xl sm:text-3xl font-extrabold text-[#5e43f3] tracking-tight font-mono" x-text="calculatedTotal"></span>
                            <span class="text-xs font-bold text-slate-400">/ 100</span>
                        </div>
                    </div>

                    <!-- Notifikasi Sukses -->
                    <template x-if="scoreSuccessMsg">
                        <div class="p-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold text-center border border-emerald-200">
                            <span x-text="scoreSuccessMsg"></span>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer (Always Visible & Pinned) -->
                <div class="p-4 sm:p-5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3 shrink-0">
                    <button type="button" @click="closeScoring()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                    <button 
                        type="button" 
                        @click="submitScore()" 
                        :disabled="isSubmittingScore"
                        class="px-5 sm:px-6 py-2.5 text-xs font-bold text-white bg-[#5e43f3] hover:bg-indigo-700 rounded-xl shadow-xs transition cursor-pointer flex items-center justify-center gap-2"
                    >
                        <svg x-show="isSubmittingScore" x-cloak class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmittingScore ? 'Menyimpan...' : 'Simpan Nilai Karya'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL PENGATURAN PROFIL JURI                            -->
        <!-- ======================================================= -->
        <div 
            x-show="showProfileModal" 
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto"
            @keydown.escape.window="showProfileModal = false"
        >
            <div 
                class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-auto max-h-[90vh] flex flex-col"
                @click.away="showProfileModal = false"
            >
                <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-[#5e43f3] text-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-purple-200">Kredensial Akun</span>
                        <h3 class="text-base font-bold">Pengaturan Akun Juri</h3>
                    </div>
                    <button type="button" @click="showProfileModal = false" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('judge.profile.update') }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="profileForm.name" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Username Login</label>
                        <input type="text" name="username" x-model="profileForm.username" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" x-model="profileForm.email" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi Baru (Opsional)</label>
                        <input type="password" name="password" x-model="profileForm.password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" x-model="profileForm.password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showProfileModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#5e43f3] hover:bg-indigo-700 rounded-xl shadow-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
