<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Users — CRUD akun (admin/guru/siswa). Hanya bisa diakses admin.
 *
 * Catatan desain:
 *  - Untuk role 'guru': setelah user dibuat, penugasan kelas disimpan
 *    lewat Guru_kelas_model (tabel relasi), bukan string "7A,7B" seperti
 *    di versi Apps Script lama.
 *  - Untuk role 'siswa': satu akun user SELALU dipasangkan dengan satu
 *    baris di tabel siswa (nisn = username). Keduanya dibuat/diupdate
 *    dalam satu transaction supaya tidak ada akun user tanpa data siswa,
 *    atau sebaliknya.
 */
class Users extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->require_role(array('admin'));

		$this->load->model('Kelas_model');
		$this->load->model('Siswa_model');
		$this->load->library('Upload_handler');
	}

	/**
	 * GET /users?role=guru  (role opsional, default tampilkan semua)
	 */
	public function index()
	{
		$role_filter = $this->input->get('role');

		$data = array(
			'title'       => 'Kelola Users',
			'role_filter' => $role_filter,
			'users_list'  => $this->User_model->get_all_by_role($role_filter ?: null),
			'kelas_list'  => $this->Kelas_model->get_all(),
		);

		$this->render('users/index', $data);
	}

	/**
	 * GET /users/form            -> form tambah user baru
	 * GET /users/form/(:num)     -> form edit user
	 */
	public function form($id = null)
	{
		$user = null;
		$siswa = null;
		$kelas_guru_ids = array();

		if ($id) {
			$user = $this->User_model->get_by_id($id);

			if (! $user) {
				show_404();
				return;
			}

			if ($user->role === 'siswa') {
				$siswa = $this->Siswa_model->get_by_nisn($user->username);
			} elseif ($user->role === 'guru') {
				$kelas_guru_ids = $this->Guru_kelas_model->get_kelas_ids_by_guru($user->id);
			}
		}

		$data = array(
			'title'          => $id ? 'Edit User' : 'Tambah User',
			'user'           => $user,
			'siswa'          => $siswa,
			'kelas_guru_ids' => $kelas_guru_ids,
			'kelas_list'     => $this->Kelas_model->get_all(),
		);

		$this->render('users/form', $data);
	}

	/**
	 * POST /users/simpan
	 * Field umum : id (kosong=baru), nama, username, password, role, foto
	 * Field guru : kelas_ids[]  (checkbox multi kelas yang diampu)
	 * Field siswa: kelas_id, target_juz
	 */
	public function simpan()
	{
		$id   = $this->input->post('id');
		$role = $this->input->post('role');

		$this->form_validation->set_rules('nama', 'Nama', 'required|trim|max_length[100]');
		$this->form_validation->set_rules('username', 'NIP/NISN', 'required|trim|max_length[50]');
		$this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,guru,siswa]');

		if (! $id) {
			// Password wajib diisi hanya saat membuat user baru.
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		}

		if ($role === 'siswa') {
			$this->form_validation->set_rules('kelas_id', 'Kelas', 'required|numeric');
			$this->form_validation->set_rules('target_juz', 'Target Juz', 'required|numeric|greater_than[0]|less_than_equal_to[30]');
		}

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect($id ? "users/form/{$id}" : 'users/form');
			return;
		}

		// Cek username unik (kecuali saat edit, boleh sama dengan dirinya sendiri)
		$existing = $this->User_model->get_by_username($this->input->post('username', TRUE));
		if ($existing && (! $id || (int) $existing->id !== (int) $id)) {
			$this->session->set_flashdata('error', 'NIP/NISN sudah dipakai oleh user lain.');
			redirect($id ? "users/form/{$id}" : 'users/form');
			return;
		}

		$upload = $this->upload_handler->upload_foto_profil('foto');
		if (! $upload['success']) {
			$this->session->set_flashdata('error', 'Gagal upload foto: ' . $upload['error']);
			redirect($id ? "users/form/{$id}" : 'users/form');
			return;
		}

		$user_data = array(
			'nama'     => $this->input->post('nama', TRUE),
			'username' => $this->input->post('username', TRUE),
			'role'     => $role,
		);

		if ($upload['path']) {
			$user_data['foto'] = $upload['path'];
		}

		$password = $this->input->post('password');
		if ($password) {
			$user_data['password'] = $password; // di-hash otomatis di dalam User_model
		}

		$this->db->trans_begin();

		try {
			if ($id) {
				$this->User_model->update($id, $user_data);
				$user_id = $id;
			} else {
				$user_id = $this->User_model->create($user_data);
			}

			if ($role === 'guru') {
				$kelas_ids = $this->input->post('kelas_ids') ?: array();
				$this->Guru_kelas_model->set_kelas_for_guru($user_id, $kelas_ids);
			}

			if ($role === 'siswa') {
				$nisn = $user_data['username'];

				$siswa_data = array(
					'nama'       => $user_data['nama'],
					'kelas_id'   => $this->input->post('kelas_id'),
					'target_juz' => $this->input->post('target_juz'),
					'user_id'    => $user_id,
				);

				if ($this->Siswa_model->nisn_exists($nisn)) {
					$this->Siswa_model->update($nisn, $siswa_data);
				} else {
					$siswa_data['nisn'] = $nisn;
					$this->Siswa_model->create($siswa_data);
				}
			}

			if ($this->db->trans_status() === FALSE) {
				throw new Exception('Transaksi database gagal.');
			}

			$this->db->trans_commit();
			$this->session->set_flashdata('success', $id ? 'User berhasil diperbarui.' : 'User baru berhasil ditambahkan.');
		} catch (Exception $e) {
			$this->db->trans_rollback();
			$this->session->set_flashdata('error', 'Gagal menyimpan: ' . $e->getMessage());
		}

		redirect('users');
	}

	/**
	 * GET /users/hapus/(:num)
	 * Menghapus user. Jika role siswa, baris di tabel siswa ikut terhapus
	 * otomatis lewat ON DELETE SET NULL/CASCADE yang relevan (lihat skema),
	 * namun di sini kita hapus eksplisit dulu agar histori setoran (yang
	 * FK-nya CASCADE ke siswa) tidak hilang tanpa sepengetahuan admin —
	 * karenanya khusus siswa yang MASIH punya setoran, hapus ditolak.
	 */
	public function hapus($id)
	{
		$user = $this->User_model->get_by_id($id);

		if (! $user) {
			show_404();
			return;
		}

		if ($user->role === 'siswa') {
			$jumlah_setoran = $this->db->where('nisn', $user->username)->count_all_results('setoran');
			if ($jumlah_setoran > 0) {
				$this->session->set_flashdata('error', "Tidak bisa dihapus: siswa ini punya {$jumlah_setoran} riwayat setoran.");
				redirect('users');
				return;
			}
			$this->Siswa_model->delete($user->username);
		}

		$this->User_model->delete($id);
		$this->session->set_flashdata('success', 'User berhasil dihapus.');
		redirect('users');
	}
}
