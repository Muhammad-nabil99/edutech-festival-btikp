<x-layouts.app title="Portal Dewan Juri — EduTech Festival BTIKP" maxWidth="max-w-7xl">
    <div 
        class="space-y-6"
        x-data="{
            activeTab: '{{ $activeTab ?? 'finalists' }}',
            selectedCategory: '{{ $category ?? 'all' }}',
            searchQuery: '{{ $search ?? '' }}',
            viewMode: localStorage.getItem('judge_view_mode') || 'grid',
            
            setViewMode(mode) {
                this.viewMode = mode;
                localStorage.setItem('judge_view_mode', mode);
            },

            // Suggestion Quota State & Map
            myGimSuggestions: {{ $stats['my_gim_suggestions'] }},
            myMediaSuggestions: {{ $stats['my_media_suggestions'] }},
            togglingSuggestId: null,

            // Reactive Suggestions Map: { [regId]: { isSuggestedByMe: bool, count: int, myReasons: [], suggestions: [...] } }
            suggestionsMap: {
                @foreach ($allWorks as $w)
                    {{ $w->id }}: {
                        isSuggestedByMe: {{ $w->isSuggestedBy($judge) ? 'true' : 'false' }},
                        count: {{ $w->judgeSuggestions->count() }},
                        myReasons: @json($w->judgeSuggestions->firstWhere('user_id', $judge->id)?->reasons ?? []),
                        suggestions: [
                            @foreach ($w->judgeSuggestions as $s)
                                {
                                    userId: {{ $s->user_id }},
                                    name: '{{ addslashes($s->user->name ?? 'Dewan Juri') }}',
                                    username: '{{ addslashes($s->user->username ?? ('juri' . $s->user_id)) }}',
                                    email: '{{ addslashes($s->user->email ?? '-') }}',
                                    code: '{{ $s->user->judge_code ?? ('J' . $s->user_id) }}',
                                    reasons: @json($s->reasons ?? []),
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
                if (this.selectedReasons.length === 0) {
                    this.suggestionError = 'Pilih minimal 1 alasan rekomendasi sebelum menyimpan saran Top 10.';
                    return;
                }
                this.isSubmittingSuggestion = true;
                this.suggestionError = null;

                const work = this.suggestionWork;
                const isAdding = !this.isSuggested(work.id);
                const isGim = work.learning_category === 'gim_edukasi';
                const currentCount = isGim ? this.myGimSuggestions : this.myMediaSuggestions;

                if (isAdding && currentCount >= 10) {
                    this.suggestionError = 'Kuota saran Top 10 Anda untuk kategori ' + (work.category_label || (isGim ? 'Gim Edukasi' : 'Media Interaktif')) + ' sudah penuh (maksimal 10 karya). Batalkan salah satu saran Anda terlebih dahulu.';
                    this.isSubmittingSuggestion = false;
                    return;
                }

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
                if (!work.profile_photo_url) {
                    work.profile_photo_url = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(work.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200';
                }
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

            get isAnyModalOpen() {
                return !!(this.scoringWork !== null || this.showProfileModal || this.isJudgeModalOpen || this.suggestionWork !== null);
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
                        Selamat datang, <strong>{{ $judge->name }}</strong> ({{ $judge->username }}). Anda dapat merekomendasikan hingga 10 karya per kategori ke Panitia dengan mengisi checklist kelayakan, serta menilai karya Finalis Top 10 yang telah disetujui Admin.
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

            <!-- Total Finalis yang Disetujui Admin -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Finalis Resmi Top 10</span>
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
                        Media Interaktif (MPI)
                    </a>
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
                                @click="openSuggestionModal(@js($work))"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer flex items-center gap-1.5"
                                :class="isSuggested({{ $work->id }}) 
                                    ? 'bg-purple-600 hover:bg-purple-700 text-white' 
                                    : 'bg-white hover:bg-purple-50 text-purple-700 border border-purple-200'"
                            >
                                <span x-text="isSuggested({{ $work->id }}) ? '✓ Disarankan' : '+ Sarankan Top 10'"></span>
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
                                            @click="openSuggestionModal(@js($work))"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer inline-flex items-center gap-1.5"
                                            :class="isSuggested({{ $work->id }}) 
                                                ? 'bg-purple-600 hover:bg-purple-700 text-white' 
                                                : 'bg-white hover:bg-purple-50 text-purple-700 border border-purple-200'"
                                        >
                                            <span x-text="isSuggested({{ $work->id }}) ? '✓ Disarankan' : '+ Sarankan Top 10'"></span>
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
                                @click="openSuggestionModal(@js($work))"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer bg-purple-100 hover:bg-purple-200 text-purple-800 border border-purple-300 flex items-center gap-1"
                            >
                                <span>✏️ Ubah / Tarik Saran</span>
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
                                            @click="openSuggestionModal(@js($work))"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer bg-purple-100 hover:bg-purple-200 text-purple-800 border border-purple-300 inline-flex items-center gap-1"
                                        >
                                            <span>✏️ Ubah / Tarik</span>
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($finalists as $work)
                    @php
                        $myScore = $work->judgeScores->first();
                    @endphp
                    <div class="bg-white rounded-3xl p-5 border {{ $myScore ? 'border-emerald-200 shadow-xs' : 'border-slate-200 shadow-xs' }} hover:shadow-soft transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Header: Kategori & Status Penilaian -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $work->learning_category === 'gim_edukasi' ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-sky-50 text-sky-800 border border-sky-200/60' }}">
                                    {{ $work->category_label }}
                                </span>

                                @if ($myScore)
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                        <span>✓ Nilai:</span>
                                        <span class="font-mono">{{ number_format($myScore->total_score, 1) }}</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
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

                            <!-- Ringkasan Aspek Nilai Jika Sudah Ada -->
                            @if ($myScore)
                                <div class="grid grid-cols-4 gap-1 p-2 rounded-xl bg-slate-50 text-center text-[10px] mb-3 font-mono">
                                    <div class="p-1 rounded bg-white border border-slate-100">
                                        <div class="text-slate-400">Ped (30%)</div>
                                        <div class="font-bold text-slate-800">{{ $myScore->pedagogical_score }}</div>
                                    </div>
                                    <div class="p-1 rounded bg-white border border-slate-100">
                                        <div class="text-slate-400">Kre (30%)</div>
                                        <div class="font-bold text-slate-800">{{ $myScore->creativity_score }}</div>
                                    </div>
                                    <div class="p-1 rounded bg-white border border-slate-100">
                                        <div class="text-slate-400">Tek (25%)</div>
                                        <div class="font-bold text-slate-800">{{ $myScore->technical_score }}</div>
                                    </div>
                                    <div class="p-1 rounded bg-white border border-slate-100">
                                        <div class="text-slate-400">Pre (15%)</div>
                                        <div class="font-bold text-slate-800">{{ $myScore->presentation_score }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Action: Tombol Input / Edit Nilai -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-slate-400 font-bold">{{ $work->registration_number }}</span>
                            <button 
                                type="button" 
                                @click="openScoring(@js($work))"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-1.5 {{ $myScore ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-[#5e43f3] hover:bg-indigo-700 text-white' }}"
                            >
                                <span>{{ $myScore ? '✏️ Ubah Penilaian' : '📝 Beri Nilai' }}</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-3xl border border-slate-100">
                        <p class="font-bold text-sm text-slate-800">Belum ada karya yang disetujui Admin masuk Top 10.</p>
                        <p class="text-xs text-slate-400 mt-1">Gunakan tab "Semua Peserta" untuk memberikan rekomendasi karya ke Admin.</p>
                    </div>
                @endforelse
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

                    <!-- Identitas Peserta -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img 
                                :src="scoringWork?.profile_photo_url || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(scoringWork?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200')" 
                                :alt="scoringWork?.full_name"
                                class="w-11 h-11 rounded-xl object-cover border border-slate-200 bg-white shrink-0 shadow-2xs"
                                x-on:error="$event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(scoringWork?.full_name || 'Peserta') + '&background=5E43F3&color=FFFFFF&size=200'"
                            >
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm" x-text="scoringWork?.full_name"></h4>
                                <p class="text-xs text-slate-600" x-text="scoringWork?.school_name + ' • ' + scoringWork?.regency_city"></p>
                            </div>
                        </div>
                        <a :href="scoringWork?.media_link" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-[#5e43f3] font-bold text-xs hover:bg-slate-100 transition whitespace-nowrap">
                            Buka Karya &rarr;
                        </a>
                    </div>

                    <!-- 4 Aspek Penilaian Sesuai Rubrik Bab IV -->
                    <div class="space-y-4">
                        <!-- Aspek 1: Pedagogis (30%) -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs">1. Aspek Pedagogis &amp; Subjek Materi (Bobot: 30%)</span>
                                <span class="font-mono font-bold text-xs text-emerald-700" x-text="pedagogical"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                • Kebenaran &amp; keakuratan substansi materi pelajaran SMA/SMK<br>
                                • Kesesuaian dengan Capaian Pembelajaran Kurikulum<br>
                                • Kejelasan alur penyampaian konsep &amp; efektivitas umpan balik (feedback) belajar
                            </p>
                            <div class="flex items-center gap-3 pt-1">
                                <input type="range" min="0" max="100" x-model.number="pedagogical" class="w-full accent-emerald-600 cursor-pointer">
                                <input type="number" min="0" max="100" x-model.number="pedagogical" class="w-16 px-2 py-1 text-center font-mono font-bold rounded-lg border border-slate-200 text-xs">
                            </div>
                        </div>

                        <!-- Aspek 2: Kreativitas & Visual (30%) -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs">2. Aspek Kreativitas &amp; Desain Visual (Bobot: 30%)</span>
                                <span class="font-mono font-bold text-xs text-emerald-700" x-text="creativity"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                • Keaslian/orisinalitas ide &amp; keunikan konsep media/gim<br>
                                • Kualitas tata letak (layout), pilihan warna, tipografi, dan estetika visual<br>
                                • Desain karakter/elemen permainan (khusus Gim) atau kerapian UI/UX (khusus MPI)
                            </p>
                            <div class="flex items-center gap-3 pt-1">
                                <input type="range" min="0" max="100" x-model.number="creativity" class="w-full accent-emerald-600 cursor-pointer">
                                <input type="number" min="0" max="100" x-model.number="creativity" class="w-16 px-2 py-1 text-center font-mono font-bold rounded-lg border border-slate-200 text-xs">
                            </div>
                        </div>

                        <!-- Aspek 3: Teknis & Interaktivitas (25%) -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs">3. Aspek Kualitas Teknis &amp; Interaktivitas (Bobot: 25%)</span>
                                <span class="font-mono font-bold text-xs text-emerald-700" x-text="technical"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                • Kemudahan navigasi, responsivitas kontrol, &amp; kebebasan dari kesalahan teknis (bug/crash)<br>
                                • Kecepatan pemuatan (loading time) dan kemudahan pengoperasian<br>
                                • Kedalaman tingkat interaktivitas antara pengguna dengan media/gim
                            </p>
                            <div class="flex items-center gap-3 pt-1">
                                <input type="range" min="0" max="100" x-model.number="technical" class="w-full accent-emerald-600 cursor-pointer">
                                <input type="number" min="0" max="100" x-model.number="technical" class="w-16 px-2 py-1 text-center font-mono font-bold rounded-lg border border-slate-200 text-xs">
                            </div>
                        </div>

                        <!-- Aspek 4: Presentasi & Dampak (15%) -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-xs">4. Aspek Presentasi &amp; Dampak Penerapan (Bobot: 15%)</span>
                                <span class="font-mono font-bold text-xs text-emerald-700" x-text="presentation"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                • Penguasaan materi &amp; kemampuan mempertahankan argumen di depan Dewan Juri<br>
                                • Kejujuran demonstrasi produk secara langsung<br>
                                • Potensi &amp; kepraktisan implementasi karya di dalam kelas secara berkelanjutan
                            </p>
                            <div class="flex items-center gap-3 pt-1">
                                <input type="range" min="0" max="100" x-model.number="presentation" class="w-full accent-emerald-600 cursor-pointer">
                                <input type="number" min="0" max="100" x-model.number="presentation" class="w-16 px-2 py-1 text-center font-mono font-bold rounded-lg border border-slate-200 text-xs">
                            </div>
                        </div>

                        <!-- Catatan Evaluasi Juri -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Catatan Masukan / Umpan Balik (Opsional):</label>
                            <textarea x-model="notes" rows="3" placeholder="Tuliskan apresiasi, masukan perbaikan, atau catatan khusus dewan juri..." class="w-full p-3 text-xs rounded-xl border border-slate-200 focus:border-emerald-600 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                    <div>
                        <span class="text-xs text-slate-500">Nilai Akhir Terbobot:</span>
                        <div class="text-2xl font-extrabold text-emerald-700 font-mono" x-text="calculatedTotal"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="closeScoring()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="button" @click="submitScore()" :disabled="isSubmittingScore" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center gap-2">
                            <span x-text="isSubmittingScore ? 'Menyimpan...' : 'Simpan Nilai Resmi'"></span>
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

                    <div class="flex items-center gap-3.5 relative z-10">
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

                    <div class="text-right relative z-10">
                        <span class="px-2.5 py-1 rounded-xl font-mono font-black text-xs shadow-sm" :class="hoveredJudge?.badgeColor || 'bg-white text-indigo-700'" x-text="'KODE: ' + (hoveredJudge?.code || '-')"></span>
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
                        <div class="text-[10px] uppercase font-bold text-purple-700 tracking-wider mb-1 flex items-center justify-between shrink-0">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Alasan Rekomendasi Top 10</span>
                            </span>
                            <span class="font-mono text-[9px] px-1.5 py-0.5 rounded-md bg-purple-200/70 text-purple-800 font-extrabold" x-text="(hoveredJudge?.reasons?.length || 0) + ' Kriteria'"></span>
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
                    class="px-5 py-4 text-white relative flex items-center justify-between bg-gradient-to-r shrink-0"
                    :class="hoveredJudge?.color || 'from-indigo-600 via-indigo-700 to-indigo-900'"
                >
                    <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>

                    <div class="flex items-center gap-3.5 relative z-10 min-w-0 pr-2">
                        <div class="w-12 h-12 sm:w-13 sm:h-13 rounded-2xl bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center font-extrabold text-xl shadow-md text-white tracking-wider shrink-0" x-text="hoveredJudge?.code"></div>
                        
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-sm sm:text-base text-white truncate max-w-[200px] sm:max-w-[240px]" x-text="hoveredJudge?.name"></h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/25 border border-white/30 text-white uppercase tracking-wider backdrop-blur-xs shrink-0">
                                    Dewan Juri
                                </span>
                            </div>
                            <div class="text-xs text-white/90 font-medium flex items-center gap-1.5 mt-0.5 flex-wrap">
                                <span class="font-mono text-white/90">@<span x-text="hoveredJudge?.username"></span></span>
                                <span class="text-white/60">•</span>
                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-200 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                    Aktif Menilai
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 relative z-10 shrink-0">
                        <span class="px-2.5 py-1 rounded-xl font-mono font-black text-xs shadow-sm" :class="hoveredJudge?.badgeColor || 'bg-white text-indigo-700'" x-text="'KODE: ' + (hoveredJudge?.code || '-')"></span>
                        <button 
                            type="button" 
                            @click="closeJudgeModal()"
                            class="w-7 h-7 rounded-xl bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition cursor-pointer"
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
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 text-emerald-950 shrink-0">
                        <div class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider mb-2.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Checklist Alasan yang Dicentang Juri</span>
                            </span>
                            <span class="font-mono text-xs px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-extrabold" x-text="(hoveredJudge?.reasons?.length || 0) + ' Kriteria Terpenuhi'"></span>
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

                    <!-- A. Detail Karya & Peserta (Read-only) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">A. Informasi Peserta &amp; Karya</span>
                            <span 
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
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
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">B. Checklist Alasan Saran Top 10</span>
                                <p class="text-[11px] text-slate-500">Centang seluruh kriteria kelayakan yang telah Anda review (minimal 1 dipilih):</p>
                            </div>
                            <span 
                                class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-full"
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
                                :disabled="isSubmittingSuggestion"
                                class="px-4 py-2.5 rounded-xl border border-rose-300 hover:bg-rose-50 text-rose-700 font-bold text-xs transition cursor-pointer"
                            >
                                <span x-text="isSubmittingSuggestion ? 'Memproses...' : '✕ Tarik Saran'"></span>
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
                            :disabled="isSubmittingSuggestion || selectedReasons.length === 0"
                            class="px-5 py-2.5 rounded-xl bg-[#5e43f3] hover:bg-indigo-700 disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition cursor-pointer flex items-center gap-2"
                        >
                            <svg x-show="isSubmittingSuggestion" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmittingSuggestion ? 'Menyimpan...' : (isSuggested(suggestionWork?.id) ? 'Perbarui Saran Top 10' : 'Simpan & Sarankan Top 10')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
