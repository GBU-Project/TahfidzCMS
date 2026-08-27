<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Upload_handler — pembungkus tipis di atas library Upload bawaan CI3,
 * khusus untuk foto profil. Dipakai oleh controller Users & Profile
 * supaya aturan validasi (tipe file, ukuran maks) hanya ditulis SEKALI.
 */
class Upload_handler
{
	private $ci;

	const FOTO_MAX_SIZE_KB = 2048; // 2MB
	const FOTO_ALLOWED_TYPES = 'jpg|jpeg|png';

	const LOGO_MAX_SIZE_KB = 2048; // 2MB
	const LOGO_ALLOWED_TYPES = 'jpg|jpeg|png|webp';

	const AUDIO_MAX_SIZE_KB = 10240; // 10MB
	// 'webm' WAJIB ada di sini: browser (Chrome/Firefox) merekam via
	// MediaRecorder API secara default menghasilkan audio/webm (codec Opus),
	// bukan mp3 asli — lihat catatan di views/setoran/form.php.
	const AUDIO_ALLOWED_TYPES = 'mp3|wav|m4a|ogg|webm';

	public function __construct()
	{
		$this->ci = &get_instance();
		$this->ci->load->library('upload');
	}

	/**
	 * Upload foto profil dari field form bernama $field_name.
	 *
	 * @param string $field_name
	 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
	 */
	public function upload_foto_profil($field_name = 'foto')
	{
		return $this->_do_upload($field_name, array(
			'upload_path'   => './uploads/profile/',
			'allowed_types' => self::FOTO_ALLOWED_TYPES,
			'max_size'      => self::FOTO_MAX_SIZE_KB,
			'encrypt_name'  => TRUE,
		));
	}

	/**
	 * Upload logo institusi/lembaga dari field form bernama $field_name.
	 *
	 * @param string $field_name
	 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
	 */
	public function upload_logo_lembaga($field_name = 'institution_logo')
	{
		// Field kosong = tidak ada file dikirim -> skip
		if (empty($_FILES[$field_name]['name'])) {
			return array('success' => TRUE, 'path' => null, 'error' => null);
		}

		$file = $_FILES[$field_name];

		if ($file['error'] !== UPLOAD_ERR_OK) {
			return array('success' => FALSE, 'path' => null, 'error' => 'Gagal mengunggah berkas (Kode error: ' . $file['error'] . ')');
		}

		// Validasi ukuran (Maks 2MB)
		if ($file['size'] > (self::LOGO_MAX_SIZE_KB * 1024)) {
			return array('success' => FALSE, 'path' => null, 'error' => 'Ukuran berkas melebihi batas 2MB.');
		}

		// Validasi Ekstensi
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$allowed_exts = array('png', 'jpg', 'jpeg', 'webp');
		if (! in_array($ext, $allowed_exts, TRUE)) {
			return array('success' => FALSE, 'path' => null, 'error' => 'Tipe berkas tidak diizinkan. Hanya format PNG, JPG, JPEG, dan WEBP yang didukung.');
		}

		// Validasi MIME content via finfo (jika tersedia)
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime  = finfo_file($finfo, $file['tmp_name']);
			finfo_close($finfo);

			$allowed_mimes = array(
				'image/png',
				'image/x-png',
				'image/jpeg',
				'image/pjpeg',
				'image/webp',
				'image/x-webp',
				'application/octet-stream'
			);

			if (! in_array($mime, $allowed_mimes, TRUE)) {
				return array('success' => FALSE, 'path' => null, 'error' => 'Tipe konten berkas tidak valid (' . htmlspecialchars($mime) . ').');
			}
		}

		// Generate nama file aman terenkripsi
		$upload_dir = FCPATH . 'uploads/branding/';
		if (! is_dir($upload_dir)) {
			@mkdir($upload_dir, 0755, TRUE);
		}

		$new_filename = bin2hex(random_bytes(16)) . '.' . $ext;
		$target_path  = $upload_dir . $new_filename;

		if (! move_uploaded_file($file['tmp_name'], $target_path)) {
			return array('success' => FALSE, 'path' => null, 'error' => 'Gagal memindahkan berkas ke folder uploads/branding/.');
		}

		return array('success' => TRUE, 'path' => 'uploads/branding/' . $new_filename, 'error' => null);
	}

	/**
	 * Upload rekaman audio bukti setoran dari field form bernama $field_name.
	 * Dipakai di Fase 3 (modul Setoran).
	 *
	 * @param string $field_name
	 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
	 */
	public function upload_audio_setoran($field_name = 'audio_bukti')
	{
		return $this->_do_upload($field_name, array(
			'upload_path'   => './uploads/setoran_audio/',
			'allowed_types' => self::AUDIO_ALLOWED_TYPES,
			'max_size'      => self::AUDIO_MAX_SIZE_KB,
			'encrypt_name'  => TRUE,
		));
	}

	private function _do_upload($field_name, array $config)
	{
		// Field kosong = tidak ada file dikirim -> bukan error, cukup skip.
		if (empty($_FILES[$field_name]['name'])) {
			return array('success' => TRUE, 'path' => null, 'error' => null);
		}

		$this->ci->load->library('upload');
		$this->ci->upload->initialize($config, TRUE);

		if (! $this->ci->upload->do_upload($field_name)) {
			return array('success' => FALSE, 'path' => null, 'error' => $this->ci->upload->display_errors('', ''));
		}

		$upload_data = $this->ci->upload->data();

		// Simpan path RELATIF (tanpa './') supaya konsisten dipakai di <img src>/<audio src>
		$relative_path = ltrim(str_replace('./', '', $config['upload_path']), '/') . $upload_data['file_name'];

		return array('success' => TRUE, 'path' => $relative_path, 'error' => null);
	}
}
