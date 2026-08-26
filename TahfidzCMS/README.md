# TahfidzCMS

Aplikasi monitoring hafalan Al-Qur'an berbasis CodeIgniter 3 + MySQL,
migrasi dari prototipe Google Apps Script.

## Yang sudah tersedia di skeleton ini (Fase 0–1)

```
application/
├── config/
│   ├── database.php     ← sudah pakai getenv(), tinggal isi kredensial
│   ├── routes.php       ← rute web (session) & api (token) sudah dipetakan
│   └── autoload.php     ← gabungkan ke autoload.php CI3 bawaan Anda
├── core/
│   ├── MY_Controller.php       ← guard session + role, untuk semua controller web
│   └── MY_API_Controller.php   ← guard Bearer token, untuk semua controller api/*
├── models/
│   ├── User_model.php          ← verifikasi login, hash password (bcrypt)
│   ├── Guru_kelas_model.php    ← satu-satunya sumber "kelas yang diampu guru"
│   ├── Api_token_model.php     ← terbit/validasi/cabut token API
│   ├── Kelas_model.php         ← CRUD data master kelas
│   └── Siswa_model.php         ← CRUD data siswa (nisn = primary key, anti-duplikat)
├── libraries/
│   └── Upload_handler.php      ← upload foto profil & (nanti) audio setoran, tervalidasi
├── controllers/
│   ├── Auth.php          ← login/logout web (session)
│   ├── Dashboard.php     ← contoh pemakaian MY_Controller + render()
│   ├── Users.php         ← CRUD users/siswa/guru-kelas, khusus admin
│   ├── Kelas.php         ← CRUD data master kelas, khusus admin
│   └── api/
│       └── Auth.php      ← login/logout api (token)
├── views/
│   ├── auth/login.php
│   ├── dashboard/index.php
│   ├── users/{index,form}.php
│   ├── kelas/index.php
│   └── templates/{header,sidebar,footer}.php
└── helpers/
    └── format_helper.php  ← format tanggal ID & durasi audio

uploads/
├── profile/          ← foto profil users
└── setoran_audio/    ← rekaman audio bukti setoran (Fase 3)

database/
└── tahfidzcms.sql    ← skema lengkap (lihat chat sebelumnya / file terpisah)
```

## Cara pakai skeleton ini

1. **Download CodeIgniter 3** resmi dari https://codeigniter.com/cifiles/CodeIgniter-3.1.13.zip
   (atau versi 3.x terbaru), lalu **timpa/gabungkan** folder `application/`
   di zip ini ke folder `application/` bawaan CI3 — jangan replace seluruh
   folder CI3, karena `application/third_party`, `system/`, dsb tetap perlu ada.

2. Import skema database:
   ```
   mysql -u root -p < database/tahfidzcms.sql
   ```

3. Set kredensial database via environment variable, atau edit langsung
   `application/config/database.php`:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=tahfidzcms
   ```

4. Pastikan hash password di seed data (`$2y$10$CONTOH_HASH_GANTI_INI`)
   sudah diganti dengan hash bcrypt asli. Bisa generate cepat lewat php -a:
   ```php
   php -r "echo password_hash('123456', PASSWORD_BCRYPT), PHP_EOL;"
   ```
   lalu update kolom `password` di tabel `users` dengan hasilnya.

5. Set folder `uploads/` writable oleh web server:
   ```
   chmod -R 755 uploads/
   ```

6. Jalankan (dengan PHP built-in server untuk development):
   ```
   php -S localhost:8080 -t .
   ```
   lalu buka `http://localhost:8080/login` (web) atau uji
   `POST http://localhost:8080/index.php/api/auth/login` (api) dengan Postman/curl.

## Contoh test login API

```bash
curl -X POST http://localhost:8080/index.php/api/auth/login \
  -d "username=1001" -d "password=123456"
```

Response sukses akan berisi `token`, yang kemudian dipakai di endpoint
lain sbg header:
```
Authorization: Bearer <token>
```

## Selanjutnya (Fase 2 dst.)

Lihat `BLUEPRINT.md` untuk urutan pengembangan modul berikutnya:
Kelola Users → Setoran (+ rekam audio) → Penilaian → Riwayat/Progress/
Leaderboard → Laporan Excel → Profile.
