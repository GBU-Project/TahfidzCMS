<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
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

	<div class="max-w-3xl mx-auto px-4 py-6 sm:py-10">

		<!-- Header identitas institusi -->
		<div class="flex items-center gap-3 mb-6">
			<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
				<img src="<?php echo htmlspecialchars(base_url($settings['institution_logo'])); ?>" alt="Logo" class="h-9 w-auto max-w-[100px] object-contain rounded">
			<?php endif; ?>
			<div>
				<div class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($settings['institution_name']); ?></div>
				<div class="text-xs text-gray-400">Rapor Perkembangan Hafalan</div>
			</div>
		</div>

		<!-- Kartu identitas siswa (privasi: nama depan + kelas saja) -->
		<div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl p-6 text-white mb-5 shadow-sm">
			<div class="flex items-center justify-between">
				<div>
					<div class="text-emerald-100 text-xs font-medium mb-1">Ananda</div>
					<div class="text-2xl font-bold"><?php echo htmlspecialchars($nama_depan); ?></div>
					<div class="text-emerald-100 text-sm mt-0.5">Kelas <?php echo htmlspecialchars($nama_kelas); ?></div>
				</div>
				<div class="text-right">
					<div class="text-3xl">🏅</div>
					<div class="text-xs font-semibold text-emerald-50 mt-1"><?php echo htmlspecialchars($badge); ?></div>
				</div>
			</div>
			<div class="grid grid-cols-2 gap-3 mt-5 pt-5 border-t border-emerald-500/40">
				<div>
					<div class="text-2xl font-bold"><?php echo $jumlah_juz_selesai; ?> <span class="text-sm font-normal text-emerald-100">/ <?php echo $target_juz; ?> Juz</span></div>
					<div class="text-xs text-emerald-100">Progress Hafalan</div>
				</div>
				<div>
					<div class="text-2xl font-bold"><?php echo number_format($total_poin); ?></div>
					<div class="text-xs text-emerald-100">Total Poin</div>
				</div>
			</div>
		</div>

		<!-- Progress 30 Juz -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
			<h2 class="text-sm font-bold text-gray-800 mb-3">Progress Hafalan per Juz</h2>
			<div class="grid grid-cols-6 sm:grid-cols-10 gap-1.5">
				<?php for ($j = 1; $j <= 30; $j++): ?>
					<?php $selesai = in_array($j, $juz_selesai, TRUE); ?>
					<div class="aspect-square rounded-lg flex items-center justify-center text-[10px] font-bold <?php echo $selesai ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400'; ?>" title="Juz <?php echo $j; ?>">
						<?php echo $j; ?>
					</div>
				<?php endfor; ?>
			</div>
			<div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
				<span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Sudah lancar</span>
				<span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-100 inline-block"></span> Belum</span>
			</div>
		</div>

		<div class="grid sm:grid-cols-2 gap-5 mb-5">
			<!-- Tren skor -->
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
				<h2 class="text-sm font-bold text-gray-800 mb-3">Tren Skor Setoran</h2>
				<?php if (empty($skor_trend)): ?>
					<p class="text-xs text-gray-400 italic py-8 text-center">Belum ada data setoran.</p>
				<?php else: ?>
					<canvas id="chartTrend" height="160"></canvas>
				<?php endif; ?>
			</div>

			<!-- Distribusi keterangan -->
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
				<h2 class="text-sm font-bold text-gray-800 mb-3">Kualitas Bacaan</h2>
				<?php if (array_sum($distribusi) === 0): ?>
					<p class="text-xs text-gray-400 italic py-8 text-center">Belum ada data setoran.</p>
				<?php else: ?>
					<canvas id="chartDistribusi" height="160"></canvas>
				<?php endif; ?>
			</div>
		</div>

		<!-- Riwayat terbaru -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
			<h2 class="text-sm font-bold text-gray-800 mb-3">Setoran Terbaru</h2>
			<?php if (empty($riwayat_recent)): ?>
				<p class="text-xs text-gray-400 italic py-4 text-center">Belum ada riwayat setoran.</p>
			<?php else: ?>
				<div class="divide-y divide-gray-100">
					<?php foreach ($riwayat_recent as $r): ?>
						<?php
							$badge_color = array('L' => 'bg-emerald-50 text-emerald-700', 'CL' => 'bg-blue-50 text-blue-700', 'KL' => 'bg-amber-50 text-amber-700', 'TL' => 'bg-rose-50 text-rose-700');
							$color = isset($badge_color[$r->keterangan]) ? $badge_color[$r->keterangan] : 'bg-gray-50 text-gray-700';
						?>
						<div class="py-3 flex items-center justify-between gap-3">
							<div class="min-w-0">
								<div class="text-sm font-semibold text-gray-800 truncate">
									<?php echo htmlspecialchars($r->surat); ?> <span class="text-gray-400 font-normal">Ayat <?php echo (int) $r->ayat_dari; ?>-<?php echo (int) $r->ayat_sampai; ?></span>
								</div>
								<div class="text-xs text-gray-400">
									<?php echo isset($jenis_setoran_label[$r->jenis_setoran]) ? htmlspecialchars($jenis_setoran_label[$r->jenis_setoran]) : htmlspecialchars($r->jenis_setoran); ?>
									&bull; <?php echo date('d M Y', strtotime($r->tanggal)); ?>
								</div>
							</div>
							<span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-lg <?php echo $color; ?>"><?php echo htmlspecialchars($r->keterangan); ?> &bull; <?php echo (int) $r->skor; ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<p class="text-center text-xs text-gray-400 pb-6">
			Tautan ini bersifat privat, mohon tidak dibagikan ke pihak lain di luar keluarga.
		</p>
	</div>

	<?php if (! empty($skor_trend)): ?>
	<script>
	new Chart(document.getElementById('chartTrend'), {
		type: 'line',
		data: {
			labels: <?php echo json_encode(array_map(function ($r) { return date('d/m', strtotime($r->tanggal)); }, $skor_trend)); ?>,
			datasets: [{
				label: 'Skor',
				data: <?php echo json_encode(array_map(function ($r) { return (int) $r->skor; }, $skor_trend)); ?>,
				borderColor: '#059669',
				backgroundColor: 'rgba(5,150,105,0.1)',
				tension: 0.3,
				fill: true,
				pointRadius: 2,
			}]
		},
		options: {
			responsive: true,
			plugins: { legend: { display: false } },
			scales: { y: { min: 50, max: 100, ticks: { stepSize: 10 } } }
		}
	});
	</script>
	<?php endif; ?>

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
