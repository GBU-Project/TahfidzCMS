-- =====================================================================
-- MIGRATION: Rapor Publik untuk Orangtua (access_token) + Statistik
-- Jamaah (tidak perlu migrasi DB, murni query agregat read-only)
-- =====================================================================
-- Jalankan HANYA jika database sudah pernah di-install sebelumnya.
-- Instalasi BARU cukup import tahfidzcms.sql yang sudah memuat kolom ini.
-- =====================================================================

ALTER TABLE `siswa`
  ADD COLUMN `access_token` CHAR(32) NULL
    COMMENT 'Token acak (hex) untuk link rapor publik /rapor/{token}, dibaca orangtua TANPA login. NULL sampai pertama kali di-generate oleh admin/guru.'
    AFTER `badge`;

ALTER TABLE `siswa` ADD UNIQUE KEY `uq_access_token` (`access_token`);

-- Selesai. Token akan ter-generate otomatis satu-per-satu saat admin/guru
-- pertama kali membuka tombol "Bagikan ke Orangtua" di halaman siswa —
-- TIDAK perlu di-generate massal lewat SQL, karena setiap token harus
-- benar-benar acak & unik (dibuat via random_bytes() di PHP, bukan di SQL).
