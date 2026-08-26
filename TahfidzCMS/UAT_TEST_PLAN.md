# UAT TEST PLAN — TAHFIDZCMS

**Versi Dokumen**: 1.0  
**Tanggal Rilis**: 2026-08-26  
**Target Sistem**: TahfidzCMS CodeIgniter 3 (Branch: `develop` | Baseline Commit: `2e6f398`)  
**Penulis**: Antigravity QA & Release Engineering Team  

---

## 1. UAT Objective (Tujuan UAT)

Dokumen ini disusun sebagai panduan pengujian penerimaan pengguna (*User Acceptance Testing* / UAT) untuk memvalidasi bahwa sistem **TahfidzCMS** telah memenuhi seluruh kebutuhan fungsional dan operasional madrasah/pesantren dari sudut pandang pengguna nyata (**Admin**, **Guru**, dan **Siswa**).

Pengujian ini fokus pada pengalaman pengguna, integritas alur bisnis (*business workflow*), isolasi hak akses (*role security*), serta kenyamanan penggunaan pada perangkat desktop dan mobile/tablet.

---

## 2. UAT Scope (Ruang Lingkup UAT)

Berikut adalah modul dan alur kerja yang **termasuk dalam pengujian (IN SCOPE)**:

1. **Authentication & Session**: Login, Logout, Session Timeout, Proteksi Akses Langsung URL.
2. **Admin Users Management**: Tambah, Edit, Nonaktifkan/Hapus User (Admin, Guru, Siswa).
3. **Admin Kelas Management**: Tambah Kelas, Edit Nama Kelas, Proteksi Hapus Kelas Berisi Siswa.
4. **Relasi Guru - Kelas**: Penugasan kelas yang diampu guru (*multi-classes assignment*).
5. **Manajemen Data Siswa**: Pendaftaran santri, penentuan target juz, dan sinkronisasi akun.
6. **Pencatatan Setoran**: Input setoran hafalan Al-Qur'an (Surat, Juz, Ayat, Nilai, Status).
7. **Rekaman Audio Bukti**: Rekam langsung via browser (MediaRecorder) & upload file audio pendukung.
8. **Penilaian & Evaluasi**: Modul koreksi nilai tajwid, catatan guru, dan penyesuaian poin otomatis.
9. **Riwayat Setoran**: Filter tanggal, filter kelas, filter santri, pencarian keyword, & pagination.
10. **Progress Visual 30 Juz**: Peta capaian juz 1–30 santri vs target hafalan.
11. **Leaderboard Ranking**: Peringkat santri global dan per rombel/kelas berbasis akumulasi poin.
12. **Laporan Rekapitulasi**: Rekap setoran per santri/kelas dalam rentang tanggal.
13. **Export Dokumen Excel**: Pengunduhan berkas laporan berformat spreadsheet `.xls` rapi.
14. **Profile & Kata Sandi**: Pengubahan profil, ganti kata sandi mandiri, dan upload foto profil.
15. **Dashboard Sesuai Role**: Tampilan analitik ringkas sesuai peran masing-masing aktor.
16. **Responsive & Usability**: Pengoperasian lancar di desktop, tablet, dan smartphone (sidebar toggle & form responsif).
17. **Role Isolation & Security**: Guru tidak dapat melihat/mengubah kelas guru lain; Siswa hanya dapat melihat datanya sendiri.

---

## 3. Out of Scope (Future Scope)

Fitur berikut **TIDAK TERMASUK** dalam scope pengujian rilis ini dan dicatat sebagai *Future Scope Roadmap* (bukan merupakan defect/bug):

- **Executive Dashboard Khusus**: Analitik multi-tahun khusus yayasan/eksekutif (kebutuhan ringkasan tingkat sekolah saat ini sudah terpenuhi melalui Dashboard Admin).
- **Modul Master Tahun Ajaran Khusus**: Pengaturan tahun akademik tersendiri (filter histori dan laporan saat ini sudah dapat dilakukan secara presisi melalui filter rentang tanggal `tanggal_awal` & `tanggal_akhir`).

---

## 4. Actors (Aktor Pengguna)

| Aktor | Deskripsi Peran | Hak Akses Utama |
|---|---|---|
| **Super Admin** | Pengelola sistem dan data master sekolah | Mengelola pengguna, kelas, penugasan guru, reset password, seluruh setoran, laporan, dan export Excel. |
| **Dewan Guru** | Pengampu halaqah tahfidz santri | Menginput setoran, merekam audio, menilai tajwid, melihat riwayat, progress, dan leaderboard kelas yang diampunya. |
| **Siswa / Santri** | Peserta hafalan Al-Qur'an | Melihat progress juz sendiri, riwayat hafalan sendiri, peringkat leaderboard, dan mengelola profil pribadi. |

---

## 5. Test Environment (Lingkungan UAT)

- **URL Aplikasi**: `http://localhost/TahfidzCMS/` (atau URL staging yang disiapkan)
- **Web Server**: Apache / Nginx + PHP 8.x
- **Database**: MySQL 8.0 / MariaDB 10.4+
- **Browser yang Didukung**: Google Chrome (v110+), Mozilla Firefox (v110+), Microsoft Edge, Safari Mobile, Chrome Mobile.
- **Hardware Rekomendasi**: Laptop/Desktop (min. 1366x768) dan Smartphone Android/iOS dengan mikrofon aktif.

---

## 6. Test Data (Data Uji Awal)

| Role | Username (NIP/NISN) | Password Default | Keterangan |
|---|---|---|---|
| **Admin** | `197501012005011001` | `123456` | Akun Super Admin |
| **Guru A** | `197802022010011002` | `123456` | Guru Pengampu Kelas 7A & 7B |
| **Guru B** | `198003032012011003` | `123456` | Guru Pengampu Kelas 8A & 8B |
| **Siswa A** | `1001` | `123456` | Santri Kelas 7A (Fulan bin Fulan) |
| **Siswa B** | `1002` | `123456` | Santri Kelas 7A (Ahmad Fauzi) |

---

## 7. User Test Scenarios (Per Aktor)

### 👨‍💼 A. Skenario UAT: SUPER ADMIN

| UAT-ID | Actor | Feature | Business Objective | Precondition | Test Steps | Expected Result | Actual Result | Status | Tester | Date | Notes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **UAT-ADM-01** | Admin | Authentication | Masuk ke panel admin | Akun admin aktif | 1. Buka `/login`<br>2. Masukkan NIP Admin & Password<br>3. Klik Masuk | Berhasil masuk ke Dashboard Admin, muncul sambutan nama admin | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-02** | Admin | Dashboard | Melihat statistik global sekolah | Login sebagai Admin | 1. Buka halaman Dashboard | Menampilkan kartu total santri, total setoran, setoran bulan ini, breakdown lancar/cukup/perbaikan, dan top santri | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-03** | Admin | Kelola Kelas | Menambah rombel kelas baru | Berada di panel admin | 1. Buka menu Kelola Kelas<br>2. Masukkan nama kelas "9A"<br>3. Klik Simpan | Kelas 9A muncul di tabel daftar kelas, muncul notifikasi sukses | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-04** | Admin | Kelola Kelas | Mengubah nama kelas | Ada kelas terdaftar | 1. Edit nama kelas di tabel<br>2. Klik Update | Nama kelas berubah di tabel dan relasi siswa/guru tetap konsisten | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-05** | Admin | Kelola Kelas | Proteksi hapus kelas aktif | Kelas memiliki santri | 1. Klik Hapus pada kelas yang ada santrinya | Ditolak dengan pesan jelas: "Tidak bisa dihapus: masih ada N siswa terdaftar" | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-06** | Admin | Kelola Users | Membuat akun Guru baru | Berada di menu Users | 1. Klik Tambah User<br>2. Isi Nama, NIP, Password<br>3. Pilih Role Guru<br>4. Centang kelas yang diampu<br>5. Klik Simpan | Akun guru terbuat, data tersimpan di tabel users & guru_kelas | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-07** | Admin | Kelola Users | Membuat akun Siswa baru | Berada di menu Users | 1. Klik Tambah User<br>2. Isi Nama, NISN, Password<br>3. Pilih Role Siswa<br>4. Pilih Kelas & Target Juz<br>5. Klik Simpan | Akun siswa terbuat di tabel users dan profil santri terbuat di tabel siswa | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-08** | Admin | Reset Password | Mereset password akun yang lupa sandi | Ada akun user | 1. Di daftar Users, klik tombol Reset Pass pada salah satu akun<br>2. Konfirmasi dialog | Kata sandi akun tersebut di-reset kembali ke `123456`, pesan sukses muncul | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-09** | Admin | Laporan | Menampilkan rekapitulasi setoran | Ada data setoran | 1. Buka menu Laporan<br>2. Pilih filter kelas & tanggal<br>3. Klik Filter | Tabel rekapitulasi menampilkan data ringkasan santri sesuai filter | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-10** | Admin | Export Excel | Mengunduh berkas laporan Excel | Ada data setoran | 1. Di menu Laporan, klik "Export ke Excel (.xls)" | Berkas `.xls` terunduh, dapat dibuka di MS Excel dengan format tabel rapi | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-11** | Admin | Profile | Memperbarui profil dan foto admin | Login Admin | 1. Buka menu Profil<br>2. Ganti nama & upload foto baru<br>3. Simpan | Profil dan foto profil terbarui di header dan database | - | `NOT TESTED` | - | - | - |
| **UAT-ADM-12** | Admin | Logout | Keluar dari sesi admin | Login Admin | 1. Klik tombol Keluar di sidebar | Sesi berakhir, diarahkan kembali ke halaman login | - | `NOT TESTED` | - | - | - |

---

### 👳‍♂️ B. Skenario UAT: DEWAN GURU

| UAT-ID | Actor | Feature | Business Objective | Precondition | Test Steps | Expected Result | Actual Result | Status | Tester | Date | Notes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **UAT-GUR-01** | Guru | Authentication | Masuk ke panel guru | Akun guru aktif | 1. Buka `/login`<br>2. Masukkan NIP Guru & Password<br>3. Klik Masuk | Masuk ke dashboard guru, hanya melihat data kelas yang diampu | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-02** | Guru | Dashboard | Melihat statistik kelas binaan | Login Guru A (ampu 7A) | 1. Buka Dashboard | Statistik total santri dan setoran hanya menghitung kelas 7A/7B | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-03** | Guru | Input Setoran | Mengisi form setoran santri | Santri terdaftar di kelas guru | 1. Buka menu Input Setoran -> Tambah<br>2. Pilih Santri, Surat, Juz, Ayat, Nilai Tajwid, Status | Form menampilkan dropdown santri kelasnya, estimasi poin otomatis terhitung | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-04** | Guru | Rekaman Audio | Merekam suara hafalan santri | Browser mendukung mikrofon | 1. Di form setoran, klik "Mulai Rekam"<br>2. Santri membaca ayat (min. 5 detik)<br>3. Klik "Selesai Rekam" | Widget player preview muncul, timer rekaman tercatat, file audio terlampir | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-05** | Guru | Upload Audio File | Mengunggah file audio alternatif | Ada file MP3/WebM | 1. Pilih tab Upload File Audio<br>2. Pilih file rekaman dari perangkat<br>3. Klik Simpan Data Setoran | Tombol submit disable (anti double-submit), data & audio berhasil disimpan | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-06** | Guru | Penilaian & Audio | Mengoreksi nilai & mendengar rekaman | Ada setoran masuk | 1. Buka menu Penilaian<br>2. Putar audio di tabel/modal<br>3. Ubah nilai tajwid & catatan<br>4. Simpan | Audio berputar jernih, nilai terupdate, selisih poin santri otomatis disesuaikan | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-07** | Guru | Riwayat & Pagination | Memeriksa riwayat setoran kelas | Data setoran > 20 | 1. Buka menu Riwayat<br>2. Gunakan filter status & search<br>3. Klik halaman 2 | Riwayat terfilter rapi, navigasi pagination berjalan tanpa reset filter | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-08** | Guru | Progress Hafalan | Memantau peta capaian juz santri | Ada data santri | 1. Buka menu Progress<br>2. Pilih santri binaan | Grid 30 Juz menandai juz yang sudah tuntas dengan warna hijau emerald | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-09** | Guru | Leaderboard | Melihat peringkat kelas | Ada data poin | 1. Buka menu Leaderboard<br>2. Filter per kelas binaan | Santri dengan akumulasi poin tertinggi berada di urutan atas | - | `NOT TESTED` | - | - | - |
| **UAT-GUR-10** | Guru | Role Isolation | Isolasi akses kelas guru lain | Guru A (hanya ampu 7A) | 1. Coba buka `/setoran?kelas_id=3` (kelas 8A)<br>2. Coba nilai santri kelas 8A | Ditolak dengan HTTP 403 / "Akses Ditolak" | - | `NOT TESTED` | - | - | - |

---

### 🧑‍🎓 C. Skenario UAT: SISWA / SANTRI

| UAT-ID | Actor | Feature | Business Objective | Precondition | Test Steps | Expected Result | Actual Result | Status | Tester | Date | Notes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **UAT-SIS-01** | Siswa | Authentication | Masuk ke portal santri | Akun santri aktif | 1. Buka `/login`<br>2. Masukkan NISN & Password<br>3. Klik Masuk | Masuk ke Dashboard Santri, menu admin/guru tidak muncul di sidebar | - | `NOT TESTED` | - | - | - |
| **UAT-SIS-02** | Siswa | Dashboard Pribadi | Melihat capaian hafalan sendiri | Login Siswa A (NISN 1001) | 1. Buka Dashboard | Menampilkan badge santri, total setoran pribadi, juz selesai, dan riwayat terakhir | - | `NOT TESTED` | - | - | - |
| **UAT-SIS-03** | Siswa | Progress 30 Juz | Memeriksa juz yang sudah disetor | Ada setoran tervalidasi | 1. Buka menu Progress | Menampilkan status juz 1–30 milik sendiri dan progress bar target juz | - | `NOT TESTED` | - | - | - |
| **UAT-SIS-04** | Siswa | Riwayat Sendiri | Memeriksa catatan & evaluasi guru | Ada riwayat setoran | 1. Buka menu Riwayat | Hanya menampilkan daftar setoran milik NISN sendiri, catatan guru terbaca | - | `NOT TESTED` | - | - | - |
| **UAT-SIS-05** | Siswa | Leaderboard | Melihat posisi ranking di sekolah | Login Siswa | 1. Buka menu Leaderboard | Peringkat kelas & global terlihat, memotivasi santri berkompetisi positif | - | `NOT TESTED` | - | - | - |
| **UAT-SIS-06** | Siswa | Ganti Password | Mengganti kata sandi mandiri | Login Siswa | 1. Buka menu Profil<br>2. Masukkan password lama & baru<br>3. Simpan<br>4. Login ulang | Password berhasil diganti, login dengan password baru sukses | - | `NOT TESTED` | - | - | - |
| **UAT-SIS-07** | Siswa | Data Privacy Isolation | Isolasi kerahasiaan data santri | Siswa A login | 1. Coba akses `/riwayat?nisn=1002`<br>2. Coba akses menu `/setoran` atau `/users` | Sistem menolak / tetap hanya menampilkan data milik Siswa A sendiri (403) | - | `NOT TESTED` | - | - | - |

---

## 8. End-to-End (E2E) Business Scenarios

Pengujian integrasi alur bisnis lengkap (*End-to-End*) dari hulu ke hilir:

### 🔄 E2E-01: Siklus Penuh Input Setoran, Audio, Poin, & Leaderboard
- **Aktor**: Guru A & Siswa A
- **Alur Pengujian**:
  1. Guru A login ke sistem.
  2. Guru A membuka menu **Input Setoran** -> **Tambah Setoran**.
  3. Memilih Siswa A (`1001 - Fulan bin Fulan`), Surat `Al-Baqarah`, Juz `1`, Ayat `1` s/d `50`, Nilai `A`, Status `Lancar`.
  4. Guru A menyalakan rekaman audio bukti hafalan, lalu klik **Simpan Data Setoran**.
  5. Verifikasi setoran muncul di daftar **Riwayat**.
  6. Verifikasi total poin Siswa A bertambah `+120 Poin` (Base A: 100 + Lancar: 20).
  7. Buka menu **Progress** -> Verifikasi Juz 1 tercentang tuntas.
  8. Buka menu **Leaderboard** -> Verifikasi peringkat Siswa A naik sesuai total poin barunya.
- **Status**: `NOT TESTED`

---

### 🔄 E2E-02: Siklus Koreksi Penilaian & Penyesuaian Selisih Poin Otomatis
- **Aktor**: Guru A
- **Alur Pengujian**:
  1. Guru A membuka menu **Penilaian**.
  2. Memilih setoran Siswa A yang sebelumnya bernilai `A` / `Lancar` (120 poin).
  3. Memutar audio rekaman untuk evaluasi ulang.
  4. Mengubah nilai menjadi `B` dan status menjadi `Cukup` (85 poin), lalu mengisi catatan: *"Perhatikan makhraj huruf tsa"*.
  5. Klik **Simpan Penilaian**.
  6. Verifikasi poin setoran berubah menjadi 85.
  7. Verifikasi `total_poin` pada profil Siswa A berkurang sebesar selisihnya (`-35 poin`), dan Leaderboard otomatis terupdate.
- **Status**: `NOT TESTED`

---

### 🔄 E2E-03: Siklus Pembuatan Guru Baru, Penugasan Kelas, & Input Setoran
- **Aktor**: Admin & Guru Baru
- **Alur Pengujian**:
  1. Admin login dan membuka **Kelola Kelas** -> Membuat kelas `"7C"`.
  2. Admin membuka **Kelola Users** -> Menambahkan Guru baru (*Ust. Zaid*, NIP `198505052020011005`, password `123456`).
  3. Menugaskan kelas `"7C"` kepada Ust. Zaid.
  4. Mendaftarkan santri baru (*Ibrahim*, NISN `1005`) ke kelas `"7C"`.
  5. Logout Admin -> Login sebagai Guru Baru (*Ust. Zaid*).
  6. Verifikasi Ust. Zaid hanya melihat kelas `"7C"` di dashboard dan dropdown input setoran.
  7. Ust. Zaid berhasil menginput setoran untuk santri Ibrahim.
- **Status**: `NOT TESTED`

---

### 🔄 E2E-04: Siklus Reset Password oleh Admin & Pembaruan Mandiri oleh Pengguna
- **Aktor**: Admin & Guru A
- **Alur Pengujian**:
  1. Admin login ke menu **Kelola Users**.
  2. Klik **Reset Pass** pada akun Guru A.
  3. Logout Admin.
  4. Guru A login menggunakan kata sandi default (`123456`).
  5. Guru A diarahkan ke menu **Profil Saya** -> mengisi password lama `123456` dan password baru `GuruTahfidz2026!`.
  6. Guru A logout dan mencoba login kembali dengan password baru.
  7. Login berhasil dengan password baru.
- **Status**: `NOT TESTED`

---

### 🔄 E2E-05: Siklus Filter Rekapitulasi Laporan & Verifikasi Integritas Export Excel
- **Aktor**: Admin
- **Alur Pengujian**:
  1. Admin login dan membuka menu **Laporan**.
  2. Memilih filter Kelas `"7A"` dan rentang tanggal bulan berjalan.
  3. Memeriksa ringkasan rekapitulasi pada layar.
  4. Klik tombol **Export ke Excel (.xls)**.
  5. Membuka file spreadsheet yang terunduh di Microsoft Excel / LibreOffice.
  6. Memverifikasi header tabel hijau emerald, border rapi, data NISN tidak terpotong (format teks), dan jumlah baris data tepat sesuai filter di layar.
- **Status**: `NOT TESTED`

---

## 9. Acceptance Criteria (Kriteria Kelulusan UAT)

Aplikasi dinyatakan **LULUS UAT dan SIAP PRODUCTION DEPLOYMENT** apabila:

1. ✅ Seluruh skenario pengujian bisnis (P0 & P1) berstatus **PASS**.
2. ✅ Seluruh skenario End-to-End (E2E-01 s/d E2E-05) berstatus **PASS**.
3. ✅ Tidak ditemukan defect berkategori **Critical** atau **High** yang belum terselesaikan.
4. ✅ Admin dapat menyelesaikan seluruh tugas administrasi (kelas, user, laporan, reset password) tanpa kendala teknis.
5. ✅ Guru dapat menjalankan alur setoran, rekaman audio, dan koreksi penilaian dengan lancar.
6. ✅ Siswa dapat memantau capaian hafalan dan peringkatnya secara real-time.
7. ✅ Isolasi hak akses (*Role Isolation*) terbukti 100% aman (tidak ada kebocoran data antar kelas/santri).
8. ✅ Rekaman audio dapat diputar lancar di peramban desktop maupun mobile.
9. ✅ Berkas laporan Excel dapat diunduh dan dibuka dengan struktur data yang valid dan rapi.
10. ✅ Antarmuka aplikasi nyaman dan responsif digunakan di smartphone, tablet, dan desktop.

---

## 10. Defect Classification (Tingkat Keparahan Temuan)

Jika tester menemukan ketidaksesuaian selama pengujian, kategorikan temuan sebagai berikut:

- **CRITICAL**: Sistem crash, database corrupt, kebocoran data antar santri/kelas, atau alur utama terhenti total tanpa workaround.
- **HIGH**: Fitur utama tidak berjalan sesuai fungsi bisnis (mis. poin tidak terhitung, audio gagal diunggah/diputar) namun tidak merusak data lain.
- **MEDIUM**: Ketidaksesuaian alur sekunder, masalah tampilan visual yang mengganggu keterbacaan, atau navigasi yang membingungkan.
- **LOW**: Minor typo teks, penyelarasan alignment visual kecil, atau saran kenyamanan antarmuka.

---

## 11. UAT Sign-Off (Lembar Pengesahan)

| Peran Penguji | Nama Lengkap | Tanda Tangan / Status | Tanggal |
|---|---|---|---|
| **Wakil Kurikulum / Admin** | _______________________ | `[ ] APPROVED  [ ] REJECTED` | ___ / ___ / 2026 |
| **Koordinator Guru Tahfidz** | _______________________ | `[ ] APPROVED  [ ] REJECTED` | ___ / ___ / 2026 |
| **Lead QA / Release Eng.** | Antigravity QA Engine | `[ ] APPROVED  [ ] REJECTED` | 26 / 08 / 2026 |

*Catatan Akhir: Dokumen ini siap dibagikan kepada tim penguji lapangan dan pengguna madrasah untuk pelaksanaan pengujian langsung.*
