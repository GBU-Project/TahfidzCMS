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
	 * Export data setoran ke format spreadsheet Excel (.xls)
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
		$this->load->library('Excel_exporter');
		$this->load->library('Poin_calculator');

		$headers = array(
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
			'Jenis Setoran',
			'Jumlah Kesalahan',
			'Kualitas Bacaan',
			'Keterangan',
			'Skor',
			'Hasil QC',
			'Poin Diperoleh',
			'Guru Pengoreksi',
			'Catatan'
		);

		$rows = array();
		$no = 1;
		foreach ($setoran_list as $row) {
			$rows[] = array(
				$no++,
				$row->kode_setoran,
				$row->tanggal,
				substr($row->waktu, 0, 5),
				$row->nisn,
				$row->nama_siswa,
				$row->nama_kelas,
				$row->juz,
				$row->surat,
				$row->ayat_dari,
				$row->ayat_sampai,
				isset(Poin_calculator::JENIS_SETORAN_LABEL[$row->jenis_setoran]) ? Poin_calculator::JENIS_SETORAN_LABEL[$row->jenis_setoran] : $row->jenis_setoran,
				$row->jumlah_kesalahan,
				$row->kualitas_bacaan === 'baik' ? 'Baik' : 'Kurang Baik',
				$row->keterangan,
				$row->skor,
				$row->hasil_qc ? (isset(Poin_calculator::HASIL_QC_LABEL[$row->hasil_qc]) ? Poin_calculator::HASIL_QC_LABEL[$row->hasil_qc] : $row->hasil_qc) : '-',
				$row->poin,
				$row->nama_guru ?: '-',
				$row->catatan ?: '-'
			);
		}

		$filename = 'Laporan_Tahfidz_' . date('Ymd_His') . '.xls';
		$this->excel_exporter->download_excel($filename, $headers, $rows, 'Rekap Setoran');
	}
}
