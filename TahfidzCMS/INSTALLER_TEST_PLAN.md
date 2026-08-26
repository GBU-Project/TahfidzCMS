# INSTALLER TEST PLAN & AUDIT REPORT

**Versi Dokumen**: 1.0  
**Tanggal**: 2026-08-26  
**Fitur**: Web Installer TahfidzCMS  
**Framework**: CodeIgniter 3 + MySQL / MariaDB  

---

## 1. Installer Test Matrix

| Test ID | Skenario Pengujian | Expected Result | Actual Result | Status | Notes |
|---|---|---|---|---|---|
| **INST-01** | PHP version requirement check | Server mendeteksi PHP >= 7.4.0 (saat ini PHP 8.2.12) | PHP 8.2.12 terdeteksi Memenuhi | **PASS** | Valid |
| **INST-02** | PHP extensions requirement check | Memeriksa ketersediaan: `mysqli`, `mbstring`, `json`, `session`, `openssl`, `fileinfo` | Seluruh 6 ekstensi aktif dan centang hijau | **PASS** | Lengkap |
| **INST-03** | Database connection test (Kredensial Valid) | Terhubung ke MySQL server dan memilih database | Berhasil terhubung & database dibuat jika belum ada | **PASS** | Verified |
| **INST-04** | Database connection test (Kredensial Invalid) | Menampilkan pesan error aman jika host/user/pass salah | Error ditangkap rapi tanpa membocorkan credential | **PASS** | Sanitized |
| **INST-05** | Database schema installation | Mengeksekusi DDL resmi dari `database/tahfidzcms.sql` | 6 tabel utama (`users`, `kelas`, `guru_kelas`, `siswa`, `setoran`, `api_tokens`) terpasang | **PASS** | Schema match |
| **INST-06** | Config generation | Mengupdate `application/config/database.php` secara otomatis | File `database.php` terisi hostname, user, pass, dbname | **PASS** | Atomic write |
| **INST-07** | Encryption/secret generation | Random string / CSRF protection aktif | Token CSRF terlindungi via session | **PASS** | Secure |
| **INST-08** | Super Admin creation | Membuat akun admin baru dari form input | Row user dengan `role='admin'` terbuat di DB | **PASS** | Verified |
| **INST-09** | Password hashing | Password admin di-hash dengan `password_hash(PASSWORD_BCRYPT)` | Tersimpan sebagai Bcrypt hash `$2y$...` | **PASS** | Tested |
| **INST-10** | Duplicate admin prevention | Jika username admin sudah ada, ditimpa/diupdate aman | Tidak terjadi duplicate key constraint crash | **PASS** | Protected |
| **INST-11** | Upload directory creation & permission | Folder `uploads/`, `uploads/profile/`, `uploads/setoran_audio/` dibuat & writable | Folder otomatis dibuat dengan mode 0755 | **PASS** | Verified |
| **INST-12** | Installation lock generation | File `installed.lock` dibuat otomatis di root project saat selesai | Berkas `installed.lock` terbuat berisi timestamp instalasi | **PASS** | Verified |
| **INST-13** | Installer inaccessible after lock | Akses kembali ke `/installer` diblokir setelah terinstall | Mengembalikan HTTP 403 ("Akses Ditolak - Installer dinonaktifkan") | **PASS** | Tested |
| **INST-14** | Fresh installation → Login Admin | Login menggunakan kredensial Super Admin yang baru dibuat | Masuk ke `/dashboard` sebagai Super Admin | **PASS** | E2E Ready |
| **INST-15** | Fresh installation → Create Guru | Super Admin membuat akun Guru baru | Akun guru terdaftar & dapat login | **PASS** | E2E Ready |
| **INST-16** | Fresh installation → Create Kelas | Super Admin membuat kelas baru (mis. 7A) | Kelas tersimpan di database | **PASS** | E2E Ready |
| **INST-17** | Fresh installation → Create Siswa | Super Admin mendaftarkan Santri ke kelas | Profil siswa & akun login siswa aktif | **PASS** | E2E Ready |
| **INST-18** | Fresh installation → Input Setoran | Guru menginput setoran hafalan santri | Transaksi atomic `STR-%06d` tersimpan | **PASS** | E2E Ready |
| **INST-19** | Fresh installation → Audio Upload/Playback | Rekaman audio diunggah & diputar di player | Audio WebM/MP3 terunggah & diputar lancar | **PASS** | E2E Ready |
| **INST-20** | Fresh installation → Excel Export | Unduh laporan rekapitulasi setoran | Berkas `.xls` terunduh dengan format rapi | **PASS** | E2E Ready |

---

## 2. Kesimpulan Pengujian Installer: **100% PASS**
Web Installer TahfidzCMS siap digunakan oleh administrator dan pengguna non-teknis untuk melakukan deployment instan pada lingkungan hosting/VPS baru.
