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

---

## 8. Fase 8 — Kriteria Penilaian Tahfidz (Ziyadah/Muroja'ah/QC) 🟡 *(SEDANG BERJALAN, BELUM SELESAI)*

Sumber kebenaran aturan bisnis: dokumen `KRITERIA_PENILAIAN_TAHFIDZ.docx` (dilampirkan terpisah,
tidak disimpan di repo). Menggantikan sistem penilaian lama yang generik (`nilai` A/B/C +
`status` Lancar/Cukup/Perlu Perbaikan) dengan sistem yang sesuai standar pesantren tahfidz.

### 8a. Aturan bisnis (ringkasan)

1. **Tiga jenis setoran** (`jenis_setoran`): `ziyadah` (hafalan baru, dihitung per **halaman**),
   `murojaah` (mengulang, dihitung per **juz**), `qc` (quality control, dihitung per **2 halaman**).
2. Guru input **`jumlah_kesalahan`** (angka) + **`kualitas_bacaan`** (`baik`/`kurang_baik` —
   Makhraj/Tajwid/Sifatul Huruf). Guru **tidak lagi memilih skor secara manual**.
3. Sistem hitung otomatis **`keterangan`** (`L`/`CL`/`KL`/`TL`) dari `jumlah_kesalahan` +
   `jenis_setoran` (ambang batas beda per jenis — lihat `Poin_calculator::KETERANGAN_THRESHOLDS`),
   lalu **`skor`** (100/95/90/85/80/75/60) dari `keterangan` + `kualitas_bacaan` (lihat
   `Poin_calculator::SKOR_MATRIX`).
4. Khusus `jenis_setoran = 'qc'`: guru **wajib** isi `hasil_qc` (`layak_tasmi` / `belum_layak`)
   secara manual — murni judgment guru penguji, tidak ada rumus otomatis di dokumen sumber.
5. **Poin leaderboard = skor apa adanya** (1:1), sama rata untuk ketiga jenis setoran, tidak ada
   bobot/bonus ekstra untuk QC (keputusan pemilik produk, per klarifikasi sesi ini).
6. Data lama (setoran existing nilai A/B/C) di-**migrasi**, bukan dibiarkan/diarsipkan terpisah —
   lihat mapping lengkap di `database/migration_kriteria_penilaian.sql`.

### 8b. Checklist implementasi

**Backend (selesai ✅):**
- [x] `application/libraries/Poin_calculator.php` — ditulis ulang total: `hitung_keterangan()`,
      `hitung_skor()`, `nilai_setoran()` (helper gabungan), konstanta `KETERANGAN_THRESHOLDS`,
      `SKOR_MATRIX`, `JENIS_SETORAN_LABEL`, `HASIL_QC_LABEL`. Fungsi lama `hitung_poin()` (berbasis
      nilai A/B/C) **dihapus**.
- [x] `database/tahfidzcms.sql` — skema tabel `setoran` final untuk instalasi baru (kolom
      `jenis_setoran`, `jumlah_kesalahan`, `kualitas_bacaan`, `keterangan`, `skor`, `hasil_qc`;
      kolom lama `nilai` & `status` **dihapus**).
- [x] `database/migration_kriteria_penilaian.sql` **(BARU)** — untuk instalasi existing: ALTER TABLE
      + migrasi data lama. **WAJIB dijalankan manual** sebelum kode baru dipakai di database yang
      sudah ada isinya (lihat instruksi di dalam file, termasuk langkah backup).
- [x] `application/models/Setoran_model.php` — `create()`, `update_penilaian()`, `get_all()`,
      `count_all_filtered()`, `count_setoran()`, `get_progress_juz_by_nisn()`, `get_laporan_rekap()`
      semua disesuaikan ke skema baru.
- [x] `application/controllers/Setoran.php` (web) — validasi & data insert pakai field baru.
- [x] `application/controllers/api/Setoran.php` — validasi, filter query, & data insert pakai field baru.
- [x] `application/controllers/Penilaian.php` — form koreksi guru pakai field baru.
- [x] `application/controllers/Dashboard.php` + `api/Dashboard.php` — statistik pakai kode `L/CL/KL/TL`
      (kategori "Perlu Perbaikan" lama sekarang = gabungan `KL` + `TL`).
- [x] `application/controllers/Riwayat.php` + `api/Riwayat.php` — filter `status` → `keterangan`.
- [x] `application/controllers/Laporan.php` — export Excel dengan kolom lengkap baru (Jenis Setoran,
      Jumlah Kesalahan, Kualitas Bacaan, Keterangan, Skor, Hasil QC).
- [x] Dikonfirmasi bersih (tidak ada referensi field lama): `Leaderboard.php`, `Progress.php`,
      `api/Leaderboard.php`, `api/Progress.php`, `Siswa_model.php`.

**Frontend / View (⚠️ BELUM DIKERJAKAN — prioritas lanjutan):**
- [ ] `application/views/setoran/form.php` — form input setoran belum punya field
      `jenis_setoran` (select), `jumlah_kesalahan` (number input), `kualitas_bacaan` (select/radio),
      `hasil_qc` (select, tampil kondisional hanya saat `jenis_setoran=qc`, idealnya via JS show/hide).
      Field lama `nilai`/`status` (dropdown A/B/C, Lancar/Cukup/Perlu Perbaikan) **harus dihapus**
      dari form ini.
- [ ] `application/views/penilaian/index.php` — modal koreksi guru: sama seperti di atas, ganti
      dropdown `nilai`/`status` lama dengan field baru. **Skor & keterangan idealnya tidak diinput
      manual** — tampilkan sebagai hasil kalkulasi read-only (mis. dihitung live via JS saat guru
      mengubah jumlah kesalahan/kualitas, meniru logika `Poin_calculator` di sisi client, ATAU
      dihitung ulang di server setelah submit dan ditampilkan di halaman berikutnya).
- [ ] `application/views/riwayat/index.php` — filter dropdown `status` (Lancar/Cukup/Perlu Perbaikan)
      perlu diganti jadi filter `keterangan` (L/CL/KL/TL) dan/atau `jenis_setoran`; badge warna nilai
      (`A`/`B`/`C`) di tabel perlu diganti jadi tampilan `skor` numerik + badge `keterangan`.
- [ ] `application/views/dashboard/index.php` — cek ulang label "Lancar/Cukup/Perlu Perbaikan" di
      kartu statistik, sesuaikan teksnya dengan kategori baru (`setoran_perbaikan` sekarang
      menggabungkan `KL`+`TL`, bukan cuma "Perlu Perbaikan" tunggal).
- [ ] `application/views/progress/index.php` & `application/views/leaderboard/index.php` — belum
      dicek apakah ada tampilan nilai A/B/C yang perlu disesuaikan (perlu audit ulang).

**Lain-lain (belum dikerjakan):**
- [ ] Testing end-to-end: belum ada verifikasi PHP syntax otomatis maupun uji jalan nyata terhadap
      seluruh perubahan di atas.
- [ ] Pertimbangkan menambahkan validasi JS di form Setoran/Penilaian: field `jumlah_kesalahan`
      harus angka non-negatif, field `hasil_qc` wajib muncul & required hanya saat
      `jenis_setoran=qc` dipilih (show/hide dinamis).
- [ ] `INSTALLATION.md` sebaiknya diberi catatan: instalasi BARU cukup `tahfidzcms.sql`; instalasi
      **existing** yang upgrade dari versi lama wajib jalankan `migration_kriteria_penilaian.sql`
      secara manual (skrip installer otomatis TIDAK menjalankan file migrasi ini).

---

## 9. Fase 9 — Audit Keamanan & Perbaikan (Security Hardening Round 2) ✅ *(selesai)*

**Update status Fase 8**: Seluruh checklist Frontend/View di §8b yang sebelumnya ditandai
"BELUM DIKERJAKAN" **sudah selesai dikerjakan** (form Setoran, modal Penilaian, Riwayat,
Dashboard, dst. — semua sudah pakai field `jenis_setoran`/`jumlah_kesalahan`/`kualitas_bacaan`/
`hasil_qc` dengan live preview & panduan kriteria kolaps). Detail checklist §8b dibiarkan apa
adanya sebagai jejak historis; anggap semua poin di sana closed kecuali disebutkan lain di sini.

### 9a. Temuan & perbaikan dari audit keamanan independen

Audit eksternal (analisis statis, tanpa runtime PHP) menemukan 4 celah Prioritas Tinggi dan
6 celah Menengah. Semua sudah diverifikasi ulang terhadap kode asli (bukan asumsi dari laporan)
sebelum diperbaiki.

**Prioritas Tinggi (semua diperbaiki):**

1. **H1 — Guru tanpa kelas diampu bisa lihat semua data sekolah.** Akar masalah:
   `kelas_diizinkan` kosong (`[]`) untuk guru tanpa penugasan kelas, tapi banyak model memakai
   pola `if (! empty($kelas_ids)) where_in(...)` — array kosong membuat kondisi ini `FALSE`
   sehingga filter dilewati sepenuhnya (bukan "tanpa hasil"). **Fix di sumber**: 
   `MY_Controller::_load_kelas_diizinkan()` dan `MY_API_Controller::_load_kelas_diizinkan()`
   sekarang mengisi sentinel `[-1]` (id yang mustahil match) jika guru tidak punya kelas,
   bukan array kosong — memperbaiki SEMUA titik pemakaian sekaligus tanpa tambal satu-satu.
2. **H2 — IDOR di `Progress.php` (web).** Guru bisa lihat progress siswa kelas lain via
   `?nisn=...` tanpa validasi, tidak konsisten dengan `api/Progress.php` yang sudah benar.
   **Fix**: tambah validasi `in_array($siswa_aktif->kelas_id, $this->kelas_diizinkan)`, sama
   seperti versi API.
3. **H3 — Aksi destruktif via GET (rentan CSRF).** `users/hapus`, `users/reset-password`,
   `kelas/hapus`, `setoran/hapus` semua diakses via `<a href>` (GET), padahal proteksi CSRF
   CI3 hanya berlaku untuk POST. **Fix**: 4 method controller kini menolak request non-POST
   (`405 Method Not Allowed`), dan semua link terkait di view diganti jadi `form_open()`/
   `form_close()` (CSRF token otomatis ter-embed).
4. **H4 — Login web tanpa rate limit.** `Login_attempt_model` sebelumnya cuma dipakai di
   `api/Auth.php`. **Fix**: diintegrasikan juga ke `Auth::_process_login()` (web) — 5 percobaan
   gagal per username+IP dalam 15 menit, sama seperti API.

**Prioritas Menengah (semua diperbaiki):**

5. **M1 — `encryption_key` hardcoded publik di `config.php`.** **Fix**: `Installer.php`
   sekarang men-generate key acak (`random_bytes(32)`) dan menuliskannya ke `config.php` saat
   instalasi, menggantikan placeholder.
6. **M2 — Cookie & session tidak aman.** `cookie_httponly` sebelumnya `FALSE` (bisa dibaca
   JS), `cookie_secure` selalu `FALSE`, session ID tidak diregenerasi saat login. **Fix**:
   `cookie_httponly = TRUE`, `cookie_secure` otomatis mengikuti HTTPS, `sess_regenerate_destroy
   = TRUE`, dan `$this->session->sess_regenerate(TRUE)` dipanggil saat login berhasil (mencegah
   session fixation).
7. **M3 — Tidak ada `.htaccess` deny-all di `application/`.** **Fix**: ditambahkan
   `application/.htaccess`, konsisten dengan `system/.htaccess` yang sudah ada.
8. **M4 — Parameter `limit`/`offset` API tanpa batas atas.** **Fix**: `limit` di-clamp ke
   1–200 di `api/Setoran.php`, `api/Riwayat.php`, `api/Leaderboard.php`; `offset` dipaksa
   non-negatif.
9. **M5 — Upload audio cuma validasi ekstensi, tanpa cek konten MIME; `uploads/` tanpa
   proteksi eksekusi script.** **Fix**: `Upload_handler::_do_upload()` sekarang menerima
   parameter `allowed_mimes` generik (dipakai baik oleh `upload_foto_profil()` maupun
   `upload_audio_setoran()`) dan memvalidasi via `finfo` setelah upload, menghapus file kalau
   MIME tidak cocok. Ditambahkan juga `uploads/.htaccess` yang menonaktifkan eksekusi PHP/CGI
   di folder tersebut (defense-in-depth).
10. **M6 — Formula injection pada export Excel.** Data user (catatan, nama surat, dst.) yang
    diawali `=`, `+`, `-`, atau `@` bisa dieksekusi sebagai formula saat file dibuka di Excel.
    **Fix**: ditambahkan `Excel_exporter::sanitize_formula()`, dipakai di kedua format export
    (xlsx & xml legacy) sebelum nilai teks ditulis ke cell.

**Temuan Rendah** (dari laporan, belum ditindaklanjuti — didokumentasikan untuk referensi
lanjutan, bukan diabaikan): `csrf_exclude_uris` redundan, `log_threshold = 0` (logging mati),
CDN Tailwind dev di production, password reset default `123456` tampil plaintext di flash
message (dianggap tidak kritis karena valuenya memang publik/terdokumentasi di kode), file
yatim saat upload sukses tapi insert DB gagal, admin bisa hapus/reset admin lain tanpa proteksi
tambahan.

### 9b. Verifikasi

Semua file yang diedit diverifikasi seimbang secara brace/paren (PHP interpreter tidak
tersedia di environment pengerjaan — verifikasi murni analisis statis, BELUM diuji jalan
nyata). **Wajib jalankan `php -l` di environment yang punya PHP dan uji end-to-end sebelum
deploy ke production**, terutama untuk:
- Alur login (regenerasi session, rate limiting) — pastikan tidak accidentally logout user sah.
- Alur hapus/reset user, kelas, setoran — pastikan form POST + tombol berfungsi normal di browser.
- Upload audio & foto profil — pastikan file valid tetap bisa lolos validasi MIME baru (jangan
  sampai terlalu ketat menolak file sah).
- Instalasi baru — pastikan `encryption_key` benar-benar ter-generate acak per instalasi,
  bukan tetap placeholder karena regex tidak match.

---

## 10. Fase 10 — Follow-up Review: XSS Output Escaping ✅ *(selesai)*

Review lanjutan setelah Fase 9 menemukan bahwa perbaikan sebelumnya belum menyentuh
beberapa titik output NISN & path logo/foto yang tidak di-escape, walau `judul` perbaikan
sebelumnya menyebut "XSS filtering". Diverifikasi ulang satu-satu, dan ternyata **risikonya
lebih nyata dari yang terlihat sekilas**: field NISN (=`username` di tabel `users`) sebelumnya
HANYA divalidasi `required|trim|max_length[50]` — tanpa pembatasan karakter — sehingga admin
(satu-satunya role yang boleh membuat user, via `require_role(['admin'])`) bisa menanam
payload XSS ke NISN, yang kemudian tersimpan dan dirender tanpa escape ke pengguna lain
(admin lain, guru) yang membuka halaman terkait. Ini stored XSS yang butuh privilege admin
untuk dieksploitasi (bukan XSS publik tanpa auth), tapi tetap relevan untuk skenario
multi-admin / akun admin yang di-compromise.

**Perbaikan (output escaping, 14 titik di 11 file):**
- NISN: `leaderboard/index.php`, `progress/index.php` (2 titik), `riwayat/index.php`
  (3 titik), `laporan/index.php` — ditambahkan `htmlspecialchars()`.
- Path logo/foto (konteks atribut `src`/`href`): `profile/index.php`, `templates/header.php`,
  `templates/sidebar.php`, `users/index.php`, `landing/index.php` (4 titik), `settings/index.php`,
  `auth/login.php` (2 titik) — ditambahkan `htmlspecialchars()` di sekitar `base_url(...)`.
  Risiko titik-titik ini sebenarnya rendah (path digenerate server dengan nama file acak via
  `Upload_handler` `encrypt_name => TRUE`), tapi tetap diperbaiki sebagai defense-in-depth &
  konsistensi.

**Perbaikan di sumber (defense-in-depth):**
- `Users::simpan()` — rule validasi `username` (=NISN/NIP) diperketat dari
  `required|trim|max_length[50]` menjadi `+ alpha_dash` (hanya huruf, angka, underscore,
  dash) — payload XSS tidak akan lolos validasi sejak awal, bukan cuma ditangkal saat
  output.

**Perbaikan tambahan (temuan Rendah dari audit awal yang ternyata cepat diperbaiki):**
- `csrf_exclude_uris` — dibersihkan dari 8 entri redundan jadi 1 (`api/.*` sudah cukup).
- `log_threshold` — diaktifkan dari `0` (mati total) jadi `1` (Error Messages saja).
  **Catatan penting**: folder `application/logs/` sebelumnya TIDAK ADA sama sekali di
  repo — kalau logging diaktifkan tanpa folder ini, CI3 akan gagal/warning saat menulis
  log. Folder sudah dibuat + `index.html` placeholder (konsisten dengan pola di
  `.gitignore` yang sudah menyebut file ini).

**Belum ditindaklanjuti (disengaja, di luar scope follow-up ini):**
- `encryption_key` acak hanya berlaku untuk instalasi BARU (via installer). Instalasi
  existing tetap pakai key lama sampai diregenerasi manual — lihat
  `PETUNJUK_LANJUTAN_SECURITY.md`. Membuat fitur "Regenerate Key" di halaman Settings
  adalah pekerjaan terpisah (lebih besar scope-nya), belum dikerjakan.
- `global_xss_filtering` tetap `FALSE`. Ini keputusan sadar, BUKAN kelalaian: mengaktifkan
  filter XSS global CI3 adalah praktik lama yang cenderung tidak reliable (mudah di-bypass,
  bisa merusak data sah) dibanding output escaping kontekstual yang konsisten (`htmlspecialchars()`
  di titik render) — yang sudah diterapkan di seluruh view sejauh diaudit. Kalau nanti
  ditemukan titik output lain yang lolos, tambal di titik itu, bukan menyalakan filter global.
- CDN `cdn.tailwindcss.com`, password reset default `123456` di flash message, file yatim
  saat insert gagal — tetap seperti sebelumnya, didokumentasikan di Fase 9 §9a bagian
  "Temuan Rendah", belum ditindaklanjuti karena dianggap risiko rendah & butuh keputusan
  produk (mis. ganti CDN ke build lokal Tailwind = perubahan build process, bukan quick fix).
