<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller — Tampilan statistik ringkas dinamis sesuai role.
 */
class Dashboard extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Setoran_model');
		$this->load->model('Siswa_model');
		$this->load->model('Kelas_model');
	}

	public function index()
	{
		$role = $this->role;
		$data = array(
			'title' => 'Dashboard',
			'role'  => $role,
		);

		if ($role === 'siswa') {
			// Data khusus siswa
			$siswa = $this->Siswa_model->get_by_user_id($this->user->id);
			if (! $siswa) {
				// Coba fallback by NISN (jika username adalah NISN)
				$siswa = $this->Siswa_model->get_by_nisn($this->user->username);
			}

			$data['siswa'] = $siswa;
			if ($siswa) {
				$data['total_setoran'] = $this->Setoran_model->count_setoran(array(), null, $siswa->nisn);
				$data['setoran_bulan_ini'] = $this->Setoran_model->count_setoran_bulan_ini(array(), $siswa->nisn);
				$data['riwayat_terbaru'] = $this->Setoran_model->get_all(array('nisn' => $siswa->nisn), 5);
				$progress_juz = $this->Setoran_model->get_progress_juz_by_nisn($siswa->nisn);
				$data['total_juz_selesai'] = count($progress_juz);
			} else {
				$data['total_setoran'] = 0;
				$data['setoran_bulan_ini'] = 0;
				$data['riwayat_terbaru'] = array();
				$data['total_juz_selesai'] = 0;
			}
		} else {
			// Data untuk Admin & Guru
			$kelas_ids = $this->is_guru() ? $this->kelas_diizinkan : array();

			$data['total_siswa']       = $this->Siswa_model->count_siswa($kelas_ids);
			$data['total_setoran']     = $this->Setoran_model->count_setoran($kelas_ids);
			$data['setoran_bulan_ini'] = $this->Setoran_model->count_setoran_bulan_ini($kelas_ids);
			$data['setoran_lancar']    = $this->Setoran_model->count_setoran($kelas_ids, 'L');
			$data['setoran_cukup']     = $this->Setoran_model->count_setoran($kelas_ids, 'CL');
			$data['setoran_perbaikan'] = $this->Setoran_model->count_setoran($kelas_ids, 'KL')
			                            + $this->Setoran_model->count_setoran($kelas_ids, 'TL');

			// Top 5 Siswa Leaderboard
			$data['top_siswa'] = $this->Siswa_model->get_leaderboard(null, 5, $kelas_ids);

			// Riwayat setoran terbaru
			$filter_terbaru = array(
				'kelas_ids' => $kelas_ids,
			);
			$data['riwayat_terbaru'] = $this->Setoran_model->get_all($filter_terbaru, 7);
		}

		$this->render('dashboard/index', $data);
	}
}
