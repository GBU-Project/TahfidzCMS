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
				Step 4 of 4
			</div>
		</div>
	</header>

	<main class="max-w-4xl w-full mx-auto px-4 py-8 flex-1">
		
		<!-- Step Tracker -->
		<div class="grid grid-cols-4 gap-2 mb-8 text-center text-xs font-semibold">
			<div class="py-2.5 rounded-xl bg-emerald-100 text-emerald-800">1. Syarat Sistem</div>
			<div class="py-2.5 rounded-xl bg-emerald-100 text-emerald-800">2. Database</div>
			<div class="py-2.5 rounded-xl bg-emerald-100 text-emerald-800">3. Skema DB</div>
			<div class="py-2.5 rounded-xl bg-emerald-600 text-white shadow-sm">4. Super Admin</div>
		</div>

		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
			<div>
				<h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
					<i class="fa-solid fa-user-shield text-emerald-600"></i> Pembuatan Akun Super Admin
				</h1>
				<p class="text-sm text-gray-500 mt-1">Buat akun administrator utama yang memiliki hak penuh mengelola master data dan pengguna.</p>
			</div>

			<?php if (! empty($error)): ?>
				<div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl flex items-center gap-2">
					<i class="fa-solid fa-circle-exclamation text-base"></i>
					<div><?php echo $error; ?></div>
				</div>
			<?php endif; ?>

			<?php echo form_open('installer/process_final', array('class' => 'space-y-4')); ?>
				
				<div>
					<label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap Administrator <span class="text-red-500">*</span></label>
					<input type="text" name="admin_nama" required placeholder="Contoh: Super Admin Madrasah"
						class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>

				<div>
					<label class="block text-xs font-medium text-gray-600 mb-1">Username / NIP Admin <span class="text-red-500">*</span></label>
					<input type="text" name="admin_username" required placeholder="Contoh: admin atau 197501012005011001"
						class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono">
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1">Password Administrator <span class="text-red-500">*</span></label>
						<input type="password" name="admin_password" required placeholder="Minimal 6 karakter"
							class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					</div>
					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1">Ulangi Password <span class="text-red-500">*</span></label>
						<input type="password" name="admin_confirm" required placeholder="Ketik ulang password..."
							class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					</div>
				</div>

				<div class="pt-4 border-t flex items-center justify-between">
					<a href="<?php echo site_url('installer/step3'); ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium">
						<i class="fa-solid fa-arrow-left mr-1"></i> Kembali
					</a>
					<button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-medium text-sm transition shadow-sm inline-flex items-center gap-2">
						Selesaikan Instalasi & Kunci <i class="fa-solid fa-lock"></i>
					</button>
				</div>
			<?php echo form_close(); ?>

		</div>
	</main>

	<!-- Footer -->
	<footer class="text-center py-4 text-xs text-gray-400 border-t bg-white">
		&copy; <?php echo date('Y'); ?> <strong>GBU-Projects</strong> &bull; TahfidzCMS Open Source Monitoring Hafalan Santri.
	</footer>

</body>
</html>
