<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard — contoh pemakaian MY_Controller.
 * Isi statistik lengkap (top siswa, chart, dsb) baru digarap di Fase 4;
 * untuk sekarang cukup membuktikan alur session guard + render bekerja,
 * dan tampilan berbeda per role sudah dapat dibedakan.
 */
class Dashboard extends MY_Controller
{
	public function index()
	{
		$data = array(
			'title' => 'Dashboard',
		);

		// Nanti di Fase 4: load Setoran_model/Siswa_model utk isi statistik asli,
		// difilter pakai $this->kelas_diizinkan kalau role guru.
		$this->render('dashboard/index', $data);
	}
}
