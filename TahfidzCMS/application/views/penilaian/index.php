<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Review kualitas bacaan, dengarkan rekaman audio bukti, dan perbarui penilaian</p>
	</div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
	<form method="get" action="<?php echo site_url('penilaian'); ?>" class="flex flex-wrap items-center gap-3">
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

		<div class="w-full sm:w-48">
			<select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				<option value="">-- Semua Status --</option>
				<option value="Lancar" <?php echo ($selected_status === 'Lancar') ? 'selected' : ''; ?>>Lancar</option>
				<option value="Cukup" <?php echo ($selected_status === 'Cukup') ? 'selected' : ''; ?>>Cukup</option>
				<option value="Perlu Perbaikan" <?php echo ($selected_status === 'Perlu Perbaikan') ? 'selected' : ''; ?>>Perlu Perbaikan</option>
			</select>
		</div>

		<div class="flex-1 min-w-[200px]">
			<input type="text" name="q" value="<?php echo htmlspecialchars($search ?: ''); ?>"
				placeholder="Cari siswa, NISN, kode..."
				class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
		</div>

		<div class="flex items-center gap-2">
			<button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm px-4 py-2 rounded-lg">
				<i class="fa-solid fa-filter mr-1"></i> Filter
			</button>
			<?php if ($selected_kelas || $selected_status || $search): ?>
				<a href="<?php echo site_url('penilaian'); ?>" class="text-sm text-gray-500 hover:text-gray-700 px-2 py-2">Reset</a>
			<?php endif; ?>
		</div>
	</form>
</div>

<!-- List Penilaian & Audio Review Card/Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-left font-medium">
				<tr>
					<th class="px-4 py-3.5">Kode & Santri</th>
					<th class="px-4 py-3.5">Materi Hafalan</th>
					<th class="px-4 py-3.5">Bukti Audio</th>
					<th class="px-4 py-3.5">Nilai & Status Saat Ini</th>
					<th class="px-4 py-3.5">Catatan Evaluasi</th>
					<th class="px-4 py-3.5 text-right">Aksi</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100">
				<?php if (empty($setoran_list)): ?>
					<tr>
						<td colspan="6" class="px-4 py-12 text-center text-gray-400">
							<i class="fa-solid fa-clipboard-check text-3xl mb-2 block"></i>
							Belum ada data setoran untuk dinilai.
						</td>
					</tr>
				<?php endif; ?>

				<?php foreach ($setoran_list as $s): ?>
					<tr class="hover:bg-gray-50/80 transition-colors">
						<td class="px-4 py-3.5">
							<span class="font-mono font-semibold text-emerald-800 text-xs bg-emerald-50 px-2 py-0.5 rounded">
								<?php echo htmlspecialchars($s->kode_setoran); ?>
							</span>
							<div class="font-medium text-gray-800 mt-1"><?php echo htmlspecialchars($s->nama_siswa); ?></div>
							<div class="text-xs text-gray-400">Kelas <?php echo htmlspecialchars($s->nama_kelas); ?> • <?php echo format_tanggal_id($s->tanggal); ?></div>
						</td>

						<td class="px-4 py-3.5">
							<div class="font-medium text-gray-900">QS. <?php echo htmlspecialchars($s->surat); ?></div>
							<div class="text-xs text-gray-500">Juz <?php echo $s->juz; ?> (Ayat <?php echo $s->ayat_dari; ?> - <?php echo $s->ayat_sampai; ?>)</div>
						</td>

						<td class="px-4 py-3.5">
							<?php if (! empty($s->audio_bukti)): ?>
								<div class="flex flex-col gap-1">
									<audio controls class="h-8 w-48">
										<source src="<?php echo base_url($s->audio_bukti); ?>" type="audio/mpeg">
										Browser tidak mendukung player audio.
									</audio>
									<?php if ($s->durasi_audio): ?>
										<span class="text-[11px] text-gray-400">Durasi: <?php echo format_durasi_audio($s->durasi_audio); ?></span>
									<?php endif; ?>
								</div>
							<?php else: ?>
								<span class="text-xs text-gray-400 italic">Tidak ada rekaman</span>
							<?php endif; ?>
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
							<div class="text-xs font-semibold text-emerald-700">+<?php echo $s->poin; ?> Poin</div>
						</td>

						<td class="px-4 py-3.5">
							<p class="text-xs text-gray-600 max-w-xs <?php echo empty($s->catatan) ? 'italic text-gray-400' : ''; ?>">
								<?php echo ! empty($s->catatan) ? htmlspecialchars($s->catatan) : 'Belum ada catatan'; ?>
							</p>
						</td>

						<td class="px-4 py-3.5 text-right">
							<button type="button"
								onclick="bukaModalEdit(<?php echo htmlspecialchars(json_encode($s)); ?>)"
								class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-1">
								<i class="fa-solid fa-pen-to-square"></i> Koreksi Nilai
							</button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<!-- Modal Koreksi Penilaian -->
<div id="modal-edit-penilaian" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
	<div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
		<div class="flex items-center justify-between border-b pb-3">
			<h3 class="font-bold text-gray-800 text-base">Koreksi Penilaian Setoran</h3>
			<button type="button" onclick="tutupModalEdit()" class="text-gray-400 hover:text-gray-600">
				<i class="fa-solid fa-xmark text-lg"></i>
			</button>
		</div>

		<?php echo form_open('', array('id' => 'form-edit-modal', 'class' => 'space-y-4')); ?>
			<div>
				<span id="modal-info-siswa" class="text-xs text-gray-500 block"></span>
				<span id="modal-info-materi" class="text-sm font-semibold text-gray-800 block"></span>
			</div>

			<div id="modal-audio-container" class="hidden p-3 bg-gray-50 rounded-xl border border-gray-200">
				<p class="text-xs text-gray-500 mb-1">Dengarkan Rekaman Audio:</p>
				<audio id="modal-audio-player" controls class="w-full h-8"></audio>
			</div>

			<div class="grid grid-cols-2 gap-3">
				<div>
					<label class="block text-xs font-medium text-gray-700 mb-1">Nilai Tajwid</label>
					<select name="nilai" id="modal-select-nilai" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="A">A (Mumtaz - 100)</option>
						<option value="B">B (Jayyid - 75)</option>
						<option value="C">C (Maqbul - 50)</option>
					</select>
				</div>

				<div>
					<label class="block text-xs font-medium text-gray-700 mb-1">Kelancaran</label>
					<select name="status" id="modal-select-status" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="Lancar">Lancar (+20)</option>
						<option value="Cukup">Cukup (+10)</option>
						<option value="Perlu Perbaikan">Perlu Perbaikan (+0)</option>
					</select>
				</div>
			</div>

			<div>
				<label class="block text-xs font-medium text-gray-700 mb-1">Catatan Koreksi / Masukan</label>
				<textarea name="catatan" id="modal-catatan" rows="3" placeholder="Tulis masukan untuk santri..."
					class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500"></textarea>
			</div>

			<div class="flex gap-2 pt-2">
				<button type="submit" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-2.5 rounded-xl text-sm">
					Simpan Perubahan
				</button>
				<button type="button" onclick="tutupModalEdit()" class="px-4 py-2.5 border rounded-xl text-sm text-gray-600 hover:bg-gray-50">
					Batal
				</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>

<script>
function bukaModalEdit(data) {
	const modal = document.getElementById('modal-edit-penilaian');
	const form = document.getElementById('form-edit-modal');
	form.action = "<?php echo site_url('penilaian/simpan/'); ?>" + data.id;

	document.getElementById('modal-info-siswa').innerText = data.nama_siswa + " (" + data.nisn + ") • Kelas " + data.nama_kelas;
	document.getElementById('modal-info-materi').innerText = "QS. " + data.surat + " (Juz " + data.juz + ", Ayat " + data.ayat_dari + " - " + data.ayat_sampai + ")";
	document.getElementById('modal-select-nilai').value = data.nilai;
	document.getElementById('modal-select-status').value = data.status;
	document.getElementById('modal-catatan').value = data.catatan || '';

	const audioContainer = document.getElementById('modal-audio-container');
	const audioPlayer = document.getElementById('modal-audio-player');

	if (data.audio_bukti) {
		audioPlayer.src = "<?php echo base_url(); ?>" + data.audio_bukti;
		audioContainer.classList.remove('hidden');
	} else {
		audioPlayer.src = '';
		audioContainer.classList.add('hidden');
	}

	modal.classList.remove('hidden');
}

function tutupModalEdit() {
	const modal = document.getElementById('modal-edit-penilaian');
	const audioPlayer = document.getElementById('modal-audio-player');
	audioPlayer.pause();
	modal.classList.add('hidden');
}
</script>
