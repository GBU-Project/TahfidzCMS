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
		$keterangan    = $this->input->get('keterangan');
		$tanggal_awal  = $this->input->get('tanggal_awal');
		$tanggal_akhir = $this->input->get('tanggal_akhir');
		$search        = $this->input->get('q');

		$filter = array(
			'keterangan'    => $keterangan,
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

		// Konfigurasi Pagination
		$this->load->library('pagination');
		$total_rows = $this->Setoran_model->count_all_filtered($filter);
		$per_page   = 20;
		$page       = (int) $this->input->get('page');
		$offset     = ($page > 1) ? ($page - 1) * $per_page : 0;

		$get_params = $this->input->get();
		unset($get_params['page']);
		$query_string = ! empty($get_params) ? '?' . http_build_query($get_params) : '';

		$config['base_url']             = site_url('riwayat') . $query_string;
		$config['total_rows']           = $total_rows;
		$config['per_page']             = $per_page;
		$config['page_query_string']    = TRUE;
		$config['query_string_segment'] = 'page';
		$config['use_page_numbers']     = TRUE;
		$config['reuse_query_string']   = TRUE;

		// Styling Tailwind
		$config['full_tag_open']   = '<nav class="flex items-center gap-1 text-sm font-medium">';
		$config['full_tag_close']  = '</nav>';
		$config['cur_tag_open']    = '<span class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-bold">';
		$config['cur_tag_close']   = '</span>';
		$config['num_tag_open']    = '<span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">';
		$config['num_tag_close']   = '</span>';
		$config['prev_tag_open']   = '<span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">';
		$config['prev_tag_close']  = '</span>';
		$config['next_tag_open']   = '<span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">';
		$config['next_tag_close']  = '</span>';
		$config['first_tag_open']  = '<span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">';
		$config['first_tag_close'] = '</span>';
		$config['last_tag_open']   = '<span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">';
		$config['last_tag_close']  = '</span>';

		$this->pagination->initialize($config);

		$data = array(
			'title'          => 'Riwayat Setoran Hafalan',
			'setoran_list'   => $this->Setoran_model->get_all($filter, $per_page, $offset),
			'total_rows'     => $total_rows,
			'pagination'     => $this->pagination->create_links(),
			'kelas_list'     => $kelas_list,
			'siswa_list'     => $siswa_list,
			'selected_kelas' => $kelas_id,
			'selected_nisn'  => $nisn,
			'selected_keterangan'=> $keterangan,
			'tanggal_awal'   => $tanggal_awal,
			'tanggal_akhir'  => $tanggal_akhir,
			'search'         => $search,
			'role'           => $role,
		);

		$this->render('riwayat/index', $data);
	}
}
