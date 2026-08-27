<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Setting_model — Model konfigurasi key-value aplikasi (app_settings).
 * Menyimpan data identitas lembaga (nama lembaga, brand/nama singkat, tagline, logo).
 */
class Setting_model extends CI_Model
{
	private $table = 'app_settings';

	/** @var array Default fallback settings jika database belum memiliki data */
	private $defaults = array(
		'institution_name'       => 'TahfidzCMS',
		'institution_short_name' => 'TahfidzCMS',
		'institution_tagline'    => "Sistem Monitoring Hafalan Al-Qur'an",
		'institution_logo'       => '',
	);

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Ambil semua pengaturan sebagai associative array [key => value].
	 *
	 * @return array
	 */
	public function get_all()
	{
		$settings = $this->defaults;

		// Cek apakah tabel app_settings sudah ada
		if (! $this->db->table_exists($this->table)) {
			return $settings;
		}

		$query = $this->db->get($this->table);
		if ($query && $query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$settings[$row->setting_key] = $row->setting_value;
			}
		}

		return $settings;
	}

	/**
	 * Ambil nilai satu pengaturan berdasarkan key.
	 *
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if (! $this->db->table_exists($this->table)) {
			return isset($this->defaults[$key]) ? $this->defaults[$key] : $default;
		}

		$row = $this->db->get_where($this->table, array('setting_key' => $key))->row();
		if ($row && $row->setting_value !== null && $row->setting_value !== '') {
			return $row->setting_value;
		}

		if (isset($this->defaults[$key])) {
			return $this->defaults[$key];
		}

		return $default;
	}

	/**
	 * Simpan atau perbarui nilai satu pengaturan (upsert).
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $type
	 * @return bool
	 */
	public function set($key, $value, $type = 'text')
	{
		$this->_ensure_table_exists();

		$exists = $this->db->get_where($this->table, array('setting_key' => $key))->row();
		if ($exists) {
			$this->db->where('setting_key', $key);
			return $this->db->update($this->table, array(
				'setting_value' => $value,
				'setting_type'  => $type,
			));
		}

		return $this->db->insert($this->table, array(
			'setting_key'   => $key,
			'setting_value' => $value,
			'setting_type'  => $type,
		));
	}

	/**
	 * Simpan banyak pengaturan sekaligus dari array [key => value].
	 *
	 * @param array $data
	 * @return bool
	 */
	public function set_many(array $data)
	{
		$this->_ensure_table_exists();
		$this->db->trans_start();

		foreach ($data as $key => $value) {
			$type = ($key === 'institution_logo') ? 'image' : 'text';
			$this->set($key, $value, $type);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * Pastikan tabel app_settings tersedia (backward compatibility).
	 */
	private function _ensure_table_exists()
	{
		if (! $this->db->table_exists($this->table)) {
			$sql = "CREATE TABLE IF NOT EXISTS `app_settings` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`setting_key` VARCHAR(100) NOT NULL,
				`setting_value` TEXT NULL,
				`setting_type` VARCHAR(50) NOT NULL DEFAULT 'text',
				`updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY `uq_setting_key` (`setting_key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			$this->db->query($sql);
		}
	}
}
