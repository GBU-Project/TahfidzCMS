<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title; ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">
	
	<!-- Header Bar -->
	<header class="bg-emerald-900 text-white shadow-sm py-4 px-6">
		<div class="max-w-4xl mx-auto flex items-center justify-between">
			<div class="flex items-center gap-3">
				<span class="text-2xl">📖</span>
				<div>
					<div class="text-lg font-bold tracking-tight">TahfidzCMS Web Installer</div>
					<div class="text-xs text-emerald-200">Sistem Monitoring Hafalan Al-Qur'an</div>
				</div>
			</div>
			<div class="text-xs bg-emerald-800 text-emerald-100 px-3 py-1.5 rounded-full font-medium border border-emerald-700">
				Step 1 of 4
			</div>
		</div>
	</header>

	<!-- Main Container -->
	<main class="max-w-4xl w-full mx-auto px-4 py-8 flex-1">
		
		<!-- Step Tracker -->
		<div class="grid grid-cols-4 gap-2 mb-8 text-center text-xs font-semibold">
			<div class="py-2.5 rounded-xl bg-emerald-600 text-white shadow-sm">1. Syarat Sistem</div>
			<div class="py-2.5 rounded-xl bg-gray-200 text-gray-500">2. Database</div>
			<div class="py-2.5 rounded-xl bg-gray-200 text-gray-500">3. Skema DB</div>
			<div class="py-2.5 rounded-xl bg-gray-200 text-gray-500">4. Super Admin</div>
		</div>

		<!-- Card Requirements -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
			<div>
				<h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
					<i class="fa-solid fa-server text-emerald-600"></i> Pemeriksaan Kebutuhan Server
				</h1>
				<p class="text-sm text-gray-500 mt-1">Pastikan lingkungan server hosting memenuhi seluruh dependensi sebelum memulai instalasi.</p>
			</div>

			<!-- PHP Version Check -->
			<div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50 flex items-center justify-between">
				<div>
					<div class="font-semibold text-sm text-gray-800">Versi PHP (Minimal 7.4.0)</div>
					<div class="text-xs text-gray-500">Versi PHP server saat ini: <strong><?php echo $php_version; ?></strong></div>
				</div>
				<div>
					<?php if ($php_ok): ?>
						<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
							<i class="fa-solid fa-circle-check"></i> Memenuhi
						</span>
					<?php else: ?>
						<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-bold">
							<i class="fa-solid fa-circle-xmark"></i> Tidak Memenuhi
						</span>
					<?php endif; ?>
				</div>
			</div>

			<!-- PHP Extensions Check -->
			<div>
				<h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
					<i class="fa-solid fa-puzzle-piece text-emerald-600"></i> Ekstensi PHP yang Dibutuhkan
				</h2>
				<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
					<?php foreach ($ext_checks as $ext): ?>
						<div class="p-3 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between text-xs">
							<div>
								<div class="font-bold text-gray-800 font-mono"><?php echo $ext['name']; ?></div>
								<div class="text-gray-400 text-[11px]"><?php echo $ext['desc']; ?></div>
							</div>
							<div>
								<?php if ($ext['status']): ?>
									<span class="text-emerald-600 font-bold"><i class="fa-solid fa-check mr-1"></i>Aktif</span>
								<?php else: ?>
									<span class="text-rose-600 font-bold"><i class="fa-solid fa-xmark mr-1"></i>Belum Ada</span>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Folder Permissions Check -->
			<div>
				<h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
					<i class="fa-solid fa-folder-tree text-emerald-600"></i> Izin Menulis Direktori (Writable Permissions)
				</h2>
				<div class="space-y-2">
					<?php foreach ($dir_checks as $dir): ?>
						<div class="p-3 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between text-xs font-mono">
							<span class="text-gray-700 font-medium"><?php echo $dir['path']; ?></span>
							<?php if ($dir['writable']): ?>
								<span class="text-emerald-600 font-bold"><i class="fa-solid fa-check mr-1"></i>Writable</span>
							<?php else: ?>
								<span class="text-rose-600 font-bold"><i class="fa-solid fa-xmark mr-1"></i>Read-Only</span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Navigation Buttons -->
			<div class="pt-4 border-t flex items-center justify-between">
				<div class="text-xs text-gray-400">
					<?php if ($can_proceed): ?>
						<i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Server siap untuk instalasi TahfidzCMS.
					<?php else: ?>
						<i class="fa-solid fa-triangle-exclamation text-rose-600 mr-1"></i> Mohon lengkapi syarat di atas sebelum melanjutkan.
					<?php endif; ?>
				</div>
				<a href="<?php echo site_url('installer/step2'); ?>" 
				   class="<?php echo $can_proceed ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer' : 'bg-gray-300 text-gray-500 pointer-events-none'; ?> px-6 py-2.5 rounded-xl font-medium text-sm transition shadow-sm inline-flex items-center gap-2">
					Lanjut ke Konfigurasi Database <i class="fa-solid fa-arrow-right"></i>
				</a>
			</div>

		</div>
	</main>

	<!-- Footer -->
	<footer class="text-center py-4 text-xs text-gray-400 border-t bg-white">
		&copy; <?php echo date('Y'); ?> <strong>GBU-Projects</strong> &bull; TahfidzCMS Open Source Monitoring Hafalan Santri.
	</footer>

</body>
</html>
