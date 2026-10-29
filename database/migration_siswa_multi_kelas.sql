-- =====================================================================
-- MIGRASI: satu santri boleh ikut BEBERAPA kelas/halaqah
-- (mis. Hafalan, Murojaah, Quality Control), dengan poin & badge
-- TERPISAH per kelas.
--
-- Aman dijalankan sekali pada database yang sudah berjalan. BACKUP DULU.
--
-- Ringkasan:
--   * Tabel baru `siswa_kelas` (nisn, kelas_id, total_poin, badge)
--   * Data lama disalin: tiap siswa -> 1 baris di siswa_kelas dengan
--     kelas, poin, dan badge-nya yang sekarang
--   * Poin per kelas dihitung ulang dari tabel setoran (sumber kebenaran)
--   * siswa.kelas_id tetap ada sebagai "kelas utama"; siswa.total_poin &
--     siswa.badge menjadi RINGKASAN gabungan semua kelas (dijaga oleh kode)
-- =====================================================================

START TRANSACTION;

CREATE TABLE IF NOT EXISTS `siswa_kelas` (
  `nisn`       VARCHAR(20)  NOT NULL,
  `kelas_id`   INT UNSIGNED NOT NULL,
  `total_poin` INT          NOT NULL DEFAULT 0,
  `badge`      VARCHAR(50)  NOT NULL DEFAULT 'Pemula',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nisn`, `kelas_id`),
  KEY `idx_sk_kelas` (`kelas_id`),
  KEY `idx_sk_poin` (`total_poin`),
  CONSTRAINT `fk_sk_siswa` FOREIGN KEY (`nisn`)     REFERENCES `siswa`(`nisn`) ON DELETE CASCADE,
  CONSTRAINT `fk_sk_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas`(`id`)   ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 1. Salin keanggotaan kelas yang ada sekarang.
INSERT IGNORE INTO `siswa_kelas` (`nisn`, `kelas_id`, `total_poin`, `badge`)
SELECT `nisn`, `kelas_id`, `total_poin`, `badge` FROM `siswa`;

-- 2. Pastikan setiap kelas yang pernah dipakai di setoran juga terdaftar.
INSERT IGNORE INTO `siswa_kelas` (`nisn`, `kelas_id`, `total_poin`, `badge`)
SELECT DISTINCT `nisn`, `kelas_id`, 0, 'Pemula' FROM `setoran`;

-- 3. Hitung ulang poin per kelas dari setoran.
UPDATE `siswa_kelas` sk
LEFT JOIN (
  SELECT `nisn`, `kelas_id`, SUM(`poin`) AS total
  FROM `setoran` GROUP BY `nisn`, `kelas_id`
) x ON x.nisn = sk.nisn AND x.kelas_id = sk.kelas_id
SET sk.total_poin = GREATEST(COALESCE(x.total, 0), 0);

-- 3b. Badge per kelas (ambang sama dengan Poin_calculator::BADGE_LEVELS).
UPDATE `siswa_kelas` SET `badge` = CASE
  WHEN `total_poin` >= 3000 THEN 'Bintang Tahfidz'
  WHEN `total_poin` >= 1500 THEN 'Hafidz Muda'
  WHEN `total_poin` >= 500  THEN 'Mujahid'
  ELSE 'Pemula' END;

-- 4. Ringkasan gabungan di siswa = jumlah semua kelas.
UPDATE `siswa` s
JOIN (
  SELECT `nisn`, SUM(`total_poin`) AS total FROM `siswa_kelas` GROUP BY `nisn`
) x ON x.nisn = s.nisn
SET s.total_poin = x.total,
    s.badge = CASE
      WHEN x.total >= 3000 THEN 'Bintang Tahfidz'
      WHEN x.total >= 1500 THEN 'Hafidz Muda'
      WHEN x.total >= 500  THEN 'Mujahid'
      ELSE 'Pemula' END;

COMMIT;
