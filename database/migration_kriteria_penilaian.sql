-- =====================================================================
-- MIGRATION: Kriteria Penilaian Tahfidz (Ziyadah / Muroja'ah / QC)
-- =====================================================================
-- Jalankan file ini HANYA jika database Anda sudah pernah di-install
-- sebelumnya (sudah punya tabel `setoran` dengan kolom lama `nilai` &
-- `status`). Untuk instalasi BARU, cukup import `tahfidzcms.sql` yang
-- sudah memuat skema final ini — TIDAK PERLU jalankan file ini.
--
-- Urutan aman: backup dulu tabel setoran sebelum menjalankan file ini.
--   mysqldump -u USER -p NAMA_DB setoran > backup_setoran_sebelum_migrasi.sql
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. Tambah kolom-kolom baru (nullable dulu supaya ALTER tidak gagal
--    walau tabel sudah berisi banyak baris data).
-- ---------------------------------------------------------------------
ALTER TABLE `setoran`
  ADD COLUMN `jenis_setoran`    ENUM('ziyadah','murojaah','qc') NULL
                                  COMMENT 'Menentukan ambang batas kesalahan yang dipakai: ziyadah=per halaman, murojaah=per juz, qc=per 2 halaman'
                                  AFTER `ayat_sampai`,
  ADD COLUMN `jumlah_kesalahan` SMALLINT UNSIGNED NULL
                                  COMMENT 'Input manual guru saat simak; dasar perhitungan otomatis kolom keterangan'
                                  AFTER `jenis_setoran`,
  ADD COLUMN `kualitas_bacaan`  ENUM('baik','kurang_baik') NULL
                                  COMMENT 'Kualitas Makhraj/Tajwid/Sifatul Huruf; dasar perhitungan otomatis kolom skor'
                                  AFTER `jumlah_kesalahan`,
  ADD COLUMN `keterangan`       ENUM('L','CL','KL','TL') NULL
                                  COMMENT 'Dihitung otomatis dari jumlah_kesalahan + jenis_setoran'
                                  AFTER `kualitas_bacaan`,
  ADD COLUMN `skor`             TINYINT UNSIGNED NULL
                                  COMMENT 'Dihitung otomatis dari keterangan + kualitas_bacaan (100/95/90/85/80/75/60)'
                                  AFTER `keterangan`,
  ADD COLUMN `hasil_qc`         ENUM('layak_tasmi','belum_layak') NULL
                                  COMMENT 'Wajib diisi manual oleh guru HANYA jika jenis_setoran = qc'
                                  AFTER `skor`;

-- ---------------------------------------------------------------------
-- 2. Migrasi data lama: semua setoran existing dianggap 'ziyadah'
--    (asumsi default paling umum di lapangan), jumlah_kesalahan diisi 0
--    karena tidak pernah dicatat sebelumnya (bukan berarti 0 kesalahan
--    sungguhan, hanya penanda "data historis, sebelum sistem kesalahan
--    diterapkan").
-- ---------------------------------------------------------------------
UPDATE `setoran` SET `jenis_setoran` = 'ziyadah' WHERE `jenis_setoran` IS NULL;
UPDATE `setoran` SET `jumlah_kesalahan` = 0 WHERE `jumlah_kesalahan` IS NULL;

-- Mapping nilai (A/B/C) + status (Lancar/Cukup/Perlu Perbaikan) lama
-- ke kualitas_bacaan + keterangan + skor baru:
--
--   nilai A + Lancar           -> kualitas=baik,        keterangan=L,  skor=100
--   nilai A + Cukup            -> kualitas=baik,        keterangan=CL, skor=90
--   nilai A + Perlu Perbaikan  -> kualitas=baik,        keterangan=KL, skor=80
--   nilai B + Lancar           -> kualitas=kurang_baik, keterangan=L,  skor=95
--   nilai B + Cukup            -> kualitas=kurang_baik, keterangan=CL, skor=85
--   nilai B + Perlu Perbaikan  -> kualitas=kurang_baik, keterangan=KL, skor=75
--   nilai C + (status apapun)  -> kualitas=kurang_baik, keterangan=TL, skor=60

UPDATE `setoran` SET `kualitas_bacaan` = 'baik',        `keterangan` = 'L',  `skor` = 100 WHERE `nilai` = 'A' AND `status` = 'Lancar';
UPDATE `setoran` SET `kualitas_bacaan` = 'baik',        `keterangan` = 'CL', `skor` = 90  WHERE `nilai` = 'A' AND `status` = 'Cukup';
UPDATE `setoran` SET `kualitas_bacaan` = 'baik',        `keterangan` = 'KL', `skor` = 80  WHERE `nilai` = 'A' AND `status` = 'Perlu Perbaikan';
UPDATE `setoran` SET `kualitas_bacaan` = 'kurang_baik', `keterangan` = 'L',  `skor` = 95  WHERE `nilai` = 'B' AND `status` = 'Lancar';
UPDATE `setoran` SET `kualitas_bacaan` = 'kurang_baik', `keterangan` = 'CL', `skor` = 85  WHERE `nilai` = 'B' AND `status` = 'Cukup';
UPDATE `setoran` SET `kualitas_bacaan` = 'kurang_baik', `keterangan` = 'KL', `skor` = 75  WHERE `nilai` = 'B' AND `status` = 'Perlu Perbaikan';
UPDATE `setoran` SET `kualitas_bacaan` = 'kurang_baik', `keterangan` = 'TL', `skor` = 60  WHERE `nilai` = 'C';

-- Jaga-jaga kombinasi nilai/status yang tidak terduga (seharusnya tidak
-- ada karena ENUM lama cuma A/B/C x Lancar/Cukup/Perlu Perbaikan), supaya
-- tidak ada baris tersisa dengan keterangan/skor NULL setelah migrasi:
UPDATE `setoran` SET `kualitas_bacaan` = 'kurang_baik', `keterangan` = 'KL', `skor` = 75
  WHERE `keterangan` IS NULL OR `skor` IS NULL;

-- Poin leaderboard baru = skor apa adanya.
UPDATE `setoran` SET `poin` = `skor`;

-- ---------------------------------------------------------------------
-- 3. Setelah semua baris terisi, kunci kolom yang wajib (NOT NULL).
-- ---------------------------------------------------------------------
ALTER TABLE `setoran`
  MODIFY COLUMN `jenis_setoran`    ENUM('ziyadah','murojaah','qc') NOT NULL DEFAULT 'ziyadah',
  MODIFY COLUMN `jumlah_kesalahan` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  MODIFY COLUMN `kualitas_bacaan`  ENUM('baik','kurang_baik') NOT NULL DEFAULT 'baik',
  MODIFY COLUMN `keterangan`       ENUM('L','CL','KL','TL') NOT NULL,
  MODIFY COLUMN `skor`             TINYINT UNSIGNED NOT NULL;
  -- `hasil_qc` sengaja TETAP nullable — hanya wajib diisi untuk jenis_setoran='qc'.

-- ---------------------------------------------------------------------
-- 4. Hapus kolom lama yang sudah tidak dipakai.
-- ---------------------------------------------------------------------
ALTER TABLE `setoran`
  DROP COLUMN `nilai`,
  DROP COLUMN `status`;

-- ---------------------------------------------------------------------
-- 5. Index tambahan untuk filter berdasarkan jenis setoran.
-- ---------------------------------------------------------------------
ALTER TABLE `setoran` ADD KEY `idx_jenis_setoran` (`jenis_setoran`);

-- =====================================================================
-- Selesai. Verifikasi cepat:
--   SELECT jenis_setoran, keterangan, skor, COUNT(*) FROM setoran GROUP BY 1,2,3;
-- =====================================================================
