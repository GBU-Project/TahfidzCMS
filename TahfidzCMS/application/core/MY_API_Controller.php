<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_API_Controller — Base controller untuk semua controller API (token-based).
 *
 * Autentikasi terpisah dari session web: client (mis. app mobile) mengirim
 * header `Authorization: Bearer <token>`, token dicocokkan ke tabel
 * `api_tokens`. Model & library bisnis yang dipakai TETAP SAMA dengan
 * controller web (Siswa_model, Setoran_model, Poin_calculator, dll) —
 * supaya hasil web dan API selalu konsisten.
 */
class MY_API_Controller extends CI_Controller
{
	/** @var object|null Data user yang terautentikasi via token */
	protected $user;

	/** @var string|null Role user ('admin'|'guru'|'siswa'), disalin dari $this->user->role */
	protected $role;

	/** @var array Daftar kelas_id yang boleh diakses (khusus role guru) */
	protected $kelas_diizinkan = array();

	public function __construct()
	{
		parent::__construct();

		// Tidak load 'session' di sini — API murni stateless via token.
		$this->load->model('User_model');
		$this->load->model('Api_token_model');
		$this->load->model('Guru_kelas_model');

		$this->_check_token();
		$this->_load_kelas_diizinkan();
	}

	/**
	 * Ambil token dari header Authorization, validasi ke tabel api_tokens.
	 * Kalau tidak valid/kedaluwarsa, hentikan request dengan 401 JSON.
	 */
	private function _check_token()
	{
		$header = $this->input->get_request_header('Authorization', TRUE);

		if (! $header || stripos($header, 'Bearer ') !== 0) {
			$this->json_error('Token tidak ditemukan. Sertakan header Authorization: Bearer <token>', 401);
			return;
		}

		$token = trim(substr($header, 7));
		$token_row = $this->Api_token_model->get_valid_token($token);

		if (! $token_row) {
			$this->json_error('Token tidak valid atau sudah kedaluwarsa.', 401);
			return;
		}

		$this->user = $this->User_model->get_by_id($token_row->user_id);

		if (! $this->user || (int) $this->user->is_active !== 1) {
			$this->json_error('Akun tidak aktif.', 401);
			return;
		}

		$this->role = $this->user->role;
	}

	private function _load_kelas_diizinkan()
	{
		if ($this->user && $this->user->role === 'guru') {
			$this->kelas_diizinkan = $this->Guru_kelas_model->get_kelas_ids_by_guru($this->user->id);
		}
	}

	protected function require_role(array $allowed_roles)
	{
		if (! in_array($this->user->role, $allowed_roles, TRUE)) {
			$this->json_error('Anda tidak memiliki akses ke endpoint ini.', 403);
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
	 * Response sukses standar.
	 *
	 * @param mixed $data
	 * @param string $message
	 */
	protected function json_success($data = null, $message = 'OK')
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode(array(
				'status'  => 'success',
				'message' => $message,
				'data'    => $data,
			)));
	}

	/**
	 * Response error standar. Otomatis stop eksekusi (exit) supaya
	 * controller pemanggil tidak lanjut memproses setelah error.
	 *
	 * @param string $message
	 * @param int    $http_code
	 */
	protected function json_error($message = 'Terjadi kesalahan', $http_code = 400)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($http_code)
			->set_output(json_encode(array(
				'status'  => 'error',
				'message' => $message,
			)));
		exit;
	}
}
