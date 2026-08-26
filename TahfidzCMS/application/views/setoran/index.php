<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Pencatatan dan pemantauan data setoran hafalan siswa</p>
	</div>
	<a href="<?php echo site_url('setoran/tambah'); ?>"
		class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-all">
		<i class="fa-solid fa-plus"></i> Input Setoran Baru
	</a>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
	<form method="get" action="<?php echo site_url('setoran'); ?>" class="flex flex-wrap items-center gap-3">
		<div class="w-full sm:w-48">
			<select name="kelas_id" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				<option value="">-- Semua Kelas --</option>
				<?php foreach ($kelas_list as $k): ?>
					<option value="<?php echo $k->id; ?>" <?php echo ($selected_kelas == $k->id) ? 'selected' : ''; ?>>
						Kelas <?php echo htmlspecialchars($k->nama_kelas); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="flex-1 min-w-[200px]">
			<input type="text" name="q" value="<?php echo htmlspecialchars($search ?: ''); ?>"
				placeholder="Cari nama siswa, NISN, surat..."
				class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
		</div>

		<div class="flex items-center gap-2">
			<button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm px-4 py-2 rounded-lg">
				<i class="fa-solid fa-filter mr-1"></i> Filter
			</button>
			<?php if ($selected_kelas || $search): ?>
				<a href="<?php echo site_url('setoran'); ?>" class="text-sm text-gray-500 hover:text-gray-700 px-2 py-2">
					Reset
				</a>
			<?php endif; ?>
		</div>
	</form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-left font-medium">
				<tr>
					<th class="px-4 py-3.5">Kode & Tanggal</th>
					<th class="px-4 py-3.5">Siswa & Kelas</th>
					<th class="px-4 py-3.5">Hafalan</th>
					<th class="px-4 py-3.5">Nilai & Status</th>
					<th class="px-4 py-3.5">Poin</th>
					<th class="px-4 py-3.5">Audio Bukti</th>
					<th class="px-4 py-3.5 text-right">Aksi</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100">
				<?php if (empty($setoran_list)): ?>
					<tr>
						<td colspan="7" class="px-4 py-12 text-center text-gray-400">
							<i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
							Belum ada data setoran hafalan yang tercatat.
						</td>
					</tr>
				<?php endif; ?>

				<?php foreach ($setoran_list as $s): ?>
					<tr class="hover:bg-gray-50/80 transition-colors">
						<td class="px-4 py-3.5">
							<span class="font-semibold text-gray-900 block"><?php echo htmlspecialchars($s->kode_setoran); ?></span>
							<span class="text-xs text-gray-400">
								<?php echo format_tanggal_id($s->tanggal); ?> • <?php echo substr($s->waktu, 0, 5); ?>
							</span>
						</td>
						<td class="px-4 py-3.5">
							<div class="font-medium text-gray-800"><?php echo htmlspecialchars($s->nama_siswa); ?></div>
							<div class="text-xs text-gray-400">NISN: <?php echo htmlspecialchars($s->nisn); ?> • Kelas <?php echo htmlspecialchars($s->nama_kelas); ?></div>
						</td>
						<td class="px-4 py-3.5">
							<div class="font-medium text-emerald-800">QS. <?php echo htmlspecialchars($s->surat); ?></div>
							<div class="text-xs text-gray-500">Juz <?php echo $s->juz; ?> : Ayat <?php echo $s->ayat_dari; ?> - <?php echo $s->ayat_sampai; ?></div>
						</td>
						<td class="px-4 py-3.5">
							<div class="flex items-center gap-1.5 mb-1">
								<span class="px-2 py-0.5 rounded text-xs font-bold <?php echo $s->nilai === 'A' ? 'bg-emerald-100 text-emerald-800' : ($s->nilai === 'B' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'); ?>">
									Nilai <?php echo $s->nilai; ?>
								</span>
								<span class="px-2 py-0.5 rounded text-xs <?php echo $s->status === 'Lancar' ? 'bg-green-50 text-green-700' : ($s->status === 'Cukup' ? 'bg-yellow-50 text-yellow-700' : 'bg-rose-50 text-rose-700'); ?>">
									<?php echo $s->status; ?>
								</span>
							</div>
							<?php if (! empty($s->catatan)): ?>
								<div class="text-xs text-gray-400 italic truncate max-w-xs" title="<?php echo htmlspecialchars($s->catatan); ?>">
									"<?php echo htmlspecialchars($s->catatan); ?>"
								</div>
							<?php endif; ?>
						</td>
						<td class="px-4 py-3.5 font-bold text-emerald-700">
							+<?php echo $s->poin; ?>
						</td>
						<td class="px-4 py-3.5">
							<?php if (! empty($s->audio_bukti)): ?>
								<div class="flex items-center gap-2">
									<audio controls class="h-8 w-44">
										<source src="<?php echo base_url($s->audio_bukti); ?>" type="audio/mpeg">
										Browser Anda tidak mendukung audio player.
									</audio>
									<?php if ($s->durasi_audio): ?>
										<span class="text-xs text-gray-400"><?php echo format_durasi_audio($s->durasi_audio); ?></span>
									<?php endif; ?>
								</div>
							<?php else: ?>
								<span class="text-xs text-gray-400 italic">Tanpa audio</span>
							<?php endif; ?>
						</td>
						<td class="px-4 py-3.5 text-right">
							<a href="<?php echo site_url('setoran/hapus/' . $s->id); ?>"
								onclick="return confirm('Apakah Anda yakin ingin menghapus data setoran <?php echo htmlspecialchars(addslashes($s->kode_setoran)); ?>? Poin siswa akan dikurangi.');"
								class="text-red-500 hover:text-red-700 p-1.5 rounded hover:bg-red-50 text-xs font-medium">
								<i class="fa-solid fa-trash mr-1"></i> Hapus
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
