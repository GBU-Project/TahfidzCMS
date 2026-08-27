<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('format_tanggal_id')) {
	/**
	 * Format tanggal MySQL (Y-m-d) menjadi format Indonesia, mis. "26 Agu 2026".
	 *
	 * @param string $mysql_date
	 * @return string
	 */
	function format_tanggal_id($mysql_date)
	{
		if (! $mysql_date) {
			return '-';
		}

		$bulan = array(
			1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
			7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
		);

		$ts = strtotime($mysql_date);
		return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
	}
}

if (! function_exists('format_durasi_audio')) {
	/**
	 * Format durasi audio (detik) menjadi "mm:ss", untuk ditampilkan
	 * di daftar riwayat/penilaian tanpa perlu load file audio-nya.
	 *
	 * @param int|null $detik
	 * @return string
	 */
	function format_durasi_audio($detik)
	{
		if (! $detik) {
			return '-';
		}

		$menit = floor($detik / 60);
		$sisa  = $detik % 60;

		return sprintf('%02d:%02d', $menit, $sisa);
	}
}
