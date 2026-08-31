# Petunjuk Lanjutan — Implementasi Kriteria Penilaian Tahfidz

Dokumen ini untuk siapapun (developer atau AI agent) yang melanjutkan pekerjaan
migrasi sistem penilaian ke skema baru (Ziyadah/Muroja'ah/QC). Baca ini dulu
sebelum menyentuh kode, supaya tidak mengulang analisis dari nol.

## 1. Konteks singkat

Sistem penilaian lama (`nilai` A/B/C + `status` Lancar/Cukup/Perlu Perbaikan)
diganti total dengan sistem sesuai dokumen resmi `KRITERIA_PENILAIAN_TAHFIDZ.docx`
(tidak disimpan di repo — kalau perlu rujukan ulang, minta ke pemilik produk).

Aturan intinya sudah didokumentasikan lengkap di **`docs/BLUEPRINT.md` bagian
"8. Fase 8 — Kriteria Penilaian Tahfidz"** — baca bagian itu dulu untuk detail
aturan bisnis (ambang batas kesalahan per jenis setoran, matriks skor, dst.).
Jangan duplikasi penjelasan itu di sini, cukup rujuk ke sana.

## 2. Cara deploy paket ini

1. Extract ZIP ini, timpa file dengan path yang sama di project Anda.
2. **Jika project sudah pernah di-install & punya data existing**: jalankan
   `database/migration_kriteria_penilaian.sql` secara manual (baca instruksi
   di dalam file itu, termasuk langkah backup sebelum jalan).
3. **Jika instalasi baru**: cukup import `database/tahfidzcms.sql` seperti biasa,
   TIDAK PERLU jalankan file migrasi.
4. Belum ada langkah build/compile tambahan — ini PHP native, langsung jalan.

## 3. Status pekerjaan (JANGAN percaya begitu saja, verifikasi ulang)

Checklist detail ada di `docs/BLUEPRINT.md` §8b. Ringkasan cepat:

✅ **Backend sudah selesai**: library kalkulasi (`Poin_calculator.php`), skema
   database, model (`Setoran_model.php`), dan SEMUA controller (`Setoran`,
   `api/Setoran`, `Penilaian`, `Dashboard`, `api/Dashboard`, `Riwayat`,
   `api/Riwayat`, `Laporan`) sudah disesuaikan ke field baru.

❌ **View/frontend BELUM disentuh sama sekali** — ini pekerjaan prioritas
   berikutnya. Tanpa ini, field baru tidak bisa diinput dari UI:
   - `application/views/setoran/form.php`
   - `application/views/penilaian/index.php`
   - `application/views/riwayat/index.php`
   - `application/views/dashboard/index.php`
   - `application/views/progress/index.php` & `leaderboard/index.php` (belum diaudit sama sekali)

❌ **Belum ada testing** — tidak ada PHP interpreter di environment kerja saya
   saat mengerjakan ini, jadi verifikasi sintaks hanya lewat brace-balance
   checking manual (Python), BUKAN `php -l` sungguhan. **Wajib jalankan
   `php -l` di environment yang punya PHP sebelum deploy ke production**,
   dan idealnya uji end-to-end (buka form, submit, cek DB).

## 4. Bug yang sempat ditemukan & diperbaiki selama proses ini

`application/libraries/Poin_calculator.php` sempat punya method
`get_daftar_surat()` **terduplikasi** (sisa dari proses `str_replace` yang
kurang bersih), dengan brace penutup class yang salah tempat di tengah file
— ini akan menyebabkan **fatal PHP error** kalau ter-deploy tanpa dicek.
Sudah diperbaiki dan diverifikasi ulang (brace balance + depth tracking).
**Pelajaran untuk siapapun yang lanjut edit file ini**: setelah `str_replace`
pada blok besar, selalu `view` ulang file secara penuh untuk pastikan tidak
ada sisa duplikat, jangan hanya percaya pesan "Successfully replaced".

## 5. Panduan mengerjakan sisa view (untuk yang melanjutkan)

Referensi field baru yang tersedia di objek `$setoran`/row hasil query:
`jenis_setoran` (ziyadah/murojaah/qc), `jumlah_kesalahan` (int),
`kualitas_bacaan` (baik/kurang_baik), `keterangan` (L/CL/KL/TL), `skor`
(100/95/90/85/80/75/60), `hasil_qc` (layak_tasmi/belum_layak/NULL), `poin`.

Label siap pakai ada sebagai konstanta di `Poin_calculator`:
`JENIS_SETORAN_LABEL`, `KETERANGAN_LABEL`, `HASIL_QC_LABEL` — load library ini
di controller lalu pass ke view, jangan hardcode ulang label di view.

**Form Setoran & modal Penilaian** — urutan input yang disarankan:
1. Dropdown `jenis_setoran` (pakai `JENIS_SETORAN_LABEL`)
2. Input number `jumlah_kesalahan` (min 0)
3. Radio/select `kualitas_bacaan` (Baik / Kurang Baik)
4. **Jika `jenis_setoran === 'qc'`**: tampilkan dropdown `hasil_qc` (pakai JS
   show/hide, required hanya saat kondisi ini true)
5. Skor & keterangan **sebaiknya tidak diinput manual** — kalau mau preview
   real-time, replikasi logika `Poin_calculator::hitung_keterangan()` dan
   `hitung_skor()` di JS (constant-nya sederhana, tinggal port). Kalkulasi
   final tetap dilakukan ulang di server (sudah otomatis lewat controller),
   jadi preview JS murni untuk UX, bukan sumber kebenaran.

**Riwayat & filter** — ganti dropdown filter `status` lama jadi filter
`keterangan` (L/CL/KL/TL) dan/atau `jenis_setoran`. Badge warna: pertimbangkan
skema warna baru karena sekarang ada 4 kategori (L/CL/KL/TL), bukan 3.

## 6. Kontak balik / pertanyaan terbuka

Kalau ketemu kasus ambigu yang tidak dijawab oleh `KRITERIA_PENILAIAN_TAHFIDZ.docx`
maupun catatan di blueprint, JANGAN improvisasi aturan bisnis baru — tanyakan
dulu ke pemilik produk. Yang sudah dikonfirmasi eksplisit sejauh ini:
- Skor & keterangan dihitung OTOMATIS oleh sistem (bukan manual guru).
- Data lama di-migrasi (bukan diarsipkan terpisah).
- Poin leaderboard = skor apa adanya, tidak ada bobot ekstra untuk QC.
