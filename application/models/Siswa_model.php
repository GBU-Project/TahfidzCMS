<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa_model extends CI_Model
{
	private $table = 'siswa';

	/**
	 * Ambil semua siswa, opsional difilter ke kelas tertentu saja.
	 * $kelas_ids kosong = semua kelas (dipakai untuk role admin).
	 *
	 * @param array<int> $kelas_ids
	 * @return array
	 */
	public function get_all(array $kelas_ids = array())
	{
		$this->db->select('siswa.*, kelas.nama_kelas')
			->from($this->table)
			->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
			->order_by('siswa.nama', 'ASC');

		if (! empty($kelas_ids)) {
			$this->db->where_in('siswa.kelas_id', $kelas_ids);
		}

		return $this->db->get()->result();
	}

	public function get_by_nisn($nisn)
	{
		return $this->db->select('siswa.*, kelas.nama_kelas')
			->from($this->table)
			->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
			->where('siswa.nisn', $nisn)
			->get()
			->row();
	}

	public function nisn_exists($nisn)
	{
		return $this->db->where('nisn', $nisn)->count_all_results($this->table) > 0;
	}

	/**
	 * Buat data siswa baru. NISN sebagai primary key mencegah duplikasi
	 * di level database — tidak perlu lagi fungsi "fixDuplicateSiswa"
	 * seperti di versi Apps Script lama.
	 *
	 * @param array $data  wajib berisi: nisn, nama, kelas_id, target_juz. opsional: user_id
	 * @return bool
	 */
	public function create(array $data)
	{
		$data['total_poin'] = 0;
		$data['badge'] = 'Pemula';

		return $this->db->insert($this->table, $data);
	}

	public function update($nisn, array $data)
	{
		// total_poin & badge sengaja tidak diubah lewat form ini —
		// keduanya hanya boleh berubah lewat proses setoran (Fase 3),
		// supaya tidak ada jalur lain yang bisa merusak konsistensi poin.
		unset($data['total_poin'], $data['badge'], $data['nisn']);

		return $this->db->update($this->table, $data, array('nisn' => $nisn));
	}

	public function delete($nisn)
	{
		return $this->db->delete($this->table, array('nisn' => $nisn));
	}

	/**
	 * Tautkan siswa ke akun login (users.id). Dipanggil setelah akun
	 * user dengan role=siswa dibuat oleh controller Users.
	 *
	 * @param string $nisn
	 * @param int $user_id
	 */
	public function set_user_id($nisn, $user_id)
	{
		return $this->db->update($this->table, array('user_id' => $user_id), array('nisn' => $nisn));
	}

	/**
	 * Ambil data siswa berdasarkan user_id (akun login).
	 *
	 * @param int $user_id
	 * @return object|null
	 */
	public function get_by_user_id($user_id)
	{
		return $this->db->select('siswa.*, kelas.nama_kelas')
			->from($this->table)
			->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
			->where('siswa.user_id', $user_id)
			->get()
			->row();
	}

	/**
	 * Hitung total siswa (opsional difilter per kelas_ids).
	 *
	 * @param array<int> $kelas_ids
	 * @return int
	 */
	public function count_siswa(array $kelas_ids = array())
	{
		if (! empty($kelas_ids)) {
			$this->db->where_in('kelas_id', $kelas_ids);
		}
		return $this->db->count_all_results($this->table);
	}

	/**
	 * Ambil data peringkat / leaderboard santri (global atau per kelas)
	 *
	 * @param int|null $kelas_id
	 * @param int|null $limit
	 * @param array<int> $kelas_ids
	 * @return array
	 */
	public function get_leaderboard($kelas_id = null, $limit = null, array $kelas_ids = array())
	{
		$this->db->select('siswa.*, kelas.nama_kelas')
			->from($this->table)
			->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
			->order_by('siswa.total_poin', 'DESC')
			->order_by('siswa.nama', 'ASC');

		if (! empty($kelas_id)) {
			$this->db->where('siswa.kelas_id', $kelas_id);
		} elseif (! empty($kelas_ids)) {
			$this->db->where_in('siswa.kelas_id', $kelas_ids);
		}

		if ($limit !== null) {
			$this->db->limit($limit);
		}

		return $this->db->get()->result();
	}

	/**
	 * Generate (atau regenerate) token akses rapor publik untuk siswa
	 * tertentu. Dipanggil oleh admin/guru lewat tombol "Bagikan ke
	 * Orangtua" / "Regenerasi Link" di halaman kelola siswa.
	 *
	 * Token 32 karakter hex (128-bit acak via random_bytes) — jauh lebih
	 * aman ditebak dibanding NISN yang polanya sering berurutan.
	 *
	 * @param string $nisn
	 * @return string Token baru yang tersimpan
	 */
	public function generate_access_token($nisn)
	{
		$token = bin2hex(random_bytes(16));
		$this->db->update($this->table, array('access_token' => $token), array('nisn' => $nisn));
		return $token;
	}

	/**
	 * Ambil data siswa dari token akses rapor publik. Dipakai oleh
	 * controller publik Rapor (tanpa login).
	 *
	 * @param string $token
	 * @return object|null
	 */
	public function get_by_token($token)
	{
		return $this->db->select('siswa.*, kelas.nama_kelas')
			->from($this->table)
			->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
			->where('siswa.access_token', $token)
			->get()
			->row();
	}
}

