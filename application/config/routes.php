<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| ROUTES - TahfidzCMS
| -------------------------------------------------------------------
| Web routes: render view (session-based auth)
| API routes: JSON response (token-based auth), semua diawali /api/
*/

$route['default_controller'] = 'landing';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ---------------------------------------------------------------
// WEB ROUTES
// ---------------------------------------------------------------
$route['landing']          = 'landing/index';

// Rapor publik (orangtua, via token) & Statistik publik (jamaah, agregat)
$route['rapor/(:any)']     = 'rapor/index/$1';
$route['statistik']        = 'statistik/index';
$route['installer']        = 'installer/index';
$route['installer/(:any)'] = 'installer/$1';

$route['login']            = 'auth/login';
$route['logout']           = 'auth/logout';
$route['dashboard']        = 'dashboard/index';

$route['settings']         = 'settings/index';
$route['settings/update']  = 'settings/update';

$route['setoran']              = 'setoran/index';
$route['setoran/tambah']       = 'setoran/tambah';
$route['setoran/simpan']       = 'setoran/simpan';

$route['penilaian']            = 'penilaian/index';
$route['penilaian/simpan/(:num)'] = 'penilaian/simpan/$1';

$route['riwayat']           = 'riwayat/index';
$route['progress']          = 'progress/index';
$route['leaderboard']       = 'leaderboard/index';

$route['laporan']           = 'laporan/index';
$route['laporan/export']    = 'laporan/export';

$route['users']             = 'users/index';
$route['users/form']        = 'users/form';
$route['users/form/(:num)'] = 'users/form/$1';
$route['users/simpan']      = 'users/simpan';
$route['users/hapus/(:num)'] = 'users/hapus/$1';
$route['users/reset-password/(:num)'] = 'users/reset_password/$1';
$route['users/generate-rapor-token/(:num)'] = 'users/generate_rapor_token/$1';

$route['kelas']             = 'kelas/index';
$route['kelas/simpan']      = 'kelas/simpan';
$route['kelas/hapus/(:num)'] = 'kelas/hapus/$1';

$route['profile']           = 'profile/index';
$route['profile/update']    = 'profile/update';

// ---------------------------------------------------------------
// API ROUTES (JSON, prefix /api)
// Semua diarahkan ke controllers/api/*
// ---------------------------------------------------------------
$route['api/auth/login']      = 'api/auth/login';
$route['api/auth/logout']     = 'api/auth/logout';

$route['api/dashboard']       = 'api/dashboard/index';

$route['api/setoran']         = 'api/setoran/index';       // GET list
$route['api/setoran/simpan']  = 'api/setoran/simpan';      // POST (multipart, dukung audio)

$route['api/riwayat']         = 'api/riwayat/index';
$route['api/progress']        = 'api/progress/index';
$route['api/leaderboard']     = 'api/leaderboard/index';
