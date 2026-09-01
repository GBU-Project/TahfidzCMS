# TahfidzCMS

<p align="center">
  <strong>Sistem Informasi & Monitoring Hafalan Al-Qur'an Berbasis Web</strong><br>
  <em>Solusi manajemen halaqah tahfidz yang terstruktur, akuntabel, dan mendukung multi-lembaga (pesantren, madrasah, dan sekolah Islam).</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%20%7C%208.1%20%7C%208.2-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Framework-CodeIgniter%203-EF4444?style=flat-square&logo=codeigniter&logoColor=white" alt="Framework">
  <img src="https://img.shields.io/badge/Database-MySQL%208.0%2B-00758F?style=flat-square&logo=mysql&logoColor=white" alt="Database">
  <img src="https://img.shields.io/badge/License-Open%20Source-10B981?style=flat-square" alt="License">
  <img src="https://img.shields.io/badge/Maintained%20by-GBU--Projects-047857?style=flat-square" alt="GBU-Projects">
</p>

---

## 📖 Tentang TahfidzCMS

**TahfidzCMS** adalah aplikasi web open-source yang dirancang untuk mempermudah pencatatan, evaluasi kualitas tajwid & kelancaran, monitoring target 30 Juz, serta pelaporan capaian hafalan santri secara real-time. 

Aplikasi ini mendukung arsitektur **kustomisasi identitas lembaga dinamis** sehingga dapat langsung digunakan oleh berbagai yayasan, pesantren, maupun sekolah tahfidz tanpa perlu mengubah source code.

---

## ✨ Fitur Utama

- 🌐 **Public Landing Page Dinamis**: Halaman profil depan institusi yang modern dan responsif, menampilkan visi, alur tahfidz, dan branding lembaga secara otomatis.
- ⚙️ **Modul Identitas Lembaga**: Super Admin dapat mengunggah logo, mengubah nama lembaga, nama brand, dan tagline secara langsung dari Dashboard.
- 🎙️ **Pencatatan Setoran & Audio Bukti**: Input setoran ziyadah/muroja'ah santri dilengkapi perekam suara langsung via browser (WebM/Opus) atau upload file audio sebagai bukti verifikasi.
- 📊 **Evaluasi & Penilaian Tajwid**: Review kelancaran (Lancar, Cukup, Perlu Perbaikan) dan penilaian mutu (A/B/C) dengan perhitungan poin otomatis.
- 🏆 **Gamifikasi & Leaderboard**: Papan peringkat santri (Top Santri per kelas & global) serta sistem lencana bertingkat (*Pemula* hingga *Hafidz 30 Juz*).
- 📈 **Matriks Kemajuan 30 Juz**: Visualisasi interaktif capaian juz santri vs target hafalan jenjang kelas.
- 📑 **Export Laporan Excel**: Ekspor rekapitulasi data setoran, nilai, dan statistik santri ke format spreadsheet (.xls) yang rapi dan siap cetak.
- 🔒 **Keamanan & Role-Based Access Control (RBAC)**:
  - **Super Admin**: Kelola master pengguna, kelas, penugasan guru, dan identitas lembaga.
  - **Dewan Guru**: Mengelola dan menilai santri khusus pada kelas yang diampu (IDOR-safe).
  - **Santri / Siswa**: Mengakses riwayat hafalan, target juz, dan leaderboard pribadi.
  - **API Rate Limiting & CSRF Protected**: Perlindungan brute-force login dan verifikasi token form.

---

## 🗂️ Struktur Proyek

```text
TahfidzCMS/
├── application/             # Source code aplikasi CodeIgniter 3
│   ├── config/              # Konfigurasi routes, database, CSRF, MIME types
│   ├── controllers/         # Web controllers & API JSON endpoints
│   ├── core/                # MY_Controller (Web Guard) & MY_API_Controller (Token Guard)
│   ├── helpers/             # Format tanggal & durasi audio helper
│   ├── libraries/           # Kalkulator poin hafalan, upload handler, exporter
│   ├── models/              # Model data (User, Siswa, Setoran, Kelas, Settings, dll)
│   └── views/               # Antarmuka tampilan (Landing, Dashboard, Setoran, dll)
├── database/                # Skema SQL database (tahfidzcms.sql)
├── docs/                    # Dokumentasi teknis lengkap & rencana pengujian
│   ├── BLUEPRINT.md         # Blueprint arsitektur dan fase pengembangan
│   ├── BRANDING_TEST_PLAN.md# Rencana pengujian modul identitas lembaga
│   ├── INSTALLATION.md      # Panduan instalasi dan deployment
│   ├── INSTALLER_TEST_PLAN.md# Rencana uji Web Installer interaktif
│   └── QA_REGRESSION.md     # Laporan audit regresi dan keamanan
├── uploads/                 # Direktori penyimpanan berkas upload
│   ├── branding/            # Logo lembaga
│   ├── profile/             # Foto profil pengguna
│   └── setoran_audio/       # Berkas audio rekaman bukti setoran
└── README.md
```

---

## 🚀 Panduan Instalasi Cepat

TahfidzCMS dilengkapi **Web Installer Interaktif** untuk memudahkan pemasangan di server hosting cPanel, VPS, maupun local server (XAMPP/Laragon).

1. **Persyaratan Server**:
   - PHP versi **7.4**, **8.1**, atau **8.2**.
   - Ekstensi PHP: `mysqli`, `mbstring`, `json`, `session`, `openssl`, `fileinfo`.
   - Database: MySQL 8.0+ atau MariaDB 10.4+.

2. **Langkah Pemasangan via Browser**:
   - Ekstrak source code ke folder web server Anda (misal `c:/xampp/htdocs/tahfidzcms/` atau `public_html/`).
   - Buka browser dan akses alamat instalasi:
     ```
     http://localhost/tahfidzcms/installer
     ```
   - Ikuti 4 langkah panduan Web Installer:
     1. **Pemeriksaan Syarat Server & Izin Folder**.
     2. **Konfigurasi Database MySQL**.
     3. **Pemasangan Skema Database**.
     4. **Pembuatan Akun Super Admin**.
   - Setelah selesai, sistem otomatis terkunci (`installed.lock`) dan siap digunakan.

> 📖 **Dokumentasi Lengkap Instalasi**: Silakan lihat di [docs/INSTALLATION.md](docs/INSTALLATION.md).

---

## ⚙️ Kustomisasi Identitas Lembaga

Setelah instalasi selesai, Super Admin dapat langsung menyesuaikan branding sekolah/pesantren:
1. Login sebagai Super Admin melalui `/login`.
2. Masuk ke menu **Pengaturan → Identitas Lembaga** (`/settings`).
3. Unggah Logo resmi, ubah Nama Lembaga, Nama Singkat / Brand, dan Tagline.
4. Klik **Simpan Perubahan**.
5. Identitas akan langsung terefleksi secara otomatis di **Public Landing Page**, **Header Dashboard**, **Sidebar**, dan **Login Portal**.

---

## 📚 Dokumentasi Teknis Tambahan

Seluruh dokumen arsitektur dan pengujian tersimpan rapi di direktori **[`docs/`](docs/)**:
- [Panduan Instalasi & Deployment](docs/INSTALLATION.md)
- [Blueprint Arsitektur Sistem](docs/BLUEPRINT.md)
- [Petunjuk Lanjutan & Fitur](docs/PETUNJUK_LANJUTAN.md)
- [Petunjuk Hardening Keamanan](docs/PETUNJUK_LANJUTAN_SECURITY.md)
- [Petunjuk Mitigasi XSS & Audit](docs/PETUNJUK_LANJUTAN_XSS.md)
- [Laporan Audit QA & Regresi Keamanan](docs/QA_REGRESSION.md)
- [Rencana Pengujian Identitas Lembaga & Branding](docs/BRANDING_TEST_PLAN.md)
- [Rencana Pengujian Web Installer](docs/INSTALLER_TEST_PLAN.md)

---

## 📄 Lisensi & Atribusi

- **Pengembang & Pemelihara**: **GBU-Projects**
- **Lisensi**: Proyek ini dirilis sebagai perangkat lunak **Open Source**.
- &copy; 2026 **GBU-Projects** &bull; *TahfidzCMS - Sistem Monitoring Hafalan Al-Qur'an Santri*.
