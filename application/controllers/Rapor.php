<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rapor Controller — Halaman publik "Rapor Digital" untuk orangtua/wali
 * santri, diakses via token acak (BUKAN NISN — NISN polanya sering
 * berurutan/gampang ditebak) tanpa perlu login.
 *
 * URL: /rapor/{token}  (lihat application/config/routes.php)
 *
 * Privasi: identitas siswa yang ditampilkan sengaja dibatasi (nama depan +
 * kelas saja, bukan nama lengkap/NISN), dan TIDAK menampilkan ranking/posisi
 * leaderboard — keputusan produk untuk menghindari tekanan sosial pada anak.
 */
class Rapor extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->model('Siswa_model');
		$this->load->model('Setoran_model');
		$this->load->model('Setting_model');
		$this->load->library('Poin_calculator');
	}

	/**
	 * @param string $token
	 */
	public function index($token = '')
	{
		$token = trim($token);

		if ($token === '' || ! preg_match('/^[a-f0-9]{32}$/', $token)) {
			show_404();
			return;
		}

		$siswa = $this->Siswa_model->get_by_token($token);

		if (! $siswa) {
			show_404();
			return;
		}

		// Privasi: tampilkan nama depan saja, bukan nama lengkap.
		$nama_depan = trim(strtok($siswa->nama, ' '));

		$progress_juz  = $this->Setoran_model->get_progress_juz_by_nisn($siswa->nisn);
		$juz_selesai   = array_column($progress_juz, 'juz');

		$skor_trend     = $this->Setoran_model->get_skor_trend_by_nisn($siswa->nisn, 30);
		$distribusi     = $this->Setoran_model->get_keterangan_distribution_by_nisn($siswa->nisn);
		$riwayat_recent = $this->Setoran_model->get_recent_by_nisn($siswa->nisn, 10);

		$settings = $this->Setting_model->get_all();

		$data = array(
			'title'               => 'Rapor Hafalan — ' . $nama_depan,
			'settings'            => $settings,
			'nama_depan'          => $nama_depan,
			'nama_kelas'          => $siswa->nama_kelas,
			'target_juz'          => (int) $siswa->target_juz,
			'total_poin'          => (int) $siswa->total_poin,
			'badge'               => $siswa->badge,
			'juz_selesai'         => $juz_selesai,
			'jumlah_juz_selesai'  => count($juz_selesai),
			'skor_trend'          => $skor_trend,
			'distribusi'          => $distribusi,
			'riwayat_recent'      => $riwayat_recent,
			'jenis_setoran_label' => Poin_calculator::JENIS_SETORAN_LABEL,
			'keterangan_label'    => Poin_calculator::KETERANGAN_LABEL,
		);

		$this->load->view('rapor/index', $data);
	}
}
