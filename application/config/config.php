<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Deteksi apakah koneksi ASLI dari browser ke aplikasi ini adalah HTTPS.
 *
 * $_SERVER['HTTPS'] SAJA tidak cukup di belakang reverse proxy/tunnel
 * (ngrok, nginx, load balancer, dst.) — proxy tsb menerima HTTPS dari
 * browser tapi meneruskan ke server aplikasi sebagai HTTP polos, sehingga
 * $_SERVER['HTTPS'] tidak pernah ter-set walau browser sungguhan HTTPS.
 * Ini menyebabkan base_url()/site_url() salah generate 'http://' padahal
 * halaman diakses via 'https://', memicu peringatan browser "This form is
 * not secure" saat submit form (mixed content).
 *
 * Proxy yang benar akan mengirim header X-Forwarded-Proto: https untuk
 * memberi tahu protokol ASLI dari sisi browser — kita cek header ini juga.
 *
 * CATATAN KEAMANAN: header X-Forwarded-* bisa dipalsukan klien jika
 * aplikasi diekspos LANGSUNG ke internet tanpa reverse proxy tepercaya di
 * depannya (tanpa proxy, siapapun bisa kirim header ini sendiri). Untuk
 * deployment production dengan reverse proxy sungguhan (nginx/ngrok/dst),
 * ini aman & perlu, karena proxy tsb yang menetapkan header ini, bukan
 * klien. Kalau app pernah dipasang tanpa reverse proxy sama sekali (akses
 * langsung ke port PHP), pertimbangkan hapus pengecekan X-Forwarded-Proto.
 */
if (! function_exists('tahfidzcms_is_https')) {
	function tahfidzcms_is_https()
	{
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && strtolower($_SERVER['HTTPS']) !== 'off') {
			return TRUE;
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
			return TRUE;
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
			return TRUE;
		}
		if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
			return TRUE;
		}
		return FALSE;
	}
}

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will try guess the protocol, root
| path and default port of your installation.
|
*/
$config['base_url'] = (tahfidzcms_is_https() ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . rtrim(dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : ''), '/\\') . '/';

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = '';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
*/
$config['uri_protocol']	= 'REQUEST_URI';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
*/
$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
*/
$config['language']	= 'english';

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
*/
$config['enable_hooks'] = FALSE;

/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
*/
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
*/
$config['composer_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

/*
|--------------------------------------------------------------------------
| Allow $_GET array
|--------------------------------------------------------------------------
*/
$config['allow_get_array'] = TRUE;

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
| 0 = Disables logging, Error logging TURNED OFF
| 1 = Error Messages (including PHP errors)
| 2 = Debug Messages
| 3 = Informational Messages
| 4 = All Messages
*/
$config['log_threshold'] = 1; // 1 = Error Messages saja — cukup untuk audit trail tanpa membanjiri log

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Log File Extension
|--------------------------------------------------------------------------
*/
$config['log_file_extension'] = '';

/*
|--------------------------------------------------------------------------
| Log File Permissions
|--------------------------------------------------------------------------
*/
$config['log_file_permissions'] = 0644;

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Error Views Directory Path
|--------------------------------------------------------------------------
*/
$config['error_views_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Webpage Query String
|--------------------------------------------------------------------------
*/
$config['cache_query_string'] = FALSE;

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
*/
$config['encryption_key'] = 'TahfidzCMS_Secret_Key_2026_Secure_Key';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
*/
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'tahfidz_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = TRUE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
*/
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
// Fix keamanan: cookie_secure otomatis TRUE saat diakses via HTTPS (aman utk
// dev lokal tanpa HTTPS, tapi wajib aktif di production yang selalu HTTPS).
// Pakai tahfidzcms_is_https() (bukan cek $_SERVER['HTTPS'] langsung) supaya
// tetap terdeteksi benar di belakang reverse proxy/tunnel (ngrok, nginx,
// dst.) yang meneruskan request sebagai HTTP polos ke server aplikasi
// walau browser sungguhan mengakses via HTTPS — lihat komentar lengkap di
// fungsi tahfidzcms_is_https() di awal file ini.
// cookie_httponly TRUE mencegah cookie dibaca lewat JavaScript (mitigasi XSS
// terhadap pencurian session).
$config['cookie_secure']	= tahfidzcms_is_https();
$config['cookie_httponly'] 	= TRUE;

/*
|--------------------------------------------------------------------------
| Standardize newlines
|--------------------------------------------------------------------------
*/
$config['standardize_newlines'] = FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery (CSRF) Protection
|--------------------------------------------------------------------------
*/
$config['csrf_protection']   = TRUE;
$config['csrf_token_name']   = 'csrf_tahfidz_token';
$config['csrf_cookie_name']  = 'csrf_tahfidz_cookie';
$config['csrf_expire']       = 7200;
$config['csrf_regenerate']   = TRUE;
// Semua endpoint di bawah application/controllers/api/ dikecualikan dari
// proteksi CSRF karena memakai Bearer Token (stateless), bukan session cookie.
// 'api/.*' saja sudah mencakup semua sub-path api/*, entri per-endpoint yang
// sebelumnya ada di sini redundan dan dihapus.
$config['csrf_exclude_uris'] = array(
	'api/.*',
);

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
*/
$config['time_reference'] = 'local';

/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
*/
$config['rewrite_short_tags'] = FALSE;

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
*/
$config['proxy_ips'] = '';
