<x-layouts.app title="Pendaftaran Peserta — EduTech Festival BTIKP">
    <div x-data="registrationForm()" class="space-y-8">

        <!-- Hero Header Card (Google Forms Style but Premium & Elevated) -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 text-white shadow-xl shadow-indigo-950/20 border border-indigo-700/30">
            <!-- Decorative Glow & Blobs -->
            <div class="absolute -top-24 -right-24 w-80 h-80 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-blue-600/30 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- Top Colorful Accent Bar -->
            <div class="h-2.5 w-full bg-gradient-to-r from-blue-500 via-indigo-400 to-cyan-400"></div>

            <div class="p-6 sm:p-10 relative z-10">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white p-2.5 shadow-lg border border-white/80 flex items-center justify-center shrink-0">
                        <img src="{{ asset('logoV2.png') }}" alt="Logo EduTech BTIKP" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-cyan-300 border border-cyan-400/30 backdrop-blur-md">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            Pendaftaran Dibuka &bull; BTIKP Sultra
                        </div>
                        <p class="text-xs text-slate-300 font-medium mt-1">Balai Teknologi Informasi dan Komunikasi Pendidikan</p>
                    </div>
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white mb-3">
                    Formulir Pendaftaran Karya Digital EduTech Festival
                </h1>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mb-6">
                    Ajang apresiasi dan seleksi karya inovasi pembelajaran digital (Gim Edukasi &amp; Media Interaktif) bagi pendidik dan talenta teknologi pendidikan di lingkungan Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara.
                </p>

                <!-- Information Chips -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 border-t border-white/10 text-xs sm:text-sm text-slate-200">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-white/10 text-cyan-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <span>Terbuka 17 Kab/Kota</span>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-white/10 text-cyan-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span>2 Kategori Pilihan</span>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-white/10 text-cyan-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <span>Sertifikat &amp; Tiket Resmi</span>
                    </div>
                </div>
            </div>

            <!-- Sticky Live Progress Tracker Header -->
            <div class="bg-indigo-950/80 backdrop-blur-md px-6 sm:px-10 py-3 border-t border-white/10 flex items-center justify-between text-xs sm:text-sm">
                <span class="text-slate-300 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Kelengkapan Isian Formulir
                </span>
                <span class="font-bold text-cyan-300" x-text="progressPercent() + '% Lengkap (' + completedCount() + '/8 Field)'"></span>
            </div>
            <!-- Progress Line Bar -->
            <div class="h-1.5 w-full bg-white/10">
                <div class="h-full bg-gradient-to-r from-cyan-400 to-emerald-400 transition-all duration-300" :style="'width: ' + progressPercent() + '%'"></div>
            </div>
        </div>

        <!-- Banner Peringatan jika Validasi Server Gagal -->
        @if ($errors->any())
            <div class="p-5 rounded-3xl bg-rose-50 border-2 border-rose-200 text-rose-900 shadow-sm flex items-start gap-4 animate-shake">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1 text-xs sm:text-sm">
                    <h3 class="font-extrabold text-rose-950 text-sm sm:text-base mb-1">
                        Pendaftaran Belum Lengkap ({{ $errors->count() }} Isian Perlu Diperbaiki)
                    </h3>
                    <p class="text-rose-700 leading-relaxed mb-2.5">
                        Mohon periksa kolom yang bertanda merah di bawah ini sebelum mengirim ulang formulir.
                    </p>
                    <button 
                        type="button" 
                        @click="showErrorModal = true" 
                        class="inline-flex items-center gap-1.5 font-bold text-xs bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-lg shadow-xs transition cursor-pointer"
                    >
                        <span>Lihat Rincian Kesalahan Isian</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Pop-Up Modal Peringatan Gagal Pengisian Form -->
        <div 
            x-show="showErrorModal" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            @keydown.escape.window="showErrorModal = false"
        >
            <div 
                class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-rose-100 overflow-hidden transform transition-all"
                @click.away="showErrorModal = false"
            >
                <!-- Top Colorful Accent Bar -->
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-rose-500 via-amber-500 to-rose-600"></div>

                <!-- Icon & Header -->
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-inner">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                            Peringatan Pengisian Formulir!
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Mohon periksa kembali isian formulir Anda. Terdapat data yang belum lengkap atau belum sesuai:
                        </p>
                    </div>
                </div>

                <!-- List of Errors -->
                <div class="my-4 max-h-60 overflow-y-auto space-y-2 p-4 bg-rose-50/80 border border-rose-200/80 rounded-2xl text-xs sm:text-sm text-rose-900">
                    <template x-if="serverErrors.length > 0">
                        <ul class="space-y-1.5 list-disc list-inside">
                            <template x-for="(err, index) in serverErrors" :key="index">
                                <li class="font-semibold" x-text="err"></li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="clientErrorMessage">
                        <div class="flex items-start gap-2 text-rose-800 font-semibold">
                            <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="clientErrorMessage"></span>
                        </div>
                    </template>
                </div>

                <!-- Action Button -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="closeAndFocusError()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-rose-600 via-rose-700 to-rose-800 hover:from-rose-700 hover:to-rose-900 text-white font-bold text-xs sm:text-sm shadow-md shadow-rose-600/30 hover:shadow-lg transition cursor-pointer"
                    >
                        <span>Periksa &amp; Perbaiki Isian</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Submission -->
        <form method="POST" action="{{ route('registration.store', [], false) }}" enctype="multipart/form-data" @submit="validateBeforeSubmit($event)" class="space-y-6">
            @csrf

            <!-- ============================================== -->
            <!-- SECTION 1: Identitas & Kontak Peserta -->
            <!-- ============================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm transition hover:shadow-md">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                        1
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Identitas &amp; Kontak Peserta</h2>
                        <p class="text-xs text-slate-500">Lengkapi data pribadi dan informasi kontak aktif Anda</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 1. Nama Lengkap -->
                    <div class="md:col-span-2">
                        <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Nama Lengkap (beserta gelar jika ada) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="full_name" 
                                name="full_name" 
                                x-model="formData.full_name"
                                value="{{ old('full_name') }}"
                                placeholder="Contoh: Ahmad, S.T., S.S."
                                class="w-full px-4 py-3 rounded-xl border {{ $errors->has('full_name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                                required
                            >
                        </div>
                        @error('full_name')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 4. Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Alamat Email Aktif <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                x-model="formData.email"
                                value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                                required
                            >
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Digunakan untuk verifikasi dan konfirmasi pendaftaran.</p>
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 5. Nomor WA -->
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Nomor WhatsApp (Aktif) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="tel" 
                                id="whatsapp_number" 
                                name="whatsapp_number" 
                                x-model="formData.whatsapp_number"
                                value="{{ old('whatsapp_number') }}"
                                placeholder="Contoh: 081234567890"
                                class="w-full px-4 py-3 rounded-xl border {{ $errors->has('whatsapp_number') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                                required
                            >
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Panitia akan menghubungi nomor ini untuk koordinasi teknis.</p>
                        @error('whatsapp_number')
                            <p class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 7. Foto Profil (Drag and drop with live preview) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Foto Profil Resmi <span class="text-rose-500">*</span>
                        </label>

                        <div class="flex flex-col sm:flex-row items-center gap-6 p-5 rounded-2xl border-2 border-dashed {{ $errors->has('profile_photo') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 hover:border-blue-400 bg-slate-50/60' }} transition">
                            <!-- Circular Preview -->
                            <div class="relative shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="Preview Foto" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover shadow-md border-2 border-white ring-4 ring-blue-100">
                                </template>
                                <template x-if="!photoPreview">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-slate-200 border-2 border-white ring-4 ring-slate-100 flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-[10px] font-medium mt-1">Pratinjau</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Upload Button & Hints -->
                            <div class="flex-1 text-center sm:text-left">
                                <input type="hidden" name="temp_photo" :value="tempPhoto">
                                <label for="profile_photo" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:border-blue-500 text-sm font-semibold text-slate-700 hover:text-blue-600 shadow-xs cursor-pointer transition">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span x-text="tempPhoto ? 'Ganti File Foto' : 'Pilih File Foto'"></span>
                                </label>
                                <input 
                                    type="file" 
                                    id="profile_photo" 
                                    name="profile_photo" 
                                    accept="image/png,image/jpeg,image/webp"
                                    @change="updatePhotoPreview"
                                    class="hidden"
                                    :required="!tempPhoto"
                                >
                                <p class="text-xs text-slate-500 mt-2">
                                    Format: JPG, PNG, atau WEBP. Maksimal ukuran file: <strong class="text-slate-700">2 MB</strong>. Gunakan foto formal/semi-formal yang jelas.
                                </p>
                                <template x-if="tempPhoto">
                                    <div class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span x-text="fileName ? 'Foto tersimpan: ' + fileName : 'Foto berhasil diunggah & tersimpan'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        @error('profile_photo')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- SECTION 2: Asal Instansi & Wilayah -->
            <!-- ============================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm transition hover:shadow-md">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                        2
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Asal Instansi &amp; Wilayah</h2>
                        <p class="text-xs text-slate-500">Nama sekolah / satuan pendidikan tempat Anda bertugas / belajar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 2. Asal Sekolah -->
                    <div>
                        <label for="school_name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Asal Sekolah / Satuan Pendidikan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="school_name" 
                                name="school_name" 
                                x-model="formData.school_name"
                                @blur="if (formData.school_name) formData.school_name = formData.school_name.toUpperCase()"
                                value="{{ old('school_name') }}"
                                placeholder="CONTOH: SMAN 1 KENDARI / SMPN 2 BAUBAU"
                                class="w-full px-4 py-3 rounded-xl uppercase border {{ $errors->has('school_name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                                required
                            >
                        </div>
                        @error('school_name')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 3. Kabupaten / Kota -->
                    <div>
                        <label for="regency_city" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Kabupaten / Kota (Sulawesi Tenggara) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="regency_city" 
                                name="regency_city" 
                                x-model="formData.regency_city"
                                class="w-full px-4 py-3 rounded-xl border {{ $errors->has('regency_city') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm bg-white transition cursor-pointer"
                                required
                            >
                                <option value="" disabled {{ old('regency_city') ? '' : 'selected' }}>-- Pilih Kabupaten / Kota --</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}" {{ old('regency_city') === $region ? 'selected' : '' }}>
                                        {{ $region }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('regency_city')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- SECTION 3: Kategori Pembelajaran & Link Karya -->
            <!-- ============================================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm transition hover:shadow-md">
                <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold text-sm">
                        3
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Kategori &amp; Tautan Karya Digital</h2>
                        <p class="text-xs text-slate-500">Pilih kategori karya inovatif Anda dan sertakan tautan media</p>
                    </div>
                </div>

                <!-- 6. Pilihan Pembelajaran (Interactive Cards) -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">
                        Pilihan Pembelajaran <span class="text-rose-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Option 1: Gim Edukasi -->
                        <label 
                            class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                            :class="formData.learning_category === 'gim_edukasi' 
                                ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-600/20' 
                                : 'border-slate-200 hover:border-slate-300 bg-white'"
                        >
                            <input 
                                type="radio" 
                                name="learning_category" 
                                value="gim_edukasi" 
                                x-model="formData.learning_category"
                                class="sr-only"
                                required
                            >
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div 
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                    :class="formData.learning_category === 'gim_edukasi' ? 'border-blue-600 bg-blue-600' : 'border-slate-300'"
                                >
                                    <div class="w-2 h-2 rounded-full bg-white" x-show="formData.learning_category === 'gim_edukasi'"></div>
                                </div>
                            </div>
                            <span class="text-base font-bold text-slate-900 mb-1">Gim Edukasi</span>
                            <span class="text-xs text-slate-500 leading-relaxed">
                                Karya permainan interaktif berbasis komputer/web/mobile yang dirancang khusus untuk mempermudah pemahaman materi belajar.
                            </span>
                        </label>

                        <!-- Option 2: Media Pembelajaran Interaktif -->
                        <label 
                            class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                            :class="formData.learning_category === 'media_pembelajaran_interaktif' 
                                ? 'border-blue-600 bg-blue-50/50 shadow-md ring-2 ring-blue-600/20' 
                                : 'border-slate-200 hover:border-slate-300 bg-white'"
                        >
                            <input 
                                type="radio" 
                                name="learning_category" 
                                value="media_pembelajaran_interaktif" 
                                x-model="formData.learning_category"
                                class="sr-only"
                                required
                            >
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-500 text-white flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div 
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                    :class="formData.learning_category === 'media_pembelajaran_interaktif' ? 'border-blue-600 bg-blue-600' : 'border-slate-300'"
                                >
                                    <div class="w-2 h-2 rounded-full bg-white" x-show="formData.learning_category === 'media_pembelajaran_interaktif'"></div>
                                </div>
                            </div>
                            <span class="text-base font-bold text-slate-900 mb-1">Media Pembelajaran Interaktif</span>
                            <span class="text-xs text-slate-500 leading-relaxed">
                                Modul digital, video interaktif, aplikasi simulasi, atau presentasi berbasis multimedia untuk kegiatan belajar mengajar.
                            </span>
                        </label>
                    </div>

                    @error('learning_category')
                        <p class="mt-2 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 8. Link Media -->
                <div>
                    <label for="media_link" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tautan / Link Media Karya Digital <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <input 
                            type="url" 
                            id="media_link" 
                            name="media_link" 
                            x-model="formData.media_link"
                            value="{{ old('media_link') }}"
                            placeholder="https://drive.google.com/... atau https://canva.com/..."
                            class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('media_link') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300 focus:border-blue-500' }} focus:ring-4 focus:ring-blue-100 outline-none text-slate-800 text-sm transition"
                            required
                        >
                    </div>
                    <div class="mt-2.5 p-3 rounded-xl bg-blue-50/70 border border-blue-200/60 text-xs text-blue-900 flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>
                            <strong>Petunjuk:</strong> Pastikan tautan (Google Drive, YouTube, Canva, itch.io, atau Web App Anda) telah diatur ke akses <strong>Publik / Siapa saja yang memiliki tautan</strong> agar dewan juri panitia dapat membuka dan menguji karya.
                        </span>
                    </div>
                    @error('media_link')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 flex items-center gap-2 text-center sm:text-left">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Data Anda dilindungi dan hanya digunakan untuk keperluan seleksi EduTech Festival BTIKP.</span>
                </div>

                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-700 via-indigo-600 to-blue-800 text-white font-bold text-sm shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-75 disabled:pointer-events-none transition cursor-pointer"
                >
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'Mengirim Pendaftaran...' : 'Kirim Pendaftaran Sekarang'"></span>
                    <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>

    </div>

    <!-- Alpine.js State Management -->
    <script>
        function registrationForm() {
            return {
                isSubmitting: false,
                showErrorModal: {{ $errors->any() ? 'true' : 'false' }},
                serverErrors: @json($errors->all()),
                clientErrorMessage: '',
                photoPreview: '{{ old('temp_photo') ? asset('storage/' . old('temp_photo')) : '' }}',
                tempPhoto: '{{ old('temp_photo', '') }}',
                fileName: '{{ old('temp_photo') ? 'Foto tersimpan sebelumnya' : '' }}',
                formData: {
                    full_name: '{{ old('full_name', '') }}',
                    school_name: '{{ old('school_name', '') }}',
                    regency_city: '{{ old('regency_city', '') }}',
                    email: '{{ old('email', '') }}',
                    whatsapp_number: '{{ old('whatsapp_number', '') }}',
                    learning_category: '{{ old('learning_category', '') }}',
                    media_link: '{{ old('media_link', '') }}',
                    hasPhoto: {{ old('temp_photo') ? 'true' : 'false' }}
                },
                closeAndFocusError() {
                    this.showErrorModal = false;
                    this.$nextTick(() => {
                        const firstErrorInput = document.querySelector('.border-rose-400') || document.querySelector(':invalid');
                        if (firstErrorInput) {
                            firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstErrorInput.focus();
                        }
                    });
                },
                validateBeforeSubmit(event) {
                    if (this.completedCount() < 8) {
                        event.preventDefault();
                        this.clientErrorMessage = 'Mohon lengkapi seluruh isian wajib (8 dari 8 isian) sebelum mengirim pendaftaran.';
                        this.showErrorModal = true;
                        this.isSubmitting = false;
                        return false;
                    }
                    this.isSubmitting = true;
                },
                updatePhotoPreview(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.fileName = file.name;
                        this.formData.hasPhoto = true;

                        // 1. Tampilkan pratinjau instan di sisi browser
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.photoPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);

                        // 2. Upload sementara ke server agar tersimpan jika field lain gagal validasi
                        const uploadData = new FormData();
                        uploadData.append('profile_photo', file);
                        uploadData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route('registration.upload-temp') }}', {
                            method: 'POST',
                            body: uploadData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.path) {
                                this.tempPhoto = data.path;
                            }
                        })
                        .catch(err => {
                            console.warn('Upload foto sementara gagal, akan menggunakan pengiriman form biasa:', err);
                        });
                    }
                },
                completedCount() {
                    let count = 0;
                    if (this.formData.full_name && this.formData.full_name.trim().length >= 3) count++;
                    if (this.formData.school_name && this.formData.school_name.trim().length >= 3) count++;
                    if (this.formData.regency_city) count++;
                    if (this.formData.email && this.formData.email.includes('@')) count++;
                    if (this.formData.whatsapp_number && this.formData.whatsapp_number.length >= 8) count++;
                    if (this.formData.learning_category) count++;
                    if (this.formData.media_link && this.formData.media_link.startsWith('http')) count++;
                    if (this.formData.hasPhoto) count++;
                    return count;
                },
                progressPercent() {
                    return Math.round((this.completedCount() / 8) * 100);
                }
            };
        }
    </script>
</x-layouts.app>

