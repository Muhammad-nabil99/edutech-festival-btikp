# Feature Request: Top 10 Selection, Registration Schedule, and Multi-Judge Scoring

## Context
This is an admin/committee (panitia) system for the EduTech Festival digital works competition. Works are submitted through the "Formulir Pendaftaran Karya Digital EduTech Festival" page. Please implement the three features below.

---

## 1. Admin: Select Top 10 Works per Category

### Categories and quota
- Two categories: **Gim Edukasi** and **Media Interaktif**
- Each category has **10 slots** (maximum **20 selected works** in total)

### Behavior
- Add a **toggle button** on each item in the works list.
  - Click once: the item is moved into its selected category (selected).
  - Click again: the item is moved back to the original list (unselected).
- Show a **badge/mark** on items that are already selected as Top 10 best works.
- Enforce the quota: once a category reaches 10 items, disable the select button for other items in that category and show a notice that the category is full.
- Only **admin/panitia** can select or unselect works.

---

## 2. Registration Open/Close Settings

### Admin settings
- A setting to open/close registration, with two modes:
  - **Manual**: on/off switch
  - **Scheduled**: start and end date and time (start – end)

### When registration is closed
- Users cannot access the registration page. The form content is hidden.
- Show one of these messages:
  - Closed: `Pendaftaran ditutup`
  - Not yet open: `Pendaftaran dibuka pada tanggal xx - xx`

### Countdown
- Show a **countdown timer** in the header of the "Formulir Pendaftaran Karya Digital EduTech Festival" registration page (front-facing).

---

## 3. Multi-User Judge Accounts

### Accounts
- Create **3 judge accounts**, all with the role `judge`.
- Each has a unique username and a **randomly generated, secure** password.
- Passwords must be hashed. Do not store them in plain text.

### Login and profile settings
- Login currently uses **email only** (no username field).
- Add a settings page where each judge can change their own **username, email, and password**.
- Admin can also change any judge's username, email, and password.

### Access permissions
- Judges can only **view the list of selected works** (the Top 10 per category).
- Judges **cannot** edit works or move them between categories. Only admin can.

---

## 4. Judge Scoring Cards

Create a scoring card for each work (Gim Edukasi and Media Interaktif), where each judge enters scores per aspect. Each judge's scores are stored separately.

### Rubric (Bab IV: Kriteria dan Rubrik Penilaian)

| Aspect | Weight | Indicators |
|---|---|---|
| **Aspek Pedagogis & Subjek Materi** | 30% | • Kebenaran dan keakuratan substansi materi pelajaran SMA/SMK<br>• Kesesuaian dengan Capaian Pembelajaran Kurikulum<br>• Kejelasan alur penyampaian konsep dan efektivitas umpan balik (feedback) belajar |
| **Aspek Kreativitas & Desain Visual** | 30% | • Keaslian/orisinalitas ide dan keunikan konsep media/gim<br>• Kualitas tata letak (layout), pilihan warna, tipografi, dan estetika visual<br>• Desain karakter/elemen permainan (khusus Gim) atau kerapian UI/UX (khusus MPI) |
| **Aspek Kualitas Teknis & Interaktivitas** | 25% | • Kemudahan navigasi, responsivitas kontrol, dan kebebasan dari kesalahan teknis (bug/crash)<br>• Kecepatan pemuatan (loading time) dan kemudahan pengoperasian<br>• Kedalaman tingkat interaktivitas antara pengguna dengan media/gim |
| **Aspek Presentasi & Dampak Penerapan** | 15% | • Penguasaan materi dan kemampuan mempertahankan argumen di depan Dewan Juri<br>• Kejujuran demonstrasi produk secara langsung<br>• Potensi dan kepraktisan implementasi karya di dalam kelas secara berkelanjutan |

### Scoring behavior
- Each judge scores every aspect, and the weighted total is calculated automatically (weights sum to 100%).
- Show the indicators as guidance on each aspect's input.

---

## Deliverables
- UI changes, backend logic, and database changes/migrations needed for all features above
- Short notes on the generated judge credentials and how to change them