-- =====================================================================
-- GABUNG AKUN SANTRI DUPLIKAT (satu anak diinput berkali-kali, mis. per kelas)
--
-- PRASYARAT: migration_siswa_multi_kelas.sql sudah dijalankan. BACKUP DULU.
--
-- Aturan: santri dianggap sama bila NAMA-nya sama (tanpa beda huruf besar/
-- kecil & spasi pinggir). Akun dengan NISN TERKECIL dipertahankan; akun lain:
--   * setorannya dipindah ke NISN yang dipertahankan (kelas & poin ikut),
--   * kelasnya didaftarkan ke santri yang dipertahankan,
--   * akun login + data siswa duplikatnya dihapus.
--
-- CARA PAKAI
--   1. Jalankan BAGIAN A (preview) saja. Periksa: apakah setiap pasangan
--      memang anak yang sama? (dua anak berbeda yang kebetulan senama
--      akan ikut tergabung — keluarkan dulu dari daftar bila ada)
--   2. Jalankan BAGIAN B. Skrip berakhir dengan ROLLBACK (percobaan, tidak
--      mengubah apa pun) dan menampilkan hasil akhirnya.
--   3. Jika hasil benar, ganti baris "ROLLBACK;" paling bawah menjadi
--      "COMMIT;" lalu jalankan BAGIAN B sekali lagi.
-- =====================================================================


-- ======================= BAGIAN A : PREVIEW ==========================
SELECT d.nisn AS nisn_duplikat, d.nama, d.kelas_id AS kelas_duplikat,
       c.nisn AS nisn_dipertahankan,
       (SELECT COUNT(*) FROM setoran WHERE nisn = d.nisn) AS setoran_dipindah
FROM siswa d
JOIN (
  SELECT UPPER(TRIM(nama)) AS nm, MIN(nisn) AS nisn
  FROM siswa GROUP BY UPPER(TRIM(nama)) HAVING COUNT(*) > 1
) c ON c.nm = UPPER(TRIM(d.nama)) AND c.nisn <> d.nisn
ORDER BY d.nama, d.nisn;


-- ======================= BAGIAN B : EKSEKUSI =========================
START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS map_dup;
CREATE TEMPORARY TABLE map_dup (
  dup_nisn   VARCHAR(20) NOT NULL PRIMARY KEY,
  canon_nisn VARCHAR(20) NOT NULL,
  dup_user   INT UNSIGNED NULL
);

INSERT INTO map_dup (dup_nisn, canon_nisn, dup_user)
SELECT d.nisn, c.nisn, d.user_id
FROM siswa d
JOIN (
  SELECT UPPER(TRIM(nama)) AS nm, MIN(nisn) AS nisn
  FROM siswa GROUP BY UPPER(TRIM(nama)) HAVING COUNT(*) > 1
) c ON c.nm = UPPER(TRIM(d.nama)) AND c.nisn <> d.nisn;

-- 1. Kelas milik duplikat -> daftarkan ke santri yang dipertahankan.
INSERT INTO siswa_kelas (nisn, kelas_id, total_poin, badge)
SELECT m.canon_nisn, sk.kelas_id, 0, 'Pemula'
FROM siswa_kelas sk JOIN map_dup m ON m.dup_nisn = sk.nisn
ON DUPLICATE KEY UPDATE nisn = siswa_kelas.nisn;

-- 2. Pindahkan semua setoran.
UPDATE setoran s JOIN map_dup m ON m.dup_nisn = s.nisn
SET s.nisn = m.canon_nisn;

-- 3. Hapus data siswa duplikat (siswa_kelas ikut terhapus via CASCADE),
--    lalu akun loginnya (hanya role siswa).
DELETE s FROM siswa s JOIN map_dup m ON m.dup_nisn = s.nisn;
DELETE u FROM users u JOIN map_dup m ON m.dup_user = u.id WHERE u.role = 'siswa';

-- 4. Hitung ulang poin per kelas dari setoran (sumber kebenaran) + badge.
UPDATE siswa_kelas sk
LEFT JOIN (
  SELECT nisn, kelas_id, SUM(poin) AS total FROM setoran GROUP BY nisn, kelas_id
) x ON x.nisn = sk.nisn AND x.kelas_id = sk.kelas_id
SET sk.total_poin = GREATEST(COALESCE(x.total, 0), 0);

UPDATE siswa_kelas SET badge = CASE
  WHEN total_poin >= 3000 THEN 'Bintang Tahfidz'
  WHEN total_poin >= 1500 THEN 'Hafidz Muda'
  WHEN total_poin >= 500  THEN 'Mujahid'
  ELSE 'Pemula' END;

-- 5. Ringkasan gabungan di tabel siswa.
UPDATE siswa s
JOIN (SELECT nisn, SUM(total_poin) AS total FROM siswa_kelas GROUP BY nisn) x ON x.nisn = s.nisn
SET s.total_poin = x.total,
    s.badge = CASE
      WHEN x.total >= 3000 THEN 'Bintang Tahfidz'
      WHEN x.total >= 1500 THEN 'Hafidz Muda'
      WHEN x.total >= 500  THEN 'Mujahid'
      ELSE 'Pemula' END;

DROP TEMPORARY TABLE map_dup;

-- Hasil akhir: satu baris per santri beserta kelas & poin tiap kelas.
SELECT s.nisn, s.nama, GROUP_CONCAT(CONCAT(k.nama_kelas, '=', sk.total_poin) ORDER BY k.nama_kelas SEPARATOR ' | ') AS poin_per_kelas
FROM siswa s
JOIN siswa_kelas sk ON sk.nisn = s.nisn
JOIN kelas k ON k.id = sk.kelas_id
GROUP BY s.nisn, s.nama ORDER BY s.nama;

SELECT COUNT(*) AS jumlah_siswa_sekarang FROM siswa;

ROLLBACK;   -- <-- ganti menjadi COMMIT; setelah hasil di atas diperiksa benar
