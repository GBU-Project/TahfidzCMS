<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Riwayat — Controller Web untuk histori & filter pencarian setoran hafalan siswa.
 */
class Riwayat extends MY_Controller
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

		$kelas_id      = $this->input->get('kelas_id');
		$nisn          = $this->input->get('nisn');
		$status        = $this->input->get('status');
		$tanggal_awal  = $this->input->get('tanggal_awal');
		$tanggal_akhir = $this->input->get('tanggal_akhir');
		$search        = $this->input->get('q');

		$filter = array(
			'status'        => $status,
			'tanggal_awal'  => $tanggal_awal,
			'tanggal_akhir' => $tanggal_akhir,
			'search'        => $search,
		);

		if ($role === 'siswa') {
			$siswa = $this->Siswa_model->get_by_user_id($this->user->id);
			if (! $siswa) {
				$siswa = $this->Siswa_model->get_by_nisn($this->user->username);
			}
			$filter['nisn'] = $siswa ? $siswa->nisn : 'NON_EXISTENT';
			$kelas_list = array();
			$siswa_list = array();
		} else {
			// Role Guru & Admin
			if ($this->is_guru()) {
				$filter['kelas_ids'] = $this->kelas_diizinkan;
				if ($kelas_id && ! in_array((int)$kelas_id, $this->kelas_diizinkan, TRUE)) {
					show_error('Anda tidak memiliki akses ke kelas ini.', 403, 'Akses Ditolak');
					return;
				}
				$kelas_list = $this->Kelas_model->get_by_ids($this->kelas_diizinkan);
				$siswa_list = $this->Siswa_model->get_all($this->kelas_diizinkan);
			} else {
				// Admin
				$kelas_list = $this->Kelas_model->get_all();
				$siswa_list = $this->Siswa_model->get_all();
			}

			if (! empty($kelas_id)) {
				$filter['kelas_id'] = $kelas_id;
			}
			if (! empty($nisn)) {
				$filter['nisn'] = $nisn;
			}
		}

		$data = array(
			'title'          => 'Riwayat Setoran Hafalan',
			'setoran_list'   => $this->Setoran_model->get_all($filter, 200),
			'kelas_list'     => $kelas_list,
			'siswa_list'     => $siswa_list,
			'selected_kelas' => $kelas_id,
			'selected_nisn'  => $nisn,
			'selected_status'=> $status,
			'tanggal_awal'   => $tanggal_awal,
			'tanggal_akhir'  => $tanggal_akhir,
			'search'         => $search,
			'role'           => $role,
		);

		$this->render('riwayat/index', $data);
	}
}
