<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
	private $table = 'users';

	public function get_by_id($id)
	{
		return $this->db->get_where($this->table, array('id' => $id))->row();
	}

	public function get_by_username($username)
	{
		return $this->db->get_where($this->table, array('username' => $username))->row();
	}

	/**
	 * Verifikasi kredensial login. Mengembalikan row user jika cocok,
	 * atau FALSE jika tidak ditemukan/password salah/akun nonaktif.
	 *
	 * @param string $username
	 * @param string $password  (plain text, akan dicocokkan dg password_verify)
	 * @return object|false
	 */
	public function verify_credentials($username, $password)
	{
		$user = $this->get_by_username($username);

		if (! $user) {
			return FALSE;
		}

		if ((int) $user->is_active !== 1) {
			return FALSE;
		}

		if (! password_verify($password, $user->password)) {
			return FALSE;
		}

		return $user;
	}

	public function create(array $data)
	{
		if (isset($data['password'])) {
			$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		}

		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		if (isset($data['password']) && $data['password'] !== '') {
			$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		} else {
			unset($data['password']); // jangan timpa password kalau field dikosongkan di form edit
		}

		return $this->db->update($this->table, $data, array('id' => $id));
	}

	public function update_password($id, $new_password)
	{
		return $this->db->update(
			$this->table,
			array('password' => password_hash($new_password, PASSWORD_BCRYPT)),
			array('id' => $id)
		);
	}

	public function delete($id)
	{
		return $this->db->delete($this->table, array('id' => $id));
	}

	public function get_all_by_role($role = null)
	{
		$this->db->select('users.*, siswa.access_token, siswa.nisn as siswa_nisn')
			->from($this->table)
			->join('siswa', 'siswa.user_id = users.id', 'left');

		if ($role) {
			$this->db->where('users.role', $role);
		}
		return $this->db->get()->result();
	}
}
