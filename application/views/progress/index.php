<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Peta capaian hafalan 30 Juz Al-Qur'an dan status kelulusan juz</p>
	</div>
</div>

<?php if ($role !== 'siswa'): ?>
	<!-- Selector Santri untuk Guru / Admin -->
	<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
		<form method="get" action="<?php echo site_url('progress'); ?>" class="flex flex-wrap items-center gap-3">
			<div class="w-full sm:w-72">
				<label class="block text-xs font-medium text-gray-600 mb-1">Pilih Santri</label>
				<select name="nisn" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					<?php foreach ($siswa_list as $s): ?>
						<option value="<?php echo $s->nisn; ?>" <?php echo ($selected_nisn === $s->nisn) ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($s->nama); ?> (Kelas <?php echo htmlspecialchars($s->nama_kelas); ?>)
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</form>
	</div>
<?php endif; ?>

<?php if ($siswa_aktif): ?>
	<!-- Profile Card Santri & Status Capaian -->
	<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
		<div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
			<div class="flex items-center gap-4">
				<div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl font-bold">
					<i class="fa-solid fa-user-graduate"></i>
				</div>
				<div>
					<h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($siswa_aktif->nama); ?></h2>
					<div class="text-sm text-gray-500">
						NISN: <span class="font-mono text-gray-700"><?php echo $siswa_aktif->nisn; ?></span> &bull; 
						Kelas: <span class="font-medium text-gray-700"><?php echo htmlspecialchars($siswa_aktif->nama_kelas); ?></span>
					</div>
					<div class="mt-1 flex items-center gap-2">
						<span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
							<i class="fa-solid fa-medal mr-1"></i> <?php echo htmlspecialchars($siswa_aktif->badge); ?>
						</span>
						<span class="text-xs text-gray-400">Total Poin: <strong class="text-emerald-600"><?php echo number_format($siswa_aktif->total_poin); ?></strong> pts</span>
					</div>
				</div>
			</div>

			<!-- Target Progress Mini Box -->
			<div class="bg-gray-50 rounded-xl p-4 min-w-[240px] border border-gray-100">
				<div class="flex justify-between items-center text-sm font-semibold mb-1">
					<span class="text-gray-600">Capaian Target</span>
					<?php 
						$target = (int)$siswa_aktif->target_juz ?: 30;
						$pct = round(($total_juz_tuntas / $target) * 100);
						$pct = min(100, $pct);
					?>
					<span class="text-emerald-600 font-bold"><?php echo $total_juz_tuntas; ?> / <?php echo $target; ?> Juz (<?php echo $pct; ?>%)</span>
				</div>
				<div class="w-full bg-gray-200 h-2.5 rounded-full overflow-hidden">
					<div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%"></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Interactive 30 Juz Grid -->
	<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
		<h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
			<i class="fa-solid fa-grip text-emerald-600"></i> Matriks 30 Juz Al-Qur'an
		</h3>

		<div class="grid grid-cols-2 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-10 gap-3">
			<?php for ($j = 1; $j <= 30; $j++): ?>
				<?php 
					$is_tuntas = isset($progress_juz_map[$j]);
					$info = $is_tuntas ? $progress_juz_map[$j] : null;
				?>
				<div class="relative p-3 rounded-xl border text-center transition-all <?php echo $is_tuntas ? 'bg-emerald-50 border-emerald-200 text-emerald-900 shadow-sm' : 'bg-gray-50 border-gray-100 text-gray-400'; ?>">
					<div class="text-xs font-semibold text-gray-400 mb-0.5">JUZ</div>
					<div class="text-xl font-bold <?php echo $is_tuntas ? 'text-emerald-700' : 'text-gray-400'; ?>"><?php echo $j; ?></div>
					
					<div class="mt-2">
						<?php if ($is_tuntas): ?>
							<span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full">
								<i class="fa-solid fa-check"></i> <?php echo $info->total_setoran; ?>x
							</span>
						<?php else: ?>
							<span class="text-[10px] text-gray-400">Belum</span>
						<?php endif; ?>
					</div>
				</div>
			<?php endfor; ?>
		</div>

		<div class="flex items-center gap-4 mt-6 pt-4 border-t text-xs text-gray-500">
			<div class="flex items-center gap-1.5">
				<span class="w-3.5 h-3.5 rounded bg-emerald-500 inline-block"></span> Juz Sudah Disetor & Lulus (Lancar / Cukup Lancar)
			</div>
			<div class="flex items-center gap-1.5">
				<span class="w-3.5 h-3.5 rounded bg-gray-200 inline-block"></span> Juz Belum Disetorkan
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center text-gray-400 italic">
		Tidak ada data santri yang dipilih atau ditemukan.
	</div>
<?php endif; ?>
