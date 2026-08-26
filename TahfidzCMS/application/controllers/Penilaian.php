<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Penilaian — Controller Web untuk mereview, memutar audio rekaman bukti setoran,
 * dan mengoreksi nilai tajwid / kelancaran serta catatan evaluasi.
 * Akses dibatasi untuk role admin dan guru.
 */
class Penilaian extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->require_role(array('admin', 'guru'));

		$this->load->model('Setoran_model');
		$this->load->model('Kelas_model');
		$this->load->model('Siswa_model');
		$this->load->library('Poin_calculator');
	}

	/**
	 * GET /penilaian -> Halaman utama daftar penilaian setoran
	 */
	public function index()
	{
		$kelas_id = $this->input->get('kelas_id');
		$status   = $this->input->get('status');
		$search   = $this->input->get('q');

		if ($kelas_id && ! $this->boleh_akses_kelas($kelas_id)) {
			show_error('Anda tidak memiliki akses ke kelas ini.', 403, 'Akses Ditolak');
			return;
		}

		$filter = array(
			'kelas_id'  => $kelas_id,
			'kelas_ids' => $this->is_guru() ? $this->kelas_diizinkan : array(),
			'status'    => $status,
			'search'    => $search,
		);

		$data = array(
			'title'          => 'Penilaian & Evaluasi Setoran',
			'setoran_list'   => $this->Setoran_model->get_all($filter, 100),
			'kelas_list'     => $this->is_guru() ? $this->Kelas_model->get_by_ids($this->kelas_diizinkan) : $this->Kelas_model->get_all(),
			'selected_kelas' => $kelas_id,
			'selected_status'=> $status,
			'search'         => $search,
		);

		$this->render('penilaian/index', $data);
	}

	/**
	 * POST /penilaian/simpan/(:num) -> Simpan perubahan nilai / koreksi setoran
	 */
	public function simpan($id)
	{
		$setoran = $this->Setoran_model->get_by_id($id);
		if (! $setoran) {
			show_404();
			return;
		}

		if ($this->is_guru() && ! in_array((int) $setoran->kelas_id, $this->kelas_diizinkan, TRUE)) {
			show_error('Anda tidak memiliki akses untuk menilai setoran ini.', 403, 'Akses Ditolak');
			return;
		}

		$this->form_validation->set_rules('nilai', 'Nilai Tajwid', 'required|in_list[A,B,C]');
		$this->form_validation->set_rules('status', 'Status Kelancaran', 'required|in_list[Lancar,Cukup,Perlu Perbaikan]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('penilaian');
			return;
		}

		$update_data = array(
			'nilai'              => $this->input->post('nilai', TRUE),
			'status'             => $this->input->post('status', TRUE),
			'catatan'            => $this->input->post('catatan', TRUE) ?: null,
			'guru_pengoreksi_id' => $this->user->id,
		);

		try {
			$this->Setoran_model->update_penilaian($id, $update_data);
			$this->session->set_flashdata('success', 'Penilaian setoran ' . htmlspecialchars($setoran->kode_setoran) . ' berhasil diperbarui.');
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Gagal memperbarui penilaian: ' . $e->getMessage());
		}

		redirect('penilaian');
	}
}
