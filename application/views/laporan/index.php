<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Rekapitulasi capaian setoran hafalan per santri dan unduh berkas laporan</p>
	</div>
	<div class="flex items-center gap-3">
		<a href="<?php echo site_url('laporan/export?' . http_build_query(array('kelas_id' => $selected_kelas, 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir))); ?>" 
		   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition shadow-sm">
			<i class="fa-solid fa-file-excel"></i> Export ke Excel (.xlsx)
		</a>
	</div>
</div>

<!-- Filter Periode & Kelas -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
	<form method="get" action="<?php echo site_url('laporan'); ?>" class="flex flex-wrap items-end gap-3">
		<div class="w-full sm:w-56">
			<label class="block text-xs font-medium text-gray-600 mb-1">Kelas</label>
			<select name="kelas_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				<option value="">-- Semua Kelas --</option>
				<?php foreach ($kelas_list as $k): ?>
					<option value="<?php echo $k->id; ?>" <?php echo ($selected_kelas == $k->id) ? 'selected' : ''; ?>>
						Kelas <?php echo htmlspecialchars($k->nama_kelas); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="w-full sm:w-44">
			<label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
			<input type="date" name="tanggal_awal" value="<?php echo htmlspecialchars($tanggal_awal); ?>"
				class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
		</div>

		<div class="w-full sm:w-44">
			<label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
			<input type="date" name="tanggal_akhir" value="<?php echo htmlspecialchars($tanggal_akhir); ?>"
				class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
		</div>

		<div>
			<button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition shadow-sm">
				<i class="fa-solid fa-filter mr-1"></i> Tampilkan
			</button>
		</div>
	</form>
</div>

<!-- Table Rekapitulasi -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-left font-medium">
				<tr>
					<th class="px-5 py-3.5">Santri / Siswa</th>
					<th class="px-5 py-3.5">Kelas</th>
					<th class="px-5 py-3.5 text-center">Total Setoran</th>
					<th class="px-5 py-3.5 text-center">Lancar (L)</th>
					<th class="px-5 py-3.5 text-center">Cukup (CL)</th>
					<th class="px-5 py-3.5 text-center">Kurang (KL)</th>
					<th class="px-5 py-3.5 text-center">Tidak Lancar (TL)</th>
					<th class="px-5 py-3.5 text-center">Setoran Terakhir</th>
					<th class="px-5 py-3.5 text-right">Poin Periode Ini</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100">
				<?php if (empty($rekap_data)): ?>
					<tr>
						<td colspan="9" class="text-center py-10 text-gray-400 italic">Tidak ada rekaman setoran pada periode ini.</td>
					</tr>
				<?php else: ?>
					<?php foreach ($rekap_data as $row): ?>
						<tr class="hover:bg-gray-50/50 transition">
							<td class="px-5 py-4 whitespace-nowrap">
								<div class="font-semibold text-gray-800"><?php echo htmlspecialchars($row->nama_siswa); ?></div>
								<div class="text-xs text-gray-400">NISN: <?php echo htmlspecialchars($row->nisn); ?></div>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-gray-700 font-medium">
								Kelas <?php echo htmlspecialchars($row->nama_kelas); ?>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-center font-bold text-gray-800">
								<?php echo $row->total_setoran; ?>x
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-center">
								<span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
									<?php echo $row->total_lancar; ?>
								</span>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-center">
								<span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
									<?php echo $row->total_cukup_lancar; ?>
								</span>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-center">
								<span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
									<?php echo $row->total_kurang_lancar; ?>
								</span>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-center">
								<span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">
									<?php echo $row->total_tidak_lancar; ?>
								</span>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-center text-xs text-gray-500">
								<?php echo $row->setoran_terakhir ? format_tanggal_id($row->setoran_terakhir) : '-'; ?>
							</td>
							<td class="px-5 py-4 whitespace-nowrap text-right font-bold text-emerald-600 text-base">
								+<?php echo number_format($row->total_poin_periode); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
