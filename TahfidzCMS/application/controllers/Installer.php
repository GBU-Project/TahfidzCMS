<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Installer Controller — Pemandu instalasi web interaktif untuk TahfidzCMS.
 *
 * Alur Kerja:
 * 1. Step 1: System Requirements & Folder Permissions Check
 * 2. Step 2: Database Configuration & Test Connection
 * 3. Step 3: Install Database Schema (Fresh / Skip if existing)
 * 4. Step 4: Application Configuration (Base URL & Secret Key) & Create Super Admin
 * 5. Step 5: Finalization & Create installed.lock File
 */
class Installer extends CI_Controller
{
	private $lock_file;

	public function __construct()
	{
		parent::__construct();
		$this->lock_file = FCPATH . 'installed.lock';
		$this->_check_if_installed();
	}

	/**
	 * Guard: Jika aplikasi sudah terinstall (installed.lock ada),
	 * semua aksi di controller ini akan ditolak secara permanen.
	 */
	private function _check_if_installed()
	{
		if (file_exists($this->lock_file)) {
			show_error('Aplikasi TahfidzCMS sudah terinstall. Untuk alasan keamanan, installer dinonaktifkan.', 403, 'Akses Ditolak');
			exit();
		}
	}

	/**
	 * GET /installer -> Step 1: Welcome & Requirements Check
	 */
	public function index()
	{
		$php_version = phpversion();
		$php_ok = version_compare($php_version, '7.4.0', '>=');

		$required_extensions = array(
			'mysqli'     => 'Koneksi database MySQL/MariaDB',
			'mbstring'   => 'Pemrosesan string multibyte',
			'json'       => 'Parsing JSON & token API',
			'session'    => 'Session autentikasi web',
			'openssl'    => 'Enkripsi & random token generator',
			'fileinfo'   => 'Validasi MIME type upload audio & foto',
		);

		$ext_checks = array();
		$all_ext_ok = true;
		foreach ($required_extensions as $ext => $desc) {
			$loaded = extension_loaded($ext);
			$ext_checks[] = array(
				'name'   => $ext,
				'desc'   => $desc,
				'status' => $loaded
			);
			if (! $loaded) {
				$all_ext_ok = false;
			}
		}

		$directories = array(
			'uploads/'                => FCPATH . 'uploads',
			'uploads/branding/'       => FCPATH . 'uploads/branding',
			'uploads/profile/'        => FCPATH . 'uploads/profile',
			'uploads/setoran_audio/'  => FCPATH . 'uploads/setoran_audio',
			'application/config/'     => APPPATH . 'config',
		);

		$dir_checks = array();
		$all_dir_ok = true;
		foreach ($directories as $label => $path) {
			if (! is_dir($path)) {
				@mkdir($path, 0755, true);
			}
			$writable = is_writable($path);
			$dir_checks[] = array(
				'path'     => $label,
				'writable' => $writable
			);
			if (! $writable) {
				$all_dir_ok = false;
			}
		}

		$can_proceed = ($php_ok && $all_ext_ok && $all_dir_ok);

		$data = array(
			'title'        => 'Web Installer - TahfidzCMS',
			'step'         => 1,
			'php_version'  => $php_version,
			'php_ok'       => $php_ok,
			'ext_checks'   => $ext_checks,
			'dir_checks'   => $dir_checks,
			'can_proceed'  => $can_proceed
		);

		$this->load->view('installer/step1_requirements', $data);
	}

	/**
	 * GET /installer/step2 -> Step 2: Database Configuration Form
	 */
	public function step2()
	{
		$data = array(
			'title'    => 'Konfigurasi Database - TahfidzCMS Installer',
			'step'     => 2,
			'error'    => $this->session->flashdata('error'),
			'db_host'  => $this->session->userdata('db_host') ?: 'localhost',
			'db_port'  => $this->session->userdata('db_port') ?: '3306',
			'db_user'  => $this->session->userdata('db_user') ?: 'root',
			'db_name'  => $this->session->userdata('db_name') ?: 'tahfidzcms',
		);

		$this->load->view('installer/step2_database', $data);
	}

	/**
	 * POST /installer/test_db -> Proses tes koneksi database
	 */
	public function test_db()
	{
		$this->form_validation->set_rules('db_host', 'Host Database', 'required|trim');
		$this->form_validation->set_rules('db_port', 'Port Database', 'required|numeric');
		$this->form_validation->set_rules('db_user', 'Username Database', 'required|trim');
		$this->form_validation->set_rules('db_name', 'Nama Database', 'required|trim');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('installer/step2');
			return;
		}

		$db_host = $this->input->post('db_host', TRUE);
		$db_port = (int) $this->input->post('db_port', TRUE);
		$db_user = $this->input->post('db_user', TRUE);
		$db_pass = $this->input->post('db_pass', FALSE); // Password raw
		$db_name = $this->input->post('db_name', TRUE);

		// Tes koneksi mysqli langsung
		mysqli_report(MYSQLI_REPORT_OFF);
		$link = @mysqli_init();
		if (! $link) {
			$this->session->set_flashdata('error', 'Inisialisasi MySQLi gagal di server ini.');
			redirect('installer/step2');
			return;
		}

		$link->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
		$connected = @$link->real_connect($db_host, $db_user, $db_pass, null, $db_port);

		if (! $connected) {
			$this->session->set_flashdata('error', 'Gagal terhubung ke MySQL Server: ' . $link->connect_error);
			redirect('installer/step2');
			return;
		}

		// Buat database jika belum ada
		$safe_dbname = str_replace('`', '``', $db_name);
		$created_db = @$link->query("CREATE DATABASE IF NOT EXISTS `{$safe_dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		if (! $created_db) {
			$this->session->set_flashdata('error', 'Gagal membuat/mengakses database: ' . $link->error);
			$link->close();
			redirect('installer/step2');
			return;
		}

		$link->select_db($db_name);

		// Cek apakah database sudah berisi tabel tahfidzcms
		$tables_res = $link->query("SHOW TABLES LIKE 'users'");
		$is_existing = ($tables_res && $tables_res->num_rows > 0);

		$link->close();

		// Simpan konfigurasi database ke session untuk step berikutnya
		$this->session->set_userdata(array(
			'db_host'     => $db_host,
			'db_port'     => $db_port,
			'db_user'     => $db_user,
			'db_pass'     => $db_pass,
			'db_name'     => $db_name,
			'db_existing' => $is_existing
		));

		redirect('installer/step3');
	}

	/**
	 * GET /installer/step3 -> Step 3: Konfirmasi Instalasi Skema Database
	 */
	public function step3()
	{
		$db_name = $this->session->userdata('db_name');
		if (! $db_name) {
			redirect('installer/step2');
			return;
		}

		$data = array(
			'title'       => 'Instalasi Skema Database - TahfidzCMS',
			'step'        => 3,
			'db_name'     => $db_name,
			'is_existing' => $this->session->userdata('db_existing'),
			'error'       => $this->session->flashdata('error')
		);

		$this->load->view('installer/step3_schema', $data);
	}

	/**
	 * POST /installer/process_schema -> Eksekusi DDL Schema dari project
	 */
	public function process_schema()
	{
		$install_type = $this->input->post('install_type'); // 'fresh' or 'keep'
		
		$db_host = $this->session->userdata('db_host');
		$db_port = $this->session->userdata('db_port');
		$db_user = $this->session->userdata('db_user');
		$db_pass = $this->session->userdata('db_pass');
		$db_name = $this->session->userdata('db_name');

		if (! $db_name) {
			redirect('installer/step2');
			return;
		}

		if ($install_type === 'fresh' || ! $this->session->userdata('db_existing')) {
			$sql_file = FCPATH . 'database/tahfidzcms.sql';
			if (! file_exists($sql_file)) {
				$this->session->set_flashdata('error', 'File skema database database/tahfidzcms.sql tidak ditemukan!');
				redirect('installer/step3');
				return;
			}

			$sql_content = file_get_contents($sql_file);

			// Hubungkan dan eksekusi multi_query
			$link = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, (int)$db_port);
			if (! $link) {
				$this->session->set_flashdata('error', 'Koneksi ke database terputus.');
				redirect('installer/step3');
				return;
			}

			$link->set_charset('utf8mb4');

			// Bersihkan statement CREATE DATABASE / USE agar kompatibel dengan db_name yang dipilih
			$cleaned_sql = preg_replace('/CREATE DATABASE[^\;]+\;/i', '', $sql_content);
			$cleaned_sql = preg_replace('/USE `[^\`]+`\;/i', '', $cleaned_sql);

			if ($link->multi_query($cleaned_sql)) {
				do {
					// flush multi_queries
					if ($result = $link->store_result()) {
						$result->free();
					}
				} while ($link->more_results() && $link->next_result());
			}

			// Masukkan kelas default awal jika belum ada
			$link->query("INSERT IGNORE INTO `kelas` (`nama_kelas`) VALUES ('7A'), ('7B'), ('8A'), ('8B')");
			$link->close();
		}

		redirect('installer/step4');
	}

	/**
	 * GET /installer/step4 -> Step 4: Konfigurasi App & Buat Super Admin
	 */
	public function step4()
	{
		$db_name = $this->session->userdata('db_name');
		if (! $db_name) {
			redirect('installer/step2');
			return;
		}

		$current_base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

		$data = array(
			'title'       => 'Akun Super Admin & Konfigurasi - TahfidzCMS',
			'step'        => 4,
			'base_url'    => $current_base_url,
			'error'       => $this->session->flashdata('error')
		);

		$this->load->view('installer/step4_admin', $data);
	}

	/**
	 * POST /installer/process_final -> Simpan Admin, Tulis Config, dan Buat installed.lock
	 */
	public function process_final()
	{
		$this->form_validation->set_rules('admin_nama', 'Nama Lengkap', 'required|trim|max_length[100]');
		$this->form_validation->set_rules('admin_username', 'NIP / Username Admin', 'required|trim|max_length[50]');
		$this->form_validation->set_rules('admin_password', 'Password Admin', 'required|min_length[6]');
		$this->form_validation->set_rules('admin_confirm', 'Konfirmasi Password', 'required|matches[admin_password]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('installer/step4');
			return;
		}

		$db_host = $this->session->userdata('db_host');
		$db_port = $this->session->userdata('db_port');
		$db_user = $this->session->userdata('db_user');
		$db_pass = $this->session->userdata('db_pass');
		$db_name = $this->session->userdata('db_name');

		$admin_nama     = $this->input->post('admin_nama', TRUE);
		$admin_username = $this->input->post('admin_username', TRUE);
		$admin_password = $this->input->post('admin_password');

		// Simpan akun Super Admin ke database
		$link = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, (int)$db_port);
		if (! $link) {
			$this->session->set_flashdata('error', 'Koneksi ke database gagal saat membuat admin.');
			redirect('installer/step4');
			return;
		}

		$link->set_charset('utf8mb4');

		// Cek apakah username admin sudah ada
		$stmt_check = $link->prepare("SELECT id FROM users WHERE username = ?");
		$stmt_check->bind_param("s", $admin_username);
		$stmt_check->execute();
		$stmt_check->store_result();

		$password_hash = password_hash($admin_password, PASSWORD_BCRYPT);
		$role_admin = 'admin';
		$is_active = 1;

		if ($stmt_check->num_rows > 0) {
			// Update akun admin yang ada
			$stmt_update = $link->prepare("UPDATE users SET nama = ?, password = ?, role = 'admin', is_active = 1 WHERE username = ?");
			$stmt_update->bind_param("sss", $admin_nama, $password_hash, $admin_username);
			$stmt_update->execute();
			$stmt_update->close();
		} else {
			// Buat akun baru
			$stmt_insert = $link->prepare("INSERT INTO users (nama, username, password, role, is_active) VALUES (?, ?, ?, ?, ?)");
			$stmt_insert->bind_param("ssssi", $admin_nama, $admin_username, $password_hash, $role_admin, $is_active);
			$stmt_insert->execute();
			$stmt_insert->close();
		}
		$stmt_check->close();
		$link->close();

		// Update file application/config/database.php
		$db_config_file = APPPATH . 'config/database.php';
		$db_content = "<?php\n"
			. "defined('BASEPATH') OR exit('No direct script access allowed');\n\n"
			. "/*\n| -------------------------------------------------------------------\n"
			. "| DATABASE CONNECTIVITY SETTINGS - TahfidzCMS\n"
			. "| Terkonfigurasi otomatis oleh Web Installer pada " . date('Y-m-d H:i:s') . "\n"
			. "| -------------------------------------------------------------------\n*/\n\n"
			. "\$active_group = 'default';\n"
			. "\$query_builder = TRUE;\n\n"
			. "\$db['default'] = array(\n"
			. "\t'dsn'\t=> '',\n"
			. "\t'hostname' => " . var_export($db_host, true) . ",\n"
			. "\t'username' => " . var_export($db_user, true) . ",\n"
			. "\t'password' => " . var_export($db_pass, true) . ",\n"
			. "\t'database' => " . var_export($db_name, true) . ",\n"
			. "\t'dbdriver' => 'mysqli',\n"
			. "\t'dbprefix' => '',\n"
			. "\t'pconnect' => FALSE,\n"
			. "\t'db_debug' => (ENVIRONMENT !== 'production'),\n"
			. "\t'cache_on' => FALSE,\n"
			. "\t'cachedir' => '',\n"
			. "\t'char_set' => 'utf8mb4',\n"
			. "\t'dbcollat' => 'utf8mb4_unicode_ci',\n"
			. "\t'swap_pre' => '',\n"
			. "\t'encrypt' => FALSE,\n"
			. "\t'compress' => FALSE,\n"
			. "\t'stricton' => FALSE,\n"
			. "\t'failover' => array(),\n"
			. "\t'save_queries' => TRUE\n"
			. ");\n";

		@file_put_contents($db_config_file, $db_content);

		// Buat file installed.lock
		$lock_content = "Installed on: " . date('Y-m-d H:i:s') . "\nDatabase: " . $db_name . "\nAdmin: " . $admin_username . "\n";
		@file_put_contents($this->lock_file, $lock_content);

		// Hapus session installer
		$this->session->sess_destroy();

		redirect('installer/finish');
	}

	/**
	 * GET /installer/finish -> Halaman Sukses
	 */
	public function finish()
	{
		$data = array(
			'title' => 'Instalasi Berhasil - TahfidzCMS',
			'step'  => 5
		);

		$this->load->view('installer/finish', $data);
	}
}
