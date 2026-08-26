<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * api/Dashboard — Statistik ringkas untuk aplikasi mobile, mengikuti
 * logika yang sama dengan controller web Dashboard.php (per-role).
 */
class Dashboard extends MY_API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Setoran_model');
		$this->load->model('Siswa_model');
		$this->load->model('Kelas_model');
	}

	/**
	 * GET /api/dashboard
	 */
	public function index()
	{
		$data = array('role' => $this->role);

		if ($this->is_siswa()) {
			$siswa = $this->Siswa_model->get_by_user_id($this->user->id);
			if (! $siswa) {
				$siswa = $this->Siswa_model->get_by_nisn($this->user->username);
			}

			if ($siswa) {
				$progress_juz = $this->Setoran_model->get_progress_juz_by_nisn($siswa->nisn);

				$data['siswa']             = $siswa;
				$data['total_setoran']     = $this->Setoran_model->count_setoran(array(), null, $siswa->nisn);
				$data['setoran_bulan_ini'] = $this->Setoran_model->count_setoran_bulan_ini(array(), $siswa->nisn);
				$data['total_juz_selesai'] = count($progress_juz);
				$data['riwayat_terbaru']   = $this->Setoran_model->get_all(array('nisn' => $siswa->nisn), 5);
			} else {
				$data['siswa'] = null;
				$data['total_setoran'] = 0;
				$data['setoran_bulan_ini'] = 0;
				$data['total_juz_selesai'] = 0;
				$data['riwayat_terbaru'] = array();
			}
		} else {
			// Admin & Guru
			$kelas_ids = $this->is_guru() ? $this->kelas_diizinkan : array();

			$data['total_siswa']       = $this->Siswa_model->count_siswa($kelas_ids);
			$data['total_setoran']     = $this->Setoran_model->count_setoran($kelas_ids);
			$data['setoran_bulan_ini'] = $this->Setoran_model->count_setoran_bulan_ini($kelas_ids);
			$data['setoran_lancar']    = $this->Setoran_model->count_setoran($kelas_ids, 'Lancar');
			$data['setoran_cukup']     = $this->Setoran_model->count_setoran($kelas_ids, 'Cukup');
			$data['setoran_perbaikan'] = $this->Setoran_model->count_setoran($kelas_ids, 'Perlu Perbaikan');
			$data['top_siswa']         = $this->Siswa_model->get_leaderboard(null, 5, $kelas_ids);
			$data['riwayat_terbaru']   = $this->Setoran_model->get_all(array('kelas_ids' => $kelas_ids), 7);
		}

		$this->json_success($data, 'Statistik dashboard berhasil diambil.');
	}
}
