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

**Fase 0 — Fondasi** *(sudah mulai)*
- [x] Skema database (`tahfidzcms.sql`) — perlu tambahan `foto` & `api_tokens` (lihat §7a)
- [ ] Setup project CI3 kosong + config database/routes/autoload
- [ ] `MY_Controller` (session guard + role guard, untuk web)
- [ ] `MY_API_Controller` (Bearer token guard, untuk api)

**Fase 1 — Auth & Kerangka Tampilan**
- [ ] Model `User_model`
- [ ] Controller `Auth` (login, logout, web/session) + hash password
- [ ] Controller `api/Auth` (login -> issue token ke tabel `api_tokens`)
- [ ] Template header/sidebar/footer (adaptasi dari `index.html` yang sudah ada, dipecah jadi partial view)
- [ ] Form & handler upload foto profil (dipakai di Users & Profile)

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

**Fase 4 — Laporan & Insight**
- [ ] Controller `Dashboard` (statistik ringkas per role)
- [ ] Controller `Riwayat` (list + filter)
- [ ] Controller `Progress` (juz tercapai vs target)
- [ ] Controller `Leaderboard` (query RANK() / sort manual)
- [ ] Controller `Laporan` (export Excel pakai PHPSpreadsheet)

**Fase 5 — Profil & Pemolesan**
- [ ] Controller `Profile` (edit profil, ganti password)
- [ ] Validasi form menyeluruh (`form_validation` library)
- [ ] Testing role-based access (guru tidak bisa akses kelas lain, siswa tidak bisa akses data siswa lain)

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
