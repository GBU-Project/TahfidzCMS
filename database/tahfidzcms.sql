-- =====================================================================
-- TAHFIDZCMS - Skema Database (MySQL / MariaDB)
-- Migrasi dari struktur Google Sheets (Apps Script) ke RDBMS
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `tahfidzcms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tahfidzcms`;

-- ---------------------------------------------------------------------
-- 1. TABEL KELAS
-- Pengganti sheet 'kelas'
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nama_kelas` VARCHAR(20)     NOT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nama_kelas` (`nama_kelas`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. TABEL USERS
-- Pengganti sheet 'users'. Menyimpan admin, guru, DAN siswa
-- (siswa juga login pakai NISN, jadi tetap dicatat di sini sebagai akun).
-- Password WAJIB di-hash (bcrypt) di level aplikasi, bukan plain text.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`         INT UNSIGNED                         NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(100)                         NOT NULL,
  `foto`       VARCHAR(255)                         NULL COMMENT 'Path file foto profil, mis. uploads/profile/xxx.jpg',
  `username`   VARCHAR(50)                          NOT NULL COMMENT 'NIP untuk admin/guru, NISN untuk siswa',
  `password`   VARCHAR(255)                         NOT NULL COMMENT 'Disimpan dalam bentuk hash (password_hash)',
  `role`       ENUM('admin','guru','siswa')         NOT NULL,
  `is_active`  TINYINT(1)                           NOT NULL DEFAULT 1,
  `created_at` DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME                             NULL     ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2b. TABEL API_TOKENS
-- Autentikasi untuk jalur API (mis. konsumsi dari aplikasi mobile),
-- terpisah dari session web. Token diterbitkan saat login via /api/auth/login.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `api_tokens`;
CREATE TABLE `api_tokens` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `token`      VARCHAR(64)  NOT NULL COMMENT 'Random string, mis. bin2hex(random_bytes(32))',
  `expired_at` DATETIME     NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_api_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2c. TABEL LOGIN_ATTEMPTS (rate limiting brute-force api/auth/login)
-- Mencatat percobaan login GAGAL saja, per kombinasi username+IP.
-- Dibersihkan otomatis via WHERE attempted_at, tidak perlu cron terpisah.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`     VARCHAR(100) NOT NULL,
  `ip_address`   VARCHAR(45)  NOT NULL COMMENT 'Mendukung IPv4 & IPv6',
  `attempted_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username_ip_time` (`username`, `ip_address`, `attempted_at`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2a. TABEL GURU_KELAS (relasi many-to-many guru <-> kelas)
-- Di Apps Script ini disimpan sebagai string "7A,7B" di kolom 'kelas'.
-- Dipecah jadi tabel relasi agar bisa di-query dengan JOIN, bukan parsing string.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `guru_kelas`;
CREATE TABLE `guru_kelas` (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`  INT UNSIGNED NOT NULL COMMENT 'FK ke users.id, role = guru',
  `kelas_id` INT UNSIGNED NOT NULL COMMENT 'FK ke kelas.id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_guru_kelas` (`user_id`, `kelas_id`),
  CONSTRAINT `fk_guru_kelas_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_guru_kelas_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. TABEL SISWA
-- Pengganti sheet 'siswa'. NISN sebagai Primary Key -> duplikasi NISN
-- (masalah utama di versi Apps Script) tidak mungkin terjadi lagi.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa` (
  `nisn`        VARCHAR(20)     NOT NULL,
  `user_id`     INT UNSIGNED    NULL COMMENT 'FK ke users.id (akun login siswa)',
  `nama`        VARCHAR(100)    NOT NULL,
  `kelas_id`    INT UNSIGNED    NOT NULL,
  `target_juz`  TINYINT UNSIGNED NOT NULL DEFAULT 30,
  `total_poin`  INT             NOT NULL DEFAULT 0,
  `badge`       VARCHAR(50)     NOT NULL DEFAULT 'Pemula',
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME        NULL     ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`nisn`),
  KEY `idx_kelas` (`kelas_id`),
  KEY `idx_total_poin` (`total_poin`),
  CONSTRAINT `fk_siswa_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE SET NULL,
  CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. TABEL SETORAN
-- Pengganti sheet 'setoran'. Ini tabel transaksi utama.
-- id_setoran pakai AUTO_INCREMENT (bukan UUID manual) supaya urut & efisien untuk index.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `setoran`;
CREATE TABLE `setoran` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_setoran`     VARCHAR(20)     NOT NULL COMMENT 'Kode tampilan, contoh: STR-0001',
  `nisn`             VARCHAR(20)     NOT NULL,
  `kelas_id`         INT UNSIGNED    NOT NULL COMMENT 'Disimpan juga di sini (denormalisasi) agar histori kelas tidak berubah jika siswa pindah kelas',
  `tanggal`          DATE            NOT NULL,
  `waktu`            TIME            NOT NULL,
  `juz`              TINYINT UNSIGNED NOT NULL,
  `surat`             VARCHAR(50)     NOT NULL,
  `ayat_dari`        SMALLINT UNSIGNED NOT NULL,
  `ayat_sampai`      SMALLINT UNSIGNED NOT NULL,
  `jenis_setoran`    ENUM('ziyadah','murojaah','qc') NOT NULL DEFAULT 'ziyadah'
                       COMMENT 'Menentukan ambang batas kesalahan yang dipakai: ziyadah=per halaman, murojaah=per juz, qc=per 2 halaman',
  `jumlah_kesalahan` SMALLINT UNSIGNED NOT NULL DEFAULT 0
                       COMMENT 'Input manual guru saat simak; dasar perhitungan otomatis kolom keterangan',
  `kualitas_bacaan`  ENUM('baik','kurang_baik') NOT NULL DEFAULT 'baik'
                       COMMENT 'Kualitas Makhraj/Tajwid/Sifatul Huruf; dasar perhitungan otomatis kolom skor',
  `keterangan`       ENUM('L','CL','KL','TL') NOT NULL
                       COMMENT 'Dihitung otomatis dari jumlah_kesalahan + jenis_setoran, lihat Poin_calculator::hitung_keterangan()',
  `skor`             TINYINT UNSIGNED NOT NULL
                       COMMENT 'Dihitung otomatis dari keterangan + kualitas_bacaan (100/95/90/85/80/75/60), lihat Poin_calculator::hitung_skor()',
  `hasil_qc`         ENUM('layak_tasmi','belum_layak') NULL
                       COMMENT 'Wajib diisi manual oleh guru HANYA jika jenis_setoran = qc; NULL untuk ziyadah/murojaah',
  `poin`             INT             NOT NULL DEFAULT 0 COMMENT 'Poin leaderboard = skor apa adanya, sama rata semua jenis setoran',
  `catatan`          TEXT            NULL,
  `audio_bukti`      VARCHAR(255)    NULL COMMENT 'Path file rekaman audio bukti setoran, mis. uploads/setoran_audio/xxx.mp3',
  `durasi_audio`     SMALLINT UNSIGNED NULL COMMENT 'Durasi rekaman dalam detik',
  `guru_pengoreksi_id` INT UNSIGNED  NULL COMMENT 'FK ke users.id, role = guru',
  `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kode_setoran` (`kode_setoran`),
  KEY `idx_nisn` (`nisn`),
  KEY `idx_kelas` (`kelas_id`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_guru` (`guru_pengoreksi_id`),
  KEY `idx_jenis_setoran` (`jenis_setoran`),
  CONSTRAINT `fk_setoran_siswa` FOREIGN KEY (`nisn`) REFERENCES `siswa`(`nisn`) ON DELETE CASCADE,
  CONSTRAINT `fk_setoran_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_setoran_guru`  FOREIGN KEY (`guru_pengoreksi_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- NOTE PENTING:
-- Tabel 'leaderboard' dan 'sessions' dari versi Apps Script SENGAJA
-- TIDAK dibuatkan tabel fisik:
--
-- - leaderboard -> dihitung on-the-fly (server sudah dikonfirmasi
--   MySQL 8+ / MariaDB 10.2+, jadi window function aman dipakai):
--
--     SELECT s.nisn, s.nama, s.kelas_id, s.total_poin,
--            RANK() OVER (PARTITION BY s.kelas_id ORDER BY s.total_poin DESC) AS ranking_kelas,
--            RANK() OVER (ORDER BY s.total_poin DESC) AS ranking_global
--     FROM siswa s;
--
--   Ini menghindari data leaderboard "basi" karena lupa di-refresh manual,
--   dan query yang sama bisa dipakai baik oleh controller web maupun api.
--
-- - sessions (web) -> pakai session bawaan CodeIgniter 3.
-- - auth api -> pakai tabel 'api_tokens' di atas (Bearer token),
--   terpisah dari session web karena dikonsumsi client yang berbeda
--   (mis. aplikasi mobile).
-- ---------------------------------------------------------------------

-- ---------------------------------------------------------------------
-- 5. TABEL APP_SETTINGS
-- Konfigurasi identitas lembaga dan pengaturan global aplikasi
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `app_settings`;
CREATE TABLE `app_settings` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(100) NOT NULL,
  `setting_value` TEXT         NULL,
  `setting_type`  VARCHAR(50)  NOT NULL DEFAULT 'text',
  `updated_at`    DATETIME     NULL     ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_setting_key` (`setting_key`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- SEED DATA AWAL (opsional, setara setupInitialData() di Apps Script)
-- ---------------------------------------------------------------------

INSERT INTO `app_settings` (`setting_key`, `setting_value`, `setting_type`) VALUES
('institution_name', 'TahfidzCMS', 'text'),
('institution_short_name', 'TahfidzCMS', 'text'),
('institution_tagline', 'Sistem Monitoring Hafalan Al-Qur\'an', 'text'),
('institution_logo', '', 'image');


INSERT INTO `kelas` (`nama_kelas`) VALUES ('7A'), ('7B'), ('8A'), ('8B');

-- Password di bawah ini HARUS diganti dengan hash bcrypt sesungguhnya
-- saat di-generate lewat aplikasi (password_hash('123456', PASSWORD_BCRYPT)).
-- Contoh hash untuk '123456' (ilustrasi saja, generate ulang di aplikasi Anda):
INSERT INTO `users` (`nama`, `username`, `password`, `role`) VALUES
('Super Admin',       '197501012005011001', '$2y$10$CONTOH_HASH_GANTI_INI', 'admin'),
('Ust. Ahmad Yusuf',  '197802022010011002', '$2y$10$CONTOH_HASH_GANTI_INI', 'guru'),
('Ust. Budi Santoso', '198003032012011003', '$2y$10$CONTOH_HASH_GANTI_INI', 'guru');

INSERT INTO `guru_kelas` (`user_id`, `kelas_id`)
SELECT u.id, k.id FROM users u, kelas k
WHERE u.username = '197802022010011002' AND k.nama_kelas IN ('7A','7B');

INSERT INTO `guru_kelas` (`user_id`, `kelas_id`)
SELECT u.id, k.id FROM users u, kelas k
WHERE u.username = '198003032012011003' AND k.nama_kelas IN ('8A','8B');

-- Siswa contoh (akun login siswa dibuat juga di tabel users, lalu ditautkan)
INSERT INTO `users` (`nama`, `username`, `password`, `role`) VALUES
('Fulan bin Fulan', '1001', '$2y$10$CONTOH_HASH_GANTI_INI', 'siswa'),
('Ahmad Fauzi',      '1002', '$2y$10$CONTOH_HASH_GANTI_INI', 'siswa');

INSERT INTO `siswa` (`nisn`, `user_id`, `nama`, `kelas_id`, `target_juz`, `total_poin`, `badge`)
SELECT '1001', u.id, 'Fulan bin Fulan', k.id, 30, 0, 'Pemula'
FROM users u, kelas k WHERE u.username = '1001' AND k.nama_kelas = '7A';

INSERT INTO `siswa` (`nisn`, `user_id`, `nama`, `kelas_id`, `target_juz`, `total_poin`, `badge`)
SELECT '1002', u.id, 'Ahmad Fauzi', k.id, 30, 0, 'Pemula'
FROM users u, kelas k WHERE u.username = '1002' AND k.nama_kelas = '7A';

SET FOREIGN_KEY_CHECKS = 1;
