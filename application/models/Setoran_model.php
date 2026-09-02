<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Setoran_model — Model transaksi inti data setoran hafalan siswa.
 *
 * Mengelola tabel `setoran` dan memastikan konsistensi `total_poin` serta `badge`
 * di tabel `siswa` menggunakan database transaction dan query atomic.
 */
class Setoran_model extends CI_Model
{
	private $table = 'setoran';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Poin_calculator');
	}

	/**
	 * Generate kode setoran untuk PREVIEW di form saja (ditampilkan sebelum
	 * disimpan). Kode SESUNGGUHNYA yang tersimpan dihasilkan di dalam
	 * create(), berbasis insert_id — lihat catatan di sana kenapa preview
	 * ini tidak dipakai langsung untuk insert.
	 *
	 * @return string
	 */
	public function generate_kode_setoran()
	{
		$this->db->select('kode_setoran')
			->from($this->table)
			->order_by('id', 'DESC')
			->limit(1);

		$row = $this->db->get()->row();
		if (! $row || empty($row->kode_setoran)) {
			return 'STR-000001';
		}

		$parts = explode('-', $row->kode_setoran);
		$next_num = isset($parts[1]) ? ((int) $parts[1] + 1) : 1;

		return sprintf('STR-%06d', $next_num);
	}

	/**
	 * Ambil semua data setoran dengan filter dan relasi join.
	 *
	 * @param array $filter [kelas_id, kelas_ids, nisn, tanggal_awal, tanggal_akhir, keterangan, jenis_setoran, search]
	 * @param int|null $limit
	 * @param int|null $offset
	 * @return array
	 */
	public function get_all(array $filter = array(), $limit = null, $offset = null)
	{
		$this->db->select('setoran.*, siswa.nama AS nama_siswa, kelas.nama_kelas, guru.nama AS nama_guru')
			->from($this->table)
			->join('siswa', 'siswa.nisn = setoran.nisn', 'left')
			->join('kelas', 'kelas.id = setoran.kelas_id', 'left')
			->join('users AS guru', 'guru.id = setoran.guru_pengoreksi_id', 'left')
			->order_by('setoran.tanggal', 'DESC')
			->order_by('setoran.waktu', 'DESC')
			->order_by('setoran.id', 'DESC');

		if (! empty($filter['nisn'])) {
			$this->db->where('setoran.nisn', $filter['nisn']);
		}

		if (! empty($filter['kelas_id'])) {
			$this->db->where('setoran.kelas_id', $filter['kelas_id']);
		} elseif (! empty($filter['kelas_ids'])) {
			$this->db->where_in('setoran.kelas_id', $filter['kelas_ids']);
		}

		if (! empty($filter['tanggal_awal'])) {
			$this->db->where('setoran.tanggal >=', $filter['tanggal_awal']);
		}

		if (! empty($filter['tanggal_akhir'])) {
			$this->db->where('setoran.tanggal <=', $filter['tanggal_akhir']);
		}

		if (! empty($filter['keterangan'])) {
			$this->db->where('setoran.keterangan', $filter['keterangan']);
		}

		if (! empty($filter['jenis_setoran'])) {
			$this->db->where('setoran.jenis_setoran', $filter['jenis_setoran']);
		}

		if (! empty($filter['search'])) {
			$search = $filter['search'];
			$this->db->group_start()
				->like('siswa.nama', $search)
				->or_like('setoran.nisn', $search)
				->or_like('setoran.surat', $search)
				->or_like('setoran.kode_setoran', $search)
				->group_end();
		}

		if ($limit !== null) {
			$this->db->limit($limit, $offset);
		}

		return $this->db->get()->result();
	}

	/**
	 * Hitung total baris setoran yang cocok dengan filter untuk pagination.
	 *
	 * @param array $filter
	 * @return int
	 */
	public function count_all_filtered(array $filter = array())
	{
		$this->db->from($this->table)
			->join('siswa', 'siswa.nisn = setoran.nisn', 'left')
			->join('kelas', 'kelas.id = setoran.kelas_id', 'left');

		if (! empty($filter['nisn'])) {
			$this->db->where('setoran.nisn', $filter['nisn']);
		}

		if (! empty($filter['kelas_id'])) {
			$this->db->where('setoran.kelas_id', $filter['kelas_id']);
		} elseif (! empty($filter['kelas_ids'])) {
			$this->db->where_in('setoran.kelas_id', $filter['kelas_ids']);
		}

		if (! empty($filter['tanggal_awal'])) {
			$this->db->where('setoran.tanggal >=', $filter['tanggal_awal']);
		}

		if (! empty($filter['tanggal_akhir'])) {
			$this->db->where('setoran.tanggal <=', $filter['tanggal_akhir']);
		}

		if (! empty($filter['keterangan'])) {
			$this->db->where('setoran.keterangan', $filter['keterangan']);
		}

		if (! empty($filter['jenis_setoran'])) {
			$this->db->where('setoran.jenis_setoran', $filter['jenis_setoran']);
		}

		if (! empty($filter['search'])) {
			$search = $filter['search'];
			$this->db->group_start()
				->like('siswa.nama', $search)
				->or_like('setoran.nisn', $search)
				->or_like('setoran.surat', $search)
				->or_like('setoran.kode_setoran', $search)
				->group_end();
		}

		return $this->db->count_all_results();
	}

	/**
	 * Ambil satu data setoran berdasarkan ID.
	 *
	 * @param int $id
	 * @return object|null
	 */
	public function get_by_id($id)
	{
		return $this->db->select('setoran.*, siswa.nama AS nama_siswa, siswa.total_poin AS siswa_total_poin, siswa.badge AS siswa_badge, kelas.nama_kelas, guru.nama AS nama_guru')
			->from($this->table)
			->join('siswa', 'siswa.nisn = setoran.nisn', 'left')
			->join('kelas', 'kelas.id = setoran.kelas_id', 'left')
			->join('users AS guru', 'guru.id = setoran.guru_pengoreksi_id', 'left')
			->where('setoran.id', $id)
			->get()
			->row();
	}

	/**
	 * Tambah setoran baru dengan atomic database transaction.
	 *
	 * Catatan desain (perbaikan race condition): kode_setoran TIDAK dihitung
	 * dari "baca baris terakhir + 1" sebelum insert — pendekatan itu rawan
	 * duplikat kalau dua guru submit persis bersamaan (keduanya baca nomor
	 * terakhir yang sama sebelum salah satu sempat insert). Sebagai gantinya,
	 * baris di-insert dulu dengan kode_setoran sementara yang unik (berbasis
	 * uniqid), lalu langsung ditimpa dengan kode final yang diturunkan dari
	 * insert_id (dijamin unik oleh auto-increment MySQL) — semua dalam
	 * transaction yang sama.
	 *
	 * @param array $data Data kolom tabel `setoran`. Nilai 'kode_setoran' yang
	 *                     dikirim (jika ada) akan DIABAIKAN demi konsistensi ini.
	 * @return int ID setoran yang baru dibuat
	 * @throws Exception
	 */
	public function create(array $data)
	{
		// Kode sementara, hanya untuk memenuhi constraint NOT NULL UNIQUE
		// selama sepersekian detik sebelum ditimpa kode final di bawah.
		$data['kode_setoran'] = 'TMP-' . uniqid('', true);

		// Hitung keterangan (L/CL/KL/TL) & skor secara OTOMATIS dan konsisten,
		// jika belum disediakan oleh caller (controller sudah menghitungnya
		// lebih dulu, tapi dihitung ulang di sini juga sebagai jaring pengaman
		// supaya model ini tetap konsisten dipakai dari jalur manapun).
		if (! isset($data['keterangan']) || ! isset($data['skor'])) {
			$data['keterangan'] = $this->poin_calculator->hitung_keterangan($data['jumlah_kesalahan'], $data['jenis_setoran']);
			$data['skor']       = $this->poin_calculator->hitung_skor($data['keterangan'], $data['kualitas_bacaan']);
		}
		$data['poin'] = $data['skor']; // poin = skor apa adanya
		$poin = $data['poin'];

		$this->db->trans_begin();

		// 1. Insert data setoran (dengan kode sementara)
		$this->db->insert($this->table, $data);
		$insert_id = $this->db->insert_id();

		// 2. Timpa dengan kode final berbasis insert_id -> dijamin unik,
		//    tidak mungkin bentrok walau ada request bersamaan.
		$kode_final = sprintf('STR-%06d', $insert_id);
		$this->db->update($this->table, array('kode_setoran' => $kode_final), array('id' => $insert_id));

		// 3. Atomic update total_poin siswa
		$this->db->set('total_poin', 'total_poin + ' . (int) $poin, FALSE)
			->where('nisn', $data['nisn'])
			->update('siswa');

		// 4. Ambil total poin terbaru untuk evaluasi badge
		$siswa = $this->db->select('total_poin')->where('nisn', $data['nisn'])->get('siswa')->row();
		if ($siswa) {
			$new_badge = $this->poin_calculator->hitung_badge($siswa->total_poin);
			$this->db->update('siswa', array('badge' => $new_badge), array('nisn' => $data['nisn']));
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			throw new Exception('Gagal menyimpan transaksi setoran.');
		}

		$this->db->trans_commit();
		return $insert_id;
	}

	/**
	 * Update penilaian setoran (oleh guru/admin), menyesuaikan selisih poin siswa.
	 *
	 * @param int   $id
	 * @param array $data Kolom yang diperbarui: jenis_setoran, jumlah_kesalahan,
	 *                     kualitas_bacaan, hasil_qc, catatan, guru_pengoreksi_id
	 * @return bool
	 * @throws Exception
	 */
	public function update_penilaian($id, array $data)
	{
		$setoran = $this->get_by_id($id);
		if (! $setoran) {
			throw new Exception('Data setoran tidak ditemukan.');
		}

		$poin_lama = (int) $setoran->poin;

		$jenis_setoran_baru    = isset($data['jenis_setoran']) ? $data['jenis_setoran'] : $setoran->jenis_setoran;
		$jumlah_kesalahan_baru = isset($data['jumlah_kesalahan']) ? $data['jumlah_kesalahan'] : $setoran->jumlah_kesalahan;
		$kualitas_bacaan_baru  = isset($data['kualitas_bacaan']) ? $data['kualitas_bacaan'] : $setoran->kualitas_bacaan;

		$keterangan_baru = $this->poin_calculator->hitung_keterangan($jumlah_kesalahan_baru, $jenis_setoran_baru);
		$skor_baru       = $this->poin_calculator->hitung_skor($keterangan_baru, $kualitas_bacaan_baru);
		$poin_baru       = $skor_baru;
		$selisih         = $poin_baru - $poin_lama;

		$data['jenis_setoran']    = $jenis_setoran_baru;
		$data['jumlah_kesalahan'] = $jumlah_kesalahan_baru;
		$data['kualitas_bacaan']  = $kualitas_bacaan_baru;
		$data['keterangan']       = $keterangan_baru;
		$data['skor']             = $skor_baru;
		$data['poin']             = $poin_baru;

		// hasil_qc hanya relevan untuk jenis 'qc'; kosongkan jika berubah
		// menjadi jenis lain supaya tidak menyisakan data tidak konsisten.
		if ($jenis_setoran_baru !== 'qc') {
			$data['hasil_qc'] = null;
		} elseif (! isset($data['hasil_qc'])) {
			$data['hasil_qc'] = $setoran->hasil_qc;
		}

		$this->db->trans_begin();

		// 1. Update baris setoran
		$this->db->update($this->table, $data, array('id' => $id));

		// 2. Sesuaikan total_poin siswa jika ada selisih
		if ($selisih !== 0) {
			$operator = $selisih > 0 ? '+' : '-';
			$abs_selisih = abs($selisih);

			$this->db->set('total_poin', "total_poin {$operator} {$abs_selisih}", FALSE)
				->where('nisn', $setoran->nisn)
				->update('siswa');
		}

		// 3. Update badge berdasarkan total poin terbaru
		$siswa = $this->db->select('total_poin')->where('nisn', $setoran->nisn)->get('siswa')->row();
		if ($siswa) {
			$new_badge = $this->poin_calculator->hitung_badge($siswa->total_poin);
			$this->db->update('siswa', array('badge' => $new_badge), array('nisn' => $setoran->nisn));
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			throw new Exception('Gagal memperbarui penilaian setoran.');
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Hapus data setoran dan kurangi total poin siswa terkait.
	 *
	 * @param int $id
	 * @return bool
	 * @throws Exception
	 */
	public function delete($id)
	{
		$setoran = $this->get_by_id($id);
		if (! $setoran) {
			throw new Exception('Data setoran tidak ditemukan.');
		}

		$this->db->trans_begin();

		// 1. Hapus baris setoran
		$this->db->delete($this->table, array('id' => $id));

		// 2. Kurangi poin siswa
		$this->db->set('total_poin', 'total_poin - ' . (int) $setoran->poin, FALSE)
			->where('nisn', $setoran->nisn)
			->update('siswa');

		// 3. Hitung ulang badge
		$siswa = $this->db->select('total_poin')->where('nisn', $setoran->nisn)->get('siswa')->row();
		if ($siswa) {
			$total = max(0, (int) $siswa->total_poin);
			$new_badge = $this->poin_calculator->hitung_badge($total);
			$this->db->update('siswa', array('total_poin' => $total, 'badge' => $new_badge), array('nisn' => $setoran->nisn));
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			throw new Exception('Gagal menghapus setoran.');
		}

		$this->db->trans_commit();

		// Hapus file audio fisik jika ada
		if (! empty($setoran->audio_bukti) && file_exists('./' . $setoran->audio_bukti)) {
			@unlink('./' . $setoran->audio_bukti);
		}

		return TRUE;
	}

	/**
	 * Hitung total setoran untuk keperluan statistik/dashboard.
	 *
	 * @param array<int> $kelas_ids
	 * @param string|null $keterangan 'L' | 'CL' | 'KL' | 'TL'
	 * @param string|null $nisn
	 * @return int
	 */
	public function count_setoran(array $kelas_ids = array(), $keterangan = null, $nisn = null)
	{
		if (! empty($kelas_ids)) {
			$this->db->where_in('kelas_id', $kelas_ids);
		}
		if (! empty($keterangan)) {
			$this->db->where('keterangan', $keterangan);
		}
		if (! empty($nisn)) {
			$this->db->where('nisn', $nisn);
		}
		return $this->db->count_all_results($this->table);
	}

	/**
	 * Hitung total setoran bulan ini
	 *
	 * @param array<int> $kelas_ids
	 * @param string|null $nisn
	 * @return int
	 */
	public function count_setoran_bulan_ini(array $kelas_ids = array(), $nisn = null)
	{
		$awal_bulan = date('Y-m-01');
		$akhir_bulan = date('Y-m-t');

		$this->db->where('tanggal >=', $awal_bulan)
			->where('tanggal <=', $akhir_bulan);

		if (! empty($kelas_ids)) {
			$this->db->where_in('kelas_id', $kelas_ids);
		}
		if (! empty($nisn)) {
			$this->db->where('nisn', $nisn);
		}
		return $this->db->count_all_results($this->table);
	}

	/**
	 * Ambil daftar juz unik yang sudah disetorkan (dengan keterangan L/CL,
	 * yaitu tidak perlu mengulang) per santri
	 *
	 * @param string $nisn
	 * @return array
	 */
	public function get_progress_juz_by_nisn($nisn)
	{
		return $this->db->select('juz, COUNT(id) as total_setoran, MAX(tanggal) as tanggal_terakhir')
			->from($this->table)
			->where('nisn', $nisn)
			->where_in('keterangan', array('L', 'CL'))
			->group_by('juz')
			->order_by('juz', 'ASC')
			->get()
			->result();
	}

	/**
	 * Ambil rekap laporan setoran berdasarkan filter
	 *
	 * @param array $filter
	 * @return array
	 */
	public function get_laporan_rekap(array $filter = array())
	{
		$this->db->select('
			setoran.nisn,
			siswa.nama as nama_siswa,
			kelas.nama_kelas,
			COUNT(setoran.id) as total_setoran,
			SUM(setoran.poin) as total_poin_periode,
			SUM(CASE WHEN setoran.keterangan = "L"  THEN 1 ELSE 0 END) as total_lancar,
			SUM(CASE WHEN setoran.keterangan = "CL" THEN 1 ELSE 0 END) as total_cukup_lancar,
			SUM(CASE WHEN setoran.keterangan = "KL" THEN 1 ELSE 0 END) as total_kurang_lancar,
			SUM(CASE WHEN setoran.keterangan = "TL" THEN 1 ELSE 0 END) as total_tidak_lancar,
			MAX(setoran.tanggal) as setoran_terakhir
		')
		->from($this->table)
		->join('siswa', 'siswa.nisn = setoran.nisn', 'left')
		->join('kelas', 'kelas.id = setoran.kelas_id', 'left')
		->group_by('setoran.nisn, siswa.nama, kelas.nama_kelas')
		->order_by('total_poin_periode', 'DESC');

		if (! empty($filter['kelas_id'])) {
			$this->db->where('setoran.kelas_id', $filter['kelas_id']);
		} elseif (! empty($filter['kelas_ids'])) {
			$this->db->where_in('setoran.kelas_id', $filter['kelas_ids']);
		}

		if (! empty($filter['tanggal_awal'])) {
			$this->db->where('setoran.tanggal >=', $filter['tanggal_awal']);
		}

		if (! empty($filter['tanggal_akhir'])) {
			$this->db->where('setoran.tanggal <=', $filter['tanggal_akhir']);
		}

		return $this->db->get()->result();
	}

	// =====================================================================
	// Fitur Rapor Publik (untuk orangtua, via token — lihat controller Rapor)
	// =====================================================================

	/**
	 * Tren skor setoran seorang siswa dari waktu ke waktu, untuk line chart
	 * di halaman rapor publik. Diurutkan tanggal ASC supaya grafik terbaca
	 * kiri->kanan sebagai timeline maju, dan dibatasi $limit data TERBARU
	 * (diambil dari ekor lalu dibalik) supaya grafik tidak terlalu padat
	 * kalau riwayat setoran sudah sangat banyak.
	 *
	 * @param string $nisn
	 * @param int    $limit
	 * @return array [['tanggal' => 'Y-m-d', 'skor' => int], ...]
	 */
	public function get_skor_trend_by_nisn($nisn, $limit = 30)
	{
		$rows = $this->db->select('tanggal, skor')
			->from($this->table)
			->where('nisn', $nisn)
			->order_by('tanggal', 'DESC')
			->order_by('id', 'DESC')
			->limit($limit)
			->get()
			->result();

		return array_reverse($rows);
	}

	/**
	 * Distribusi jumlah setoran per kode keterangan (L/CL/KL/TL) untuk
	 * seorang siswa, untuk donut chart di halaman rapor publik.
	 *
	 * @param string $nisn
	 * @return array ['L' => int, 'CL' => int, 'KL' => int, 'TL' => int]
	 */
	public function get_keterangan_distribution_by_nisn($nisn)
	{
		$rows = $this->db->select('keterangan, COUNT(id) as jumlah')
			->from($this->table)
			->where('nisn', $nisn)
			->group_by('keterangan')
			->get()
			->result();

		$dist = array('L' => 0, 'CL' => 0, 'KL' => 0, 'TL' => 0);
		foreach ($rows as $r) {
			if (isset($dist[$r->keterangan])) {
				$dist[$r->keterangan] = (int) $r->jumlah;
			}
		}
		return $dist;
	}

	/**
	 * Riwayat setoran terbaru seorang siswa (ringkas, untuk daftar di
	 * halaman rapor publik — bukan tabel lengkap seperti Riwayat.php).
	 *
	 * @param string $nisn
	 * @param int    $limit
	 * @return array
	 */
	public function get_recent_by_nisn($nisn, $limit = 10)
	{
		return $this->db->select('tanggal, juz, surat, ayat_dari, ayat_sampai, jenis_setoran, keterangan, skor')
			->from($this->table)
			->where('nisn', $nisn)
			->order_by('tanggal', 'DESC')
			->order_by('id', 'DESC')
			->limit($limit)
			->get()
			->result();
	}

	// =====================================================================
	// Fitur Statistik Publik (untuk jamaah/komunitas masjid — agregat SAJA,
	// tidak boleh mengembalikan data identifiable per-siswa. Lihat controller
	// Statistik.)
	// =====================================================================

	/**
	 * Distribusi jumlah setoran per kode keterangan (L/CL/KL/TL) SE-SEKOLAH
	 * (bukan per siswa), untuk donut chart di halaman statistik publik.
	 *
	 * @return array ['L' => int, 'CL' => int, 'KL' => int, 'TL' => int]
	 */
	public function get_keterangan_distribution_global()
	{
		$rows = $this->db->select('keterangan, COUNT(id) as jumlah')
			->from($this->table)
			->group_by('keterangan')
			->get()
			->result();

		$dist = array('L' => 0, 'CL' => 0, 'KL' => 0, 'TL' => 0);
		foreach ($rows as $r) {
			if (isset($dist[$r->keterangan])) {
				$dist[$r->keterangan] = (int) $r->jumlah;
			}
		}
		return $dist;
	}

	/**
	 * Jumlah total setoran per bulan, N bulan terakhir, SE-SEKOLAH — untuk
	 * bar/line chart tren di halaman statistik publik.
	 *
	 * @param int $bulan_terakhir
	 * @return array [['bulan' => 'YYYY-MM', 'jumlah' => int], ...] terurut lama->baru
	 */
	public function get_tren_bulanan_global($bulan_terakhir = 6)
	{
		$mulai = date('Y-m-01', strtotime('-' . ($bulan_terakhir - 1) . ' months'));

		$rows = $this->db->select("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(id) as jumlah")
			->from($this->table)
			->where('tanggal >=', $mulai)
			->group_by("DATE_FORMAT(tanggal, '%Y-%m')")
			->order_by('bulan', 'ASC')
			->get()
			->result();

		// Isi bulan yang kosong (tanpa setoran sama sekali) dengan 0, supaya
		// grafik tidak "meloncat" melewati bulan yang datanya nihil.
		$map = array();
		foreach ($rows as $r) {
			$map[$r->bulan] = (int) $r->jumlah;
		}

		$hasil = array();
		for ($i = $bulan_terakhir - 1; $i >= 0; $i--) {
			$key = date('Y-m', strtotime("-{$i} months"));
			$hasil[] = array('bulan' => $key, 'jumlah' => isset($map[$key]) ? $map[$key] : 0);
		}
		return $hasil;
	}

	/**
	 * Total jumlah setoran sepanjang masa & bulan berjalan, SE-SEKOLAH.
	 *
	 * @return array ['total' => int, 'bulan_ini' => int]
	 */
	public function get_ringkasan_global()
	{
		$total = $this->db->count_all_results($this->table);
		$bulan_ini = $this->count_setoran_bulan_ini();
		return array('total' => $total, 'bulan_ini' => $bulan_ini);
	}
}

