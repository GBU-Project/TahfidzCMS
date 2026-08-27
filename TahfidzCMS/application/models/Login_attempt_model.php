<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login_attempt_model — Rate limiting sederhana untuk api/auth/login.
 * Mencegah brute-force credential dengan membatasi jumlah percobaan
 * login GAGAL dalam jendela waktu tertentu, per kombinasi username+IP.
 */
class Login_attempt_model extends CI_Model
{
	private $table = 'login_attempts';

	/** Maksimal percobaan gagal yang diizinkan dalam jendela waktu. */
	const MAX_ATTEMPTS = 5;

	/** Lebar jendela waktu (detik) untuk menghitung percobaan. */
	const WINDOW_SECONDS = 15 * 60; // 15 menit

	/**
	 * Cek apakah username+IP ini sedang di-lock karena terlalu banyak
	 * percobaan gagal dalam jendela waktu berjalan.
	 *
	 * @param string $username
	 * @param string $ip_address
	 * @return bool TRUE jika terkena limit (harus ditolak)
	 */
	public function is_locked($username, $ip_address)
	{
		$count = $this->db
			->where('username', $username)
			->where('ip_address', $ip_address)
			->where('attempted_at >=', date('Y-m-d H:i:s', time() - self::WINDOW_SECONDS))
			->count_all_results($this->table);

		return $count >= self::MAX_ATTEMPTS;
	}

	/**
	 * Catat satu percobaan login gagal.
	 *
	 * @param string $username
	 * @param string $ip_address
	 */
	public function record_failed($username, $ip_address)
	{
		$this->db->insert($this->table, array(
			'username'   => $username,
			'ip_address' => $ip_address,
		));
	}

	/**
	 * Hapus riwayat percobaan gagal untuk username+IP ini, dipanggil
	 * setelah login berhasil supaya limit ter-reset.
	 *
	 * @param string $username
	 * @param string $ip_address
	 */
	public function clear($username, $ip_address)
	{
		$this->db
			->where('username', $username)
			->where('ip_address', $ip_address)
			->delete($this->table);
	}

	/** Bersihkan histori lama di luar jendela waktu (opsional, via cron). */
	public function purge_old()
	{
		return $this->db
			->where('attempted_at <', date('Y-m-d H:i:s', time() - self::WINDOW_SECONDS))
			->delete($this->table);
	}
}
