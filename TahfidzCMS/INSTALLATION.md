# PANDUAN INSTALASI & DEPLOYMENT — TAHFIDZCMS

Dokumen ini menjelaskan langkah-langkah instalasi aplikasi **TahfidzCMS** (CodeIgniter 3) pada server hosting, cPanel, VPS, atau local server (XAMPP/Laragon) menggunakan **Web Installer Interaktif**.

---

## 1. Persyaratan Server (System Requirements)

- **PHP**: Versi **7.4** atau lebih tinggi (disarankan **PHP 8.1 / 8.2**).
- **Database**: **MySQL 8.0+** atau **MariaDB 10.4+**.
- **Ekstensi PHP Wajib**:
  - `mysqli` (Koneksi database)
  - `mbstring` (Pemrosesan karakter multibyte)
  - `json` (Response & token API)
  - `session` (Autentikasi web)
  - `openssl` (Enkripsi & random token)
  - `fileinfo` (Validasi MIME rekaman audio & foto)
- **Web Server**: Apache (`mod_rewrite` aktif) atau Nginx.

---

## 2. Langkah-Langkah Instalasi via Browser

### Langkah 1: Unggah Berkas Aplikasi
1. Ekstrak berkas `TahfidzCMS.zip` ke direktori root web server Anda (misal: `public_html/` pada cPanel, atau `htdocs/tahfidzcms/` pada XAMPP).
2. Pastikan permission direktori berikut dapat ditulisi (*writable* / `0755` atau `0775`):
   - `uploads/`
   - `uploads/profile/`
   - `uploads/setoran_audio/`
   - `application/config/`

### Langkah 2: Buka Web Installer di Browser
1. Akses alamat web Anda dengan menambahkan `/installer`, contoh:
   ```
   http://localhost/tahfidzcms/installer
   atau
   https://domainsekolah.sch.id/installer
   ```
2. **Step 1 (Pemeriksaan Sistem)**: Sistem akan otomatis mengecek versi PHP, ekstensi, dan izin folder. Jika semua bercentang hijau, klik **Lanjut ke Konfigurasi Database**.

### Langkah 3: Konfigurasi Database
1. **Step 2 (Database)**: Masukkan parameter koneksi MySQL Anda:
   - **Host Database**: `localhost` (atau IP server database)
   - **Port**: `3306`
   - **Username**: Username database MySQL Anda
   - **Password**: Password database MySQL Anda
   - **Nama Database**: Nama database (misal: `tahfidzcms`). *Database akan dibuat otomatis jika belum ada.*
2. Klik **Tes Koneksi & Lanjut**.

### Langkah 4: Pemasangan Skema Database
1. **Step 3 (Skema DB)**: Sistem akan memverifikasi apakah database baru atau sudah berisi tabel lama.
2. Pilih **Fresh Installation** untuk memasang skema tabel resmi TahfidzCMS (`users`, `kelas`, `guru_kelas`, `siswa`, `setoran`, `api_tokens`).
3. Klik **Pasang Skema & Lanjut**.

### Langkah 5: Pembuatan Akun Super Admin
1. **Step 4 (Super Admin)**: Masukkan data akun administrator utama:
   - **Nama Lengkap**: Nama admin madrasah/pesantren
   - **Username / NIP**: NIP atau username admin untuk login
   - **Password**: Kata sandi admin (minimal 6 karakter)
2. Klik **Selesaikan Instalasi & Kunci**.

### Langkah 6: Selesai
1. Sistem akan otomatis:
   - Mengisi konfigurasi database di `application/config/database.php`.
   - Membuat file proteksi `installed.lock`.
   - Menghancurkan session installer.
2. Klik **Masuk ke Halaman Login** untuk mulai menggunakan TahfidzCMS.

---

## 3. Fitur Keamanan Web Installer (Security)

- **Permanent Lock**: Setelah instalasi selesai, sistem membuat file `installed.lock`. Selama file ini ada, semua akses ke `/installer` akan **ditolak secara permanen (HTTP 403)** demi mencegah penimpaan data oleh pihak yang tidak berhak.
- **Bcrypt Encryption**: Kata sandi administrator di-hash menggunakan algoritma Bcrypt standar industri (`password_hash`), tidak disimpan dalam teks polos.
- **Zero-Credential Exposure**: Password database tidak pernah ditampilkan di layar ataupun disimpan di dalam source code installer.
- **CSRF Protected**: Seluruh form pada alur installer dilindungi token CSRF CodeIgniter 3.

---

## 4. Troubleshooting Umum

| Gejala | Penyebab | Solusi |
|---|---|---|
| **Folder Permission Read-Only pada Step 1** | Izin folder `uploads/` atau `application/config/` belum writable | Ubah CHMOD folder menjadi `0755` atau `0775` via File Manager cPanel / terminal `chmod -R 775 uploads/ application/config/`. |
| **Gagal Terhubung ke MySQL Server pada Step 2** | Host, username, atau password MySQL salah | Periksa kembali kredensial database di cPanel / MySQL manager Anda. |
| **Akses ke /installer Ditolak (403)** | Aplikasi sudah terpasang (`installed.lock` ada) | Jika Anda sengaja ingin menginstall ulang dari awal, hapus file `installed.lock` di direktori utama aplikasi. |
| **Halaman 404 saat membuka menu** | `mod_rewrite` Apache belum aktif atau `.htaccess` hilang | Pastikan file `.htaccess` ada di root folder aplikasi dan modul `rewrite` pada web server aktif. |
