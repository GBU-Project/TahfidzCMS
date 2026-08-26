<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profile — Controller Web untuk melihat & memperbarui profil serta ganti password.
 */
class Profile extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->model('Siswa_model');
		$this->load->library('Upload_handler');
	}

	public function index()
	{
		$user = $this->User_model->get_by_id($this->user->id);
		$siswa_detail = null;

		if ($user->role === 'siswa') {
			$siswa_detail = $this->Siswa_model->get_by_user_id($user->id);
			if (! $siswa_detail) {
				$siswa_detail = $this->Siswa_model->get_by_nisn($user->username);
			}
		}

		$data = array(
			'title'        => 'Profil Pengguna',
			'user'         => $user,
			'siswa_detail' => $siswa_detail,
		);

		$this->render('profile/index', $data);
	}

	public function update()
	{
		$user_id = $this->user->id;
		$user    = $this->User_model->get_by_id($user_id);

		$this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');

		$password_lama = $this->input->post('password_lama');
		$password_baru = $this->input->post('password_baru');
		$konfirmasi    = $this->input->post('konfirmasi_password');

		if (! empty($password_baru)) {
			$this->form_validation->set_rules('password_lama', 'Password Lama', 'required');
			$this->form_validation->set_rules('password_baru', 'Password Baru', 'min_length[6]');
			$this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required|matches[password_baru]');
		}

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('profile');
			return;
		}

		$update_data = array(
			'nama' => $this->input->post('nama', TRUE),
		);

		// Jika ganti password
		if (! empty($password_baru)) {
			if (! password_verify($password_lama, $user->password)) {
				$this->session->set_flashdata('error', 'Password lama yang Anda masukkan salah.');
				redirect('profile');
				return;
			}
			$update_data['password'] = $password_baru;
		}

		// Handle Upload Foto Profil
		$upload = $this->upload_handler->upload_foto_profil('foto');
		if (! $upload['success']) {
			$this->session->set_flashdata('error', 'Gagal upload foto: ' . $upload['error']);
			redirect('profile');
			return;
		}

		if (! empty($upload['path'])) {
			$update_data['foto'] = $upload['path'];
			// Hapus foto lama jika ada
			if (! empty($user->foto) && file_exists('./' . $user->foto)) {
				@unlink('./' . $user->foto);
			}
		}

		$this->User_model->update($user_id, $update_data);

		// Update data nama di tabel siswa jika role siswa
		if ($user->role === 'siswa') {
			$siswa = $this->Siswa_model->get_by_user_id($user_id);
			if ($siswa) {
				$this->Siswa_model->update($siswa->nisn, array('nama' => $update_data['nama']));
			}
		}

		$this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
		redirect('profile');
	}
}
