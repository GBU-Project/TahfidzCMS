<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Setoran — Controller Web untuk manajemen dan pencatatan setoran hafalan siswa.
 * Akses dibatasi untuk role admin dan guru.
 */
class Setoran extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->require_role(array('admin', 'guru'));

		$this->load->model('Setoran_model');
		$this->load->model('Siswa_model');
		$this->load->model('Kelas_model');
		$this->load->library('Poin_calculator');
		$this->load->library('Upload_handler');
	}

	/**
	 * GET /setoran -> Halaman daftar setoran / riwayat input
	 */
	public function index()
	{
		$kelas_id = $this->input->get('kelas_id');
		$nisn     = $this->input->get('nisn');
		$search   = $this->input->get('q');

		// Jika guru memilih kelas yang bukan haknya, tolak
		if ($kelas_id && ! $this->boleh_akses_kelas($kelas_id)) {
			show_error('Anda tidak memiliki akses ke kelas ini.', 403, 'Akses Ditolak');
			return;
		}

		$filter = array(
			'kelas_id'  => $kelas_id,
			'kelas_ids' => $this->is_guru() ? $this->kelas_diizinkan : array(),
			'nisn'      => $nisn,
			'search'    => $search,
		);

		// Konfigurasi Pagination
		$this->load->library('pagination');
		$total_rows = $this->Setoran_model->count_all_filtered($filter);
		$per_page   = 15;
		$page       = (int) $this->input->get('page');
		$offset     = ($page > 1) ? ($page - 1) * $per_page : 0;

		$get_params = $this->input->get();
		unset($get_params['page']);
		$query_string = ! empty($get_params) ? '?' . http_build_query($get_params) : '';

		$config['base_url']             = site_url('setoran') . $query_string;
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
			'title'          => 'Data Input Setoran',
			'setoran_list'   => $this->Setoran_model->get_all($filter, $per_page, $offset),
			'total_rows'     => $total_rows,
			'pagination'     => $this->pagination->create_links(),
			'kelas_list'     => $this->is_guru() ? $this->Kelas_model->get_by_ids($this->kelas_diizinkan) : $this->Kelas_model->get_all(),
			'selected_kelas' => $kelas_id,
			'selected_nisn'  => $nisn,
			'search'         => $search,
		);

		$this->render('setoran/index', $data);
	}

	/**
	 * GET /setoran/tambah -> Form input setoran baru + widget rekam audio
	 */
	public function tambah()
	{
		$kelas_ids = $this->is_guru() ? $this->kelas_diizinkan : array();
		$siswa_list = $this->Siswa_model->get_all($kelas_ids);
		$kelas_list = $this->is_guru() ? $this->Kelas_model->get_by_ids($this->kelas_diizinkan) : $this->Kelas_model->get_all();

		$data = array(
			'title'              => 'Input Setoran Baru',
			'siswa_list'         => $siswa_list,
			'kelas_list'         => $kelas_list,
			'daftar_surat'       => $this->poin_calculator->get_daftar_surat(),
			'jenis_setoran_list' => Poin_calculator::JENIS_SETORAN_LABEL,
			'auto_kode'          => $this->Setoran_model->generate_kode_setoran(),
			'default_date'       => date('Y-m-d'),
			'default_time'       => date('H:i'),
		);

		$this->render('setoran/form', $data);
	}

	/**
	 * POST /setoran/simpan -> Proses validasi, upload audio, dan simpan setoran
	 */
	public function simpan()
	{
		$this->form_validation->set_rules('nisn', 'Siswa', 'required|trim');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'required|trim');
		$this->form_validation->set_rules('waktu', 'Waktu', 'required|trim');
		$this->form_validation->set_rules('juz', 'Juz', 'required|numeric|greater_than[0]|less_than_equal_to[30]');
		$this->form_validation->set_rules('surat', 'Nama Surat', 'required|trim');
		$this->form_validation->set_rules('ayat_dari', 'Ayat Dari', 'required|numeric|greater_than[0]');
		$this->form_validation->set_rules('ayat_sampai', 'Ayat Sampai', 'required|numeric|greater_than_equal_to[' . $this->input->post('ayat_dari') . ']');
		$this->form_validation->set_rules('jenis_setoran', 'Jenis Setoran', 'required|in_list[ziyadah,murojaah,qc]');
		$this->form_validation->set_rules('jumlah_kesalahan', 'Jumlah Kesalahan', 'required|numeric|greater_than_equal_to[0]');
		$this->form_validation->set_rules('kualitas_bacaan', 'Kualitas Bacaan', 'required|in_list[baik,kurang_baik]');

		// hasil_qc wajib HANYA jika jenis_setoran = qc
		if ($this->input->post('jenis_setoran') === 'qc') {
			$this->form_validation->set_rules('hasil_qc', 'Hasil Quality Control', 'required|in_list[layak_tasmi,belum_layak]');
		}

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->tambah();
			return;
		}

		$nisn = $this->input->post('nisn', TRUE);
		$siswa = $this->Siswa_model->get_by_nisn($nisn);

		if (! $siswa) {
			$this->session->set_flashdata('error', 'Data siswa tidak ditemukan.');
			redirect('setoran/tambah');
			return;
		}

		// Validasi otorisasi guru untuk siswa terkait
		if ($this->is_guru() && ! in_array((int) $siswa->kelas_id, $this->kelas_diizinkan, TRUE)) {
			$this->session->set_flashdata('error', 'Anda tidak memiliki hak untuk menginput setoran siswa di kelas ini.');
			redirect('setoran/tambah');
			return;
		}

		// Proses Upload Rekaman Audio jika ada
		$upload = $this->upload_handler->upload_audio_setoran('audio_bukti');
		if (! $upload['success']) {
			$this->session->set_flashdata('error', 'Gagal upload rekaman audio: ' . $upload['error']);
			redirect('setoran/tambah');
			return;
		}

		$durasi_audio = $this->input->post('durasi_audio');

		$jenis_setoran    = $this->input->post('jenis_setoran', TRUE);
		$jumlah_kesalahan = (int) $this->input->post('jumlah_kesalahan');
		$kualitas_bacaan  = $this->input->post('kualitas_bacaan', TRUE);

		// Keterangan (L/CL/KL/TL) & skor dihitung OTOMATIS oleh sistem,
		// bukan dipilih manual guru, supaya penilaian konsisten dan sesuai
		// KRITERIA_PENILAIAN_TAHFIDZ.docx.
		$hasil_nilai = $this->poin_calculator->nilai_setoran($jumlah_kesalahan, $jenis_setoran, $kualitas_bacaan);

		$data_setoran = array(
			// 'kode_setoran' sengaja TIDAK di-generate di sini — create()
			// di Setoran_model menentukan kode final secara atomic berbasis
			// insert_id, untuk menghindari race condition. Lihat komentar
			// di Setoran_model::create().
			'nisn'               => $nisn,
			'kelas_id'           => $siswa->kelas_id, // denormalisasi sengaja untuk menjaga histori kelas
			'tanggal'            => $this->input->post('tanggal', TRUE),
			'waktu'              => $this->input->post('waktu', TRUE),
			'juz'                => (int) $this->input->post('juz'),
			'surat'              => $this->input->post('surat', TRUE),
			'ayat_dari'          => (int) $this->input->post('ayat_dari'),
			'ayat_sampai'        => (int) $this->input->post('ayat_sampai'),
			'jenis_setoran'      => $jenis_setoran,
			'jumlah_kesalahan'   => $jumlah_kesalahan,
			'kualitas_bacaan'    => $kualitas_bacaan,
			'keterangan'         => $hasil_nilai['keterangan'],
			'skor'               => $hasil_nilai['skor'],
			'poin'               => $hasil_nilai['poin'],
			'hasil_qc'           => ($jenis_setoran === 'qc') ? $this->input->post('hasil_qc', TRUE) : null,
			'catatan'            => $this->input->post('catatan', TRUE) ?: null,
			'audio_bukti'        => $upload['path'],
			'durasi_audio'       => ! empty($durasi_audio) ? (int) $durasi_audio : null,
			'guru_pengoreksi_id' => $this->user->id,
		);

		try {
			$this->Setoran_model->create($data_setoran);
			$this->session->set_flashdata('success', 'Setoran hafalan untuk ' . htmlspecialchars($siswa->nama) . ' berhasil disimpan.');
			redirect('setoran');
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Gagal menyimpan setoran: ' . $e->getMessage());
			redirect('setoran/tambah');
		}
	}

	/**
	 * POST /setoran/hapus/(:num) -> Hapus data setoran
	 * Wajib POST — fix keamanan (lihat catatan serupa di Users::hapus()).
	 */
	public function hapus($id)
	{
		if ($this->input->method() !== 'post') {
			show_error('Method tidak diizinkan.', 405, 'Method Not Allowed');
			return;
		}

		$setoran = $this->Setoran_model->get_by_id($id);
		if (! $setoran) {
			show_404();
			return;
		}

		if ($this->is_guru() && ! in_array((int) $setoran->kelas_id, $this->kelas_diizinkan, TRUE)) {
			show_error('Anda tidak memiliki akses untuk menghapus setoran ini.', 403, 'Akses Ditolak');
			return;
		}

		try {
			$this->Setoran_model->delete($id);
			$this->session->set_flashdata('success', 'Setoran berhasil dihapus.');
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Gagal menghapus setoran: ' . $e->getMessage());
		}

		redirect('setoran');
	}
}
