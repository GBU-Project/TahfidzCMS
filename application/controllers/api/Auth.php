<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * api/Auth — Login/logout untuk API (token-based).
 * Sengaja tidak extends MY_API_Controller karena endpoint login belum
 * punya token untuk divalidasi (justru endpoint ini yang menerbitkannya).
 */
class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->model('Api_token_model');
		$this->load->model('Login_attempt_model');
	}

	/**
	 * POST /api/auth/login
	 * Body (form-urlencoded/json): username, password
	 * Response sukses: { token, user: { id, nama, role, ... } }
	 *
	 * Dibatasi rate limit: maksimal Login_attempt_model::MAX_ATTEMPTS
	 * percobaan gagal per username+IP dalam jendela Login_attempt_model::WINDOW_SECONDS,
	 * untuk mencegah brute-force credential.
	 */
	public function login()
	{
		if ($this->input->method() !== 'post') {
			$this->_json_error('Method tidak diizinkan, gunakan POST.', 405);
			return;
		}

		$username   = trim((string) $this->input->post('username'));
		$password   = (string) $this->input->post('password');
		$ip_address = $this->input->ip_address();

		if ($username === '' || $password === '') {
			$this->_json_error('username dan password wajib diisi.', 422);
			return;
		}

		if ($this->Login_attempt_model->is_locked($username, $ip_address)) {
			$this->_json_error('Terlalu banyak percobaan login gagal. Silakan coba lagi dalam beberapa menit.', 429);
			return;
		}

		$user = $this->User_model->verify_credentials($username, $password);

		if (! $user) {
			$this->Login_attempt_model->record_failed($username, $ip_address);
			$this->_json_error('NIP/NISN atau password salah.', 401);
			return;
		}

		$this->Login_attempt_model->clear($username, $ip_address);

		$token = $this->Api_token_model->issue($user->id);

		$this->_json_success(array(
			'token' => $token,
			'user'  => array(
				'id'       => $user->id,
				'nama'     => $user->nama,
				'username' => $user->username,
				'role'     => $user->role,
				'foto'     => $user->foto,
			),
		), 'Login berhasil');
	}

	/**
	 * POST /api/auth/logout
	 * Header: Authorization: Bearer <token>
	 */
	public function logout()
	{
		$header = $this->input->get_request_header('Authorization', TRUE);

		if ($header && stripos($header, 'Bearer ') === 0) {
			$token = trim(substr($header, 7));
			$this->Api_token_model->revoke($token);
		}

		$this->_json_success(null, 'Logout berhasil');
	}

	private function _json_success($data, $message)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode(array('status' => 'success', 'message' => $message, 'data' => $data)));
	}

	private function _json_error($message, $code)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($code)
			->set_output(json_encode(array('status' => 'error', 'message' => $message)));
	}
}
