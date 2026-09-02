<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Statistik Controller — Halaman publik statistik AGREGAT sekolah, untuk
 * jamaah/komunitas masjid yang lebih luas (bukan orangtua siswa spesifik —
 * untuk itu lihat Rapor.php). Tanpa login, tanpa token.
 *
 * PENTING (privasi): controller & view ini TIDAK BOLEH menampilkan data
 * yang identifiable ke siswa tertentu (nama, NISN, ranking individual).
 * Semua angka yang ditampilkan harus murni agregat se-sekolah. Kalau nanti
 * ada penambahan data di sini, pertahankan batasan ini.
 */
class Statistik extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->model('Siswa_model');
		$this->load->model('Setoran_model');
		$this->load->model('Kelas_model');
		$this->load->model('Setting_model');
	}

	public function index()
	{
		$settings = $this->Setting_model->get_all();

		$ringkasan   = $this->Setoran_model->get_ringkasan_global();
		$distribusi  = $this->Setoran_model->get_keterangan_distribution_global();
		$tren_bulanan= $this->Setoran_model->get_tren_bulanan_global(6);

		$bulan_label = array(
			'01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
			'05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
			'09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
		);
		foreach ($tren_bulanan as &$t) {
			list($tahun, $bulan) = explode('-', $t['bulan']);
			$t['label'] = (isset($bulan_label[$bulan]) ? $bulan_label[$bulan] : $bulan) . ' ' . $tahun;
		}
		unset($t);

		$data = array(
			'title'        => 'Statistik Perkembangan Santri — ' . $settings['institution_name'],
			'settings'     => $settings,
			'total_santri' => $this->Siswa_model->count_siswa(),
			'total_kelas'  => count($this->Kelas_model->get_all()),
			'ringkasan'    => $ringkasan,
			'distribusi'   => $distribusi,
			'tren_bulanan' => $tren_bulanan,
		);

		$this->load->view('statistik/index', $data);
	}
}
