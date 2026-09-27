<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Poin_calculator — Library logika murni (testable, tanpa dependensi DB langsung)
 * untuk kalkulasi penilaian hafalan, poin, dan badge siswa.
 *
 * Aturan bisnis diadaptasi dari dokumen resmi "KRITERIA PENILAIAN TAHFIDZ"
 * (Ziyadah / Muroja'ah / Quality Control):
 *
 * 1. Guru menginput `jumlah_kesalahan` (hasil simakan) + `jenis_setoran`.
 *    -> Sistem menentukan `keterangan` (L / CL / KL / TL) dari ambang batas
 *       kesalahan yang BERBEDA per jenis setoran (lihat KETERANGAN_THRESHOLDS).
 * 2. Guru menginput `kualitas_bacaan` (baik / kurang_baik) — penilaian
 *    Makhraj, Tajwid, Sifatul Huruf.
 *    -> Sistem menentukan `skor` (100/95/90/85/80/75/60) dari kombinasi
 *       keterangan + kualitas_bacaan (lihat SKOR_MATRIX).
 * 3. Khusus jenis_setoran = 'qc', guru WAJIB mengisi `hasil_qc` secara
 *    manual ('layak_tasmi' / 'belum_layak') — keputusan ini murni judgment
 *    guru penguji, tidak ada rumus otomatis di dokumen sumber.
 * 4. Poin leaderboard = skor apa adanya (1 poin per 1 skor), sama rata
 *    untuk ketiga jenis setoran (tidak ada bobot/bonus ekstra untuk QC).
 *
 * Skor "<70" pada dokumen disimpan sebagai angka tetap 60, supaya tetap
 * bisa diurutkan/dirata-rata secara numerik.
 */
class Poin_calculator
{
	/**
	 * Ambang batas jumlah kesalahan -> kode keterangan (L/CL/KL/TL),
	 * berbeda satuan cakupan per jenis setoran:
	 *   - ziyadah  : dihitung per HALAMAN
	 *   - murojaah : dihitung per JUZ
	 *   - qc       : dihitung per 2 HALAMAN
	 *
	 * Format tiap baris: array('max' => batas_atas_inklusif, 'kode' => ...)
	 * Baris terakhir tiap jenis wajib 'max' => NULL (menangkap sisanya / TL).
	 */
	const KETERANGAN_THRESHOLDS = array(
		'ziyadah' => array(
			array('max' => 0,    'kode' => 'L'),
			array('max' => 1,    'kode' => 'CL'),
			array('max' => 2,    'kode' => 'KL'),
			array('max' => NULL, 'kode' => 'TL'),
		),
		'murojaah' => array(
			array('max' => 0,    'kode' => 'L'),
			array('max' => 5,    'kode' => 'CL'),
			array('max' => 10,   'kode' => 'KL'),
			array('max' => NULL, 'kode' => 'TL'),
		),
		'qc' => array(
			array('max' => 0,    'kode' => 'L'),
			array('max' => 1,    'kode' => 'CL'),
			array('max' => 2,    'kode' => 'KL'),
			array('max' => NULL, 'kode' => 'TL'),
		),
	);

	/** Label lengkap tiap kode keterangan, untuk ditampilkan di UI. */
	const KETERANGAN_LABEL = array(
		'L'  => 'Lancar (Tidak Ada Kesalahan)',
		'CL' => 'Cukup Lancar',
		'KL' => 'Kurang Lancar',
		'TL' => 'Tidak Lancar',
	);

	/**
	 * Matriks skor dari kombinasi keterangan (L/CL/KL) + kualitas_bacaan.
	 * TL selalu bernilai 60 (mewakili "<70") terlepas dari kualitas bacaan,
	 * karena dokumen sumber tidak memecah TL berdasarkan kualitas.
	 */
	const SKOR_MATRIX = array(
		'L'  => array('baik' => 100, 'kurang_baik' => 95),
		'CL' => array('baik' => 90,  'kurang_baik' => 85),
		'KL' => array('baik' => 80,  'kurang_baik' => 75),
		'TL' => array('baik' => 60,  'kurang_baik' => 60),
	);

	/** Label jenis setoran untuk dropdown & tampilan. */
	const JENIS_SETORAN_LABEL = array(
		'ziyadah'  => 'Ziyadah (Hafalan Baru)',
		'murojaah' => "Muroja'ah (Mengulang Hafalan)",
		'qc'       => 'Quality Control',
	);

	const HASIL_QC_LABEL = array(
		'layak_tasmi'  => "Layak Tasmi'",
		'belum_layak'  => "Belum Layak Tasmi' / Mengulang",
	);

	const BADGE_LEVELS = array(
		array('min' => 3000, 'name' => 'Bintang Tahfidz'),
		array('min' => 1500, 'name' => 'Hafidz Muda'),
		array('min' => 500,  'name' => 'Mujahid'),
		array('min' => 0,    'name' => 'Pemula'),
	);

	/**
	 * Tentukan kode keterangan (L/CL/KL/TL) dari jumlah kesalahan dan
	 * jenis setoran, sesuai ambang batas KETERANGAN_THRESHOLDS.
	 *
	 * @param int    $jumlah_kesalahan
	 * @param string $jenis_setoran 'ziyadah' | 'murojaah' | 'qc'
	 * @return string Kode keterangan: 'L' | 'CL' | 'KL' | 'TL'
	 */
	public function hitung_keterangan($jumlah_kesalahan, $jenis_setoran)
	{
		$jumlah_kesalahan = max(0, (int) $jumlah_kesalahan);

		$thresholds = isset(self::KETERANGAN_THRESHOLDS[$jenis_setoran])
			? self::KETERANGAN_THRESHOLDS[$jenis_setoran]
			: self::KETERANGAN_THRESHOLDS['ziyadah']; // fallback aman

		foreach ($thresholds as $row) {
			if ($row['max'] === NULL || $jumlah_kesalahan <= $row['max']) {
				return $row['kode'];
			}
		}

		return 'TL'; // unreachable secara normal, jaga-jaga saja
	}

	/**
	 * Tentukan skor akhir (100/95/90/85/80/75/60) dari kode keterangan
	 * dan kualitas bacaan (Makhraj/Tajwid/Sifatul Huruf).
	 *
	 * @param string $keterangan     'L' | 'CL' | 'KL' | 'TL'
	 * @param string $kualitas_bacaan 'baik' | 'kurang_baik'
	 * @return int
	 */
	public function hitung_skor($keterangan, $kualitas_bacaan)
	{
		if (! isset(self::SKOR_MATRIX[$keterangan])) {
			return 0;
		}

		$kualitas_bacaan = ($kualitas_bacaan === 'baik') ? 'baik' : 'kurang_baik';

		return self::SKOR_MATRIX[$keterangan][$kualitas_bacaan];
	}

	/**
	 * Helper gabungan: dari input mentah guru (jumlah kesalahan, jenis
	 * setoran, kualitas bacaan) langsung hasilkan keterangan + skor + poin.
	 *
	 * @param int    $jumlah_kesalahan
	 * @param string $jenis_setoran
	 * @param string $kualitas_bacaan
	 * @return array{keterangan: string, skor: int, poin: int}
	 */
	public function nilai_setoran($jumlah_kesalahan, $jenis_setoran, $kualitas_bacaan)
	{
		$keterangan = $this->hitung_keterangan($jumlah_kesalahan, $jenis_setoran);
		$skor       = $this->hitung_skor($keterangan, $kualitas_bacaan);

		return array(
			'keterangan' => $keterangan,
			'skor'       => $skor,
			'poin'       => $skor, // poin = skor apa adanya, sama rata semua jenis setoran
		);
	}

	/**
	 * Tentukan nama badge berdasarkan total poin terkumpul.
	 *
	 * @param int $total_poin
	 * @return string
	 */
	public function hitung_badge($total_poin)
	{
		$total_poin = (int) $total_poin;

		foreach (self::BADGE_LEVELS as $level) {
			if ($total_poin >= $level['min']) {
				return $level['name'];
			}
		}

		return 'Pemula';
	}

	/**
	 * Data metadata 114 Surat dalam Al-Qur'an (nama, total ayat, dan juz awal).
	 * Diadopsi untuk mencegah salah ketik ayat dan validasi rentang ayat.
	 */
	const METADATA_SURAT = array(
		1 => array('nama' => 'Al-Fatihah', 'ayat' => 7, 'juz' => 1),
		2 => array('nama' => 'Al-Baqarah', 'ayat' => 286, 'juz' => 1),
		3 => array('nama' => "Ali 'Imran", 'ayat' => 200, 'juz' => 3),
		4 => array('nama' => "An-Nisa'", 'ayat' => 176, 'juz' => 4),
		5 => array('nama' => "Al-Ma'idah", 'ayat' => 120, 'juz' => 6),
		6 => array('nama' => "Al-An'am", 'ayat' => 165, 'juz' => 7),
		7 => array('nama' => "Al-A'raf", 'ayat' => 206, 'juz' => 8),
		8 => array('nama' => 'Al-Anfal', 'ayat' => 75, 'juz' => 9),
		9 => array('nama' => 'At-Taubah', 'ayat' => 129, 'juz' => 10),
		10 => array('nama' => 'Yunus', 'ayat' => 109, 'juz' => 11),
		11 => array('nama' => 'Hud', 'ayat' => 123, 'juz' => 11),
		12 => array('nama' => 'Yusuf', 'ayat' => 111, 'juz' => 12),
		13 => array('nama' => "Ar-Ra'd", 'ayat' => 43, 'juz' => 13),
		14 => array('nama' => 'Ibrahim', 'ayat' => 52, 'juz' => 13),
		15 => array('nama' => 'Al-Hijr', 'ayat' => 99, 'juz' => 14),
		16 => array('nama' => 'An-Nahl', 'ayat' => 128, 'juz' => 14),
		17 => array('nama' => "Al-Isra'", 'ayat' => 111, 'juz' => 15),
		18 => array('nama' => 'Al-Kahf', 'ayat' => 110, 'juz' => 15),
		19 => array('nama' => 'Maryam', 'ayat' => 98, 'juz' => 16),
		20 => array('nama' => 'Ta-Ha', 'ayat' => 135, 'juz' => 16),
		21 => array('nama' => "Al-Anbiya'", 'ayat' => 112, 'juz' => 17),
		22 => array('nama' => 'Al-Hajj', 'ayat' => 78, 'juz' => 17),
		23 => array('nama' => "Al-Mu'minun", 'ayat' => 118, 'juz' => 18),
		24 => array('nama' => 'An-Nur', 'ayat' => 64, 'juz' => 18),
		25 => array('nama' => 'Al-Furqan', 'ayat' => 77, 'juz' => 18),
		26 => array('nama' => "Asy-Syu'ara'", 'ayat' => 227, 'juz' => 19),
		27 => array('nama' => 'An-Naml', 'ayat' => 93, 'juz' => 19),
		28 => array('nama' => 'Al-Qashash', 'ayat' => 88, 'juz' => 20),
		29 => array('nama' => "Al-'Ankabut", 'ayat' => 69, 'juz' => 20),
		30 => array('nama' => 'Ar-Rum', 'ayat' => 60, 'juz' => 21),
		31 => array('nama' => 'Luqman', 'ayat' => 34, 'juz' => 21),
		32 => array('nama' => 'As-Sajdah', 'ayat' => 30, 'juz' => 21),
		33 => array('nama' => 'Al-Ahzab', 'ayat' => 73, 'juz' => 21),
		34 => array('nama' => 'Saba\'', 'ayat' => 54, 'juz' => 22),
		35 => array('nama' => 'Fathir', 'ayat' => 45, 'juz' => 22),
		36 => array('nama' => 'Ya-Sin', 'ayat' => 83, 'juz' => 22),
		37 => array('nama' => 'Ash-Shaffat', 'ayat' => 182, 'juz' => 23),
		38 => array('nama' => 'Shad', 'ayat' => 88, 'juz' => 23),
		39 => array('nama' => 'Az-Zumar', 'ayat' => 75, 'juz' => 23),
		40 => array('nama' => 'Ghafir', 'ayat' => 85, 'juz' => 24),
		41 => array('nama' => 'Fushshilat', 'ayat' => 54, 'juz' => 24),
		42 => array('nama' => 'Asy-Syura', 'ayat' => 53, 'juz' => 25),
		43 => array('nama' => 'Az-Zukhruf', 'ayat' => 89, 'juz' => 25),
		44 => array('nama' => 'Ad-Dukhan', 'ayat' => 59, 'juz' => 25),
		45 => array('nama' => 'Al-Jatsiyah', 'ayat' => 37, 'juz' => 25),
		46 => array('nama' => 'Al-Ahqaf', 'ayat' => 35, 'juz' => 26),
		47 => array('nama' => 'Muhammad', 'ayat' => 38, 'juz' => 26),
		48 => array('nama' => 'Al-Fath', 'ayat' => 29, 'juz' => 26),
		49 => array('nama' => 'Al-Hujurat', 'ayat' => 18, 'juz' => 26),
		50 => array('nama' => 'Qaf', 'ayat' => 45, 'juz' => 26),
		51 => array('nama' => 'Adz-Dzariyat', 'ayat' => 60, 'juz' => 26),
		52 => array('nama' => 'Ath-Thur', 'ayat' => 49, 'juz' => 27),
		53 => array('nama' => 'An-Najm', 'ayat' => 62, 'juz' => 27),
		54 => array('nama' => 'Al-Qamar', 'ayat' => 55, 'juz' => 27),
		55 => array('nama' => 'Ar-Rahman', 'ayat' => 78, 'juz' => 27),
		56 => array('nama' => "Al-Waqi'ah", 'ayat' => 96, 'juz' => 27),
		57 => array('nama' => 'Al-Hadid', 'ayat' => 29, 'juz' => 27),
		58 => array('nama' => 'Al-Mujadilah', 'ayat' => 22, 'juz' => 28),
		59 => array('nama' => 'Al-Hasyr', 'ayat' => 24, 'juz' => 28),
		60 => array('nama' => 'Al-Mumtahanah', 'ayat' => 13, 'juz' => 28),
		61 => array('nama' => 'Ash-Shaff', 'ayat' => 14, 'juz' => 28),
		62 => array('nama' => "Al-Jumu'ah", 'ayat' => 11, 'juz' => 28),
		63 => array('nama' => 'Al-Munafiqun', 'ayat' => 11, 'juz' => 28),
		64 => array('nama' => 'At-Taghabun', 'ayat' => 18, 'juz' => 28),
		65 => array('nama' => 'Ath-Thalaq', 'ayat' => 12, 'juz' => 28),
		66 => array('nama' => 'At-Tahrim', 'ayat' => 12, 'juz' => 28),
		67 => array('nama' => 'Al-Mulk', 'ayat' => 30, 'juz' => 29),
		68 => array('nama' => 'Al-Qalam', 'ayat' => 52, 'juz' => 29),
		69 => array('nama' => 'Al-Haqqah', 'ayat' => 52, 'juz' => 29),
		70 => array('nama' => "Al-Ma'arij", 'ayat' => 44, 'juz' => 29),
		71 => array('nama' => 'Nuh', 'ayat' => 28, 'juz' => 29),
		72 => array('nama' => 'Al-Jinn', 'ayat' => 28, 'juz' => 29),
		73 => array('nama' => 'Al-Muzzammil', 'ayat' => 20, 'juz' => 29),
		74 => array('nama' => 'Al-Muddatstsir', 'ayat' => 56, 'juz' => 29),
		75 => array('nama' => 'Al-Qiyamah', 'ayat' => 40, 'juz' => 29),
		76 => array('nama' => 'Al-Insan', 'ayat' => 31, 'juz' => 29),
		77 => array('nama' => 'Al-Mursalat', 'ayat' => 50, 'juz' => 29),
		78 => array('nama' => "An-Naba'", 'ayat' => 40, 'juz' => 30),
		79 => array('nama' => "An-Nazi'at", 'ayat' => 46, 'juz' => 30),
		80 => array('nama' => "'Abasa", 'ayat' => 42, 'juz' => 30),
		81 => array('nama' => 'At-Takwir', 'ayat' => 29, 'juz' => 30),
		82 => array('nama' => 'Al-Infithar', 'ayat' => 19, 'juz' => 30),
		83 => array('nama' => 'Al-Muthaffifin', 'ayat' => 36, 'juz' => 30),
		84 => array('nama' => 'Al-Insyiqaq', 'ayat' => 25, 'juz' => 30),
		85 => array('nama' => 'Al-Buruj', 'ayat' => 22, 'juz' => 30),
		86 => array('nama' => 'Ath-Thariq', 'ayat' => 17, 'juz' => 30),
		87 => array('nama' => "Al-A'la", 'ayat' => 19, 'juz' => 30),
		88 => array('nama' => 'Al-Ghasyiyah', 'ayat' => 26, 'juz' => 30),
		89 => array('nama' => 'Al-Fajr', 'ayat' => 30, 'juz' => 30),
		90 => array('nama' => 'Al-Balad', 'ayat' => 20, 'juz' => 30),
		91 => array('nama' => 'Asy-Syams', 'ayat' => 15, 'juz' => 30),
		92 => array('nama' => 'Al-Lail', 'ayat' => 21, 'juz' => 30),
		93 => array('nama' => 'Adh-Dhuha', 'ayat' => 11, 'juz' => 30),
		94 => array('nama' => 'Asy-Syarh', 'ayat' => 8, 'juz' => 30),
		95 => array('nama' => 'At-Tin', 'ayat' => 8, 'juz' => 30),
		96 => array('nama' => "Al-'Alaq", 'ayat' => 19, 'juz' => 30),
		97 => array('nama' => 'Al-Qadr', 'ayat' => 5, 'juz' => 30),
		98 => array('nama' => 'Al-Bayyinah', 'ayat' => 8, 'juz' => 30),
		99 => array('nama' => 'Az-Zalzalah', 'ayat' => 8, 'juz' => 30),
		100 => array('nama' => "Al-'Adiyat", 'ayat' => 11, 'juz' => 30),
		101 => array('nama' => "Al-Qari'ah", 'ayat' => 11, 'juz' => 30),
		102 => array('nama' => 'At-Takatsur', 'ayat' => 8, 'juz' => 30),
		103 => array('nama' => "Al-'Ashr", 'ayat' => 3, 'juz' => 30),
		104 => array('nama' => 'Al-Humazah', 'ayat' => 9, 'juz' => 30),
		105 => array('nama' => 'Al-Fil', 'ayat' => 5, 'juz' => 30),
		106 => array('nama' => 'Quraisy', 'ayat' => 4, 'juz' => 30),
		107 => array('nama' => "Al-Ma'un", 'ayat' => 7, 'juz' => 30),
		108 => array('nama' => 'Al-Kautsar', 'ayat' => 3, 'juz' => 30),
		109 => array('nama' => 'Al-Kafirun', 'ayat' => 6, 'juz' => 30),
		110 => array('nama' => 'An-Nashr', 'ayat' => 3, 'juz' => 30),
		111 => array('nama' => 'Al-Lahab', 'ayat' => 5, 'juz' => 30),
		112 => array('nama' => 'Al-Ikhlash', 'ayat' => 4, 'juz' => 30),
		113 => array('nama' => 'Al-Falaq', 'ayat' => 5, 'juz' => 30),
		114 => array('nama' => 'An-Nas', 'ayat' => 6, 'juz' => 30),
	);

	/**
	 * Daftar 114 Surat dalam Al-Qur'an untuk dropdown form input.
	 *
	 * @return array
	 */
	public function get_daftar_surat()
	{
		$res = array();
		foreach (self::METADATA_SURAT as $no => $item) {
			$res[$no] = $item['nama'];
		}
		return $res;
	}

	/**
	 * Ambil metadata lengkap seluruh 114 surat.
	 *
	 * @return array
	 */
	public function get_metadata_surat()
	{
		return self::METADATA_SURAT;
	}

	/**
	 * Cari metadata satu surat berdasarkan nama atau nomor surat.
	 *
	 * @param string|int $surat_identifier
	 * @return array|null
	 */
	public function cari_surat($surat_identifier)
	{
		if (is_numeric($surat_identifier) && isset(self::METADATA_SURAT[(int)$surat_identifier])) {
			$data = self::METADATA_SURAT[(int)$surat_identifier];
			$data['nomor'] = (int)$surat_identifier;
			return $data;
		}

		$clean = strtolower(trim((string)$surat_identifier));
		$clean = str_replace(array("'", '`', '’', '-', ' '), '', $clean);

		foreach (self::METADATA_SURAT as $no => $item) {
			$nama_clean = strtolower(str_replace(array("'", '`', '’', '-', ' '), '', $item['nama']));
			if ($nama_clean === $clean || strpos($nama_clean, $clean) !== false || strpos($clean, $nama_clean) !== false) {
				$item['nomor'] = $no;
				return $item;
			}
		}

		return null;
	}

	/**
	 * Validasi apakah nomor ayat sesuai dengan batas maksimum surat.
	 *
	 * @param string|int $surat
	 * @param int        $ayat_dari
	 * @param int        $ayat_sampai
	 * @return array{valid: bool, pesan: string, max_ayat: int|null}
	 */
	public function validasi_rentang_ayat($surat, $ayat_dari, $ayat_sampai)
	{
		$info = $this->cari_surat($surat);
		if (! $info) {
			return array(
				'valid'    => true, // fallback jika nama surat custom/tidak standar
				'pesan'    => '',
				'max_ayat' => null,
			);
		}

		$max_ayat = (int) $info['ayat'];
		$nama_resmi = $info['nama'];

		if ((int)$ayat_dari > $max_ayat) {
			return array(
				'valid'    => false,
				'pesan'    => "Ayat Dari ({$ayat_dari}) melebihi jumlah total ayat surat {$nama_resmi} ({$max_ayat} ayat).",
				'max_ayat' => $max_ayat,
			);
		}

		if ((int)$ayat_sampai > $max_ayat) {
			return array(
				'valid'    => false,
				'pesan'    => "Ayat Sampai ({$ayat_sampai}) melebihi jumlah total ayat surat {$nama_resmi} ({$max_ayat} ayat).",
				'max_ayat' => $max_ayat,
			);
		}

		if ((int)$ayat_dari > (int)$ayat_sampai) {
			return array(
				'valid'    => false,
				'pesan'    => "Ayat Dari ({$ayat_dari}) tidak boleh lebih besar dari Ayat Sampai ({$ayat_sampai}).",
				'max_ayat' => $max_ayat,
			);
		}

		return array(
			'valid'    => true,
			'pesan'    => '',
			'max_ayat' => $max_ayat,
		);
	}

	/**
	 * Hitung status kesegaran muroja'ah per juz:
	 * - fresh (🟢): disetor <= 14 hari
	 * - warning (🟠): disetor 15-30 hari
	 * - urgent (🔴): disetor > 30 hari
	 * - none (⚪): belum pernah disetor
	 *
	 * @param string|null $tanggal_terakhir (Format Y-m-d)
	 * @return array{status: string, label: string, color: string, days_ago: int|null}
	 */
	public function hitung_status_murojaah($tanggal_terakhir = null)
	{
		if (empty($tanggal_terakhir)) {
			return array(
				'status'   => 'none',
				'label'    => 'Belum Disetor',
				'color'    => 'gray',
				'days_ago' => null,
			);
		}

		$now = new DateTime(date('Y-m-d'));
		$last = new DateTime($tanggal_terakhir);
		$diff = $now->diff($last);
		$days = (int) $diff->format('%a');

		// Jika tanggal di masa depan karena kekeliruan input tanggal
		if ($last > $now) {
			$days = 0;
		}

		if ($days <= 14) {
			return array(
				'status'   => 'fresh',
				'label'    => 'Fresh (≤14 hari)',
				'color'    => 'emerald',
				'days_ago' => $days,
			);
		} elseif ($days <= 30) {
			return array(
				'status'   => 'warning',
				'label'    => 'Perlu Muroja\'ah (15-30 hari)',
				'color'    => 'amber',
				'days_ago' => $days,
			);
		} else {
			return array(
				'status'   => 'urgent',
				'label'    => 'Urgent Muroja\'ah (>30 hari)',
				'color'    => 'rose',
				'days_ago' => $days,
			);
		}
	}
}

