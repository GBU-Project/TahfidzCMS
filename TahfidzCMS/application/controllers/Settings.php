<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Settings Controller — Pengaturan Identitas Lembaga & Konfigurasi Global.
 * Hak Akses: Super Admin Only (Server-side RBAC Guard).
 */
class Settings extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->require_role(array('admin'));

		$this->load->model('Setting_model');
		$this->load->library('Upload_handler');
	}

	/**
	 * GET /settings -> Form Pengaturan Identitas Lembaga
	 */
	public function index()
	{
		$settings = $this->Setting_model->get_all();

		$data = array(
			'title'    => 'Pengaturan Identitas Lembaga',
			'settings' => $settings,
		);

		$this->render('settings/index', $data);
	}

	/**
	 * POST /settings/update -> Simpan Perubahan Identitas Lembaga
	 */
	public function update()
	{
		$this->form_validation->set_rules('institution_name', 'Nama Lembaga', 'required|trim|max_length[150]');
		$this->form_validation->set_rules('institution_short_name', 'Nama Singkat / Brand', 'required|trim|max_length[60]');
		$this->form_validation->set_rules('institution_tagline', 'Tagline / Deskripsi Singkat', 'trim|max_length[255]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('settings');
			return;
		}

		$institution_name       = $this->input->post('institution_name', TRUE);
		$institution_short_name = $this->input->post('institution_short_name', TRUE);
		$institution_tagline    = $this->input->post('institution_tagline', TRUE);

		$update_data = array(
			'institution_name'       => $institution_name,
			'institution_short_name' => $institution_short_name,
			'institution_tagline'    => $institution_tagline,
		);

		// Handle Upload Logo Lembaga (jika ada file diunggah)
		if (! empty($_FILES['institution_logo']['name'])) {
			$upload = $this->upload_handler->upload_logo_lembaga('institution_logo');

			if (! $upload['success']) {
				$this->session->set_flashdata('error', 'Gagal mengunggah logo: ' . $upload['error']);
				redirect('settings');
				return;
			}

			if (! empty($upload['path'])) {
				// Hapus logo lama jika ada dan file fisik eksis
				$current_logo = $this->Setting_model->get('institution_logo');
				if (! empty($current_logo) && file_exists('./' . $current_logo)) {
					@unlink('./' . $current_logo);
				}

				$update_data['institution_logo'] = $upload['path'];
			}
		}

		// Simpan perubahan ke database
		$saved = $this->Setting_model->set_many($update_data);

		if ($saved) {
			$this->session->set_flashdata('success', 'Identitas lembaga berhasil diperbarui.');
		} else {
			$this->session->set_flashdata('error', 'Terjadi kendala saat menyimpan identitas lembaga.');
		}

		redirect('settings');
	}
}
