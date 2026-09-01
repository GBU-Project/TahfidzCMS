# Petunjuk Lanjutan — Follow-up XSS & Misc (Round 3)

Paket ini berisi tindak lanjut dari catatan review: "Yang masih belum ditangani (minor)".
Detail lengkap ada di `docs/BLUEPRINT.md` §10 — baca dulu untuk konteks.

## Ringkasan super singkat

- **14 titik XSS output** (NISN + path logo/foto) di 11 view sekarang di-escape dengan
  `htmlspecialchars()`.
- **Validasi NISN/username diperketat** ke `alpha_dash` (defense-in-depth di sumber, bukan
  cuma di output).
- **`csrf_exclude_uris` dibersihkan** dari redundansi.
- **`log_threshold` diaktifkan** (dari mati total ke "Error Messages saja") — **PENTING**:
  folder `application/logs/` sebelumnya tidak ada di repo, sekarang sudah dibuat (dengan
  `index.html` placeholder). Pastikan folder ini **writable oleh web server** (`chmod 755`
  atau sesuaikan owner) setelah deploy, atau logging akan gagal diam-diam.

## Yang SENGAJA belum disentuh (baca alasannya di blueprint §10)

- `global_xss_filtering` tetap `FALSE` — ini keputusan sadar (output escaping kontekstual
  lebih reliable daripada filter global CI3 yang usang), bukan kelalaian.
- `encryption_key` untuk instalasi existing — tetap perlu regenerasi manual, lihat
  `PETUNJUK_LANJUTAN_SECURITY.md` dari paket sebelumnya.
- CDN Tailwind, password reset `123456` plaintext, file yatim saat insert gagal — belum
  ditindaklanjuti (risiko rendah, perlu keputusan produk/perubahan lebih besar).

## Wajib diuji manual

- [ ] Buat user baru dengan NISN yang sengaja berisi karakter aneh (mis. `123<b>456`) —
      pastikan DITOLAK oleh validasi `alpha_dash` dengan pesan error yang jelas, bukan
      malah lolos tersimpan.
- [ ] Pastikan NISN yang SAH (huruf, angka, underscore, dash — format NISN Indonesia
      standar 10 digit angka pasti lolos) tetap bisa dipakai normal seperti biasa.
- [ ] Setelah deploy, cek `application/logs/` menghasilkan file log saat terjadi error
      (trigger error sengaja, misal akses halaman yang tidak ada) — pastikan permission
      folder benar dan tidak menyebabkan fatal error karena tidak writable.
