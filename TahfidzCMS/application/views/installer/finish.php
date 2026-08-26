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
				Selesai
			</div>
		</div>
	</header>

	<main class="max-w-xl w-full mx-auto px-4 py-12 flex-1 flex items-center">
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center space-y-6 w-full">
			<div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-3xl">
				<i class="fa-solid fa-circle-check"></i>
			</div>

			<div>
				<h1 class="text-2xl font-bold text-gray-800">Instalasi Berhasil!</h1>
				<p class="text-sm text-gray-500 mt-2">TahfidzCMS telah berhasil dipasang dan dikonfigurasi. File pengunci <code class="bg-gray-100 px-2 py-0.5 rounded text-emerald-700 font-mono">installed.lock</code> telah dibuat untuk mengamankan sistem dari akses ulang installer.</p>
			</div>

			<div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 text-xs text-emerald-800 text-left space-y-1">
				<div class="font-bold flex items-center gap-1.5 text-emerald-900">
					<i class="fa-solid fa-shield-halved"></i> Keamanan Aktif:
				</div>
				<ul class="list-disc list-inside space-y-0.5 text-gray-600">
					<li>Proteksi CSRF aktif di seluruh form aplikasi.</li>
					<li>Password administrator tersimpan dengan enkripsi Bcrypt.</li>
					<li>Jalur <code class="font-mono text-emerald-700">/installer</code> telah terkunci permanen.</li>
				</ul>
			</div>

			<div>
				<a href="<?php echo site_url('login'); ?>" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-3 rounded-xl transition shadow-sm inline-flex items-center justify-center gap-2 text-sm">
					Masuk ke Halaman Login <i class="fa-solid fa-arrow-right"></i>
				</a>
			</div>
		</div>
	</main>

	<footer class="text-center py-4 text-xs text-gray-400 border-t bg-white">
		TahfidzCMS &copy; 2026. Dikembangkan untuk kemudahan monitoring tahfidz santri.
	</footer>

</body>
</html>
