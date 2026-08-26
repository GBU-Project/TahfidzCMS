<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelas — CRUD data master kelas. Hanya admin.
 */
class Kelas extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->require_role(array('admin'));
		$this->load->model('Kelas_model');
	}

	public function index()
	{
		$data = array(
			'title'      => 'Kelola Kelas',
			'kelas_list' => $this->Kelas_model->get_all(),
		);

		$this->render('kelas/index', $data);
	}

	/**
	 * POST /kelas/simpan
	 * Body: id (opsional, kosong = tambah baru), nama_kelas
	 */
	public function simpan()
	{
		$this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'required|trim|max_length[20]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('kelas');
			return;
		}

		$id         = $this->input->post('id');
		$nama_kelas = $this->input->post('nama_kelas', TRUE);

		if ($id) {
			$this->Kelas_model->update($id, $nama_kelas);
			$this->session->set_flashdata('success', 'Kelas berhasil diperbarui.');
		} else {
			$this->Kelas_model->create($nama_kelas);
			$this->session->set_flashdata('success', 'Kelas baru berhasil ditambahkan.');
		}

		redirect('kelas');
	}

	public function hapus($id)
	{
		$result = $this->Kelas_model->delete($id);

		if ($result === TRUE) {
			$this->session->set_flashdata('success', 'Kelas berhasil dihapus.');
		} else {
			$this->session->set_flashdata('error', $result);
		}

		redirect('kelas');
	}
}
