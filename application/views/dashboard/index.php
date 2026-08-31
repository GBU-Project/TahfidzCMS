<div class="mb-6">
	<h1 class="text-2xl font-bold text-gray-800">Dashboard Utama</h1>
	<p class="text-sm text-gray-500">Selamat datang kembali di sistem monitoring TahfidzCMS</p>
</div>

<?php if ($role === 'siswa'): ?>
	<!-- ======================================================= -->
	<!-- DASHBOARD ROLE SISWA -->
	<!-- ======================================================= -->
	<?php if ($siswa): ?>
		<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
			<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
				<div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold">
					<i class="fa-solid fa-star"></i>
				</div>
				<div>
					<div class="text-xs font-medium text-gray-400">Total Poin Hafalan</div>
					<div class="text-2xl font-bold text-gray-800"><?php echo number_format($siswa->total_poin); ?> <span class="text-xs text-gray-500 font-normal">pts</span></div>
				</div>
			</div>

			<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
				<div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold">
					<i class="fa-solid fa-medal"></i>
				</div>
				<div>
					<div class="text-xs font-medium text-gray-400">Badge Tingkatan</div>
					<div class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($siswa->badge); ?></div>
				</div>
			</div>

			<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
				<div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold">
					<i class="fa-solid fa-book-quran"></i>
				</div>
				<div>
					<div class="text-xs font-medium text-gray-400">Capaian Juz</div>
					<div class="text-2xl font-bold text-gray-800"><?php echo $total_juz_selesai; ?> <span class="text-xs text-gray-500 font-normal">/ <?php echo $siswa->target_juz; ?> Juz</span></div>
				</div>
			</div>

			<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
				<div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl font-bold">
					<i class="fa-solid fa-calendar-check"></i>
				</div>
				<div>
					<div class="text-xs font-medium text-gray-400">Setoran Bulan Ini</div>
					<div class="text-2xl font-bold text-gray-800"><?php echo $setoran_bulan_ini; ?> <span class="text-xs text-gray-500 font-normal">kali</span></div>
				</div>
			</div>
		</div>

		<!-- Progress Bar Card -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
			<div class="flex items-center justify-between mb-2">
				<span class="text-sm font-semibold text-gray-700">Target Hafalan Santri</span>
				<?php 
					$pct = ($siswa->target_juz > 0) ? round(($total_juz_selesai / $siswa->target_juz) * 100) : 0;
					$pct = min(100, $pct);
				?>
				<span class="text-sm font-bold text-emerald-600"><?php echo $pct; ?>% Selesai</span>
			</div>
			<div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
				<div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%"></div>
			</div>
			<p class="text-xs text-gray-400 mt-2">Target: <?php echo $siswa->target_juz; ?> Juz Al-Qur'an. Terus tingkatkan muraja'ah dan setoran hafalanmu!</p>
		</div>

	<?php else: ?>
		<div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl mb-6">
			<i class="fa-solid fa-triangle-exclamation mr-1"></i> Data profil santri Anda belum ditautkan ke NISN terdaftar. Silakan hubungi admin sekolah.
		</div>
	<?php endif; ?>

<?php else: ?>
	<!-- ======================================================= -->
	<!-- DASHBOARD ROLE ADMIN / GURU -->
	<!-- ======================================================= -->
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
			<div>
				<div class="text-xs font-medium text-gray-400">Total Santri Binaan</div>
				<div class="text-2xl font-bold text-gray-800 mt-1"><?php echo number_format($total_siswa); ?></div>
			</div>
			<div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
				<i class="fa-solid fa-users"></i>
			</div>
		</div>

		<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
			<div>
				<div class="text-xs font-medium text-gray-400">Total Setoran Masuk</div>
				<div class="text-2xl font-bold text-gray-800 mt-1"><?php echo number_format($total_setoran); ?></div>
			</div>
			<div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
				<i class="fa-solid fa-book-open-reader"></i>
			</div>
		</div>

		<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
			<div>
				<div class="text-xs font-medium text-gray-400">Setoran Bulan Ini</div>
				<div class="text-2xl font-bold text-gray-800 mt-1"><?php echo number_format($setoran_bulan_ini); ?></div>
			</div>
			<div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
				<i class="fa-solid fa-calendar-day"></i>
			</div>
		</div>

		<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
			<div>
				<div class="text-xs font-medium text-gray-400">Setoran Kualitas Lancar</div>
				<div class="text-2xl font-bold text-emerald-600 mt-1"><?php echo number_format($setoran_lancar); ?></div>
			</div>
			<div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
				<i class="fa-solid fa-circle-check"></i>
			</div>
		</div>
	</div>

	<!-- Section 2 Columns: Top Santri & Quality Overview -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
		<!-- Top Santri Leaderboard Card -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-1">
			<div class="flex items-center justify-between mb-4">
				<h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
					<i class="fa-solid fa-trophy text-amber-500"></i> Top 5 Santri
				</h2>
				<a href="<?php echo site_url('leaderboard'); ?>" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Lihat Semua</a>
			</div>
			<div class="space-y-3">
				<?php if (empty($top_siswa)): ?>
					<p class="text-sm text-gray-400 italic py-4 text-center">Belum ada data nilai setoran.</p>
				<?php else: ?>
					<?php foreach ($top_siswa as $idx => $s): ?>
						<div class="flex items-center justify-between p-3 rounded-xl <?php echo $idx === 0 ? 'bg-amber-50/50 border border-amber-100' : 'bg-gray-50'; ?>">
							<div class="flex items-center gap-3">
								<span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold <?php echo $idx === 0 ? 'bg-amber-500 text-white' : ($idx === 1 ? 'bg-gray-400 text-white' : ($idx === 2 ? 'bg-amber-700 text-white' : 'text-gray-500')); ?>">
									<?php echo $idx + 1; ?>
								</span>
								<div>
									<div class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($s->nama); ?></div>
									<div class="text-xs text-gray-400">Kelas <?php echo htmlspecialchars($s->nama_kelas); ?> &bull; <?php echo htmlspecialchars($s->badge); ?></div>
								</div>
							</div>
							<div class="text-sm font-bold text-emerald-600">
								<?php echo number_format($s->total_poin); ?> <span class="text-[10px] text-gray-400 font-normal">pts</span>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>

		<!-- Quick Status Overview & Shortcuts -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2 flex flex-col justify-between">
			<div>
				<h2 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
					<i class="fa-solid fa-chart-pie text-emerald-600"></i> Distribusi Kelancaran Setoran
				</h2>
				<div class="grid grid-cols-3 gap-3 text-center mb-6">
					<div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
						<div class="text-xs font-medium text-emerald-700 mb-1">Lancar (L)</div>
						<div class="text-xl font-bold text-emerald-800"><?php echo number_format($setoran_lancar); ?></div>
					</div>
					<div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
						<div class="text-xs font-medium text-blue-700 mb-1">Cukup Lancar (CL)</div>
						<div class="text-xl font-bold text-blue-800"><?php echo number_format($setoran_cukup); ?></div>
					</div>
					<div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
						<div class="text-xs font-medium text-amber-700 mb-1">Kurang / Tidak Lancar</div>
						<div class="text-xl font-bold text-amber-800"><?php echo number_format($setoran_perbaikan); ?></div>
					</div>
				</div>
			</div>

			<!-- Action Shortcuts -->
			<div class="border-t pt-4 flex flex-wrap gap-3">
				<a href="<?php echo site_url('setoran/tambah'); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm">
					<i class="fa-solid fa-microphone"></i> Rekam / Input Setoran
				</a>
				<a href="<?php echo site_url('penilaian'); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-sm font-medium transition shadow-sm">
					<i class="fa-solid fa-clipboard-check"></i> Review Penilaian
				</a>
				<a href="<?php echo site_url('laporan'); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-medium transition">
					<i class="fa-solid fa-file-excel text-emerald-600"></i> Rekap Laporan
				</a>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- ======================================================= -->
<!-- TABEL RIWAYAT SETORAN TERAKHIR (SHARED) -->
<!-- ======================================================= -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
	<div class="p-5 border-b border-gray-100 flex items-center justify-between">
		<h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
			<i class="fa-solid fa-clock-rotate-left text-emerald-600"></i> Riwayat Setoran Terkini
		</h2>
		<a href="<?php echo site_url('riwayat'); ?>" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Lihat Semua Riwayat &rarr;</a>
	</div>

	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50/75 border-b border-gray-100 text-gray-500 text-left font-medium">
				<tr>
					<th class="px-5 py-3">Tanggal & Waktu</th>
					<th class="px-5 py-3">Santri</th>
					<th class="px-5 py-3">Surat & Ayat</th>
					<th class="px-5 py-3">Juz</th>
					<th class="px-5 py-3">Jenis & Predikat</th>
					<th class="px-5 py-3">Skor</th>
					<th class="px-5 py-3">Poin</th>
					<th class="px-5 py-3">Pengoreksi</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100">
				<?php if (empty($riwayat_terbaru)): ?>
					<tr>
						<td colspan="8" class="text-center py-8 text-gray-400 italic">Belum ada riwayat setoran hafalan.</td>
					</tr>
				<?php else: ?>
					<?php foreach ($riwayat_terbaru as $row): ?>
						<tr class="hover:bg-gray-50/50 transition">
							<td class="px-5 py-3.5 whitespace-nowrap text-gray-600">
								<div><?php echo format_tanggal_id($row->tanggal); ?></div>
								<div class="text-xs text-gray-400"><?php echo substr($row->waktu, 0, 5); ?> WIB</div>
							</td>
							<td class="px-5 py-3.5 whitespace-nowrap">
								<div class="font-semibold text-gray-800"><?php echo htmlspecialchars($row->nama_siswa); ?></div>
								<div class="text-xs text-gray-400">Kelas <?php echo htmlspecialchars($row->nama_kelas); ?> (<?php echo $row->nisn; ?>)</div>
							</td>
							<td class="px-5 py-3.5 whitespace-nowrap">
								<span class="font-medium text-gray-800"><?php echo htmlspecialchars($row->surat); ?></span>
								<span class="text-xs text-gray-500 block">Ayat <?php echo $row->ayat_dari; ?> - <?php echo $row->ayat_sampai; ?></span>
							</td>
							<td class="px-5 py-3.5 whitespace-nowrap font-medium text-gray-700">Juz <?php echo $row->juz; ?></td>
							<td class="px-5 py-3.5 whitespace-nowrap">
								<?php
									$badge_class = 'bg-emerald-100 text-emerald-700';
									if ($row->keterangan === 'CL') $badge_class = 'bg-blue-100 text-blue-700';
									elseif ($row->keterangan === 'KL') $badge_class = 'bg-amber-100 text-amber-700';
									elseif ($row->keterangan === 'TL') $badge_class = 'bg-red-100 text-red-700';

									$jenis_label = ucfirst($row->jenis_setoran);
									if ($row->jenis_setoran === 'qc') $jenis_label = 'Quality Control';
									elseif ($row->jenis_setoran === 'murojaah') $jenis_label = "Muroja'ah";
								?>
								<span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600 mr-1">
									<?php echo $jenis_label; ?>
								</span>
								<span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo $badge_class; ?>">
									<?php echo $row->keterangan; ?>
								</span>
							</td>
							<td class="px-5 py-3.5 whitespace-nowrap font-bold text-gray-800"><?php echo $row->skor; ?></td>
							<td class="px-5 py-3.5 whitespace-nowrap font-bold text-emerald-600">+<?php echo $row->poin; ?></td>
							<td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500"><?php echo htmlspecialchars($row->nama_guru ?: '-'); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
