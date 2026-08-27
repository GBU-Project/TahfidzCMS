# Blueprint — TahfidzCMS (CodeIgniter 3)

Migrasi dari Google Apps Script + Spreadsheet ke CodeIgniter 3 + MySQL.
Acuan fitur diambil dari `index.html` (menu: Home, Input, Penilaian, Riwayat,
Progress, Leaderboard, Laporan, Users, Profile) dan `code_gs.txt` (logika poin,
badge, role-based access).

---

## 1. Aktor & Hak Akses

| Role  | Bisa akses                                                        |
|-------|--------------------------------------------------------------------|
| admin | Semua kelas, semua menu, kelola users & kelas                     |
| guru  | Hanya kelas yang diampu (relasi `guru_kelas`), input & nilai setoran |
| siswa | Hanya data dirinya sendiri (dashboard, progress, riwayat pribadi) |

---

## 2. Modul Aplikasi (dipetakan dari menu index.html)

| # | Modul       | Menu asal      | Deskripsi                                                       | Role akses     |
|---|-------------|----------------|------------------------------------------------------------------|----------------|
| 1 | Auth        | (login page)   | Login, logout, ganti password                                    | semua          |
| 2 | Dashboard   | page-home      | Statistik ringkas: total siswa/setoran, top siswa, chart          | semua (beda isi per role) |
| 3 | Setoran     | page-input     | Form tambah setoran hafalan + hitung poin otomatis                | admin, guru    |
| 4 | Penilaian   | page-penilaian | Review/edit nilai kelancaran & nilai tajwid setoran               | admin, guru    |
| 5 | Riwayat     | page-riwayat   | Daftar histori setoran, filter tanggal/kelas/siswa                | semua          |
| 6 | Progress    | page-progress  | Progress hafalan per siswa (juz tercapai vs target)               | semua          |
| 7 | Leaderboard | page-leaderboard | Ranking poin per kelas & global (dihitung on-the-fly)           | semua          |
| 8 | Laporan     | page-laporan   | Export laporan Excel per kelas/periode                            | admin, guru    |
| 9 | Users       | page-users     | CRUD users (admin), CRUD siswa, kelola relasi guru-kelas          | admin          |
| 10| Profile     | page-profile   | Lihat/edit profil, ganti password                                 | semua          |

---

## 3. Struktur Database (sudah dibuat — `tahfidzcms.sql`)

```
kelas ──┬──< guru_kelas >──┬── users (role=guru)
        │                  │
        └──< siswa >───────┘
              │  \
              │   └── user_id → users (role=siswa)
              │
              └──< setoran >── guru_pengoreksi_id → users (role=guru)
```

Leaderboard & sessions **tidak** jadi tabel fisik (lihat catatan di file SQL).

---

## 4. Struktur Folder CodeIgniter 3

```
application/
├── config/
│   ├── database.php
│   ├── routes.php
│   └── autoload.php          (load: database, session, form_validation)
├── controllers/
│   ├── Auth.php
│   ├── Dashboard.php
│   ├── Setoran.php
│   ├── Penilaian.php
│   ├── Riwayat.php
│   ├── Progress.php
│   ├── Leaderboard.php
│   ├── Laporan.php
│   ├── Users.php
│   └── Profile.php
├── models/
│   ├── User_model.php
│   ├── Siswa_model.php
│   ├── Kelas_model.php
│   ├── Setoran_model.php
│   └── Guru_kelas_model.php
├── libraries/
│   └── Poin_calculator.php   (hitung poin & badge — logika murni, testable)
├── core/
│   └── MY_Controller.php     (base controller: cek session + role guard)
├── helpers/
│   └── format_helper.php     (format tanggal Asia/Jakarta, dsb.)
└── views/
    ├── auth/login.php
    ├── dashboard/{admin,guru,siswa}.php
    ├── setoran/form.php          (termasuk widget rekam audio via MediaRecorder JS)
    ├── penilaian/index.php       (termasuk audio player utk review bukti setoran)
    ├── riwayat/index.php
    ├── progress/index.php
    ├── leaderboard/index.php
    ├── laporan/index.php
    ├── users/index.php
    ├── profile/index.php
    └── templates/{header,sidebar,footer}.php
```

Folder upload (di luar `application/`, sejajar `index.php` CI3):
```
uploads/
├── profile/         (foto profil users)
└── setoran_audio/   (rekaman audio bukti setoran)
```

**Pola akses role**: `MY_Controller` jadi induk semua controller (kecuali Auth),
otomatis redirect ke login kalau session kosong, dan expose `$this->role`,
`$this->kelas_diizinkan` (khusus guru) ke semua controller turunannya — supaya
logika "guru cuma boleh lihat kelasnya sendiri" (yang di Apps Script diulang di
banyak fungsi) cukup ditulis SEKALI di sini.

---

## 5. Urutan Pengembangan (Fase)

**Fase 0 — Fondasi** ✅ *(selesai)*
- [x] Skema database (`tahfidzcms.sql`) — perlu tambahan `foto` & `api_tokens` (lihat §7a)
- [x] Setup project CI3 kosong + config database/routes/autoload
- [x] `MY_Controller` (session guard + role guard, untuk web)
- [x] `MY_API_Controller` (Bearer token guard, untuk api)

**Fase 1 — Auth & Kerangka Tampilan** ✅ *(selesai)*
- [x] Model `User_model`
- [x] Controller `Auth` (login, logout, web/session) + hash password
- [x] Controller `api/Auth` (login -> issue token ke tabel `api_tokens`)
- [x] Template header/sidebar/footer (adaptasi dari `index.html` yang sudah ada, dipecah jadi partial view)
- [x] Form & handler upload foto profil (dipakai di Users & Profile)

**Fase 2 — Data Master** ✅ *(selesai)*
- [x] `Kelas_model`, `Siswa_model`, `Guru_kelas_model`
- [x] Controller & view `Users` (CRUD admin: kelola users, siswa, guru-kelas)
- [x] Controller & view `Kelas` (CRUD data master kelas)
- [x] `Upload_handler` library (upload foto profil, tervalidasi tipe & ukuran)

**Fase 3 — Transaksi Inti** ✅ *(selesai)*
- [x] `Poin_calculator` library (rumus poin + badge, port dari `calculateBadge()` & logika di `addSetoran()`)
- [x] `Setoran_model` + Controller `Setoran` (form input + **rekam/upload audio bukti**, pakai DB transaction utk update total_poin)
- [x] Frontend rekaman: pakai `MediaRecorder` API di browser (guru rekam langsung saat siswa setor) ATAU upload file audio yang sudah ada
- [x] Controller `Penilaian` (edit/koreksi nilai setoran, termasuk memutar ulang audio bukti saat menilai)
- [x] Controller API `api/Setoran` (GET list & POST simpan setoran + upload audio)

**Fase 4 — Laporan & Insight** ✅ *(web selesai, API menyusul di sesi ini)*
- [x] Controller `Dashboard` (statistik ringkas per role)
- [x] Controller `Riwayat` (list + filter)
- [x] Controller `Progress` (juz tercapai vs target)
- [x] Controller `Leaderboard` (sort `ORDER BY total_poin DESC`, siap upgrade ke `RANK()` bila perlu ranking eksplisit)
- [x] Controller `Laporan` (export CSV UTF-8 — deviasi dari rencana PHPSpreadsheet/xlsx asli, tapi tetap bisa dibuka Excel)
- [x] Controller API `api/Dashboard`, `api/Riwayat`, `api/Progress`, `api/Leaderboard` (mengikuti logika & guard yang sama persis dengan versi web)

**Fase 5 — Profil & Pemolesan** ✅ *(selesai)*
- [x] Controller `Profile` (edit profil, ganti password, upload foto)
- [x] Validasi form menyeluruh (`form_validation` library) — sudah diterapkan di Setoran/Penilaian/Users/Profile
- [x] Testing role-based access menyeluruh (guru tidak bisa akses kelas lain, siswa tidak bisa akses data siswa lain)

**Fase 6 — Keamanan & Pengerasan (Hardening)** ✅ *(selesai)*
- [x] CSRF Protection aktif di seluruh form POST web (`csrf_protection = TRUE`), otomatis via `form_open()`/`form_close()`.
- [x] Endpoint API (`api/*`) dikecualikan dari proteksi CSRF karena menggunakan Bearer Token header (stateless).

**Fase 7 — Web Installer, Landing Page & Branding** ✅ *(selesai, belum tercatat sebelumnya di blueprint ini)*
- [x] Controller `Installer` (4 langkah: cek syarat server, konfigurasi database, pasang skema, buat akun super admin) + `installed.lock` untuk mengunci `/installer` permanen setelah selesai
- [x] Controller `Landing` (halaman publik sebelum login: hero, fitur, target audiens, alur kerja, CTA — mengambil identitas dari `app_settings`)
- [x] Model `Setting_model` + tabel `app_settings` (nama lembaga, nama singkat/brand, tagline, logo)
- [x] Controller `Settings` (form ganti logo/nama/tagline institusi, khusus admin) dengan preview logo real-time & validasi client-side
- [x] Favicon dinamis (logo institusi atau fallback emoji) diterapkan konsisten di landing, login, dan seluruh halaman aplikasi
- [x] Halaman error kustom (`errors/html/*`) menggantikan tampilan default CI3, dengan detail teknis disembunyikan otomatis saat `ENVIRONMENT = production`
- [x] Dokumentasi terpisah: `INSTALLATION.md`, `INSTALLER_TEST_PLAN.md`, `BRANDING_TEST_PLAN.md`, `QA_REGRESSION.md`, `UAT_TEST_PLAN.md`

---

## 5a. Catatan Perbaikan & Keamanan (Code Review)

Saat review kode di branch `develop`, ditemukan dan diperbaiki:

1. **[KRITIS] Kebocoran data antar role** — `Dashboard.php`, `Riwayat.php`, `Progress.php`, `Leaderboard.php` memakai `$this->role` yang sebelumnya tidak pernah didefinisikan di `MY_Controller`/`MY_API_Controller`, sehingga selalu `null` dan membuat akun **siswa** salah masuk ke cabang logika admin/guru (berpotensi melihat data siswa lain & riwayat sekolah secara penuh). ✅ **Fix**: properti `$role` kini diisi otomatis di kedua base controller saat sesi/token divalidasi.
2. **[FUNGSIONAL] Upload rekaman audio gagal di Chrome/Firefox** — JS `MediaRecorder` menghasilkan `audio/webm`, tapi kode lama melabelinya sebagai `.mp3` palsu, ditolak validasi MIME CI3. ✅ **Fix**: JS kini jujur pakai `mediaRecorder.mimeType` asli, `Upload_handler` menerima ekstensi `webm`, dan `application/config/mimes.php` baru ditambahkan untuk memetakan `webm` ke `audio/webm` juga (bukan cuma `video/webm` seperti bawaan CI3).
3. **[MINOR] Race condition kode setoran** — sebelumnya `kode_setoran` dihitung dari "baca baris terakhir + 1" SEBELUM insert, rawan duplikat kalau dua guru submit bersamaan. ✅ **Fix**: `Setoran_model::create()` sekarang insert dulu dengan kode sementara unik (`uniqid`), lalu menimpa dengan kode final berbasis `insert_id` (dijamin unik oleh auto-increment MySQL) — semua dalam satu transaction. Format kode berubah dari `STR-0001` (4 digit) menjadi `STR-000001` (6 digit) untuk konsistensi dengan skala data yang lebih besar.
4. **[KEAMANAN] Proteksi CSRF (Cross-Site Request Forgery)** — Semua form POST (`auth/login.php`, `users/form.php`, `profile/index.php`, `setoran/form.php`, `kelas/index.php`, `penilaian/index.php`) dikonversi ke `form_open()`/`form_close()` CI3 dengan konfigurasi `csrf_protection` aktif di `config.php`.
5. **[KEAMANAN] Instalasi destruktif tanpa konfirmasi** — Opsi "Fresh Installation" di `installer/step3` (drop seluruh tabel) sebelumnya bisa disubmit hanya dengan satu klik radio button. ✅ **Fix**: ditambahkan konfirmasi wajib mengetik ulang nama database sebelum tombol submit aktif.
6. **[KEAMANAN] Kebocoran detail teknis di halaman error** — `error_php.php` dan `error_exception.php` sebelumnya selalu menampilkan file path lengkap server, nomor baris, dan nama exception class ke pengunjung. ✅ **Fix**: detail teknis kini hanya tampil bila `ENVIRONMENT !== 'production'`; di production, pesan diganti generik. Berlaku juga untuk pesan error database.
7. **[KEAMANAN] Endpoint `api/auth/login` rentan brute-force** — tidak ada pembatasan jumlah percobaan login gagal. ✅ **Fix**: ditambahkan `Login_attempt_model` + tabel `login_attempts` — maksimal 5 percobaan gagal per username+IP dalam jendela 15 menit, mengembalikan `429 Too Many Requests` bila terlampaui, reset otomatis saat login berhasil.
8. **[KEAMANAN] Kebocoran pesan exception mentah di API** — `api/Setoran::simpan()` mengembalikan `$e->getMessage()` mentah (bisa berisi nama tabel/kolom database) langsung ke client. ✅ **Fix**: pesan ke client diganti generik, detail teknis dicatat via `log_message('error', ...)` untuk keperluan debugging developer saja.

Controller yang memanggil `generate_kode_setoran()` untuk mengisi `kode_setoran` sebelum insert (di `Setoran.php` & `api/Setoran.php`) sudah dibersihkan — fungsi itu sekarang HANYA dipakai untuk preview di form (`auto_kode`), tidak lagi mempengaruhi data yang benar-benar tersimpan.

---


## 6. Keputusan Desain Kunci (agar tidak berubah-ubah di tengah jalan)

1. **Autentikasi**: pakai session bawaan CI3 (`$this->session->set_userdata()`), bukan token custom seperti di Apps Script — lebih standar untuk aplikasi web (bukan API murni).
2. **Update poin**: pakai SQL atomic (`total_poin = total_poin + ?`) di dalam DB transaction, bukan read-then-write dari PHP, untuk hindari race condition tanpa perlu lock manual.
3. **Leaderboard**: selalu dihitung real-time dari `siswa.total_poin`, tidak disimpan sebagai data terpisah — hindari data basi.
4. **Riwayat kelas siswa saat setoran dicatat** disimpan di `setoran.kelas_id` (denormalisasi sengaja), agar histori tidak berubah jika siswa naik/pindah kelas.
5. **Export laporan**: pakai library PHPSpreadsheet via Composer (bukan lagi client-side `xlsx.js` seperti di `index.html`), supaya data yang diexport sudah difilter & divalidasi di server sesuai hak akses role.

---

## 7. Keputusan Final (dikonfirmasi)

| Pertanyaan          | Keputusan                                   | Dampak ke desain |
|---------------------|----------------------------------------------|-------------------|
| Versi DB            | MySQL 8+ / MariaDB 10.2+                     | Leaderboard boleh pakai window function `RANK()` langsung di query — tidak perlu hitung manual di PHP. |
| Butuh API?          | Ya, sekalian disiapkan                       | CI3 dibuat **dual-mode**: controller web (render view) + controller API terpisah di bawah `api/` yang mengembalikan JSON. Keduanya pakai model & library yang SAMA, hanya beda output layer. Auth API pakai token (mis. JWT atau personal access token di tabel `api_tokens`), terpisah dari session web. |
| Upload file?        | Ya, foto profil + **rekaman audio bukti setoran** (revisi)  | Tambah kolom `foto` di `users`. Tambah kolom `audio_bukti` di `setoran` untuk menyimpan path file rekaman. Upload pakai CI3 `Upload` library, validasi tipe (mp3/wav/m4a/ogg) & ukuran maks (mis. 10MB), disimpan terpisah per jenis di `uploads/profile/` dan `uploads/setoran_audio/`. |

### 7a. Penyesuaian skema database (tambahan dari `tahfidzcms.sql`)

```sql
ALTER TABLE users   ADD COLUMN foto        VARCHAR(255) NULL AFTER nama;
ALTER TABLE setoran ADD COLUMN audio_bukti VARCHAR(255) NULL AFTER catatan
  COMMENT 'Path file rekaman audio bukti setoran, mis. uploads/setoran_audio/xxx.mp3';
ALTER TABLE setoran ADD COLUMN durasi_audio SMALLINT UNSIGNED NULL AFTER audio_bukti
  COMMENT 'Durasi rekaman dalam detik, untuk ditampilkan di player tanpa perlu baca file';

CREATE TABLE api_tokens (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  token      VARCHAR(64)  NOT NULL,
  expired_at DATETIME     NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_token (token),
  CONSTRAINT fk_api_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

**Catatan desain rekaman audio:**
- Audio bersifat **opsional** per setoran (`NULL`-able) — guru tetap bisa input nilai tanpa audio kalau setoran dilakukan langsung tatap muka.
- Path disimpan sebagai string, bukan BLOB di database — file fisik tetap di filesystem/`uploads/`, DB cuma nyimpan referensi. Ini supaya database tidak bengkak dan proses backup DB tetap ringan.
- Kalau nanti aplikasi punya banyak sekolah/traffic tinggi, kolom `audio_bukti` ini gampang diarahkan ke storage eksternal (mis. path jadi URL S3/Cloud Storage) tanpa ubah struktur tabel.
- Validasi durasi maksimal (mis. 5 menit) & ukuran file sebaiknya dicek di sisi client (JS `MediaRecorder`) **dan** di server (CI3 `Upload` library) — jangan andalkan salah satu saja.
- Endpoint API perlu jalur upload terpisah (`multipart/form-data`), bukan JSON biasa, untuk endpoint `POST /api/setoran` yang menyertakan audio.

### 7b. Penyesuaian struktur folder — pisah Web vs API

```
application/
├── controllers/
│   ├── (controller web seperti sebelumnya: Dashboard.php, Setoran.php, dst.)
│   └── api/
│       ├── Auth.php          (POST /api/auth/login -> issue token)
│       ├── Dashboard.php
│       ├── Setoran.php
│       ├── Progress.php
│       └── Leaderboard.php
├── core/
│   ├── MY_Controller.php     (guard utk web, cek session)
│   └── MY_API_Controller.php (guard utk api, cek Bearer token dari tabel api_tokens)
├── libraries/
│   ├── Poin_calculator.php
│   └── Upload_handler.php    (helper upload & validasi foto profil)
```

Model (`Siswa_model`, `Setoran_model`, dst.) tetap **satu set saja** dan dipakai bersama oleh controller web maupun api — supaya logika bisnis tidak dobel dan tidak berisiko beda hasil antara web dan mobile.

### 7c. Update urutan Fase pengembangan

- **Fase 1** (Auth & Kerangka) sekarang mencakup DUA jalur auth: session (web) dan token (api), dari awal — supaya struktur `MY_Controller` vs `MY_API_Controller` konsisten sejak awal, tidak ditambal belakangan.
- **Fase 2** (Data Master) tambah: upload & simpan foto profil di form Users/Profile.
- Modul **API** dibuat paralel mengikuti modul web di Fase 3–4 (setiap controller web punya versi api yang expose data yang sama dalam JSON).
