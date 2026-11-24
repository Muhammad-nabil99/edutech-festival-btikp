# PERAN
Kamu adalah senior full-stack developer. Kamu akan memodifikasi modul "Edutech" pada aplikasi yang sudah berjalan.

# KONTEKS PROYEK
- Stack: [mis. Next.js / Laravel / React + Firebase / dll.]
- Database: [mis. MySQL / Firestore / Supabase]
- Role yang ada: Admin, Juri, Peserta
- Kategori lomba: GIM dan MPI
- Akun juri yang sudah ada: Juri 1, Juri 2, Juri 3
- Kode saat ini: [lampirkan file/komponen terkait: halaman brekap nilai juri, halaman rekap Top 10, halaman admin, halaman saran/formulir]

# TUJUAN
Tambahkan 5 perubahan pada modul Edutech tanpa merusak fitur yang sudah ada.

# DETAIL KEBUTUHAN

## 1. Filter kategori pada Rekap Nilai Juri
- Tambahkan tab filter: [Semua] | [GIM] | [MPI].
- Tab aktif menampilkan hanya data penilaian sesuai kategori yang dipilih.
- Default tab: [Semua / GIM].

## 2. Perbaikan gambar pada "Rekapitulasi Penilaian Dewan Juri – Nominasi Top 10"
- Masalah: gambar tidak muncul.
- Selidiki penyebabnya (path/URL salah, field kosong, CORS, storage rules, lazy load, dll.) dan jelaskan temuannya.
- Perbaiki, lalu tambahkan fallback (placeholder) jika gambar gagal dimuat.

## 3. Manajemen akun juri oleh Admin
- Admin dapat membuat akun juri dengan field: nama, email/username, password, dan kategori (dropdown: GIM / MPI).
- Juri hanya melihat dan menilai data sesuai kategorinya. Juri kategori MPI hanya melihat MPI, dan juri kategori GIM hanya melihat GIM.
- Admin dapat menghapus akun juri, dengan dialog konfirmasi sebelum hapus.
- Validasi: email unik dan field wajib terisi.
- Akses fitur ini hanya untuk role Admin, dan pembatasan kategori juga diterapkan di sisi backend/query, tidak hanya di UI.

## 4. Status formulir tertutup
- Jika formulir sudah ditutup, tombol "Ubah / Tarik Saran" berstatus disabled (tidak bisa diklik).
- Tampilkan keterangan singkat, mis. "Formulir telah ditutup".
- Validasi juga dilakukan di backend agar tidak bisa ditembus lewat request langsung.

## 5. Migrasi akun juri yang sudah ada
- Jangan mengubah data akun Juri 1, Juri 2, dan Juri 3 selain menambahkan kategori:
  - Juri 1 → kategori GIM
  - Juri 2 → kategori GIM (default)
  - Juri 3 → kategori MPI
- Sediakan script/migrasi yang aman (idempotent) untuk mengisi kategori tersebut.
- Akun juri baru yang dibuat Admin bisa memilih kategori sendiri dan bisa dihapus. Akun Juri 1–3 [boleh / tidak boleh] dihapus: [tentukan].

# BATASAN
- Jangan mengubah fitur lain di luar 5 poin di atas.
- Pertahankan gaya kode, struktur folder, dan UI yang sudah ada.
- Jangan mengubah kredensial, password, atau ID akun Juri 1–3.

# KRITERIA PENERIMAAN
- [ ] Tab GIM/MPI memfilter rekap dengan benar.
- [ ] Semua gambar Top 10 tampil, atau muncul placeholder jika gagal.
- [ ] Admin dapat membuat dan menghapus akun juri, dan kategori membatasi data yang terlihat.
- [ ] Tombol "Ubah / Tarik Saran" disabled saat formulir tertutup.
- [ ] Juri 1 dan 2 berkategori GIM, Juri 3 berkategori MPI, dan data lain tidak berubah.

# FORMAT OUTPUT
1. Ringkasan analisis singkat (penyebab bug gambar, file yang akan diubah).
2. Kode lengkap per file yang diubah, diberi judul nama file.
3. Script migrasi untuk poin 5.
4. Langkah pengujian manual untuk tiap poin.
5. Asumsi yang kamu buat, jika ada.