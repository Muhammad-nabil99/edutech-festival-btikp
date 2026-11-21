<x-layouts.app title="Portal Dewan Juri — EduTech Festival BTIKP" maxWidth="max-w-7xl">
    <div 
        class="space-y-6"
        x-data="{
            activeTab: '{{ $activeTab ?? 'finalists' }}',
            selectedCategory: '{{ $category ?? 'all' }}',
            searchQuery: '{{ $search ?? '' }}',
            
            // Suggestion Quota State & Map
            myGimSuggestions: {{ $stats['my_gim_suggestions'] }},
            myMediaSuggestions: {{ $stats['my_media_suggestions'] }},
            togglingSuggestId: null,

            // Reactive Suggestions Map: { [regId]: { isSuggestedByMe: bool, count: int, suggestions: [...] } }
            suggestionsMap: {
                @foreach ($allWorks as $w)
                    {{ $w->id }}: {
                        isSuggestedByMe: {{ $w->isSuggestedBy($judge) ? 'true' : 'false' }},
                        count: {{ $w->judgeSuggestions->count() }},
                        suggestions: [
                            @foreach ($w->judgeSuggestions as $s)
                                {
                                    userId: {{ $s->user_id }},
                                    name: '{{ addslashes($s->user->name ?? 'Dewan Juri') }}',
                                    username: '{{ addslashes($s->user->username ?? ('juri' . $s->user_id)) }}',
                                    email: '{{ addslashes($s->user->email ?? '-') }}',
                                    code: '{{ $s->user->judge_code ?? ('J' . $s->user_id) }}',
                                    focus: '{{ addslashes(match ($s->user->judge_code ?? '') {
                                        'J1' => 'Gim Edukasi & Interaktivitas Media Digital',
                                        'J2' => 'Pedagogik, Kurikulum & Substansi Materi SMA/SMK',
                                        'J3' => 'Desain Visual, Estetika & Kualitas UI/UX',
                                        default => 'Evaluasi Inovasi Pembelajaran Digital BTIKP',
                                    }) }}',
                                    suggested_at: '{{ $s->created_at ? $s->created_at->translatedFormat('d M Y, H:i') . ' WITA' : 'Tercatat di sistem' }}',
                                    participant_name: '{{ addslashes($w->full_name) }}',
                                    colorClass: '{{ $s->user->judge_code === 'J1' ? 'bg-indigo-600 text-white' : ($s->user->judge_code === 'J2' ? 'bg-emerald-600 text-white' : ($s->user->judge_code === 'J3' ? 'bg-amber-600 text-white' : 'bg-purple-600 text-white')) }}',
                                    color: '{{ match ($s->user->judge_code ?? '') {
                                        'J1' => 'from-indigo-600 via-indigo-700 to-indigo-900',
                                        'J2' => 'from-emerald-600 via-teal-700 to-emerald-900',
                                        'J3' => 'from-amber-500 via-orange-600 to-amber-800',
                                        default => 'from-purple-600 via-indigo-700 to-purple-900',
                                    } }}',
                                    badgeColor: '{{ match ($s->user->judge_code ?? '') {
                                        'J1' => 'bg-indigo-500 text-white',
                                        'J2' => 'bg-emerald-500 text-white',
                                        'J3' => 'bg-amber-500 text-white',
                                        default => 'bg-purple-500 text-white',
                                    } }}'
                                },
                            @endforeach
                        ]
                    },
                @endforeach
            },

            isSuggested(id) {
                return this.suggestionsMap[id] ? this.suggestionsMap[id].isSuggestedByMe : false;
            },

            getSuggestions(id) {
                return this.suggestionsMap[id] ? this.suggestionsMap[id].suggestions : [];
            },

            async toggleSuggestion(work) {
                if (!work || this.togglingSuggestId) return;

                const currentlySuggested = this.isSuggested(work.id);
                const isAdding = !currentlySuggested;
                const isGim = work.learning_category === 'gim_edukasi';
                const currentCount = isGim ? this.myGimSuggestions : this.myMediaSuggestions;

                // Client-side quota guard
                if (isAdding && currentCount >= 10) {
                    alert('Kuota saran Top 10 Anda untuk kategori ' + (work.category_label || (isGim ? 'Gim Edukasi' : 'Media Interaktif')) + ' sudah penuh (maksimal 10 karya). Batalkan salah satu saran Anda terlebih dahulu jika ingin menggantinya.');
                    return;
                }

                this.togglingSuggestId = work.id;

                try {
                    const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch('/judge/works/' + work.id + '/toggle-suggest', {
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
                        if (!this.suggestionsMap[work.id]) {
                            this.suggestionsMap[work.id] = { isSuggestedByMe: false, count: 0, suggestions: [] };
                        }

                        this.suggestionsMap[work.id].isSuggestedByMe = data.is_suggested;
                        this.suggestionsMap[work.id].count = data.suggestions_count;
                        this.suggestionsMap[work.id].suggestions = (data.suggestions || []).map(s => ({
                            userId: s.user_id,
                            name: s.judge_name,
                            username: s.judge_username || ('juri' + s.user_id),
                            email: s.judge_email || '-',
                            code: s.judge_code,
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
                    } else {
                        alert(data.message || 'Gagal memperbarui saran.');
                    }
                } catch (e) {
                    alert('Gagal menghubungi server.');
                } finally {
                    this.togglingSuggestId = null;
                }
            },

            // Scoring State
            scoringWork: null,
            pedagogical: 0,
            creativity: 0,
            technical: 0,
            presentation: 0,
            notes: '',
            isSubmittingScore: false,
            scoreSuccessMsg: null,

            // Profile State
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
            },

            // Floating Judge Overview Popover State (Exact width 487px, height 280px)
            hoveredJudge: null,
            hoverPos: { x: 0, y: 0 },
            hoverTimeout: null,

            showJudgeOverview(judgeData, event) {
                clearTimeout(this.hoverTimeout);
                const rect = event.currentTarget.getBoundingClientRect();
                const cardW = 487;
                const cardH = 280;

                // Smart X positioning (centered on trigger, bounded within viewport)
                let x = rect.left + (rect.width / 2) - (cardW / 2);
                if (x < 15) x = 15;
                if (x + cardW > window.innerWidth - 15) {
                    x = window.innerWidth - cardW - 15;
                }

                // Smart Y positioning (prefer below, fallback above)
                let y = rect.bottom + 8;
                if (y + cardH > window.innerHeight - 15) {
                    y = rect.top - cardH - 8;
                }
                if (y < 10) y = 10;

                this.hoverPos = { x, y };
                this.hoveredJudge = judgeData;
            },

            hideJudgeOverview() {
                this.hoverTimeout = setTimeout(() => {
                    this.hoveredJudge = null;
                }, 180);
            },

            keepJudgeOverview() {
                clearTimeout(this.hoverTimeout);
            },

            toggleJudgeOverview(judgeData, event) {
                clearTimeout(this.hoverTimeout);
                if (this.hoveredJudge && this.hoveredJudge.code === judgeData.code) {
                    this.hoveredJudge = null;
                } else {
                    this.showJudgeOverview(judgeData, event);
                }
            },

            get isAnyModalOpen() {
                return !!(this.scoringWork !== null || this.showProfileModal);
            },

            init() {
                this.$watch('isAnyModalOpen', (isOpen) => {
                    if (isOpen) {
                        document.body.classList.add('overflow-hidden');
                        document.documentElement.classList.add('overflow-hidden');
                    } else {
                        document.body.classList.remove('overflow-hidden');
                        document.documentElement.classList.remove('overflow-hidden');
                    }
                });
            }
        }"
    >
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
                        Selamat datang, <strong>{{ $judge->name }}</strong> ({{ $judge->username }}). Anda dapat merekomendasikan hingga 10 karya per kategori ke Panitia, serta menilai karya Finalis Top 10 yang telah disetujui Admin.
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

        <!-- Metric Cards: Kuota Rekomendasi & Penilaian -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Kuota Saran Gim -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Saran Top 10: Gim</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1 font-mono">
                        <span x-text="myGimSuggestions"></span><span class="text-slate-300 text-lg font-normal"> / 10</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 mt-2 font-medium">
                    Tersisa: <strong class="text-slate-700" x-text="Math.max(0, 10 - myGimSuggestions)"></strong> slot saran
                </p>
            </div>

            <!-- Kuota Saran Media -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Saran Top 10: Media</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-sky-600 mt-1 font-mono">
                        <span x-text="myMediaSuggestions"></span><span class="text-slate-300 text-lg font-normal"> / 10</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 mt-2 font-medium">
                    Tersisa: <strong class="text-slate-700" x-text="Math.max(0, 10 - myMediaSuggestions)"></strong> slot saran
                </p>
            </div>

            <!-- Finalis Disetujui Admin -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Finalis Disetujui</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-[#5e43f3] mt-1 font-mono">
                        {{ $stats['finalists_count'] }}<span class="text-slate-300 text-lg font-normal"> / 20</span>
                    </div>
                </div>
                <p class="text-[11px] text-[#5e43f3] mt-2 font-semibold">
                    Keputusan resmi Admin
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
                    <span>💡 Review seluruh karya pendaftar &amp; klik <strong>Sarankan Top 10</strong>.</span>
                </template>
                <template x-if="activeTab === 'my_suggestions'">
                    <span>💡 Daftar karya yang Anda usulkan. Hanya Admin yang menentukan lolos finalis.</span>
                </template>
                <template x-if="activeTab === 'finalists'">
                    <span>⭐ Hanya karya di tab ini yang dinilai menggunakan <strong>Rubrik Bab IV</strong>.</span>
                </template>
            </div>
        </div>

        <!-- Filter Kategori & Pencarian Form -->
        <form method="GET" action="{{ route('judge.dashboard') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <input type="hidden" name="tab" :value="activeTab">

            <!-- Filter Kategori Button Group -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
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
                    Media Interaktif
                </a>
            </div>

            <!-- Pencarian Input -->
            <div class="relative w-full sm:w-72">
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
        </form>

        <!-- ======================================================= -->
        <!-- KONTEN TAB 1: SEMUA PESERTA (FULL LIST & SUGGEST)       -->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'all'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
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
                                            @click.stop="toggleJudgeOverview(j, $event)"
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

                        <!-- Footer Action: Tombol Suggest Top 10 -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                            <button 
                                type="button" 
                                @click="toggleSuggestion(@js($work))"
                                :disabled="togglingSuggestId === {{ $work->id }}"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer flex items-center gap-1.5"
                                :class="isSuggested({{ $work->id }}) 
                                    ? 'bg-purple-600 hover:bg-purple-700 text-white' 
                                    : 'bg-white hover:bg-purple-50 text-purple-700 border border-purple-200'"
                            >
                                <template x-if="togglingSuggestId === {{ $work->id }}">
                                    <span class="inline-block animate-spin">⏳</span>
                                </template>
                                <template x-if="togglingSuggestId !== {{ $work->id }}">
                                    <span>
                                        <span x-text="isSuggested({{ $work->id }}) ? '✓ Disarankan' : '+ Sarankan Top 10'"></span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                        <p class="font-bold text-sm text-slate-800">Tidak ada data pendaftar yang cocok dengan filter pencarian.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- KONTEN TAB 2: SARAN SAYA                                -->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'my_suggestions'" class="space-y-4">
            <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-xs text-purple-900 font-medium flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">📌</span>
                    <span>Ini adalah daftar karya yang telah <strong>Anda sarankan</strong> ke Panitia. Anda dapat menarik saran kapan saja sebelum Admin menetapkan Finalis.</span>
                </div>
                <div class="font-mono font-bold whitespace-nowrap text-purple-800">
                    Total Saran: <span x-text="myGimSuggestions + myMediaSuggestions"></span> / 20
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
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
                                            @click.stop="toggleJudgeOverview(j, $event)"
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

                        <!-- Footer Action: Tombol Tarik Saran -->
                        <div class="pt-3 border-t border-purple-100 flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                            <button 
                                type="button" 
                                @click="toggleSuggestion(@js($work))"
                                :disabled="togglingSuggestId === {{ $work->id }}"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200"
                            >
                                <span>✕ Tarik Rekomendasi</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                        <p class="font-bold text-sm text-slate-800">Anda belum menyarankan karya apa pun.</p>
                        <p class="text-xs text-slate-500 mt-1">Buka tab <strong>Semua Peserta</strong> untuk mulai memilih 10 karya terbaik per kategori.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- KONTEN TAB 3: FINALIS TOP 10 (SIAP DINILAI DENGAN RUBRIK)-->
        <!-- ======================================================= -->
        <div x-show="activeTab === 'finalists'" class="space-y-4">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-950 font-medium flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">🏆</span>
                    <span>Daftar karya di bawah ini adalah <strong>Finalis Resmi Top 10 yang telah disetujui Admin</strong>. Silakan evaluasi dan input nilai menggunakan <strong>Rubrik Penilaian Bab IV</strong>.</span>
                </div>
                <div class="font-mono font-bold whitespace-nowrap text-emerald-800">
                    Finalis: {{ $finalists->count() }} Karya
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($finalists as $work)
                    @php
                        $myScore = $work->judgeScores->first();
                    @endphp
                    <div class="bg-white rounded-3xl p-5 border {{ $myScore ? 'border-emerald-300 bg-emerald-50/15' : 'border-slate-200' }} shadow-xs hover:shadow-soft transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Header: Kategori & Status Nilai -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-sky-50 text-sky-800 border border-sky-200/60' }}">
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
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        ⏳ Belum Dinilai
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

                            <!-- Avatar Juri yang Menyarankan Karya Ini -->
                            <div class="mb-3 flex items-center justify-between text-[11px] text-slate-500">
                                <span>Rekomendasi Dewan Juri:</span>
                                <div class="flex items-center gap-1" @click.stop>
                                    <template x-for="j in getSuggestions({{ $work->id }})" :key="j.userId">
                                        <div 
                                            class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-extrabold ring-1 ring-white cursor-pointer hover:scale-125 transition-transform"
                                            :class="j.colorClass"
                                            x-text="j.code"
                                            @mouseenter="showJudgeOverview(j, $event)"
                                            @mouseleave="hideJudgeOverview()"
                                            @click.stop="toggleJudgeOverview(j, $event)"
                                        ></div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Action: Tombol Input Nilai / Edit Nilai Rubrik -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                            <button 
                                type="button" 
                                @click="openScoring(@js($work))"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer {{ $myScore ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-[#5e43f3] hover:bg-indigo-700 text-white' }}"
                            >
                                {{ $myScore ? 'Edit Nilai (' . number_format($myScore->total_score, 1) . ')' : 'Beri Nilai Karya' }}
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                        <p class="font-bold text-sm text-slate-800">Belum ada karya yang disetujui oleh Panitia/Admin ke dalam nominasi Finalis Top 10.</p>
                        <p class="text-xs text-slate-500 mt-1">Silakan berikan saran rekomendasi Anda di tab <strong>Semua Peserta</strong> terlebih dahulu.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- MODAL PENILAIAN JURI (SCORING CARD DENGAN RUBRIK)       -->
        <!-- ======================================================= -->
        <div 
            x-show="scoringWork !== null" 
            x-cloak
            class="fixed inset-0 z-[999998] flex items-center justify-center p-3 sm:p-6 bg-slate-900/70 backdrop-blur-xs overflow-hidden"
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

                <!-- Scrollable Rubric Form -->
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
            class="fixed inset-0 z-[999998] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto"
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
                    <button type="button" @click="showProfileModal = false" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white cursor-pointer">
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
                        <input type="text" name="name" x-model="profileForm.name" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 outline-none focus:border-[#5e43f3]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Username Login</label>
                        <input type="text" name="username" x-model="profileForm.username" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 font-mono outline-none focus:border-[#5e43f3]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat Email</label>
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
        <!-- FLOATING JUDGE OVERVIEW POPOVER (Exact 487px x 280px)   -->
        <!-- ======================================================= -->
        <div 
            x-show="hoveredJudge !== null" 
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-98"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-98"
            class="fixed z-[999999] pointer-events-auto select-none"
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
                    <!-- Background Pattern Accent -->
                    <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>

                    <div class="flex items-center gap-3.5 relative z-10">
                        <!-- Big Judge Avatar -->
                        <div class="w-13 h-13 rounded-2xl bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center font-extrabold text-xl shadow-md text-white tracking-wider" x-text="hoveredJudge?.code"></div>
                        
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-base text-white truncate max-w-[240px]" x-text="hoveredJudge?.name"></h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/25 border border-white/30 text-white uppercase tracking-wider backdrop-blur-xs">
                                    Dewan Juri
                                </span>
                            </div>
                            <div class="text-xs text-white/90 font-medium flex items-center gap-1.5 mt-0.5">
                                <span class="font-mono text-white/90">@<span x-text="hoveredJudge?.username"></span></span>
                                <span class="text-white/60">•</span>
                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-200 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                    Aktif Menilai
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Badge Kode Juri -->
                    <div class="text-right relative z-10">
                        <span class="px-2.5 py-1 rounded-xl font-mono font-black text-xs shadow-sm" :class="hoveredJudge?.badgeColor || 'bg-white text-indigo-700'" x-text="'KODE: ' + (hoveredJudge?.code || '-')"></span>
                    </div>
                </div>

                <!-- Body Info Grid (280px total height - 84px header = 196px body) -->
                <div class="p-4 flex-1 flex flex-col justify-between text-xs bg-slate-50/70">
                    <!-- Row 1: Email & Rekomendasi Info -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email Akun Juri
                            </div>
                            <div class="font-semibold text-slate-800 text-[11px] truncate" :title="hoveredJudge?.email" x-text="hoveredJudge?.email || '-'"></div>
                        </div>

                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Waktu Rekomendasi
                            </div>
                            <div class="font-semibold text-slate-800 text-[11px] truncate" x-text="hoveredJudge?.suggested_at || '-'"></div>
                        </div>
                    </div>

                    <!-- Row 2: Fokus Bidang Penilaian -->
                    <div class="p-2.5 rounded-xl bg-purple-50/70 border border-purple-200/60 text-purple-950">
                        <div class="text-[10px] uppercase font-bold text-purple-700 tracking-wider mb-0.5 flex items-center gap-1">
                            <svg class="w-3 h-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                            </svg>
                            Fokus Penilaian &amp; Keahlian
                        </div>
                        <div class="font-medium text-[11px] leading-snug line-clamp-1 text-purple-900" x-text="hoveredJudge?.focus || 'Evaluasi Inovasi Pembelajaran Digital BTIKP'"></div>
                    </div>

                    <!-- Footer Note: Keabsahan Juri -->
                    <div class="flex items-center justify-between pt-1 border-t border-slate-200/60 text-[10px] text-slate-500">
                        <span class="flex items-center gap-1.5 font-semibold text-emerald-700">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Dewan Juri Terverifikasi BTIKP Sultra</span>
                        </span>
                        <span class="text-slate-400 font-mono text-[10px]">Portal EduTech 2026</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
