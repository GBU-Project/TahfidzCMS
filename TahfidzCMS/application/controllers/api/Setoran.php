<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * api/Setoran — Controller API JSON untuk modul Setoran & Penilaian.
 *
 * Mendukung autentikasi via Bearer Token (MY_API_Controller).
 * Mendukung input JSON maupun multipart/form-data untuk upload rekaman audio.
 */
class Setoran extends MY_API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Setoran_model');
		$this->load->model('Siswa_model');
		$this->load->library('Poin_calculator');
		$this->load->library('Upload_handler');
	}

	/**
	 * GET /api/setoran
	 * Query parameter: kelas_id, nisn, status, limit, offset
	 */
	public function index()
	{
		$kelas_id = $this->input->get('kelas_id');
		$nisn     = $this->input->get('nisn');
		$status   = $this->input->get('status');
		$limit    = $this->input->get('limit') ? (int) $this->input->get('limit') : 50;
		$offset   = $this->input->get('offset') ? (int) $this->input->get('offset') : 0;

		// Jika role siswa, batasi HANYA ke data miliknya sendiri
		if ($this->is_siswa()) {
			$nisn = $this->user->username;
		}

		// Jika role guru, pastikan tidak mengambil kelas di luar otorisasi
		if ($this->is_guru() && $kelas_id && ! in_array((int) $kelas_id, $this->kelas_diizinkan, TRUE)) {
			$this->json_error('Akses kelas tidak diizinkan.', 403);
			return;
		}

		$filter = array(
			'kelas_id'  => $kelas_id,
			'kelas_ids' => $this->is_guru() ? $this->kelas_diizinkan : array(),
			'nisn'      => $nisn,
			'status'    => $status,
		);

		$data = $this->Setoran_model->get_all($filter, $limit, $offset);

		// Format output path audio ke URL absolut untuk kenyamanan client API
		foreach ($data as &$row) {
			if (! empty($row->audio_bukti)) {
				$row->audio_url = base_url($row->audio_bukti);
			} else {
				$row->audio_url = null;
			}
		}

		$this->json_success($data, 'Data setoran berhasil diambil.');
	}

	/**
	 * POST /api/setoran/simpan
	 * Role yang diperbolehkan: admin, guru
	 * Content-Type: multipart/form-data atau application/x-www-form-urlencoded
	 */
	public function simpan()
	{
		$this->require_role(array('admin', 'guru'));

		$nisn        = $this->input->post('nisn', TRUE);
		$tanggal     = $this->input->post('tanggal', TRUE) ?: date('Y-m-d');
		$waktu       = $this->input->post('waktu', TRUE) ?: date('H:i:s');
		$juz         = (int) $this->input->post('juz');
		$surat       = $this->input->post('surat', TRUE);
		$ayat_dari   = (int) $this->input->post('ayat_dari');
		$ayat_sampai = (int) $this->input->post('ayat_sampai');
		$nilai       = strtoupper(trim($this->input->post('nilai', TRUE)));
		$status      = trim($this->input->post('status', TRUE));
		$catatan     = $this->input->post('catatan', TRUE) ?: null;
		$durasi_audio= $this->input->post('durasi_audio');

		if (! $nisn || ! $juz || ! $surat || ! $ayat_dari || ! $ayat_sampai || ! $nilai || ! $status) {
			$this->json_error('Semua field wajib diisi: nisn, juz, surat, ayat_dari, ayat_sampai, nilai, status.', 422);
			return;
		}

		if (! in_array($nilai, array('A', 'B', 'C'), TRUE)) {
			$this->json_error('Nilai harus A, B, atau C.', 422);
			return;
		}

		if (! in_array($status, array('Lancar', 'Cukup', 'Perlu Perbaikan'), TRUE)) {
			$this->json_error('Status harus: Lancar, Cukup, atau Perlu Perbaikan.', 422);
			return;
		}

		$siswa = $this->Siswa_model->get_by_nisn($nisn);
		if (! $siswa) {
			$this->json_error('Data siswa tidak ditemukan.', 404);
			return;
		}

		if ($this->is_guru() && ! in_array((int) $siswa->kelas_id, $this->kelas_diizinkan, TRUE)) {
			$this->json_error('Anda tidak memiliki hak input untuk siswa di kelas ini.', 403);
			return;
		}

		// Upload audio bukti jika dilampirkan
		$upload = $this->upload_handler->upload_audio_setoran('audio_bukti');
		if (! $upload['success']) {
			$this->json_error('Gagal upload audio: ' . $upload['error'], 400);
			return;
		}

		$data_setoran = array(
			// 'kode_setoran' sengaja TIDAK di-generate di sini — lihat
			// komentar di Setoran_model::create() soal race condition.
			'nisn'               => $nisn,
			'kelas_id'           => $siswa->kelas_id,
			'tanggal'            => $tanggal,
			'waktu'              => $waktu,
			'juz'                => $juz,
			'surat'              => $surat,
			'ayat_dari'          => $ayat_dari,
			'ayat_sampai'        => $ayat_sampai,
			'nilai'              => $nilai,
			'status'             => $status,
			'catatan'            => $catatan,
			'audio_bukti'        => $upload['path'],
			'durasi_audio'       => ! empty($durasi_audio) ? (int) $durasi_audio : null,
			'guru_pengoreksi_id' => $this->user->id,
		);

		try {
			$id = $this->Setoran_model->create($data_setoran);
			$created = $this->Setoran_model->get_by_id($id);
			if ($created && ! empty($created->audio_bukti)) {
				$created->audio_url = base_url($created->audio_bukti);
			}

			$this->json_success($created, 'Setoran berhasil disimpan.');
		} catch (Exception $e) {
			log_message('error', 'Gagal menyimpan setoran (api): ' . $e->getMessage());
			$this->json_error('Gagal menyimpan setoran. Silakan coba lagi atau hubungi administrator.', 500);
		}
	}
}
