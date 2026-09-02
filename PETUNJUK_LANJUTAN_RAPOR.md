# Petunjuk Lanjutan — Rapor Publik (Orangtua) & Statistik (Jamaah)

Detail lengkap desain & keputusan produk ada di `docs/BLUEPRINT.md` §11 — baca dulu.

## Cara deploy

1. Extract, timpa file dengan path yang sama.
2. **WAJIB** migrasi database:
   - Instalasi BARU: cukup import `database/tahfidzcms.sql` (sudah termasuk kolom baru).
   - Instalasi EXISTING: jalankan `database/migration_rapor_publik.sql` manual.
3. `php -l` semua file PHP di atas sebelum production (belum diverifikasi dengan PHP
   interpreter sungguhan, hanya brace-balance check).

## Yang WAJIB diuji manual (belum sempat diuji end-to-end)

- [ ] Buka `/users?role=siswa`, klik "Buat Rapor" pada satu siswa → token ter-generate,
      tombol berubah jadi "Rapor".
- [ ] Klik "Rapor" → modal muncul dengan link + QR code yang benar.
- [ ] Buka link `/rapor/{token}` di tab baru (idealnya browser lain/incognito, simulasikan
      orangtua yang belum login) → halaman rapor tampil dengan data yang benar, TANPA
      diminta login.
- [ ] Scan QR code dengan HP sungguhan → mengarah ke link yang sama.
- [ ] Klik "Regenerasi Tautan" → link LAMA yang tadi dibuka harus jadi 404 kalau dibuka
      ulang, link BARU (yang muncul di modal setelah regenerasi) harus berfungsi.
- [ ] Buka `/statistik` → tidak error meski database kosong (siswa baru, belum ada
      setoran sama sekali) — cek terutama bagian chart tidak crash karena divide-by-zero
      atau array kosong.
- [ ] Cek dari landing page (`/landing` atau `/`), link "Statistik" di nav mengarah
      dengan benar ke `/statistik`.
- [ ] Buka `/rapor/tokenyangsalah123` (format token salah) dan
      `/rapor/aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa` (format benar tapi tidak ada di DB) —
      keduanya harus menampilkan 404 standar, bukan error PHP atau pesan yang beda
      (supaya tidak membocorkan mana yang "hampir benar").

## Yang perlu diketahui

- Chart.js & qrcodejs dimuat dari CDN `cdnjs.cloudflare.com` — sama seperti Font Awesome
  yang sudah dipakai sebelumnya di project. Kalau nanti mau audit "supply chain"
  (dependency CDN pihak ketiga), kedua library ini perlu masuk daftar.
- Token siswa (`access_token`) TIDAK di-generate otomatis massal saat migrasi — sengaja
  NULL sampai admin/guru pertama kali klik "Buat Rapor" per siswa. Kalau ingin generate
  massal untuk semua siswa existing, perlu script terpisah (belum dibuat) yang looping
  tiap NISN dan panggil `Siswa_model::generate_access_token()`.
- Halaman statistik publik sengaja TIDAK menampilkan data per-siswa apapun — kalau ada
  permintaan fitur tambahan di halaman ini nanti, pertahankan batasan privasi ini
  (sudah ditulis eksplisit sebagai warning di docblock `Statistik.php`).
