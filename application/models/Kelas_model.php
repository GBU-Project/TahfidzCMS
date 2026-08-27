<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas_model extends CI_Model
{
	private $table = 'kelas';

	public function get_all()
	{
		return $this->db->order_by('nama_kelas', 'ASC')->get($this->table)->result();
	}

	public function get_by_id($id)
	{
		return $this->db->get_where($this->table, array('id' => $id))->row();
	}

	/**
	 * Ambil beberapa kelas sekaligus berdasarkan daftar id.
	 * Dipakai untuk menampilkan "kelas yang diampu" seorang guru.
	 *
	 * @param array<int> $ids
	 * @return array
	 */
	public function get_by_ids(array $ids)
	{
		if (empty($ids)) {
			return array();
		}
		return $this->db->where_in('id', $ids)->order_by('nama_kelas', 'ASC')->get($this->table)->result();
	}

	public function create($nama_kelas)
	{
		$this->db->insert($this->table, array('nama_kelas' => $nama_kelas));
		return $this->db->insert_id();
	}

	public function update($id, $nama_kelas)
	{
		return $this->db->update($this->table, array('nama_kelas' => $nama_kelas), array('id' => $id));
	}

	/**
	 * Hapus kelas. Sengaja TIDAK diizinkan kalau masih ada siswa terdaftar
	 * di kelas tsb (FK constraint 'RESTRICT' di siswa.kelas_id dan
	 * setoran.kelas_id akan menolak juga di level DB, tapi kita cek dulu
	 * di sini supaya pesan errornya jelas untuk user, bukan error SQL mentah).
	 *
	 * @param int $id
	 * @return bool|string TRUE jika berhasil, string pesan error jika gagal
	 */
	public function delete($id)
	{
		$jumlah_siswa = $this->db->where('kelas_id', $id)->count_all_results('siswa');

		if ($jumlah_siswa > 0) {
			return "Tidak bisa dihapus: masih ada {$jumlah_siswa} siswa terdaftar di kelas ini.";
		}

		$this->db->delete('guru_kelas', array('kelas_id' => $id));
		$this->db->delete($this->table, array('id' => $id));

		return TRUE;
	}
}
