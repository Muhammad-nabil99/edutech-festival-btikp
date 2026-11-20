# Akun Dewan Juri (Judge Credentials) — EduTech Festival BTIKP

Dokumen ini berisi kredensial akun dewan juri untuk sistem penilaian karya EduTech Festival BTIKP Dinas Pendidikan dan Kebudayaan Sulawesi Tenggara.

---

## 1. Daftar Akun Dewan Juri

Setiap akun memiliki role `judge` dengan kata sandi terenkripsi (hashed menggunakan Bcrypt). Dewan juri dapat masuk menggunakan **Email** maupun **Username**.

| No | Nama Juri | Username | Email | Kata Sandi (Password) | Role |
|---|---|---|---|---|---|
| 1 | **Dewan Juri 1 (Gim & Multimedia)** | `juri1` | `juri1@edutech.btikp.id` | `Juri1#Edutech92kQ` | `judge` |
| 2 | **Dewan Juri 2 (Pedagogik & Kurikulum)** | `juri2` | `juri2@edutech.btikp.id` | `Juri2#Btikp74vLm` | `judge` |
| 3 | **Dewan Juri 3 (Desain Visual & IT)** | `juri3` | `juri3@edutech.btikp.id` | `Juri3#Sultra85xPz` | `judge` |

> **Akun Administrator Utama (Panitia):**
> - **Username**: `admin`
> - **Email**: `admin@btikp.sultra.go.id`
> - **Kata Sandi**: `adminbtikp2026!`
> - **Role**: `admin`

---

## 2. Cara Masuk (Login)
1. Buka halaman login di `/admin/login` atau `/login`.
2. Masukkan **Username** (contoh: `juri1`) atau **Email** (contoh: `juri1@edutech.btikp.id`) pada kolom isian.
3. Masukkan **Kata Sandi** yang tertera di atas.
4. Klik tombol **Masuk ke Dashboard**.
5. Akun dengan role `judge` akan otomatis diarahkan ke **Dashboard Penilaian Dewan Juri** (`/judge/dashboard`).

---

## 3. Hak Akses Dewan Juri
- **Hanya melihat karya Top 10** yang telah dikurasi oleh Panitia/Admin (Maksimal 10 Gim Edukasi dan 10 Media Interaktif).
- **Membuka link karya digital** untuk menguji coba media/gim peserta.
- **Menginput dan memperbarui nilai** secara mandiri menggunakan lembar penilaian (Scoring Card) berdasarkan 4 aspek rubrik:
  1. Aspek Pedagogis & Subjek Materi (Bobot 30%)
  2. Aspek Kreativitas & Desain Visual (Bobot 30%)
  3. Aspek Kualitas Teknis & Interaktivitas (Bobot 25%)
  4. Aspek Presentasi & Dampak Penerapan (Bobot 15%)
- **Tidak dapat** mengubah kategori, menghapus berkas, atau memindahkan karya pendaftar.

---

## 4. Cara Mengubah Username, Email, dan Kata Sandi

### A. Dilakukan Mandiri oleh Dewan Juri
1. Setelah login ke Dashboard Juri (`/judge/dashboard`), klik tombol **Pengaturan Akun** di bagian header profil atau klik ikon profil.
2. Di modal/halaman pengaturan, Juri dapat memperbarui:
   - **Nama Lengkap**
   - **Username**
   - **Email**
   - **Kata Sandi Baru** (opsional, kosongkan jika tidak ingin mengubah)
3. Klik **Simpan Perubahan**.

### B. Dilakukan oleh Administrator (Panitia)
1. Administrator dapat mengelola seluruh akun juri melalui **Dashboard Panitia** (`/admin/dashboard`) pada tab **Pengaturan & Dewan Juri**.
2. Klik tombol **Edit Akun Juri** pada juri yang ingin diubah.
3. Administrator dapat mereset username, email, maupun password juri sewaktu-waktu.
