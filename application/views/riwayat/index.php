<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Histori dan rekaman lengkap setoran hafalan santri</p>
	</div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
	<form method="get" action="<?php echo site_url('riwayat'); ?>" class="space-y-4">
		<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
			<?php if ($role !== 'siswa'): ?>
				<div>
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

				<div>
					<label class="block text-xs font-medium text-gray-600 mb-1">Santri</label>
					<select name="nisn" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="">-- Semua Santri --</option>
						<?php foreach ($siswa_list as $s): ?>
							<option value="<?php echo htmlspecialchars($s->nisn); ?>" <?php echo ($selected_nisn === $s->nisn) ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($s->nama); ?> (<?php echo htmlspecialchars($s->nisn); ?>)
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Predikat Kelancaran</label>
				<select name="keterangan" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					<option value="">-- Semua Predikat --</option>
					<option value="L" <?php echo ($selected_keterangan === 'L') ? 'selected' : ''; ?>>L (Lancar)</option>
					<option value="CL" <?php echo ($selected_keterangan === 'CL') ? 'selected' : ''; ?>>CL (Cukup Lancar)</option>
					<option value="KL" <?php echo ($selected_keterangan === 'KL') ? 'selected' : ''; ?>>KL (Kurang Lancar)</option>
					<option value="TL" <?php echo ($selected_keterangan === 'TL') ? 'selected' : ''; ?>>TL (Tidak Lancar)</option>
				</select>
			</div>

			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Pencarian Cepat</label>
				<input type="text" name="q" value="<?php echo htmlspecialchars($search ?: ''); ?>"
					placeholder="Cari surat, juz, kode..."
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>

			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
				<input type="date" name="tanggal_awal" value="<?php echo htmlspecialchars($tanggal_awal ?: ''); ?>"
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>

			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
				<input type="date" name="tanggal_akhir" value="<?php echo htmlspecialchars($tanggal_akhir ?: ''); ?>"
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>
		</div>

		<div class="flex items-center gap-2 pt-2">
			<button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition shadow-sm">
				<i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
			</button>
			<?php if ($selected_kelas || $selected_nisn || $selected_keterangan || $tanggal_awal || $tanggal_akhir || $search): ?>
				<a href="<?php echo site_url('riwayat'); ?>" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">Reset Filter</a>
			<?php endif; ?>
		</div>
	</form>
</div>

<!-- Table Data -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-left font-medium">
				<tr>
					<th class="px-5 py-3.5">Kode & Tanggal</th>
					<?php if ($role !== 'siswa'): ?>
						<th class="px-5 py-3.5">Santri</th>
					<?php endif; ?>
					<th class="px-5 py-3.5">Materi Hafalan</th>
					<th class="px-5 py-3.5">Jenis & Predikat</th>
					<th class="px-5 py-3.5">Poin</th>
					<th class="px-5 py-3.5">Audio Bukti</th>
					<th class="px-5 py-3.5">Catatan & Guru</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100">
				<?php if (empty($setoran_list)): ?>
					<tr>
						<td colspan="7" class="text-center py-10 text-gray-400 italic">Tidak ada rekaman data setoran yang cocok dengan filter.</td>
					</tr>
				<?php else: ?>
					<?php foreach ($setoran_list as $row): ?>
						<tr class="hover:bg-gray-50/50 transition">
							<td class="px-5 py-4 whitespace-nowrap">
								<span class="inline-block font-mono text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-700 mb-1">
									<?php echo $row->kode_setoran; ?>
								</span>
								<div class="text-xs text-gray-600"><?php echo format_tanggal_id($row->tanggal); ?></div>
								<div class="text-[11px] text-gray-400"><?php echo substr($row->waktu, 0, 5); ?> WIB</div>
							</td>
							<?php if ($role !== 'siswa'): ?>
								<td class="px-5 py-4 whitespace-nowrap">
									<div class="font-semibold text-gray-800"><?php echo htmlspecialchars($row->nama_siswa); ?></div>
									<div class="text-xs text-gray-400">Kelas <?php echo htmlspecialchars($row->nama_kelas); ?> (<?php echo htmlspecialchars($row->nisn); ?>)</div>
								</td>
							<?php endif; ?>
							<td class="px-5 py-4 whitespace-nowrap">
								<div class="font-semibold text-gray-800"><?php echo htmlspecialchars($row->surat); ?></div>
								<div class="text-xs text-gray-500">Ayat <?php echo $row->ayat_dari; ?> - <?php echo $row->ayat_sampai; ?> &bull; <span class="font-medium text-emerald-700">Juz <?php echo $row->juz; ?></span></div>
							</td>
							<td class="px-5 py-4 whitespace-nowrap">
								<?php
									$badge_class = 'bg-emerald-100 text-emerald-700';
									if ($row->keterangan === 'CL') $badge_class = 'bg-blue-100 text-blue-700';
									elseif ($row->keterangan === 'KL') $badge_class = 'bg-amber-100 text-amber-700';
									elseif ($row->keterangan === 'TL') $badge_class = 'bg-red-100 text-red-700';

									$jenis_label = ucfirst($row->jenis_setoran);
									if ($row->jenis_setoran === 'qc') $jenis_label = 'Quality Control';
									elseif ($row->jenis_setoran === 'murojaah') $jenis_label = "Muroja'ah";
								?>
								<div class="flex items-center gap-1.5 flex-wrap">
									<span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600">
										<?php echo $jenis_label; ?>
									</span>
									<span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo $badge_class; ?>">
										<?php echo $row->keterangan; ?> (Skor: <?php echo $row->skor; ?>)
									</span>
									<?php if ($row->jenis_setoran === 'qc' && ! empty($row->hasil_qc)): ?>
										<span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold <?php echo $row->hasil_qc === 'layak_tasmi' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'; ?>">
											<?php echo $row->hasil_qc === 'layak_tasmi' ? "Layak Tasmi'" : "Belum Layak"; ?>
										</span>
									<?php endif; ?>
								</div>
								<div class="text-[11px] text-gray-400 mt-1">
									Salah: <?php echo $row->jumlah_kesalahan; ?> &bull; Bacaan: <?php echo $row->kualitas_bacaan === 'baik' ? 'Baik' : 'Kurang Baik'; ?>
								</div>
							</td>
							<td class="px-5 py-4 whitespace-nowrap">
								<span class="font-bold text-emerald-600 text-base">+<?php echo $row->poin; ?></span>
							</td>
							<td class="px-5 py-4 whitespace-nowrap">
								<?php if (! empty($row->audio_bukti)): ?>
									<audio controls preload="none" class="h-8 max-w-[200px]">
										<source src="<?php echo base_url($row->audio_bukti); ?>">
										Browser Anda tidak mendukung pemutar audio.
									</audio>
									<?php if (! empty($row->durasi_audio)): ?>
										<div class="text-[11px] text-gray-400 mt-1"><i class="fa-solid fa-clock mr-1"></i><?php echo format_durasi_audio($row->durasi_audio); ?></div>
									<?php endif; ?>
								<?php else: ?>
									<span class="text-xs text-gray-400 italic">Tanpa audio</span>
								<?php endif; ?>
							</td>
							<td class="px-5 py-4 text-xs text-gray-600">
								<?php if (! empty($row->catatan)): ?>
									<div class="bg-gray-50 p-2 rounded-lg border border-gray-100 mb-1 max-w-xs"><?php echo nl2br(htmlspecialchars($row->catatan)); ?></div>
								<?php endif; ?>
								<div class="text-[11px] text-gray-400">Guru: <?php echo htmlspecialchars($row->nama_guru ?: '-'); ?></div>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<?php if (! empty($pagination)): ?>
		<div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
			<div class="text-xs text-gray-500">
				Menampilkan total <strong class="text-gray-800"><?php echo $total_rows; ?></strong> data riwayat setoran
			</div>
			<div>
				<?php echo $pagination; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
