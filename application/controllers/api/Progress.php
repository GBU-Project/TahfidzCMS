<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * api/Progress — Capaian juz 1-30 santri vs target_juz, mengikuti logika
 * yang sama dengan controller web Progress.php (dibatasi per-role).
 */
class Progress extends MY_API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Setoran_model');
		$this->load->model('Siswa_model');
	}

	/**
	 * GET /api/progress
	 * Query: nisn (diabaikan untuk role siswa — otomatis pakai data sendiri)
	 */
	public function index()
	{
		$selected_nisn = $this->input->get('nisn');
		$siswa_aktif = null;

		if ($this->is_siswa()) {
			$siswa_aktif = $this->Siswa_model->get_by_user_id($this->user->id);
			if (! $siswa_aktif) {
				$siswa_aktif = $this->Siswa_model->get_by_nisn($this->user->username);
			}
		} else {
			if (empty($selected_nisn)) {
				$this->json_error('Parameter nisn wajib diisi untuk role admin/guru.', 422);
				return;
			}

			$siswa_aktif = $this->Siswa_model->get_by_nisn($selected_nisn);

			if ($siswa_aktif && $this->is_guru() && ! in_array((int) $siswa_aktif->kelas_id, $this->kelas_diizinkan, TRUE)) {
				$this->json_error('Anda tidak memiliki akses ke siswa di kelas ini.', 403);
				return;
			}
		}

		if (! $siswa_aktif) {
			$this->json_error('Data siswa tidak ditemukan.', 404);
			return;
		}

		$raw_progress = $this->Setoran_model->get_progress_juz_by_nisn($siswa_aktif->nisn);

		$progress_juz_map = array();
		foreach ($raw_progress as $p) {
			$progress_juz_map[(int) $p->juz] = $p;
		}

		$this->json_success(array(
			'siswa'            => $siswa_aktif,
			'progress_juz'     => $progress_juz_map,
			'total_juz_tuntas' => count($progress_juz_map),
		), 'Data progress berhasil diambil.');
	}
}
