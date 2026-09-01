<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Peringkat perolehan poin dan lencana prestasi hafalan santri</p>
	</div>
</div>

<!-- Filter Kelas -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
	<form method="get" action="<?php echo site_url('leaderboard'); ?>" class="flex flex-wrap items-center gap-3">
		<div class="w-full sm:w-64">
			<select name="kelas_id" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				<option value="">-- Peringkat Global (Semua Kelas) --</option>
				<?php foreach ($kelas_list as $k): ?>
					<option value="<?php echo $k->id; ?>" <?php echo ($selected_kelas == $k->id) ? 'selected' : ''; ?>>
						Kelas <?php echo htmlspecialchars($k->nama_kelas); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</form>
</div>

<!-- Top 3 Podium (Jika ada data) -->
<?php if (! empty($leaderboard_list) && count($leaderboard_list) >= 3): ?>
	<?php 
		$top1 = isset($leaderboard_list[0]) ? $leaderboard_list[0] : null;
		$top2 = isset($leaderboard_list[1]) ? $leaderboard_list[1] : null;
		$top3 = isset($leaderboard_list[2]) ? $leaderboard_list[2] : null;
	?>
	<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 items-end">
		<!-- Juara 2 -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center order-2 md:order-1">
			<div class="w-12 h-12 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-xl font-bold mx-auto mb-3">
				2
			</div>
			<div class="font-bold text-gray-800 text-base"><?php echo htmlspecialchars($top2->nama); ?></div>
			<div class="text-xs text-gray-400 mb-2">Kelas <?php echo htmlspecialchars($top2->nama_kelas); ?></div>
			<span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 mb-3">
				<?php echo htmlspecialchars($top2->badge); ?>
			</span>
			<div class="text-xl font-bold text-emerald-600"><?php echo number_format($top2->total_poin); ?> <span class="text-xs font-normal text-gray-400">pts</span></div>
		</div>

		<!-- Juara 1 -->
		<div class="bg-gradient-to-b from-amber-50 to-white rounded-2xl shadow-md border-2 border-amber-300 p-8 text-center order-1 md:order-2 transform md:-translate-y-2">
			<div class="w-16 h-16 rounded-full bg-amber-400 text-white flex items-center justify-center text-2xl font-bold mx-auto mb-3 shadow-md shadow-amber-200">
				<i class="fa-solid fa-crown"></i>
			</div>
			<div class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($top1->nama); ?></div>
			<div class="text-xs text-gray-500 mb-2">Kelas <?php echo htmlspecialchars($top1->nama_kelas); ?></div>
			<span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 mb-3">
				<i class="fa-solid fa-medal mr-1"></i> <?php echo htmlspecialchars($top1->badge); ?>
			</span>
			<div class="text-3xl font-extrabold text-emerald-600"><?php echo number_format($top1->total_poin); ?> <span class="text-xs font-normal text-gray-400">pts</span></div>
		</div>

		<!-- Juara 3 -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center order-3 md:order-3">
			<div class="w-12 h-12 rounded-full bg-amber-700/20 text-amber-900 flex items-center justify-center text-xl font-bold mx-auto mb-3">
				3
			</div>
			<div class="font-bold text-gray-800 text-base"><?php echo htmlspecialchars($top3->nama); ?></div>
			<div class="text-xs text-gray-400 mb-2">Kelas <?php echo htmlspecialchars($top3->nama_kelas); ?></div>
			<span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 mb-3">
				<?php echo htmlspecialchars($top3->badge); ?>
			</span>
			<div class="text-xl font-bold text-emerald-600"><?php echo number_format($top3->total_poin); ?> <span class="text-xs font-normal text-gray-400">pts</span></div>
		</div>
	</div>
<?php endif; ?>

<!-- Tabel Peringkat Keseluruhan -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-left font-medium">
				<tr>
					<th class="px-5 py-3.5 w-16 text-center">Rank</th>
					<th class="px-5 py-3.5">Santri / Siswa</th>
					<th class="px-5 py-3.5">Kelas</th>
					<th class="px-5 py-3.5">Badge Lencana</th>
					<th class="px-5 py-3.5">Target Juz</th>
					<th class="px-5 py-3.5 text-right">Total Poin</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100">
				<?php if (empty($leaderboard_list)): ?>
					<tr>
						<td colspan="6" class="text-center py-10 text-gray-400 italic">Belum ada data nilai atau santri di kelas ini.</td>
					</tr>
				<?php else: ?>
					<?php foreach ($leaderboard_list as $index => $row): ?>
						<tr class="hover:bg-gray-50/50 transition <?php echo ($index < 3) ? 'bg-amber-50/20' : ''; ?>">
							<td class="px-5 py-4 text-center font-bold">
								<?php if ($index === 0): ?>
									<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-white text-xs">🥇</span>
								<?php elseif ($index === 1): ?>
									<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-300 text-slate-800 text-xs">🥈</span>
								<?php elseif ($index === 2): ?>
									<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700/30 text-amber-900 text-xs">🥉</span>
								<?php else: ?>
									<span class="text-gray-500 text-xs font-semibold">#<?php echo $index + 1; ?></span>
								<?php endif; ?>
							</td>
							<td class="px-5 py-4 whitespace-nowrap">
								<div class="font-semibold text-gray-800"><?php echo htmlspecialchars($row->nama); ?></div>
								<div class="text-xs text-gray-400">NISN: <?php echo htmlspecialchars($row->nisn); ?></div>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-gray-700">
								Kelas <?php echo htmlspecialchars($row->nama_kelas); ?>
							</td>
							<td class="px-5 py-4 whitespace-nowrap">
								<span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
									<i class="fa-solid fa-star text-amber-500 mr-1"></i> <?php echo htmlspecialchars($row->badge); ?>
								</span>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-gray-600">
								<?php echo $row->target_juz; ?> Juz
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-right font-bold text-emerald-600 text-base">
								<?php echo number_format($row->total_poin); ?> <span class="text-xs text-gray-400 font-normal">pts</span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
