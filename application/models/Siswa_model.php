<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa_model extends CI_Model
{
	private $table = 'siswa';

	/**
	 * Subquery: daftar nama kelas semua kelas yang diikuti siswa, dipisah koma.
	 * Dipakai sebagai kolom `nama_kelas` agar view lama tetap bekerja
	 * (satu santri bisa ikut beberapa kelas).
	 */
	private function sub_nama_kelas()
	{
		return "(SELECT GROUP_CONCAT(k.nama_kelas ORDER BY k.nama_kelas SEPARATOR ', ')
			FROM siswa_kelas sk JOIN kelas k ON k.id = sk.kelas_id
			WHERE sk.nisn = siswa.nisn) AS nama_kelas";
	}

	/**
	 * Ambil semua siswa (SATU baris per siswa), opsional difilter ke siswa
	 * yang mengikuti salah satu dari kelas tertentu.
	 * $kelas_ids kosong = semua kelas (dipakai untuk role admin).
	 *
	 * @param array<int> $kelas_ids
	 * @return array
	 */
	public function get_all(array $kelas_ids = array())
	{
		$this->db->select('siswa.*, ' . $this->sub_nama_kelas(), FALSE)
			->from($this->table)
			->order_by('siswa.nama', 'ASC');

		if (! empty($kelas_ids)) {
			$this->db->where('EXISTS (SELECT 1 FROM siswa_kelas skf WHERE skf.nisn = siswa.nisn AND skf.kelas_id IN ('
				. implode(',', array_map('intval', $kelas_ids)) . '))', NULL, FALSE);
		}

		return $this->db->get()->result();
	}

	/**
	 * Ambil keanggotaan (siswa x kelas) beserta poin & badge PER KELAS.
	 * Satu santri di 3 kelas = 3 baris. Dipakai form input setoran dan
	 * leaderboard.
	 *
	 * @param array<int> $kelas_ids kosong = semua kelas
	 * @return array
	 */
	public function get_enrollments(array $kelas_ids = array())
	{
		$this->db->select('siswa.nisn, siswa.nama, sk.kelas_id, kelas.nama_kelas, sk.total_poin, sk.badge')
			->from('siswa_kelas sk')
			->join('siswa', 'siswa.nisn = sk.nisn', 'inner')
			->join('kelas', 'kelas.id = sk.kelas_id', 'inner')
			->order_by('siswa.nama', 'ASC')
			->order_by('kelas.nama_kelas', 'ASC');

		if (! empty($kelas_ids)) {
			$this->db->where_in('sk.kelas_id', $kelas_ids);
		}

		return $this->db->get()->result();
	}

	/**
	 * ID semua kelas yang diikuti seorang siswa.
	 *
	 * @param string $nisn
	 * @return array<int>
	 */
	public function get_kelas_ids($nisn)
	{
		$rows = $this->db->select('kelas_id')->where('nisn', $nisn)->get('siswa_kelas')->result();
		return array_map(function ($r) { return (int) $r->kelas_id; }, $rows);
	}

	/**
	 * Rincian poin/badge per kelas untuk satu siswa.
	 *
	 * @param string $nisn
	 * @return array
	 */
	public function get_poin_per_kelas($nisn)
	{
		return $this->db->select('sk.kelas_id, kelas.nama_kelas, sk.total_poin, sk.badge')
			->from('siswa_kelas sk')
			->join('kelas', 'kelas.id = sk.kelas_id')
			->where('sk.nisn', $nisn)
			->order_by('kelas.nama_kelas', 'ASC')
			->get()->result();
	}

	/**
	 * Apakah siswa terdaftar di (salah satu dari) kelas-kelas ini?
	 * Dipakai cek otorisasi guru.
	 *
	 * @param string     $nisn
	 * @param array<int> $kelas_ids
	 * @return bool
	 */
	public function ikut_kelas($nisn, array $kelas_ids)
	{
		if (empty($kelas_ids)) {
			return FALSE;
		}
		return $this->db->where('nisn', $nisn)->where_in('kelas_id', $kelas_ids)
			->count_all_results('siswa_kelas') > 0;
	}

	/**
	 * Sinkronkan daftar kelas siswa. Kelas baru ditambah (poin 0); kelas yang
	 * dicabut dihapus, KECUALI sudah punya setoran (histori tidak boleh
	 * yatim) — dalam kasus itu mengembalikan pesan error.
	 *
	 * siswa.kelas_id (kelas utama) dijaga selalu salah satu kelas yang diikuti.
	 *
	 * @param string     $nisn
	 * @param array<int> $kelas_ids minimal 1
	 * @return TRUE|string TRUE jika berhasil, string pesan error jika gagal
	 */
	public function set_kelas($nisn, array $kelas_ids)
	{
		$kelas_ids = array_values(array_unique(array_filter(array_map('intval', $kelas_ids))));
		if (empty($kelas_ids)) {
			return 'Pilih minimal satu kelas.';
		}

		$ada = $this->get_kelas_ids($nisn);

		$dicabut = array_diff($ada, $kelas_ids);
		foreach ($dicabut as $kid) {
			$n = $this->db->where('nisn', $nisn)->where('kelas_id', $kid)->count_all_results('setoran');
			if ($n > 0) {
				$k = $this->db->get_where('kelas', array('id' => $kid))->row();
				$nama = $k ? $k->nama_kelas : $kid;
				return "Kelas {$nama} tidak bisa dicabut: sudah ada {$n} setoran di kelas itu.";
			}
		}

		foreach (array_diff($kelas_ids, $ada) as $kid) {
			$this->db->insert('siswa_kelas', array(
				'nisn' => $nisn, 'kelas_id' => $kid, 'total_poin' => 0, 'badge' => 'Pemula',
			));
		}
		foreach ($dicabut as $kid) {
			$this->db->delete('siswa_kelas', array('nisn' => $nisn, 'kelas_id' => $kid));
		}

		// Kelas utama: pertahankan jika masih diikuti, kalau tidak ambil yang pertama.
		$siswa = $this->db->select('kelas_id')->get_where($this->table, array('nisn' => $nisn))->row();
		if ($siswa && ! in_array((int) $siswa->kelas_id, $kelas_ids, TRUE)) {
			$this->db->update($this->table, array('kelas_id' => $kelas_ids[0]), array('nisn' => $nisn));
		}

		return TRUE;
	}

	public function get_by_nisn($nisn)
	{
		return $this->db->select('siswa.*, ' . $this->sub_nama_kelas(), FALSE)
			->from($this->table)
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
		return $this->db->select('siswa.*, ' . $this->sub_nama_kelas(), FALSE)
			->from($this->table)
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
		$this->db->select('COUNT(DISTINCT siswa.nisn) AS n', FALSE)->from($this->table);
		if (! empty($kelas_ids)) {
			$this->db->join('siswa_kelas sk', 'sk.nisn = siswa.nisn', 'inner')
				->where_in('sk.kelas_id', $kelas_ids);
		}
		return (int) $this->db->get()->row()->n;
	}

	/**
	 * Hitung santri AKTIF: baris siswa yang punya akun login (users.role='siswa')
	 * dengan users.is_active = 1. Baris siswa tanpa akun (user_id NULL / akun
	 * sudah dihapus) atau dengan akun nonaktif TIDAK dihitung.
	 *
	 * Dipakai halaman statistik publik. count_siswa() tetap menghitung semua
	 * baris tabel siswa.
	 *
	 * @param array<int> $kelas_ids
	 * @return int
	 */
	public function count_siswa_aktif(array $kelas_ids = array())
	{
		$this->db->select('COUNT(DISTINCT siswa.nisn) AS n', FALSE)
			->from($this->table)
			->join('users', 'users.id = siswa.user_id', 'inner')
			->where('users.role', 'siswa')
			->where('users.is_active', 1);

		if (! empty($kelas_ids)) {
			$this->db->join('siswa_kelas sk', 'sk.nisn = siswa.nisn', 'inner')
				->where_in('sk.kelas_id', $kelas_ids);
		}

		return (int) $this->db->get()->row()->n;
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
		// Poin & badge DIPISAH per kelas: satu baris = satu santri di satu kelas.
		$this->db->select('siswa.nisn, siswa.nama, siswa.user_id, sk.kelas_id, kelas.nama_kelas, sk.total_poin, sk.badge')
			->from('siswa_kelas sk')
			->join('siswa', 'siswa.nisn = sk.nisn', 'inner')
			->join('kelas', 'kelas.id = sk.kelas_id', 'inner')
			->order_by('sk.total_poin', 'DESC')
			->order_by('siswa.nama', 'ASC');

		if (! empty($kelas_id)) {
			$this->db->where('sk.kelas_id', $kelas_id);
		} elseif (! empty($kelas_ids)) {
			$this->db->where_in('sk.kelas_id', $kelas_ids);
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
		return $this->db->select('siswa.*, ' . $this->sub_nama_kelas(), FALSE)
			->from($this->table)
			->where('siswa.access_token', $token)
			->get()
			->row();
	}
}
