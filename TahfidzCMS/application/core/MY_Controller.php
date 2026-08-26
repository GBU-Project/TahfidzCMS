<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller — Base controller untuk semua controller WEB (session-based).
 *
 * Tanggung jawab:
 *  1. Memastikan user sudah login (redirect ke /login jika belum).
 *  2. Menyediakan $this->user (data user yang sedang login).
 *  3. Menyediakan $this->kelas_diizinkan (khusus role guru) — daftar
 *     kelas_id yang boleh diakses guru tsb. Ini pusat dari aturan
 *     "guru hanya boleh lihat kelas yang diampu", supaya tidak perlu
 *     ditulis ulang di setiap controller/model (berbeda dari versi
 *     Apps Script lama yang mengulang logika ini di banyak fungsi).
 *  4. Helper require_role() untuk role guard per-controller/per-method.
 */
class MY_Controller extends CI_Controller
{
	/** @var object|null Data user yang sedang login (dari session) */
	protected $user;

	/**
	 * @var string|null Role user yang sedang login ('admin'|'guru'|'siswa'),
	 * disalin dari $this->user->role supaya controller turunan bisa pakai
	 * $this->role langsung tanpa null-check ke $this->user setiap saat.
	 */
	protected $role;

	/** @var array Daftar kelas_id yang boleh diakses (kosong = semua, untuk admin) */
	protected $kelas_diizinkan = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('User_model');
		$this->load->model('Guru_kelas_model');

		$this->_check_session();
		$this->_load_kelas_diizinkan();
	}

	/**
	 * Pastikan session valid. Kalau tidak, redirect ke halaman login.
	 */
	private function _check_session()
	{
		$user_id = $this->session->userdata('user_id');

		if (! $user_id) {
			redirect('login');
			return;
		}

		$this->user = $this->User_model->get_by_id($user_id);

		// Jaga-jaga: user dihapus/dinonaktifkan tapi session masih ada
		if (! $this->user || (int) $this->user->is_active !== 1) {
			$this->session->sess_destroy();
			redirect('login');
			return;
		}

		$this->role = $this->user->role;
	}

	/**
	 * Kalau role = guru, muat daftar kelas_id yang diampu.
	 * Admin dianggap boleh akses semua kelas (kelas_diizinkan dibiarkan kosong,
	 * model/controller yang query HARUS treat "kosong" = "semua" HANYA jika role admin,
	 * jangan sampai tertukar dengan "guru tanpa kelas" — lihat is_admin()/is_guru() di bawah.
	 */
	private function _load_kelas_diizinkan()
	{
		if ($this->user && $this->user->role === 'guru') {
			$this->kelas_diizinkan = $this->Guru_kelas_model->get_kelas_ids_by_guru($this->user->id);
		}
	}

	/**
	 * Role guard. Panggil di awal method controller yang dibatasi role tertentu.
	 * Contoh: $this->require_role(['admin', 'guru']);
	 *
	 * @param array $allowed_roles
	 */
	protected function require_role(array $allowed_roles)
	{
		if (! in_array($this->user->role, $allowed_roles, TRUE)) {
			show_error('Anda tidak memiliki akses ke halaman ini.', 403, 'Akses Ditolak');
		}
	}

	protected function is_admin()
	{
		return $this->user->role === 'admin';
	}

	protected function is_guru()
	{
		return $this->user->role === 'guru';
	}

	protected function is_siswa()
	{
		return $this->user->role === 'siswa';
	}

	/**
	 * Helper untuk cek apakah guru yang login boleh akses kelas tertentu.
	 * Admin selalu TRUE. Siswa tidak relevan (dibatasi di level controller siswa).
	 *
	 * @param int $kelas_id
	 * @return bool
	 */
	protected function boleh_akses_kelas($kelas_id)
	{
		if ($this->is_admin()) {
			return TRUE;
		}

		if ($this->is_guru()) {
			return in_array((int) $kelas_id, $this->kelas_diizinkan, TRUE);
		}

		return FALSE;
	}

	/**
	 * Render view dengan template standar (header + sidebar + footer).
	 * Dipakai oleh semua controller turunan supaya tidak perlu load
	 * header/sidebar/footer manual satu-satu di setiap method.
	 *
	 * @param string $view_path  path view konten, mis. 'dashboard/admin'
	 * @param array  $data       data yang dipass ke view
	 */
	protected function render($view_path, array $data = array())
	{
		$data['current_user'] = $this->user;

		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view($view_path, $data);
		$this->load->view('templates/footer', $data);
	}
}
