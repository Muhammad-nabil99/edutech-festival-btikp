# EduTech Festival BTIKP — Website Pendaftaran Karya Digital

Aplikasi formulir pendaftaran karya inovasi digital (**Gim Edukasi** & **Media Pembelajaran Interaktif**) untuk kegiatan **EduTech Festival** yang diselenggarakan oleh **Balai Teknologi Informasi dan Komunikasi Pendidikan (BTIKP) Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara**.

Aplikasi ini dirancang sebagai alternatif Google Forms yang jauh lebih menarik, elegan, dan terstruktur (*Elevated Form Experience*), dilengkapi dengan sistem verifikasi data, pelacak progres *real-time*, live avatar preview, serta panel admin dan ekspor data ke Excel/CSV.

---

## 🚀 Fitur Utama

### 1. Halaman Formulir Pendaftaran Publik (`/`)
- **Desain Modern & Terstruktur**: Tampilan bertema *EdTech* dengan palet warna biru-indigo-cyan yang bersih dan profesional.
- **Indikator Progres Real-Time**: Bar persentase kelengkapan data (0% hingga 100%) yang otomatis bertambah saat field diisi.
- **8 Field Isian Wajib**:
  1. **Nama Lengkap** (beserta gelar jika ada)
  2. **Asal Sekolah**
  3. **Kabupaten/Kota** (Dropdown 17 Kab/Kota se-Sulawesi Tenggara)
  4. **Email Aktif** (dengan proteksi duplikasi)
  5. **Nomor WhatsApp Aktif** (auto-normalisasi format Indonesia `628...`)
  6. **Pilihan Pembelajaran** (*Interactive Radio Card*: **Gim Edukasi** vs **Media Pembelajaran Interaktif**)
  7. **Foto Profil** (*Live circular avatar preview* + validasi format & ukuran maksimal 2 MB)
  8. **Link Media Karya** (Google Drive, YouTube, Canva, itch.io, dll.)
- **Proteksi Anti-Double Submit**: Tombol kirim dilengkapi animasi *spinner* dan dinonaktifkan otomatis saat proses pengiriman.

### 2. Tiket Bukti Pendaftaran Resmi (`/register/success/{nomor_registrasi}`)
- **Nomor Registrasi Unik**: Format otomatis `EDUTECH-YYYY-XXXX` (contoh: `EDUTECH-2026-0001`).
- **Kartu Tiket Digital**: Desain tiket resmi berlubang (*perforated ticket visual*) yang siap dicetak langsung (`Cetak / Simpan Tiket PDF`).
- **Ringkasan Data**: Foto profil, identitas lengkap, dan tautan karya yang langsung dapat diuji.

### 3. Portal & Dashboard Panitia BTIKP (`/admin/dashboard`)
- **Autentikasi Aman**: Login khusus panitia di `/admin/login`.
  - Email default: `admin@btikp.sultra.go.id`
  - Password default: `adminbtikp2026!`
- **Ringkasan Statistik Interaktif**:
  - Total peserta terdaftar
  - Total karya kategori Gim Edukasi
  - Total karya kategori Media Pembelajaran Interaktif
  - Jumlah sebaran Kabupaten/Kota terwakili
- **Filter & Pencarian Lanjutan**: Cari berdasarkan nama, sekolah, email, no. registrasi, serta filter instan per kategori & per wilayah.
- **Tabel Pendaftar & Tindakan Cepat**:
  - Lightbox pratinjau foto profil resolusi tinggi
  - Tombol pintas *Click-to-Chat* WhatsApp
  - Tombol langsung membuka Link Media karya
  - Tombol hapus data yang tidak valid
- **Ekspor Data ke CSV/Excel**: Mengunduh seluruh rekapitulasi data pendaftar dalam format CSV ber-BOM UTF-8 (kompatibel langsung dengan Microsoft Excel & Google Sheets).

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Styling**: Tailwind CSS
- **Interactivity**: Alpine.js
- **Icons**: Heroicons (SVG)

---

## 🐳 Panduan Deployment dengan Docker (Siap Production)

Aplikasi telah dilengkapi konfigurasi Docker production-ready (Multi-stage build PHP 8.3 FPM, Nginx, dan MySQL 8.4) beserta otomasi migrasi, seeder, dan link storage.

### 1. Salin File Environment Docker
```bash
cp .env.docker.example .env
```
*(Sesuaikan `APP_URL`, `APP_PORT`, atau password database di `.env` sesuai server Anda).*

### 2. Build & Jalankan Container
```bash
docker compose up -d --build
```

Container yang akan berjalan:
- **`edutech_web`** (Nginx): Melayani web di port `8085` (atau port pilihan di `APP_PORT`).
- **`edutech_app`** (PHP 8.3 FPM): Memproses logika Laravel, otomatis migrasi database, storage link, dan caching optimasi.
- **`edutech_db`** (MySQL 8.4): Menyimpan basis data pendaftaran dengan volume persisten `edutech_mysql_data`.

### 3. Akses Website
- **Formulir Pendaftaran**: `http://localhost:8085` (atau IP server Anda)
- **Portal Panitia**: `http://localhost:8085/admin/login`
  - Email: `admin@btikp.sultra.go.id`
  - Password: `adminbtikp2026!`

### 4. Perintah Berguna Saat Deployment
```bash
# Cek log aplikasi secara real-time
docker compose logs -f app

# Masuk ke terminal container app
docker compose exec app sh

# Restart container
docker compose restart

# Hentikan container
docker compose down
```

---

## 💻 Panduan Menjalankan Manual (Tanpa Docker)

### 1. Clone Repositori
```bash
git clone https://github.com/Muhammad-nabil99/edutech-festival-btikp.git
cd edutech-festival-registration
```

### 2. Instalasi Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` ke `.env` dan sesuaikan kredensial MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edutech_festival_btikp
DB_USERNAME=root
DB_PASSWORD=password
```

### 4. Generate App Key & Jalankan Migrasi Database
```bash
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
```

> **Catatan Seeder:**
> Perintah di atas akan membuat akun administrator panitia (`admin@btikp.sultra.go.id` / `adminbtikp2026!`) dan beberapa contoh data pendaftar.

### 5. Kompilasi Aset Frontend
```bash
npm run build
```
*(Atau gunakan `npm run dev` untuk pengembangan aktif)*

### 6. Menjalankan Server Aplikasi
```bash
php artisan serve
```
Buka peramban (browser) di:
- **Formulir Pendaftaran**: [http://localhost:8000](http://localhost:8000)
- **Portal Panitia BTIKP**: [http://localhost:8000/admin/login](http://localhost:8000/admin/login)

---

## 🧪 Menjalankan Pengujian (Automated Tests)

```bash
php artisan test
```

---

&copy; {{ date('Y') }} Balai Teknologi Informasi dan Komunikasi Pendidikan (BTIKP) — Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara.
