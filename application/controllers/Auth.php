<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth — Login/logout untuk WEB (session-based).
 * Catatan: controller ini SENGAJA tidak extends MY_Controller, karena
 * MY_Controller mewajibkan session valid (justru yang mau kita buat di sini).
 */
class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		// Jika aplikasi belum diinstall (installed.lock belum ada),
		// arahkan pengunjung root/login otomatis ke installer
		if (! file_exists(FCPATH . 'installed.lock') && ! file_exists(APPPATH . 'config/installed.lock')) {
			redirect('installer');
			exit();
		}

		$this->load->database();
		$this->load->model('User_model');
		$this->load->model('Setting_model');
		$this->load->model('Login_attempt_model');
	}

	/**
	 * GET / -> Default route handler
	 */
	public function index()
	{
		$this->login();
	}

	/**
	 * GET /login  -> tampilkan form
	 * POST /login -> proses login
	 */
	public function login()
	{
		// Sudah login? langsung ke dashboard.
		if ($this->session->userdata('user_id')) {
			redirect('dashboard');
			return;
		}

		if ($this->input->method() === 'post') {
			$this->_process_login();
			return;
		}

		$settings = $this->Setting_model->get_all();

		$this->load->view('auth/login', array(
			'error'    => null,
			'settings' => $settings,
		));
	}

	private function _process_login()
	{
		$this->form_validation->set_rules('username', 'NIP/NISN', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() === FALSE) {
			$settings = $this->Setting_model->get_all();
			$this->load->view('auth/login', array('error' => validation_errors(), 'settings' => $settings));
			return;
		}

		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', FALSE);
		$ip_address = $this->input->ip_address();

		// Fix keamanan: rate limit login web, konsisten dengan api/Auth.php
		// yang sudah lebih dulu memakai Login_attempt_model. Sebelumnya
		// login web tidak dibatasi sama sekali sehingga bisa di-brute-force.
		if ($this->Login_attempt_model->is_locked($username, $ip_address)) {
			$settings = $this->Setting_model->get_all();
			$this->load->view('auth/login', array(
				'error'    => 'Terlalu banyak percobaan login gagal. Silakan coba lagi dalam beberapa menit.',
				'settings' => $settings,
			));
			return;
		}

		$user = $this->User_model->verify_credentials($username, $password);

		if (! $user) {
			$this->Login_attempt_model->record_failed($username, $ip_address);
			$settings = $this->Setting_model->get_all();
			$this->load->view('auth/login', array('error' => 'NIP/NISN atau password salah.', 'settings' => $settings));
			return;
		}

		$this->Login_attempt_model->clear($username, $ip_address);

		// Fix keamanan: regenerasi session ID saat login berhasil (mencegah
		// session fixation — attacker yang sudah tahu/menyuntik session ID
		// korban SEBELUM login tidak bisa lagi memakai ID yang sama setelahnya).
		$this->session->sess_regenerate(TRUE);

		$this->session->set_userdata(array(
			'user_id'  => $user->id,
			'username' => $user->username,
			'nama'     => $user->nama,
			'role'     => $user->role,
		));

		redirect('dashboard');
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}
}
