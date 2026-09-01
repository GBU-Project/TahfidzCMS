# Petunjuk Lanjutan — Perbaikan Keamanan (Audit Round 2)

## Isi paket ini

21 file: 4 fix Prioritas Tinggi (H1-H4) + 6 fix Prioritas Menengah (M1-M6) dari audit
keamanan independen, plus update `docs/BLUEPRINT.md` (bagian "9. Fase 9").

Detail lengkap tiap temuan & fix ada di `docs/BLUEPRINT.md` §9a — baca itu dulu untuk
konteks, jangan cuma percaya nama file di sini.

## Cara deploy

1. Extract ZIP ini, timpa file dengan path yang sama di project Anda.
2. **WAJIB** jalankan `php -l <file>` untuk semua file di atas sebelum deploy ke
   production — verifikasi saya HANYA brace/paren balance checking (Python), BUKAN
   `php -l` sungguhan, karena environment kerja saya tidak punya PHP interpreter.
3. Kalau ini instalasi BARU: tidak ada langkah tambahan, `encryption_key` acak akan
   otomatis ter-generate saat proses instalasi via web installer.
4. Kalau ini instalasi EXISTING (upgrade dari versi sebelumnya):
   - `encryption_key` di `config.php` TIDAK otomatis ter-generate ulang (installer
     tidak dijalankan lagi) — kalau mau, generate manual: ganti nilai
     `$config['encryption_key']` dengan string acak (mis. hasil
     `bin2hex(random_bytes(32))` dari `php -r "echo bin2hex(random_bytes(32));"`).
   - Semua user yang sedang login TETAP VALID (tidak ada perubahan skema session),
     tapi setelah update ini, session baru akan diregenerasi saat login berikutnya.

## WAJIB diuji manual sebelum production (belum sempat saya uji end-to-end)

- [ ] **Login web** — pastikan proses login tetap normal, tidak ada redirect loop akibat
      `sess_regenerate(TRUE)`. Coba juga skenario gagal login 6x berturut-turut dari IP
      yang sama — pastikan muncul pesan rate limit, dan reset otomatis setelah 15 menit
      atau setelah login berhasil.
- [ ] **Hapus/reset user, hapus kelas, hapus setoran** — pastikan tombol (sekarang jadi
      `<button type="submit">` di dalam `<form>`, bukan `<a>` lagi) masih terlihat &
      berfungsi sama seperti sebelumnya secara visual (saya pertahankan class CSS yang
      sama, tapi cek langsung di browser untuk pastikan tidak ada elemen yang "loncat"
      karena perbedaan default styling `<button>` vs `<a>`).
- [ ] **Upload foto profil & audio setoran** — pastikan file valid (jpg/png/webp untuk
      foto, mp3/wav/ogg/webm untuk audio) tetap lolos. Kalau ada user yang tiba-tiba
      upload-nya ditolak dengan pesan "Tipe konten berkas tidak valid", kemungkinan
      whitelist MIME di `Upload_handler.php` (`allowed_mimes`) perlu ditambah — cek MIME
      asli file tsb dulu (mis. pakai `file --mime-type namafile`) sebelum menambah ke
      whitelist, jangan asal broaden.
- [ ] **Guru tanpa kelas ditugaskan** — buat 1 akun guru test tanpa penugasan kelas apapun
      (kosongkan di `guru_kelas`), login, cek dashboard/riwayat/penilaian/laporan/
      leaderboard SEMUA menunjukkan "tidak ada data" — bukan data seluruh sekolah. Ini
      fix H1, paling kritis untuk diverifikasi.
- [ ] **Guru dengan kelas** — pastikan guru yang PUNYA kelas ditugaskan tetap bisa
      lihat & kerja normal seperti biasa (regresi check, pastikan fix H1 tidak
      mengganggu jalur guru normal).
- [ ] **Export laporan Excel** — buka file .xlsx & .xls hasil export, pastikan data
      normal (angka, teks) tetap tampil benar, tidak ada karakter aneh dari perubahan
      `sanitize_formula()`. Coba juga input catatan setoran yang diawali `=` (mis.
      "=SUM(A1:A10)") lalu export — pastikan di Excel muncul sebagai teks polos, BUKAN
      dieksekusi sebagai formula.

## Kalau ada masalah setelah deploy

Root cause H1 (guru tanpa kelas) diperbaiki di SATU tempat terpusat
(`_load_kelas_diizinkan()` di kedua base controller) — kalau ternyata masih ada
kebocoran data serupa di tempat lain, cek dulu apakah controller tsb benar-benar
extends `MY_Controller`/`MY_API_Controller` (bukan `CI_Controller` langsung), karena
fix ini hanya berlaku untuk controller yang lewat base class tersebut.
