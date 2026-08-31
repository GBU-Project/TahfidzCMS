<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="<?php echo htmlspecialchars($settings['institution_name'] . ' - ' . $settings['institution_tagline']); ?>">
	<title><?php echo htmlspecialchars($title); ?></title>
	
	<!-- Favicon -->
	<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
		<link rel="icon" type="image/png" href="<?php echo base_url($settings['institution_logo']); ?>">
	<?php else: ?>
		<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📖</text></svg>">
	<?php endif; ?>

	<!-- Google Fonts & Tailwind CSS & Font Awesome -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<style>
		body {
			font-family: 'Plus Jakarta Sans', sans-serif;
		}
		.font-arabic {
			font-family: 'Amiri', serif;
		}
		.bg-radial-gradient {
			background: radial-gradient(circle at top center, rgba(16, 185, 129, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
		}
	</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

	<!-- ========================================================= -->
	<!-- 1. NAVIGATION BAR -->
	<!-- ========================================================= -->
	<header class="sticky top-0 z-50 bg-white/85 backdrop-blur-md border-b border-slate-100 transition">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
			
			<!-- Brand / Logo -->
			<a href="<?php echo site_url(); ?>" class="flex items-center gap-3 group">
				<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
					<img src="<?php echo base_url($settings['institution_logo']); ?>" alt="Logo <?php echo htmlspecialchars($settings['institution_short_name']); ?>" class="h-11 w-auto max-w-[140px] object-contain rounded-lg">
				<?php else: ?>
					<div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-105 transition">
						📖
					</div>
				<?php endif; ?>
				<div class="flex flex-col">
					<span class="text-lg font-extrabold text-slate-900 tracking-tight leading-tight group-hover:text-emerald-700 transition">
						<?php echo htmlspecialchars($settings['institution_short_name']); ?>
					</span>
					<span class="text-[11px] font-medium text-emerald-700 uppercase tracking-wider">Tahfidz Portal</span>
				</div>
			</a>

			<!-- Nav Links (Desktop) -->
			<nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
				<a href="#fitur" class="hover:text-emerald-600 transition">Fitur Utama</a>
				<a href="#audiens" class="hover:text-emerald-600 transition">Untuk Siapa</a>
				<a href="#alur" class="hover:text-emerald-600 transition">Alur Tahfidz</a>
				<a href="#tentang" class="hover:text-emerald-600 transition">Tentang</a>
			</nav>

			<!-- Action Button -->
			<div class="flex items-center gap-3">
				<?php if ($is_logged_in): ?>
					<a href="<?php echo site_url('dashboard'); ?>" 
					   class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition">
						<i class="fa-solid fa-gauge-high"></i> Dashboard
					</a>
				<?php else: ?>
					<a href="<?php echo site_url('login'); ?>" 
					   class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition">
						<i class="fa-solid fa-right-to-bracket"></i> Masuk Sistem
					</a>
				<?php endif; ?>
			</div>

		</div>
	</header>

	<!-- ========================================================= -->
	<!-- 2. HERO SECTION -->
	<!-- ========================================================= -->
	<section class="relative pt-12 pb-20 md:pt-20 md:pb-28 overflow-hidden bg-radial-gradient">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
			
			<!-- Badge Tag -->
			<div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-6 shadow-sm">
				<span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
				<?php echo htmlspecialchars($settings['institution_tagline']); ?>
			</div>

			<!-- Dynamic Logo Big Display (if uploaded) -->
			<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
				<div class="flex justify-center mb-6">
					<div class="p-3 bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 max-w-[120px] max-h-[120px] flex items-center justify-center">
						<img src="<?php echo base_url($settings['institution_logo']); ?>" alt="<?php echo htmlspecialchars($settings['institution_name']); ?>" class="max-h-20 w-auto object-contain">
					</div>
				</div>
			<?php endif; ?>

			<!-- Main Heading -->
			<h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight max-w-4xl mx-auto leading-tight sm:leading-tight">
				<?php echo htmlspecialchars($settings['institution_name']); ?>
			</h1>

			<!-- Subtitle -->
			<p class="mt-6 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
				Platform digital terpadu untuk pencatatan setoran hafalan Al-Qur'an, evaluasi tajwid berstandar, perekaman audio bukti, dan leaderboard motivasi santri.
			</p>

			<!-- Arabic Calligraphy Quote -->
			<div class="mt-6 text-emerald-800 font-arabic text-xl sm:text-2xl font-bold tracking-wide">
				« خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ »
			</div>
			<div class="text-xs text-slate-400 mt-1 italic">
				"Sebaik-baik kalian adalah yang mempelajari Al-Qur'an dan mengajarkannya." (HR. Bukhari)
			</div>

			<!-- Action CTAs -->
			<div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
				<?php if ($is_logged_in): ?>
					<a href="<?php echo site_url('dashboard'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base px-8 py-3.5 rounded-2xl shadow-lg shadow-emerald-600/30 hover:shadow-xl transition transform hover:-translate-y-0.5">
						<i class="fa-solid fa-gauge-high"></i> Buka Dashboard
					</a>
				<?php else: ?>
					<a href="<?php echo site_url('login'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base px-8 py-3.5 rounded-2xl shadow-lg shadow-emerald-600/30 hover:shadow-xl transition transform hover:-translate-y-0.5">
						<i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Portal Hafalan
					</a>
				<?php endif; ?>
				<a href="#fitur" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-semibold text-base px-6 py-3.5 rounded-2xl transition">
					<i class="fa-solid fa-compass text-emerald-600"></i> Pelajari Fitur
				</a>
			</div>

			<!-- Metrics Quick Stats -->
			<div class="mt-14 pt-10 border-t border-slate-200/80 max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
				<div class="p-3">
					<div class="text-2xl sm:text-3xl font-extrabold text-emerald-800">30 Juz</div>
					<div class="text-xs text-slate-500 font-medium mt-1">Matriks Monitoring</div>
				</div>
				<div class="p-3">
					<div class="text-2xl sm:text-3xl font-extrabold text-emerald-800">100% Real-time</div>
					<div class="text-xs text-slate-500 font-medium mt-1">Penilaian & Poin</div>
				</div>
				<div class="p-3">
					<div class="text-2xl sm:text-3xl font-extrabold text-emerald-800">Audio Bukti</div>
					<div class="text-xs text-slate-500 font-medium mt-1">Terekam & Valid</div>
				</div>
				<div class="p-3">
					<div class="text-2xl sm:text-3xl font-extrabold text-emerald-800">Excel Export</div>
					<div class="text-xs text-slate-500 font-medium mt-1">Laporan Siap Cetak</div>
				</div>
			</div>

		</div>
	</section>

	<!-- ========================================================= -->
	<!-- 3. FITUR UTAMA -->
	<!-- ========================================================= -->
	<section id="fitur" class="py-16 md:py-24 bg-white border-y border-slate-100">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			
			<div class="text-center max-w-2xl mx-auto mb-14">
				<span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
					Keunggulan Sistem
				</span>
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-3">
					Fitur Lengkap untuk Monitoring Mutu Hafalan
				</h2>
				<p class="text-sm sm:text-base text-slate-500 mt-2">
					Dirancang khusus untuk mendukung operasional halaqah tahfidz yang terstruktur, akuntabel, dan menyenangkan.
				</p>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				
				<!-- Card 1 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 transition">
					<div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mb-4">
						<i class="fa-solid fa-pen-nib"></i>
					</div>
					<h3 class="text-base font-bold text-slate-900 mb-1.5">Input Setoran Cepat</h3>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						Pencatatan jenis setoran (Ziyadah/Muroja'ah/QC), jumlah kesalahan, kualitas bacaan tajwid, dan catatan evaluasi secara instan.
					</p>
				</div>

				<!-- Card 2 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 transition">
					<div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mb-4">
						<i class="fa-solid fa-microphone-lines"></i>
					</div>
					<h3 class="text-base font-bold text-slate-900 mb-1.5">Perekam Audio Bukti</h3>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						Merekam suara setoran langsung via browser atau unggah berkas audio sebagai dokumentasi verifikasi muroja'ah.
					</p>
				</div>

				<!-- Card 3 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 transition">
					<div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl mb-4">
						<i class="fa-solid fa-chart-pie"></i>
					</div>
					<h3 class="text-base font-bold text-slate-900 mb-1.5">Matriks 30 Juz & Target</h3>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						Visualisasi interaktif capaian juz yang telah tuntas, persentase hafalan santri, dan target juz per jenjang kelas.
					</p>
				</div>

				<!-- Card 4 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 transition">
					<div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl mb-4">
						<i class="fa-solid fa-trophy"></i>
					</div>
					<h3 class="text-base font-bold text-slate-900 mb-1.5">Leaderboard & Gamifikasi</h3>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						Sistem poin otomatis, ranking global, dan lencana bertingkat (Pemula hingga Hafidz 30 Juz) pemacu semangat santri.
					</p>
				</div>

				<!-- Card 5 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 transition">
					<div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl mb-4">
						<i class="fa-solid fa-file-excel"></i>
					</div>
					<h3 class="text-base font-bold text-slate-900 mb-1.5">Rekapitulasi & Export Excel</h3>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						Ekspor data setoran, rekap kelancaran, dan statistik hafalan per kelas / periode ke format spreadsheet yang rapi.
					</p>
				</div>

				<!-- Card 6 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 transition">
					<div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl mb-4">
						<i class="fa-solid fa-shield-halved"></i>
					</div>
					<h3 class="text-base font-bold text-slate-900 mb-1.5">Hak Akses Ketat (RBAC)</h3>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						Super Admin mengelola sistem, Guru hanya mengakses kelas binaannya, dan Santri hanya melihat rekaman pribadinya.
					</p>
				</div>

			</div>
		</div>
	</section>

	<!-- ========================================================= -->
	<!-- 4. UNTUK SIAPA (AUDIENS) -->
	<!-- ========================================================= -->
	<section id="audiens" class="py-16 md:py-24 bg-slate-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			
			<div class="text-center max-w-2xl mx-auto mb-14">
				<span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
					Solusi Terintegrasi
				</span>
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-3">
					Didesain untuk Seluruh Pemangku Kepentingan
				</h2>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
				
				<!-- Audiens 1 -->
				<div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
					<div>
						<div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl mb-6">
							<i class="fa-solid fa-user-graduate"></i>
						</div>
						<h3 class="text-xl font-bold text-slate-900 mb-2">Santri & Siswa</h3>
						<p class="text-sm text-slate-500 mb-6 leading-relaxed">
							Memantau capaian target 30 juz secara mandiri, melihat riwayat evaluasi guru, dan terpacu meraih poin tertinggi di leaderboard.
						</p>
					</div>
					<ul class="text-xs text-slate-600 space-y-2.5 pt-4 border-t border-slate-100 font-medium">
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Matriks kemajuan juz visual</li>
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Catatan koreksi tajwid ust/ustadzah</li>
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Lencana prestasi berkala</li>
					</ul>
				</div>

				<!-- Audiens 2 -->
				<div class="bg-white p-8 rounded-3xl border-2 border-emerald-600 shadow-md flex flex-col justify-between relative overflow-hidden">
					<div class="absolute top-4 right-4 bg-emerald-600 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full">
						Paling Sering Digunakan
					</div>
					<div>
						<div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl mb-6">
							<i class="fa-solid fa-chalkboard-user"></i>
						</div>
						<h3 class="text-xl font-bold text-slate-900 mb-2">Ustadz & Guru Tahfidz</h3>
						<p class="text-sm text-slate-500 mb-6 leading-relaxed">
							Memudahkan penilaian di halaqah, mengoreksi kelancaran santri, merekam audio bukti, serta mengunduh rekap kelas binaan.
						</p>
					</div>
					<ul class="text-xs text-slate-600 space-y-2.5 pt-4 border-t border-slate-100 font-medium">
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Input setoran tanpa ribet</li>
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Proteksi akses khusus kelas diampu</li>
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Cetak laporan berkala</li>
					</ul>
				</div>

				<!-- Audiens 3 -->
				<div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
					<div>
						<div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl mb-6">
							<i class="fa-solid fa-building-columns"></i>
						</div>
						<h3 class="text-xl font-bold text-slate-900 mb-2">Pimpinan & Admin</h3>
						<p class="text-sm text-slate-500 mb-6 leading-relaxed">
							Mengawasi perkembangan hafalan seluruh tingkatan secara makro, mengelola master akun pengguna, dan mengatur identitas lembaga.
						</p>
					</div>
					<ul class="text-xs text-slate-600 space-y-2.5 pt-4 border-t border-slate-100 font-medium">
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Dashboard agregat statistik sekolah</li>
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Manajemen kelas & penugasan guru</li>
						<li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Kustomisasi branding institusi</li>
					</ul>
				</div>

			</div>
		</div>
	</section>

	<!-- ========================================================= -->
	<!-- 5. ALUR TAHFIDZ -->
	<!-- ========================================================= -->
	<section id="alur" class="py-16 md:py-24 bg-white border-y border-slate-100">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			
			<div class="text-center max-w-2xl mx-auto mb-14">
				<span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
					Alur Kerja Praktis
				</span>
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-3">
					Proses Penilaian Terstruktur & Akurat
				</h2>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
				
				<!-- Step 1 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
					<span class="text-4xl font-black text-emerald-200 mb-2 block">01</span>
					<h3 class="text-base font-bold text-slate-900 mb-1">Setoran Santri</h3>
					<p class="text-xs text-slate-500">
						Santri membacakan hafalan ziyadah atau muroja'ah di depan ustadz/ustadzah pembimbing.
					</p>
				</div>

				<!-- Step 2 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
					<span class="text-4xl font-black text-emerald-200 mb-2 block">02</span>
					<h3 class="text-base font-bold text-slate-900 mb-1">Evaluasi & Audio</h3>
					<p class="text-xs text-slate-500">
						Guru menginput kesalahan & kualitas makhraj/tajwid, merekam audio bukti setoran, dan sistem otomatis mengkalkulasi predikat.
					</p>
				</div>

				<!-- Step 3 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
					<span class="text-4xl font-black text-emerald-200 mb-2 block">03</span>
					<h3 class="text-base font-bold text-slate-900 mb-1">Poin & Badge</h3>
					<p class="text-xs text-slate-500">
						Sistem otomatis menghitung penambahan poin dan menaikkan tingkat badge hafalan santri.
					</p>
				</div>

				<!-- Step 4 -->
				<div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
					<span class="text-4xl font-black text-emerald-200 mb-2 block">04</span>
					<h3 class="text-base font-bold text-slate-900 mb-1">Monitoring & Laporan</h3>
					<p class="text-xs text-slate-500">
						Data terekam di matriks juz, papan leaderboard, serta dapat dicetak menjadi laporan periodik.
					</p>
				</div>

			</div>
		</div>
	</section>

	<!-- ========================================================= -->
	<!-- 6. CALL TO ACTION (CTA) -->
	<!-- ========================================================= -->
	<section class="py-16 md:py-20 bg-emerald-900 text-white relative overflow-hidden">
		<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
			
			<div class="w-16 h-16 rounded-3xl bg-emerald-800 text-emerald-300 flex items-center justify-center text-3xl mx-auto shadow-inner">
				📖
			</div>

			<h2 class="text-2xl sm:text-4xl font-black tracking-tight">
				Mulai Monitoring Hafalan Al-Qur'an Bersama <br class="hidden sm:inline">
				<span class="text-emerald-300"><?php echo htmlspecialchars($settings['institution_name']); ?></span>
			</h2>

			<p class="text-sm sm:text-base text-emerald-100 max-w-2xl mx-auto leading-relaxed">
				Tingkatkan kualitas, kedisiplinan muroja'ah, dan capaian target santri dengan sistem informasi hafalan modern.
			</p>

			<div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
				<?php if ($is_logged_in): ?>
					<a href="<?php echo site_url('dashboard'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-emerald-50 text-emerald-900 font-bold px-8 py-3.5 rounded-2xl shadow-xl transition">
						<i class="fa-solid fa-gauge-high"></i> Masuk ke Dashboard
					</a>
				<?php else: ?>
					<a href="<?php echo site_url('login'); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-emerald-50 text-emerald-900 font-bold px-8 py-3.5 rounded-2xl shadow-xl transition">
						<i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
					</a>
				<?php endif; ?>
			</div>

		</div>
	</section>

	<!-- ========================================================= -->
	<!-- 7. FOOTER -->
	<!-- ========================================================= -->
	<footer id="tentang" class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 text-sm">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			
			<div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-slate-800">
				<div class="flex items-center gap-3">
					<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
						<img src="<?php echo base_url($settings['institution_logo']); ?>" alt="Logo" class="h-9 w-auto max-w-[120px] object-contain rounded">
					<?php else: ?>
						<span class="text-2xl">📖</span>
					<?php endif; ?>
					<div>
						<div class="text-base font-bold text-white"><?php echo htmlspecialchars($settings['institution_name']); ?></div>
						<div class="text-xs text-slate-500"><?php echo htmlspecialchars($settings['institution_tagline']); ?></div>
					</div>
				</div>

				<div class="flex items-center gap-6 text-xs text-slate-400">
					<a href="<?php echo site_url('login'); ?>" class="hover:text-emerald-400 transition">Portal Login</a>
					<a href="<?php echo site_url('dashboard'); ?>" class="hover:text-emerald-400 transition">Dashboard</a>
					<span>&bull;</span>
					<span>Powered by <strong>GBU-Projects</strong></span>
				</div>
			</div>

			<div class="pt-6 text-center text-xs text-slate-500">
				&copy; <?php echo date('Y'); ?> <strong>GBU-Projects</strong> &bull; Open Source Project. Dikelola untuk <?php echo htmlspecialchars($settings['institution_name']); ?>.
			</div>

		</div>
	</footer>

</body>
</html>
