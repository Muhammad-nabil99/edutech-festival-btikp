<x-layouts.app title="Bukti Pendaftaran — {{ $registration->registration_number }}">
    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Success Alert Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 shadow-inner">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Pendaftaran Berhasil Terkirim!
            </h1>
            <p class="text-sm text-slate-600 max-w-md mx-auto">
                Terima kasih telah mendaftar di <strong>EduTech Festival BTIKP</strong>. Simpan bukti pendaftaran ini sebagai tanda kepesertaan resmi.
            </p>
        </div>

        <!-- Official Registration Ticket Card (Printable) -->
        <div id="printable-ticket" class="relative overflow-hidden bg-white rounded-3xl border border-slate-200 shadow-xl print:border-none print:shadow-none">
            <!-- Top Gradient Header with Official Logo -->
            <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 px-6 sm:px-8 py-5 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/10 p-1 backdrop-blur-xs border border-white/20 flex items-center justify-center shrink-0">
                        <img src="{{ asset('logo.jpeg') }}" alt="Logo BTIKP" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-[10px] tracking-widest uppercase font-semibold text-cyan-300">Tiket Pendaftaran Resmi</span>
                        <h2 class="text-base sm:text-lg font-bold tracking-tight">EduTech Festival BTIKP</h2>
                        <p class="text-xs text-slate-300">Dinas Pendidikan dan Kebudayaan Sultra</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Tahun</span>
                    <span class="text-base sm:text-lg font-extrabold text-white">{{ date('Y') }}</span>
                </div>
            </div>

            <!-- Perforated Ticket Notch Visual -->
            <div class="relative flex items-center justify-between px-4 -my-3">
                <div class="w-6 h-6 rounded-full bg-slate-50 border-r border-slate-200"></div>
                <div class="flex-1 border-t-2 border-dashed border-slate-200 mx-2"></div>
                <div class="w-6 h-6 rounded-full bg-slate-50 border-l border-slate-200"></div>
            </div>

            <!-- Ticket Body -->
            <div class="p-6 sm:p-8 space-y-6">
                <!-- Registration Number Badge -->
                <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs text-blue-800 font-semibold uppercase tracking-wider block">Nomor Registrasi Unik</span>
                        <span class="text-xl sm:text-2xl font-black text-blue-900 tracking-wider font-mono">{{ $registration->registration_number }}</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 self-start sm:self-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Terkonfirmasi
                    </div>
                </div>

                <!-- Participant Profile Detail -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    <img 
                        src="{{ $registration->profile_photo_url }}" 
                        alt="{{ $registration->full_name }}" 
                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($registration->full_name) }}&background=1E40AF&color=FFFFFF&size=200';"
                        class="w-24 h-24 rounded-2xl object-cover shadow-md border-2 border-slate-100 shrink-0 bg-slate-100"
                    >
                    <div class="flex-1 text-center sm:text-left space-y-1">
                        <h3 class="text-lg font-bold text-slate-900">{{ $registration->full_name }}</h3>
                        <p class="text-sm font-medium text-slate-600">{{ $registration->school_name }}</p>
                        <p class="text-xs text-slate-500 flex items-center justify-center sm:justify-start gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $registration->regency_city }}
                        </p>
                    </div>
                </div>

                <!-- Submission Summary Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100 text-sm">
                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider block font-semibold">Email</span>
                        <span class="font-medium text-slate-800 break-all">{{ $registration->email }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider block font-semibold">Nomor WhatsApp</span>
                        <span class="font-medium text-slate-800">{{ $registration->whatsapp_number }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider block font-semibold">Pilihan Pembelajaran</span>
                        <span class="inline-flex items-center gap-1.5 font-bold text-blue-700 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            {{ $registration->category_label }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-xs text-slate-400 uppercase tracking-wider block font-semibold">Tautan Media Karya</span>
                        <a href="{{ $registration->media_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-800 underline mt-0.5 text-xs truncate max-w-full">
                            <span>Buka Karya</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 text-center pt-2 border-t border-slate-100">
                    Terdaftar pada: {{ $registration->created_at->translatedFormat('l, d F Y — H:i') }} WITA
                </div>
            </div>
        </div>

        <!-- Action Buttons (Commented out per user request) -->
        {{--
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 print:hidden">
            <button 
                type="button" 
                onclick="window.print()"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm shadow-md transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Simpan Tiket (PDF)</span>
            </button>

            <a 
                href="{{ route('registration.create') }}" 
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm border border-slate-200 shadow-xs transition"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Daftarkan Karya Lain</span>
            </a>
        </div>
        --}}

    </div>
</x-layouts.app>
