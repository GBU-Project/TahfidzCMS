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
				Step 3 of 4
			</div>
		</div>
	</header>

	<main class="max-w-4xl w-full mx-auto px-4 py-8 flex-1">
		
		<!-- Step Tracker -->
		<div class="grid grid-cols-4 gap-2 mb-8 text-center text-xs font-semibold">
			<div class="py-2.5 rounded-xl bg-emerald-100 text-emerald-800">1. Syarat Sistem</div>
			<div class="py-2.5 rounded-xl bg-emerald-100 text-emerald-800">2. Database</div>
			<div class="py-2.5 rounded-xl bg-emerald-600 text-white shadow-sm">3. Skema DB</div>
			<div class="py-2.5 rounded-xl bg-gray-200 text-gray-500">4. Super Admin</div>
		</div>

		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
			<div>
				<h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
					<i class="fa-solid fa-table text-emerald-600"></i> Pemasangan Tabel & Skema Database
				</h1>
				<p class="text-sm text-gray-500 mt-1">Koneksi ke database <code class="bg-gray-100 px-2 py-0.5 rounded text-emerald-700 font-bold"><?php echo htmlspecialchars($db_name); ?></code> berhasil diverifikasi.</p>
			</div>

			<?php if (! empty($error)): ?>
				<div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl flex items-center gap-2">
					<i class="fa-solid fa-circle-exclamation text-base"></i>
					<div><?php echo $error; ?></div>
				</div>
			<?php endif; ?>

			<?php if ($is_existing): ?>
				<div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-800 space-y-3">
					<div class="font-bold flex items-center gap-2 text-sm text-amber-900">
						<i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Database Sudah Memiliki Tabel TahfidzCMS
					</div>
					<p>Tabel aplikasi sudah terdeteksi di database ini. Anda dapat memilih untuk mempertahankan data lama atau menginstall ulang skema segar (*Fresh Installation*).</p>
				</div>
			<?php endif; ?>

			<?php echo form_open('installer/process_schema', array('class' => 'space-y-4')); ?>
				
				<?php if ($is_existing): ?>
					<div class="space-y-3">
						<label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
							<input type="radio" name="install_type" value="keep" checked class="mt-1 text-emerald-600 focus:ring-emerald-500">
							<div>
								<div class="text-sm font-bold text-gray-800">Pertahankan Tabel yang Ada (Upgrade / Re-link)</div>
								<div class="text-xs text-gray-500">Struktur dan data tabel yang ada tidak akan dihapus.</div>
							</div>
						</label>

						<label class="flex items-start gap-3 p-4 rounded-xl border border-rose-200 hover:bg-rose-50/50 cursor-pointer">
							<input type="radio" name="install_type" value="fresh" class="mt-1 text-rose-600 focus:ring-rose-500">
							<div>
								<div class="text-sm font-bold text-rose-800">Fresh Installation (Hapus & Pasang Ulang Skema)</div>
								<div class="text-xs text-rose-600">Seluruh tabel tahfidzcms akan di-drop dan diganti dengan skema fresh proyek.</div>
							</div>
						</label>
					</div>
				<?php else: ?>
					<input type="hidden" name="install_type" value="fresh">
					<div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800 space-y-2">
						<div class="font-bold text-sm text-emerald-900 flex items-center gap-2">
							<i class="fa-solid fa-circle-check text-emerald-600"></i> Siap Memasang Skema Fresh
						</div>
						<p>Skema database resmi TahfidzCMS (tabel `users`, `kelas`, `guru_kelas`, `siswa`, `setoran`, `api_tokens`) akan dipasang secara otomatis.</p>
					</div>
				<?php endif; ?>

				<div class="pt-4 border-t flex items-center justify-between">
					<a href="<?php echo site_url('installer/step2'); ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium">
						<i class="fa-solid fa-arrow-left mr-1"></i> Kembali
					</a>
					<button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-medium text-sm transition shadow-sm inline-flex items-center gap-2">
						Pasang Skema & Lanjut <i class="fa-solid fa-arrow-right"></i>
					</button>
				</div>
			<?php echo form_close(); ?>

		</div>
	</main>

	<footer class="text-center py-4 text-xs text-gray-400 border-t bg-white">
		TahfidzCMS &copy; 2026. Dikembangkan untuk kemudahan monitoring tahfidz santri.
	</footer>

</body>
</html>
