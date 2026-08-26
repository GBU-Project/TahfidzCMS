<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru_kelas_model extends CI_Model
{
	private $table = 'guru_kelas';

	/**
	 * Ambil daftar kelas_id yang diampu seorang guru.
	 * Dipakai oleh MY_Controller & MY_API_Controller untuk membangun
	 * $kelas_diizinkan — jadi SATU-SATUNYA tempat query relasi ini dilakukan.
	 *
	 * @param int $user_id
	 * @return array<int>
	 */
	public function get_kelas_ids_by_guru($user_id)
	{
		$rows = $this->db
			->select('kelas_id')
			->where('user_id', $user_id)
			->get($this->table)
			->result();

		return array_map(function ($r) {
			return (int) $r->kelas_id;
		}, $rows);
	}

	/**
	 * Set ulang relasi guru-kelas (dipakai di form Users saat admin
	 * mengubah penugasan kelas seorang guru). Hapus semua relasi lama
	 * lalu insert ulang, dibungkus transaction oleh pemanggil.
	 *
	 * @param int $user_id
	 * @param array<int> $kelas_ids
	 */
	public function set_kelas_for_guru($user_id, array $kelas_ids)
	{
		$this->db->delete($this->table, array('user_id' => $user_id));

		if (empty($kelas_ids)) {
			return;
		}

		$rows = array();
		foreach ($kelas_ids as $kelas_id) {
			$rows[] = array('user_id' => $user_id, 'kelas_id' => (int) $kelas_id);
		}

		$this->db->insert_batch($this->table, $rows);
	}
}
