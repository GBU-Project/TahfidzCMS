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
		$kelas_id   = $this->input->get('kelas_id');
		$keterangan = $this->input->get('keterangan');
		$search     = $this->input->get('q');

		if ($kelas_id && ! $this->boleh_akses_kelas($kelas_id)) {
			show_error('Anda tidak memiliki akses ke kelas ini.', 403, 'Akses Ditolak');
			return;
		}

		$filter = array(
			'kelas_id'   => $kelas_id,
			'kelas_ids'  => $this->is_guru() ? $this->kelas_diizinkan : array(),
			'keterangan' => $keterangan,
			'search'     => $search,
		);

		$data = array(
			'title'              => 'Penilaian & Evaluasi Setoran',
			'setoran_list'       => $this->Setoran_model->get_all($filter, 100),
			'kelas_list'         => $this->is_guru() ? $this->Kelas_model->get_by_ids($this->kelas_diizinkan) : $this->Kelas_model->get_all(),
			'selected_kelas'     => $kelas_id,
			'selected_keterangan'=> $keterangan,
			'search'             => $search,
			'jenis_setoran_list' => Poin_calculator::JENIS_SETORAN_LABEL,
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

		$this->form_validation->set_rules('jenis_setoran', 'Jenis Setoran', 'required|in_list[ziyadah,murojaah,qc]');
		$this->form_validation->set_rules('jumlah_kesalahan', 'Jumlah Kesalahan', 'required|numeric|greater_than_equal_to[0]');
		$this->form_validation->set_rules('kualitas_bacaan', 'Kualitas Bacaan', 'required|in_list[baik,kurang_baik]');

		if ($this->input->post('jenis_setoran') === 'qc') {
			$this->form_validation->set_rules('hasil_qc', 'Hasil Quality Control', 'required|in_list[layak_tasmi,belum_layak]');
		}

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('penilaian');
			return;
		}

		$jenis_setoran = $this->input->post('jenis_setoran', TRUE);

		$update_data = array(
			'jenis_setoran'      => $jenis_setoran,
			'jumlah_kesalahan'   => (int) $this->input->post('jumlah_kesalahan'),
			'kualitas_bacaan'    => $this->input->post('kualitas_bacaan', TRUE),
			'hasil_qc'           => ($jenis_setoran === 'qc') ? $this->input->post('hasil_qc', TRUE) : null,
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
