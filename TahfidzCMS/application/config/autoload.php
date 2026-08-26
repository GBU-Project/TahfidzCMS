<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTOLOAD - TahfidzCMS
| -------------------------------------------------------------------
| Ini bukan file autoload.php CI3 lengkap (banyak default value CI3
| dihilangkan agar ringkas) — pindahkan/gabungkan array di bawah ini
| ke autoload.php bawaan CodeIgniter 3 Anda.
*/

$autoload['packages'] = array();

// Library yang dipakai di hampir semua controller
$autoload['libraries'] = array('database', 'session', 'form_validation');

$autoload['drivers'] = array();

$autoload['helper'] = array('url', 'form', 'format'); // format = helpers/format_helper.php custom

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array(); // sengaja kosong: model di-load manual per controller yang butuh
