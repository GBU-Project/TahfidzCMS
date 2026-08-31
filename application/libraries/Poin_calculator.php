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
	 * Daftar 114 Surat dalam Al-Qur'an untuk dropdown form input.
	 *
	 * @return array
	 */
	public function get_daftar_surat()
	{
		return array(
			1 => "Al-Fatihah", 2 => "Al-Baqarah", 3 => "Ali 'Imran", 4 => "An-Nisa'", 5 => "Al-Ma'idah",
			6 => "Al-An'am", 7 => "Al-A'raf", 8 => "Al-Anfal", 9 => "At-Taubah", 10 => "Yunus",
			11 => "Hud", 12 => "Yusuf", 13 => "Ar-Ra'd", 14 => "Ibrahim", 15 => "Al-Hijr",
			16 => "An-Nahl", 17 => "Al-Isra'", 18 => "Al-Kahf", 19 => "Maryam", 20 => "Ta-Ha",
			21 => "Al-Anbiya'", 22 => "Al-Hajj", 23 => "Al-Mu'minun", 24 => "An-Nur", 25 => "Al-Furqan",
			26 => "Asy-Syu'ara'", 27 => "An-Naml", 28 => "Al-Qashash", 29 => "Al-'Ankabut", 30 => "Ar-Rum",
			31 => "Luqman", 32 => "As-Sajdah", 33 => "Al-Ahzab", 34 => "Saba'", 35 => "Fathir",
			36 => "Ya-Sin", 37 => "Ash-Shaffat", 38 => "Shad", 39 => "Az-Zumar", 40 => "Ghafir",
			41 => "Fushshilat", 42 => "Asy-Syura", 43 => "Az-Zukhruf", 44 => "Ad-Dukhan", 45 => "Al-Jatsiyah",
			46 => "Al-Ahqaf", 47 => "Muhammad", 48 => "Al-Fath", 49 => "Al-Hujurat", 50 => "Qaf",
			51 => "Adz-Dzariyat", 52 => "Ath-Thur", 53 => "An-Najm", 54 => "Al-Qamar", 55 => "Ar-Rahman",
			56 => "Al-Waqi'ah", 57 => "Al-Hadid", 58 => "Al-Mujadilah", 59 => "Al-Hasyr", 60 => "Al-Mumtahanah",
			61 => "Ash-Shaff", 62 => "Al-Jumu'ah", 63 => "Al-Munafiqun", 64 => "At-Taghabun", 65 => "Ath-Thalaq",
			66 => "At-Tahrim", 67 => "Al-Mulk", 68 => "Al-Qalam", 69 => "Al-Haqqah", 70 => "Al-Ma'arij",
			71 => "Nuh", 72 => "Al-Jinn", 73 => "Al-Muzzammil", 74 => "Al-Muddatstsir", 75 => "Al-Qiyamah",
			76 => "Al-Insan", 77 => "Al-Mursalat", 78 => "An-Naba'", 79 => "An-Nazi'at", 80 => "'Abasa",
			81 => "At-Takwir", 82 => "Al-Infithar", 83 => "Al-Muthaffifin", 84 => "Al-Insyiqaq", 85 => "Al-Buruj",
			86 => "Ath-Thariq", 87 => "Al-A'la", 88 => "Al-Ghasyiyah", 89 => "Al-Fajr", 90 => "Al-Balad",
			91 => "Asy-Syams", 92 => "Al-Lail", 93 => "Adh-Dhuha", 94 => "Asy-Syarh", 95 => "At-Tin",
			96 => "Al-'Alaq", 97 => "Al-Qadr", 98 => "Al-Bayyinah", 99 => "Az-Zalzalah", 100 => "Al-'Adiyat",
			101 => "Al-Qari'ah", 102 => "At-Takatsur", 103 => "Al-'Ashr", 104 => "Al-Humazah", 105 => "Al-Fil",
			106 => "Quraisy", 107 => "Al-Ma'un", 108 => "Al-Kautsar", 109 => "Al-Kafirun", 110 => "An-Nashr",
			111 => "Al-Lahab", 112 => "Al-Ikhlash", 113 => "Al-Falaq", 114 => "An-Nas"
		);
	}
}
