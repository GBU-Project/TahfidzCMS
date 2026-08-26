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

		$data = array(
			'title'         => 'Data Input Setoran',
			'setoran_list'  => $this->Setoran_model->get_all($filter, 100),
			'kelas_list'    => $this->is_guru() ? $this->Kelas_model->get_by_ids($this->kelas_diizinkan) : $this->Kelas_model->get_all(),
			'selected_kelas'=> $kelas_id,
			'selected_nisn' => $nisn,
			'search'        => $search,
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
			'title'         => 'Input Setoran Baru',
			'siswa_list'    => $siswa_list,
			'kelas_list'    => $kelas_list,
			'daftar_surat'  => $this->poin_calculator->get_daftar_surat(),
			'auto_kode'     => $this->Setoran_model->generate_kode_setoran(),
			'default_date'  => date('Y-m-d'),
			'default_time'  => date('H:i'),
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
		$this->form_validation->set_rules('nilai', 'Nilai Tajwid', 'required|in_list[A,B,C]');
		$this->form_validation->set_rules('status', 'Status Kelancaran', 'required|in_list[Lancar,Cukup,Perlu Perbaikan]');

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

		$data_setoran = array(
			'kode_setoran'       => $this->Setoran_model->generate_kode_setoran(),
			'nisn'               => $nisn,
			'kelas_id'           => $siswa->kelas_id, // denormalisasi sengaja untuk menjaga histori kelas
			'tanggal'            => $this->input->post('tanggal', TRUE),
			'waktu'              => $this->input->post('waktu', TRUE),
			'juz'                => (int) $this->input->post('juz'),
			'surat'              => $this->input->post('surat', TRUE),
			'ayat_dari'          => (int) $this->input->post('ayat_dari'),
			'ayat_sampai'        => (int) $this->input->post('ayat_sampai'),
			'nilai'              => $this->input->post('nilai', TRUE),
			'status'             => $this->input->post('status', TRUE),
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
	 * GET /setoran/hapus/(:num) -> Hapus data setoran
	 */
	public function hapus($id)
	{
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
