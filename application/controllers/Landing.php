<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Landing Controller — Halaman publik (Public Landing Page) TahfidzCMS.
 * Menampilkan profil dinamis institusi, fitur monitoring hafalan, alur, dan CTA.
 */
class Landing extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->model('Setting_model');
	}

	public function index()
	{
		$settings = $this->Setting_model->get_all();

		$data = array(
			'title'       => $settings['institution_name'] . ' - ' . $settings['institution_tagline'],
			'settings'    => $settings,
			'is_logged_in'=> (bool) $this->session->userdata('user_id'),
		);

		$this->load->view('landing/index', $data);
	}
}
