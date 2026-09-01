<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * api/Riwayat — Histori & filter pencarian setoran, mengikuti logika
 * yang sama dengan controller web Riwayat.php (dibatasi per-role).
 */
class Riwayat extends MY_API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Setoran_model');
		$this->load->model('Siswa_model');
	}

	/**
	 * GET /api/riwayat
	 * Query: kelas_id, nisn, keterangan, tanggal_awal, tanggal_akhir, q, limit, offset
	 * (kelas_id & nisn diabaikan untuk role siswa — otomatis dibatasi ke data sendiri)
	 */
	public function index()
	{
		$kelas_id      = $this->input->get('kelas_id');
		$nisn          = $this->input->get('nisn');
		$keterangan    = $this->input->get('keterangan');
		$tanggal_awal  = $this->input->get('tanggal_awal');
		$tanggal_akhir = $this->input->get('tanggal_akhir');
		$search        = $this->input->get('q');
		$limit         = $this->input->get('limit') ? (int) $this->input->get('limit') : 50;
		$limit         = max(1, min($limit, 200)); // fix keamanan: batas atas cegah resource exhaustion
		$offset        = $this->input->get('offset') ? max(0, (int) $this->input->get('offset')) : 0;

		$filter = array(
			'keterangan'    => $keterangan,
			'tanggal_awal'  => $tanggal_awal,
			'tanggal_akhir' => $tanggal_akhir,
			'search'        => $search,
		);

		if ($this->is_siswa()) {
			$siswa = $this->Siswa_model->get_by_user_id($this->user->id);
			if (! $siswa) {
				$siswa = $this->Siswa_model->get_by_nisn($this->user->username);
			}
			// NISN palsu yang tidak mungkin cocok, supaya query tetap aman
			// (bukan mengembalikan SEMUA data) kalau data siswa tidak ditemukan.
			$filter['nisn'] = $siswa ? $siswa->nisn : 'NON_EXISTENT';
		} elseif ($this->is_guru()) {
			if ($kelas_id && ! in_array((int) $kelas_id, $this->kelas_diizinkan, TRUE)) {
				$this->json_error('Anda tidak memiliki akses ke kelas ini.', 403);
				return;
			}
			$filter['kelas_ids'] = $this->kelas_diizinkan;
			if (! empty($kelas_id)) {
				$filter['kelas_id'] = $kelas_id;
			}
			if (! empty($nisn)) {
				$filter['nisn'] = $nisn;
			}
		} else {
			// admin: bebas filter kelas/nisn apa saja
			if (! empty($kelas_id)) {
				$filter['kelas_id'] = $kelas_id;
			}
			if (! empty($nisn)) {
				$filter['nisn'] = $nisn;
			}
		}

		$data = $this->Setoran_model->get_all($filter, $limit, $offset);

		foreach ($data as &$row) {
			$row->audio_url = ! empty($row->audio_bukti) ? base_url($row->audio_bukti) : null;
		}

		$this->json_success($data, 'Riwayat setoran berhasil diambil.');
	}
}
