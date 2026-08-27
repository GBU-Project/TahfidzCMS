<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_token_model extends CI_Model
{
	private $table = 'api_tokens';

	/** Masa berlaku token, dalam detik. 7 hari. */
	const TOKEN_LIFETIME = 7 * 24 * 60 * 60;

	/**
	 * Terbitkan token baru untuk user (dipanggil saat login API berhasil).
	 *
	 * @param int $user_id
	 * @return string token yang baru dibuat
	 */
	public function issue($user_id)
	{
		$token = bin2hex(random_bytes(32));

		$this->db->insert($this->table, array(
			'user_id'    => $user_id,
			'token'      => $token,
			'expired_at' => date('Y-m-d H:i:s', time() + self::TOKEN_LIFETIME),
		));

		return $token;
	}

	/**
	 * Ambil row token jika valid (ada & belum kedaluwarsa).
	 *
	 * @param string $token
	 * @return object|null
	 */
	public function get_valid_token($token)
	{
		return $this->db
			->where('token', $token)
			->where('expired_at >=', date('Y-m-d H:i:s'))
			->get($this->table)
			->row();
	}

	public function revoke($token)
	{
		return $this->db->delete($this->table, array('token' => $token));
	}

	/** Bersihkan token yang sudah kedaluwarsa (bisa dijadwalkan via cron) */
	public function purge_expired()
	{
		return $this->db->where('expired_at <', date('Y-m-d H:i:s'))->delete($this->table);
	}
}
