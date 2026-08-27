# Rencana Pengujian: Identitas Lembaga & Public Landing Page (TahfidzCMS)

Dokumen ini berisi spesifikasi pengujian komprehensif untuk fitur **Identitas Lembaga (app_settings)** dan **Public Landing Page** pada aplikasi **TahfidzCMS**.

---

## 1. Test Matrix (BR-01 s/d BR-20)

| Test ID | Skenario Pengujian | Role / Aktor | Langkah Pengujian | Hasil yang Diharapkan | Status |
|---|---|---|---|---|---|
| **BR-01** | Default branding setelah fresh install | Guest / Public | 1. Buka landing page `/` atau `/landing`<br>2. Buka halaman login `/login` | Menampilkan default: "TahfidzCMS", tagline "Sistem Monitoring Hafalan Al-Qur'an", dan placeholder logo buku 📖 tanpa error. | PASS |
| **BR-02** | Super Admin dapat membuka Identitas Lembaga | Super Admin (`admin`) | 1. Login sebagai Super Admin<br>2. Akses menu `Pengaturan -> Identitas Lembaga` (`/settings`) | Halaman pengaturan terbuka, form terisi nilai konfigurasi saat ini, tombol submit tersedia. | PASS |
| **BR-03** | Guru tidak dapat mengubah Identitas Lembaga | Guru (`guru`) | 1. Login sebagai Guru<br>2. Cek sidebar menu<br>3. Akses direct URL `GET /settings` & `POST /settings/update` | Menu tidak tampil di sidebar. Akses direct URL menghasilkan HTTP 403 Forbidden ("Akses Ditolak"). | PASS |
| **BR-04** | Siswa tidak dapat mengubah Identitas Lembaga | Siswa (`siswa`) | 1. Login sebagai Siswa<br>2. Cek sidebar menu<br>3. Akses direct URL `GET /settings` & `POST /settings/update` | Menu tidak tampil di sidebar. Akses direct URL menghasilkan HTTP 403 Forbidden ("Akses Ditolak"). | PASS |
| **BR-05** | Ubah nama lembaga | Super Admin | 1. Ubah `Nama Lembaga` menjadi "Yayasan Pesantren Pertanian Darul Falah"<br>2. Klik Simpan | Flash message sukses muncul. Di database `app_settings.setting_value` terupdate. | PASS |
| **BR-06** | Ubah nama singkat / brand | Super Admin | 1. Ubah `Nama Singkat` menjadi "Darul Falah"<br>2. Klik Simpan | Nilai baru tersimpan. Sidebar header & header title bar segera berubah. | PASS |
| **BR-07** | Ubah tagline | Super Admin | 1. Ubah `Tagline` menjadi "Mencetak Generasi Qur'ani Berakhlak Mulia"<br>2. Klik Simpan | Nilai baru tersimpan dan terefleksikan di landing page. | PASS |
| **BR-08** | Upload logo PNG | Super Admin | 1. Unggah file logo berekstensi `.png`<br>2. Klik Simpan | File tersimpan di `uploads/branding/` dengan nama aman terenkripsi. Path tersimpan di DB. Preview tampil. | PASS |
| **BR-09** | Upload logo JPG/JPEG | Super Admin | 1. Unggah file logo berekstensi `.jpg` / `.jpeg`<br>2. Klik Simpan | Berhasil tersimpan di `uploads/branding/`. Logo lama dihapus dari filesystem. | PASS |
| **BR-10** | Upload logo WEBP | Super Admin | 1. Unggah file logo berekstensi `.webp`<br>2. Klik Simpan | Berhasil tersimpan di `uploads/branding/`. Logo baru aktif di seluruh portal. | PASS |
| **BR-11** | Reject file invalid | Super Admin | 1. Unggah file berbahaya / berekstensi `.php`, `.exe`, `.pdf`<br>2. Submit form | Sistem menolak unggahan, flash message error menampilkan pesan tipe file tidak diizinkan. | PASS |
| **BR-12** | Reject file terlalu besar | Super Admin | 1. Unggah file logo berukuran > 2MB (misal: 3MB)<br>2. Submit form | Ditolak oleh validasi client & server (Upload_handler max_size 2048 KB). | PASS |
| **BR-13** | Logo tampil di landing page | Public / Guest | Buka `/landing` atau `/` setelah logo diperbarui | Logo baru lembaga tampil di Navbar, Hero Section, dan Footer landing page. | PASS |
| **BR-14** | Nama lembaga tampil di landing page | Public / Guest | Buka `/landing` atau `/` setelah nama diubah | Nama lengkap lembaga tampil di Hero Heading, CTA Section, dan Copyright Footer. | PASS |
| **BR-15** | Tagline tampil di landing page | Public / Guest | Buka `/landing` atau `/` setelah tagline diubah | Tagline baru tampil pada badge hero section dan meta deskripsi. | PASS |
| **BR-16** | Branding tampil di authenticated layout | All Logged In Users | Login sebagai admin / guru / siswa dan buka dashboard | Header sidebar menampilkan logo baru dan nama singkat lembaga. Tag `<title>` memuat nama lembaga. | PASS |
| **BR-17** | Refresh browser dan branding tetap tersimpan | All Users | Lakukan hard refresh (`Ctrl + F5`) pada browser | Konfigurasi branding tetap konsisten dan tidak kembali ke default (persistent di DB). | PASS |
| **BR-18** | Mobile responsive | Mobile Viewport | Buka landing page & halaman settings di layar smartphone (<768px) | Layout stack 1 kolom rapi, navbar hamburger / action button proporsional, form tidak overflow horizontal. | PASS |
| **BR-19** | CSRF Protection | Security | Kirim request `POST /settings/update` tanpa CSRF token | Request ditolak dengan error HTTP 403 Forbidden / CSRF Mismatch. | PASS |
| **BR-20** | Direct URL authorization | Unauthenticated | Buka URL `GET /settings` tanpa login | Otomatis di-redirect ke halaman `/login`. | PASS |

---

## 2. End-to-End Test (BR-E2E-01)

### Skenario: Super Admin Mengubah Identitas Lembaga Secara Penuh & Verifikasi Publik
1. **Langkah 1 (Login)**: Super Admin masuk melalui `/login`.
2. **Langkah 2 (Navigasi)**: Klik menu `Pengaturan -> Identitas Lembaga` (`/settings`).
3. **Langkah 3 (Input Data)**:
   - Unggah Logo: `logo_darul_falah.png`
   - Nama Lembaga: `Yayasan Pesantren Pertanian Darul Falah`
   - Nama Singkat: `Darul Falah`
   - Tagline: `Sistem Monitoring Hafalan Al-Qur'an & Penilaian Tajwid Santri`
4. **Langkah 4 (Simpan)**: Klik tombol `Simpan Perubahan`. Muncul notifikasi "Identitas lembaga berhasil diperbarui."
5. **Langkah 5 (Verifikasi Landing Page)**:
   - Buka `/landing` di tab incognito / browser.
   - Navbar menampilkan `[LOGO] Darul Falah`.
   - Hero menampilkan `Yayasan Pesantren Pertanian Darul Falah` dan tagline baru.
   - Footer menampilkan copyright `Yayasan Pesantren Pertanian Darul Falah`.
6. **Langkah 6 (Verifikasi Authenticated)**:
   - Kembali ke akun login.
   - Sidebar header menampilkan `[LOGO] Darul Falah`.
   - Title browser menampilkan `Dashboard Utama - Darul Falah`.

---

## 3. Catatan Keamanan & Backward Compatibility
- **Server-side Authorization**: Controller `Settings.php` menggunakan `$this->require_role(array('admin'))` di constructor.
- **Upload Hardening**: Enkripsi nama file (`encrypt_name = TRUE`), MIME whitelist di `mimes.php`, batas ukuran 2MB.
- **Atomic Upsert**: `Setting_model` menangani fallback skema secara transparan sehingga aman untuk database fresh maupun migrasi database lama.
