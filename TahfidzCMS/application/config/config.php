<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| CSRF & SECURITY CONFIG - TahfidzCMS
| -------------------------------------------------------------------
| Pindahkan/gabungkan konfigurasi di bawah ini ke config.php bawaan CI3.
|
| PENTING: Jalur API (/api/*) dikecualikan secara eksplisit dari CSRF
| karena API menggunakan Bearer Token pada header HTTP, bukan cookie/session.
*/

$config['csrf_protection']   = TRUE;
$config['csrf_token_name']   = 'csrf_tahfidz_token';
$config['csrf_cookie_name']  = 'csrf_tahfidz_cookie';
$config['csrf_expire']       = 7200;
$config['csrf_regenerate']   = TRUE;
$config['csrf_exclude_uris'] = array(
	'api/.*',
	'api/auth/login',
	'api/auth/logout',
	'api/setoran',
	'api/setoran/simpan',
	'api/riwayat',
	'api/progress',
	'api/leaderboard',
	'api/dashboard',
);
