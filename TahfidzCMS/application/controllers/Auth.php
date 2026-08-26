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

		$this->load->view('auth/login', array('error' => null));
	}

	private function _process_login()
	{
		$this->form_validation->set_rules('username', 'NIP/NISN', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() === FALSE) {
			$this->load->view('auth/login', array('error' => validation_errors()));
			return;
		}

		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', FALSE);

		$user = $this->User_model->verify_credentials($username, $password);

		if (! $user) {
			$this->load->view('auth/login', array('error' => 'NIP/NISN atau password salah.'));
			return;
		}

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
