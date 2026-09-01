<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Progress — Controller Web untuk melihat capaian juz 1-30 santri vs target_juz.
 */
class Progress extends MY_Controller
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
		$selected_nisn = $this->input->get('nisn');
		$selected_kelas = $this->input->get('kelas_id');

		$siswa_aktif = null;
		$kelas_list = array();
		$siswa_list = array();

		if ($role === 'siswa') {
			$siswa_aktif = $this->Siswa_model->get_by_user_id($this->user->id);
			if (! $siswa_aktif) {
				$siswa_aktif = $this->Siswa_model->get_by_nisn($this->user->username);
			}
		} else {
			// Guru / Admin
			if ($this->is_guru()) {
				$kelas_list = $this->Kelas_model->get_by_ids($this->kelas_diizinkan);
				$siswa_list = $this->Siswa_model->get_all($this->kelas_diizinkan);
			} else {
				$kelas_list = $this->Kelas_model->get_all();
				$siswa_list = $this->Siswa_model->get_all();
			}

			if (! empty($selected_nisn)) {
				$siswa_aktif = $this->Siswa_model->get_by_nisn($selected_nisn);

				// Fix keamanan (IDOR): guru hanya boleh lihat progress siswa
				// di kelas yang diampunya, konsisten dengan validasi yang
				// sudah ada di api/Progress.php. Tanpa ini, guru bisa melihat
				// progress siswa kelas lain hanya dengan mengganti ?nisn=...
				if ($siswa_aktif && $this->is_guru() && ! in_array((int) $siswa_aktif->kelas_id, $this->kelas_diizinkan, TRUE)) {
					show_error('Anda tidak memiliki akses ke siswa di kelas ini.', 403, 'Akses Ditolak');
					return;
				}
			} elseif (! empty($siswa_list)) {
				// Default ke siswa pertama jika belum dipilih
				$siswa_aktif = $siswa_list[0];
			}
		}

		$progress_juz_map = array();
		$total_juz_tuntas = 0;

		if ($siswa_aktif) {
			$raw_progress = $this->Setoran_model->get_progress_juz_by_nisn($siswa_aktif->nisn);
			foreach ($raw_progress as $p) {
				$progress_juz_map[(int)$p->juz] = $p;
			}
			$total_juz_tuntas = count($progress_juz_map);
		}

		$data = array(
			'title'             => 'Progress Capaian Juz Hafalan',
			'role'              => $role,
			'siswa_aktif'       => $siswa_aktif,
			'kelas_list'        => $kelas_list,
			'siswa_list'        => $siswa_list,
			'selected_kelas'    => $selected_kelas,
			'selected_nisn'     => $siswa_aktif ? $siswa_aktif->nisn : '',
			'progress_juz_map'  => $progress_juz_map,
			'total_juz_tuntas'  => $total_juz_tuntas,
		);

		$this->render('progress/index', $data);
	}
}
