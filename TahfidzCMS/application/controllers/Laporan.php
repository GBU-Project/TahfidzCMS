<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Laporan — Controller Web untuk rekapitulasi setoran dan export ke CSV/Excel.
 * Akses dibatasi untuk role admin dan guru.
 */
class Laporan extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->require_role(array('admin', 'guru'));

		$this->load->model('Setoran_model');
		$this->load->model('Kelas_model');
	}

	public function index()
	{
		$kelas_id      = $this->input->get('kelas_id');
		$tanggal_awal  = $this->input->get('tanggal_awal') ?: date('Y-m-01');
		$tanggal_akhir = $this->input->get('tanggal_akhir') ?: date('Y-m-t');

		$kelas_ids_filter = array();
		if ($this->is_guru()) {
			$kelas_ids_filter = $this->kelas_diizinkan;
			if ($kelas_id && ! in_array((int)$kelas_id, $this->kelas_diizinkan, TRUE)) {
				show_error('Anda tidak memiliki akses ke kelas ini.', 403, 'Akses Ditolak');
				return;
			}
			$kelas_list = $this->Kelas_model->get_by_ids($this->kelas_diizinkan);
		} else {
			$kelas_list = $this->Kelas_model->get_all();
		}

		$filter = array(
			'kelas_id'      => $kelas_id,
			'kelas_ids'     => $kelas_ids_filter,
			'tanggal_awal'  => $tanggal_awal,
			'tanggal_akhir' => $tanggal_akhir,
		);

		$rekap_data = $this->Setoran_model->get_laporan_rekap($filter);

		$data = array(
			'title'          => 'Laporan & Rekapitulasi Setoran',
			'kelas_list'     => $kelas_list,
			'selected_kelas' => $kelas_id,
			'tanggal_awal'   => $tanggal_awal,
			'tanggal_akhir'  => $tanggal_akhir,
			'rekap_data'     => $rekap_data,
		);

		$this->render('laporan/index', $data);
	}

	/**
	 * Export data setoran ke format Excel (CSV kompatibel UTF-8)
	 */
	public function export()
	{
		$kelas_id      = $this->input->get('kelas_id');
		$tanggal_awal  = $this->input->get('tanggal_awal');
		$tanggal_akhir = $this->input->get('tanggal_akhir');

		$kelas_ids_filter = array();
		if ($this->is_guru()) {
			$kelas_ids_filter = $this->kelas_diizinkan;
			if ($kelas_id && ! in_array((int)$kelas_id, $this->kelas_diizinkan, TRUE)) {
				show_error('Akses Ditolak', 403);
				return;
			}
		}

		$filter = array(
			'kelas_id'      => $kelas_id,
			'kelas_ids'     => $kelas_ids_filter,
			'tanggal_awal'  => $tanggal_awal,
			'tanggal_akhir' => $tanggal_akhir,
		);

		$setoran_list = $this->Setoran_model->get_all($filter);

		$filename = 'Laporan_Tahfidz_' . date('Ymd_His') . '.csv';

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		$output = fopen('php://output', 'w');
		// Add BOM for Excel UTF-8 recognition
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

		fputcsv($output, array(
			'No',
			'Kode Setoran',
			'Tanggal',
			'Waktu',
			'NISN',
			'Nama Santri',
			'Kelas',
			'Juz',
			'Surat',
			'Ayat Dari',
			'Ayat Sampai',
			'Nilai Tajwid',
			'Status Kelancaran',
			'Poin Diperoleh',
			'Guru Pengoreksi',
			'Catatan'
		));

		$no = 1;
		foreach ($setoran_list as $row) {
			fputcsv($output, array(
				$no++,
				$row->kode_setoran,
				$row->tanggal,
				$row->waktu,
				$row->nisn,
				$row->nama_siswa,
				$row->nama_kelas,
				$row->juz,
				$row->surat,
				$row->ayat_dari,
				$row->ayat_sampai,
				$row->nilai,
				$row->status,
				$row->poin,
				$row->nama_guru ?: '-',
				$row->catatan ?: '-'
			));
		}

		fclose($output);
		exit();
	}
}
