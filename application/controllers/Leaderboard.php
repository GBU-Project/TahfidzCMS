<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Leaderboard — Controller Web untuk peringkat ranking santri global dan per kelas.
 */
class Leaderboard extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Siswa_model');
		$this->load->model('Kelas_model');
	}

	public function index()
	{
		$role = $this->role;
		$kelas_id = $this->input->get('kelas_id');

		$kelas_list = $this->Kelas_model->get_all();
		$kelas_ids_filter = array();

		if ($this->is_guru()) {
			$kelas_ids_filter = $this->kelas_diizinkan;
			if ($kelas_id && ! in_array((int)$kelas_id, $this->kelas_diizinkan, TRUE)) {
				show_error('Anda tidak memiliki akses ke kelas ini.', 403, 'Akses Ditolak');
				return;
			}
		}

		$leaderboard_list = $this->Siswa_model->get_leaderboard($kelas_id, 100, $kelas_ids_filter);

		$data = array(
			'title'            => 'Leaderboard Hafalan Santri',
			'role'             => $role,
			'kelas_list'       => $kelas_list,
			'selected_kelas'   => $kelas_id,
			'leaderboard_list' => $leaderboard_list,
		);

		$this->render('leaderboard/index', $data);
	}
}
