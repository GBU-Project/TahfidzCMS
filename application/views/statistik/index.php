<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Statistik perkembangan hafalan santri <?php echo htmlspecialchars($settings['institution_name']); ?>">
	<title><?php echo htmlspecialchars($title); ?></title>

	<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
		<link rel="icon" type="image/png" href="<?php echo htmlspecialchars(base_url($settings['institution_logo'])); ?>">
	<?php else: ?>
		<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📖</text></svg>">
	<?php endif; ?>

	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

	<div class="max-w-4xl mx-auto px-4 py-6 sm:py-10">

		<!-- Header -->
		<div class="flex items-center justify-between mb-6">
			<div class="flex items-center gap-3">
				<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
					<img src="<?php echo htmlspecialchars(base_url($settings['institution_logo'])); ?>" alt="Logo" class="h-10 w-auto max-w-[110px] object-contain rounded">
				<?php endif; ?>
				<div>
					<div class="text-base font-bold text-gray-800"><?php echo htmlspecialchars($settings['institution_name']); ?></div>
					<div class="text-xs text-gray-400">Statistik Perkembangan Santri</div>
				</div>
			</div>
			<a href="<?php echo site_url(); ?>" class="text-xs text-emerald-700 hover:text-emerald-800 font-medium hidden sm:inline-flex items-center gap-1">
				<i class="fa-solid fa-arrow-left"></i> Beranda
			</a>
		</div>

		<p class="text-sm text-gray-500 mb-6">
			Halaman ini menyajikan gambaran umum perkembangan hafalan seluruh santri secara agregat,
			untuk jamaah dan masyarakat yang ingin mengikuti perkembangan program tahfidz kami.
			Data individual santri tidak ditampilkan di sini demi menjaga privasi.
		</p>

		<!-- Ringkasan angka -->
		<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
				<div class="text-2xl font-bold text-emerald-700"><?php echo number_format($total_santri); ?></div>
				<div class="text-xs text-gray-500 mt-1">Santri Aktif</div>
			</div>
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
				<div class="text-2xl font-bold text-emerald-700"><?php echo number_format($total_kelas); ?></div>
				<div class="text-xs text-gray-500 mt-1">Kelas/Halaqah</div>
			</div>
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
				<div class="text-2xl font-bold text-emerald-700"><?php echo number_format($ringkasan['bulan_ini']); ?></div>
				<div class="text-xs text-gray-500 mt-1">Setoran Bulan Ini</div>
			</div>
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
				<div class="text-2xl font-bold text-emerald-700"><?php echo number_format($ringkasan['total']); ?></div>
				<div class="text-xs text-gray-500 mt-1">Total Setoran</div>
			</div>
		</div>

		<div class="grid sm:grid-cols-2 gap-5 mb-8">
			<!-- Tren bulanan -->
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
				<h2 class="text-sm font-bold text-gray-800 mb-3">Tren Setoran 6 Bulan Terakhir</h2>
				<canvas id="chartTren" height="200"></canvas>
			</div>

			<!-- Distribusi kualitas -->
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
				<h2 class="text-sm font-bold text-gray-800 mb-3">Distribusi Kualitas Bacaan Se-Sekolah</h2>
				<?php if (array_sum($distribusi) === 0): ?>
					<p class="text-xs text-gray-400 italic py-8 text-center">Belum ada data setoran.</p>
				<?php else: ?>
					<canvas id="chartDistribusi" height="200"></canvas>
				<?php endif; ?>
			</div>
		</div>

		<p class="text-center text-xs text-gray-400 pb-6">
			Untuk melihat perkembangan hafalan putra/putri Anda secara individual, silakan hubungi
			wali kelas/ustadz pembimbing untuk mendapatkan tautan Rapor Digital pribadi.
		</p>
	</div>

	<script>
	new Chart(document.getElementById('chartTren'), {
		type: 'bar',
		data: {
			labels: <?php echo json_encode(array_column($tren_bulanan, 'label')); ?>,
			datasets: [{
				label: 'Jumlah Setoran',
				data: <?php echo json_encode(array_map(function ($r) { return $r['jumlah']; }, $tren_bulanan)); ?>,
				backgroundColor: '#10b981',
				borderRadius: 6,
			}]
		},
		options: {
			responsive: true,
			plugins: { legend: { display: false } },
			scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
		}
	});
	</script>

	<?php if (array_sum($distribusi) > 0): ?>
	<script>
	new Chart(document.getElementById('chartDistribusi'), {
		type: 'doughnut',
		data: {
			labels: ['Lancar', 'Cukup Lancar', 'Kurang Lancar', 'Tidak Lancar'],
			datasets: [{
				data: [<?php echo (int) $distribusi['L']; ?>, <?php echo (int) $distribusi['CL']; ?>, <?php echo (int) $distribusi['KL']; ?>, <?php echo (int) $distribusi['TL']; ?>],
				backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#f43f5e'],
				borderWidth: 0,
			}]
		},
		options: {
			responsive: true,
			plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
		}
	});
	</script>
	<?php endif; ?>
</body>
</html>
