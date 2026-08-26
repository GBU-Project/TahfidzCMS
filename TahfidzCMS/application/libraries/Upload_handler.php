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

		$this->ci->upload->initialize($config);

		if (! $this->ci->upload->do_upload($field_name)) {
			return array('success' => FALSE, 'path' => null, 'error' => $this->ci->upload->display_errors('', ''));
		}

		$upload_data = $this->ci->upload->data();

		// Simpan path RELATIF (tanpa './') supaya konsisten dipakai di <img src>/<audio src>
		$relative_path = ltrim(str_replace('./', '', $config['upload_path']), '/') . $upload_data['file_name'];

		return array('success' => TRUE, 'path' => $relative_path, 'error' => null);
	}
}
