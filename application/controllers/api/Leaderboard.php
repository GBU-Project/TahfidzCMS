<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * api/Leaderboard — Peringkat ranking santri global/per kelas, mengikuti
 * logika yang sama dengan controller web Leaderboard.php.
 */
class Leaderboard extends MY_API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Siswa_model');
	}

	/**
	 * GET /api/leaderboard
	 * Query: kelas_id (opsional), limit (default 100)
	 */
	public function index()
	{
		$kelas_id = $this->input->get('kelas_id');
		$limit    = $this->input->get('limit') ? (int) $this->input->get('limit') : 100;

		$kelas_ids_filter = array();

		if ($this->is_guru()) {
			$kelas_ids_filter = $this->kelas_diizinkan;
			if ($kelas_id && ! in_array((int) $kelas_id, $this->kelas_diizinkan, TRUE)) {
				$this->json_error('Anda tidak memiliki akses ke kelas ini.', 403);
				return;
			}
		}

		$data = $this->Siswa_model->get_leaderboard($kelas_id, $limit, $kelas_ids_filter);

		$this->json_success($data, 'Leaderboard berhasil diambil.');
	}
}
