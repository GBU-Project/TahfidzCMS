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
						<option value="<?php echo htmlspecialchars($s->nisn); ?>" <?php echo ($selected_nisn === $s->nisn) ? 'selected' : ''; ?>>
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
						NISN: <span class="font-mono text-gray-700"><?php echo htmlspecialchars($siswa_aktif->nisn); ?></span> &bull; 
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
					$murojaah = ($info && isset($info->murojaah_info)) ? $info->murojaah_info : array('status' => 'none', 'label' => 'Belum Disetor', 'color' => 'gray', 'days_ago' => null);
					
					// Warna border, background, dan teks sesuai indikator cerdas
					if ($murojaah['status'] === 'fresh') {
						$card_class = 'bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm';
						$num_class  = 'text-emerald-700';
						$tag_class  = 'text-emerald-700 bg-emerald-100/90 border border-emerald-200';
					} elseif ($murojaah['status'] === 'warning') {
						$card_class = 'bg-amber-50 border-amber-300 text-amber-900 shadow-sm';
						$num_class  = 'text-amber-700';
						$tag_class  = 'text-amber-800 bg-amber-100/90 border border-amber-200';
					} elseif ($murojaah['status'] === 'urgent') {
						$card_class = 'bg-rose-50 border-rose-300 text-rose-900 shadow-sm';
						$num_class  = 'text-rose-700';
						$tag_class  = 'text-rose-800 bg-rose-100/90 border border-rose-200';
					} else {
						$card_class = 'bg-gray-50 border-gray-100 text-gray-400';
						$num_class  = 'text-gray-400';
						$tag_class  = 'text-gray-400';
					}
				?>
				<div class="relative p-3 rounded-xl border text-center transition-all <?php echo $card_class; ?>" title="<?php echo $is_tuntas ? "Terakhir: {$info->tanggal_terakhir} ({$murojaah['label']})" : 'Belum pernah disetor'; ?>">
					<div class="text-[10px] font-semibold text-gray-400 mb-0.5">JUZ</div>
					<div class="text-xl font-bold <?php echo $num_class; ?>"><?php echo $j; ?></div>
					
					<div class="mt-2">
						<?php if ($is_tuntas): ?>
							<span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo $tag_class; ?>">
								<?php if ($murojaah['status'] === 'fresh'): ?>
									<i class="fa-solid fa-circle-check text-[9px]"></i>
								<?php elseif ($murojaah['status'] === 'warning'): ?>
									<i class="fa-solid fa-clock-rotate-left text-[9px]"></i>
								<?php else: ?>
									<i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
								<?php endif; ?>
								<?php echo $info->total_setoran; ?>x
							</span>
							<?php if ($murojaah['days_ago'] !== null): ?>
								<div class="text-[9px] text-gray-500 mt-1"><?php echo $murojaah['days_ago']; ?> hr lalu</div>
							<?php endif; ?>
						<?php else: ?>
							<span class="text-[10px] text-gray-400">Belum</span>
						<?php endif; ?>
					</div>
				</div>
			<?php endfor; ?>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-6 pt-4 border-t text-xs text-gray-600">
			<div class="flex items-center gap-2">
				<span class="w-3.5 h-3.5 rounded-full bg-emerald-500 border border-emerald-600 inline-block flex-shrink-0"></span>
				<span><strong>Fresh (≤14 hari):</strong> Baru disetor/diulang</span>
			</div>
			<div class="flex items-center gap-2">
				<span class="w-3.5 h-3.5 rounded-full bg-amber-400 border border-amber-500 inline-block flex-shrink-0"></span>
				<span><strong>Perlu Muroja'ah (15–30 hari):</strong> Perlu diulang</span>
			</div>
			<div class="flex items-center gap-2">
				<span class="w-3.5 h-3.5 rounded-full bg-rose-500 border border-rose-600 inline-block flex-shrink-0"></span>
				<span><strong>Urgent (>30 hari):</strong> Wajib segera tasmi'/muroja'ah</span>
			</div>
			<div class="flex items-center gap-2">
				<span class="w-3.5 h-3.5 rounded-full bg-gray-300 border border-gray-400 inline-block flex-shrink-0"></span>
				<span><strong>Belum Pernah:</strong> Belum disetor</span>
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center text-gray-400 italic">
		Tidak ada data santri yang dipilih atau ditemukan.
	</div>
<?php endif; ?>
