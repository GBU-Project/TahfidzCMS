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
			<select name="keterangan" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				<option value="">-- Semua Predikat --</option>
				<option value="L" <?php echo ($selected_keterangan === 'L') ? 'selected' : ''; ?>>L (Lancar)</option>
				<option value="CL" <?php echo ($selected_keterangan === 'CL') ? 'selected' : ''; ?>>CL (Cukup Lancar)</option>
				<option value="KL" <?php echo ($selected_keterangan === 'KL') ? 'selected' : ''; ?>>KL (Kurang Lancar)</option>
				<option value="TL" <?php echo ($selected_keterangan === 'TL') ? 'selected' : ''; ?>>TL (Tidak Lancar)</option>
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
			<?php if ($selected_kelas || $selected_keterangan || $search): ?>
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
					<th class="px-4 py-3.5">Hasil Penilaian</th>
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
									<audio controls preload="none" class="h-8 w-48">
										<source src="<?php echo base_url($s->audio_bukti); ?>">
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
							<div class="flex items-center gap-1.5 mb-1 flex-wrap">
								<?php
									$badge_bg = 'bg-emerald-100 text-emerald-800';
									if ($s->keterangan === 'CL') $badge_bg = 'bg-blue-100 text-blue-800';
									elseif ($s->keterangan === 'KL') $badge_bg = 'bg-amber-100 text-amber-800';
									elseif ($s->keterangan === 'TL') $badge_bg = 'bg-rose-100 text-rose-800';

									$jenis_label = ucfirst($s->jenis_setoran);
									if ($s->jenis_setoran === 'qc') $jenis_label = 'Quality Control';
									elseif ($s->jenis_setoran === 'murojaah') $jenis_label = "Muroja'ah";
								?>
								<span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
									<?php echo $jenis_label; ?>
								</span>
								<span class="px-2 py-0.5 rounded text-xs font-bold <?php echo $badge_bg; ?>">
									<?php echo $s->keterangan; ?> (Skor: <?php echo $s->skor; ?>)
								</span>
								<?php if ($s->jenis_setoran === 'qc' && ! empty($s->hasil_qc)): ?>
									<span class="px-2 py-0.5 rounded text-[11px] font-semibold <?php echo $s->hasil_qc === 'layak_tasmi' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
										<?php echo $s->hasil_qc === 'layak_tasmi' ? "Layak Tasmi'" : "Belum Layak"; ?>
									</span>
								<?php endif; ?>
							</div>
							<div class="text-[11px] text-gray-400">
								Salah: <?php echo $s->jumlah_kesalahan; ?> &bull; Bacaan: <?php echo $s->kualitas_bacaan === 'baik' ? 'Baik' : 'Kurang Baik'; ?>
							</div>
							<div class="text-xs font-semibold text-emerald-700 mt-0.5">+<?php echo $s->poin; ?> Poin</div>
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
	<div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
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

			<div class="grid grid-cols-1 md:grid-cols-3 gap-3">
				<div>
					<label class="block text-xs font-medium text-gray-700 mb-1">Jenis Setoran</label>
					<select name="jenis_setoran" id="modal-select-jenis" onchange="modalHitungLive()" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="ziyadah">Ziyadah</option>
						<option value="murojaah">Muroja'ah</option>
						<option value="qc">Quality Control</option>
					</select>
				</div>

				<div>
					<label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Kesalahan</label>
					<input type="number" name="jumlah_kesalahan" id="modal-input-kesalahan" min="0" oninput="modalHitungLive()"
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>

				<div>
					<label class="block text-xs font-medium text-gray-700 mb-1">Kualitas Bacaan</label>
					<select name="kualitas_bacaan" id="modal-select-kualitas" onchange="modalHitungLive()" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="baik">Baik</option>
						<option value="kurang_baik">Kurang Baik</option>
					</select>
				</div>

				<div id="modal-qc-wrapper" class="hidden md:col-span-3">
					<label class="block text-xs font-medium text-blue-900 mb-1">Hasil Quality Control (QC)</label>
					<select name="hasil_qc" id="modal-select-hasil-qc" class="w-full bg-blue-50/50 border border-blue-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
						<option value="layak_tasmi">Layak Tasmi'</option>
						<option value="belum_layak">Belum Layak Tasmi' / Mengulang</option>
					</select>
				</div>

				<!-- Live Preview in Modal -->
				<div class="md:col-span-3">
					<div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 text-xs flex items-center justify-between">
						<div>
							<span>Predikat: <strong id="modal-preview-keterangan">L (Lancar)</strong></span>
						</div>
						<div>
							<span>Skor & Poin: <strong id="modal-preview-skor" class="text-emerald-700 text-sm">100 Poin</strong></span>
						</div>
					</div>
				</div>

				<!-- Collapsible Cheat-Sheet di Modal Penilaian -->
				<div class="md:col-span-3">
					<div class="border border-amber-200 bg-amber-50/50 rounded-xl overflow-hidden text-xs text-gray-700">
						<button type="button" onclick="toggleModalPanduanRubrik()" class="w-full px-3 py-2 bg-amber-100/60 hover:bg-amber-100 font-semibold text-amber-900 flex items-center justify-between text-left transition text-[11px]">
							<span class="flex items-center gap-1.5">
								<i class="fa-solid fa-circle-info text-amber-600"></i> Panduan Rubrik & Standar Penilaian
							</span>
							<span id="modal-icon-toggle-panduan" class="text-amber-700">
								<i class="fa-solid fa-chevron-down"></i>
							</span>
						</button>

						<div id="modal-content-panduan-rubrik" class="hidden p-3 space-y-3 border-t border-amber-200 text-[11px]">
							<div>
								<div class="font-bold text-gray-800 mb-1 flex items-center gap-1">
									<i class="fa-solid fa-ruler-combined text-emerald-600"></i> Ambang Batas Kesalahan (<span id="modal-panduan-judul-jenis" class="text-emerald-700">Ziyadah</span>)
								</div>
								<div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5" id="modal-panduan-grid-threshold">
									<!-- Diisi via JS -->
								</div>
							</div>

							<div class="bg-white p-2.5 rounded-lg border border-amber-200/80 space-y-1 text-[10.5px] text-gray-600">
								<p>• <strong>Kriteria Salah:</strong> Lupa lanjutan ayat, salah kata, salah harokat, atau hukum tajwid.</p>
								<p id="modal-panduan-toleransi-isyarat">• <strong>Toleransi Isyarat:</strong> Maks. 3x diingatkan (QC maks. 2x). Jika lebih dihitung salah.</p>
							</div>
						</div>
					</div>
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
const MODAL_THRESHOLDS = {
	ziyadah:  [{ max: 0, kode: 'L' }, { max: 1, kode: 'CL' }, { max: 2, kode: 'KL' }, { max: null, kode: 'TL' }],
	murojaah: [{ max: 0, kode: 'L' }, { max: 5, kode: 'CL' }, { max: 10, kode: 'KL' }, { max: null, kode: 'TL' }],
	qc:       [{ max: 0, kode: 'L' }, { max: 1, kode: 'CL' }, { max: 2, kode: 'KL' }, { max: null, kode: 'TL' }]
};

const MODAL_RECS = {
	ziyadah: {
		'L':  { title: 'Lancar (L)', desc: '0 salah/hlm', action: 'Lanjut Hafalan', border: 'border-emerald-300 bg-emerald-50' },
		'CL': { title: 'Cukup Lancar (CL)', desc: '1 salah/hlm', action: 'Lanjut Hafalan', border: 'border-blue-300 bg-blue-50' },
		'KL': { title: 'Kurang Lancar (KL)', desc: '2 salah/hlm', action: 'Mengulang/Catatan', border: 'border-amber-300 bg-amber-50' },
		'TL': { title: 'Tidak Lancar (TL)', desc: '≥ 3 salah/hlm', action: 'Mengulang Hafalan', border: 'border-rose-300 bg-rose-50' }
	},
	murojaah: {
		'L':  { title: 'Lancar (L)', desc: '0 salah/juz', action: 'Lanjut Hafalan', border: 'border-emerald-300 bg-emerald-50' },
		'CL': { title: 'Cukup Lancar (CL)', desc: '1-5 salah/juz', action: 'Lanjut Hafalan', border: 'border-blue-300 bg-blue-50' },
		'KL': { title: 'Kurang Lancar (KL)', desc: '6-10 salah/juz', action: 'Mengulang/Catatan', border: 'border-amber-300 bg-amber-50' },
		'TL': { title: 'Tidak Lancar (TL)', desc: '≥ 11 salah/juz', action: 'Mengulang Hafalan', border: 'border-rose-300 bg-rose-50' }
	},
	qc: {
		'L':  { title: 'Lancar (L)', desc: '0 salah/2 hlm', action: 'Lanjut Hafalan', border: 'border-emerald-300 bg-emerald-50' },
		'CL': { title: 'Cukup Lancar (CL)', desc: '1 salah/2 hlm', action: 'Lanjut Hafalan', border: 'border-blue-300 bg-blue-50' },
		'KL': { title: 'Kurang Lancar (KL)', desc: '2 salah/2 hlm', action: 'Mengulang/Catatan', border: 'border-amber-300 bg-amber-50' },
		'TL': { title: 'Tidak Lancar (TL)', desc: '≥ 3 salah/2 hlm', action: 'Mengulang Hafalan', border: 'border-rose-300 bg-rose-50' }
	}
};

function toggleModalPanduanRubrik() {
	const content = document.getElementById('modal-content-panduan-rubrik');
	const icon = document.getElementById('modal-icon-toggle-panduan');
	if (content.classList.contains('hidden')) {
		content.classList.remove('hidden');
		icon.innerHTML = '<i class="fa-solid fa-chevron-up"></i>';
	} else {
		content.classList.add('hidden');
		icon.innerHTML = '<i class="fa-solid fa-chevron-down"></i>';
	}
}

function updateModalPanduanContent(jenis) {
	const jenisNamaMap = {
		ziyadah: 'Ziyadah (per Halaman)',
		murojaah: "Muroja'ah (per Juz)",
		qc: 'Quality Control (per 2 Halaman)'
	};
	document.getElementById('modal-panduan-judul-jenis').innerText = jenisNamaMap[jenis] || 'Ziyadah';

	const grid = document.getElementById('modal-panduan-grid-threshold');
	const recs = MODAL_RECS[jenis] || MODAL_RECS['ziyadah'];
	
	let html = '';
	for (const key in recs) {
		const item = recs[key];
		html += `<div class="p-2 rounded border ${item.border}">
			<div class="font-bold text-[10.5px] text-gray-800">${item.title}</div>
			<div class="text-[9.5px] text-gray-600">${item.desc} ➔ <strong class="text-emerald-800">${item.action}</strong></div>
		</div>`;
	}
	grid.innerHTML = html;

	const toleransiEl = document.getElementById('modal-panduan-toleransi-isyarat');
	if (jenis === 'qc') {
		toleransiEl.innerHTML = '• <strong>Toleransi Isyarat:</strong> Maks. 2x diingatkan (QC). Jika lebih dihitung salah.';
	} else {
		toleransiEl.innerHTML = '• <strong>Toleransi Isyarat:</strong> Maks. 3x diingatkan (Ziyadah/Murojaah). Jika lebih dihitung salah.';
	}
}

const MODAL_KETERANGAN_LABEL = {
	'L':  'L (Lancar)',
	'CL': 'CL (Cukup Lancar)',
	'KL': 'KL (Kurang Lancar)',
	'TL': 'TL (Tidak Lancar)'
};

const MODAL_SKOR_MATRIX = {
	'L':  { baik: 100, kurang_baik: 95 },
	'CL': { baik: 90,  kurang_baik: 85 },
	'KL': { baik: 80,  kurang_baik: 75 },
	'TL': { baik: 60,  kurang_baik: 60 }
};

function modalHitungLive() {
	const jenis = document.getElementById('modal-select-jenis').value;
	let kesalahan = parseInt(document.getElementById('modal-input-kesalahan').value, 10);
	if (isNaN(kesalahan) || kesalahan < 0) kesalahan = 0;

	const kualitas = document.getElementById('modal-select-kualitas').value;
	const qcWrapper = document.getElementById('modal-qc-wrapper');

	if (jenis === 'qc') {
		qcWrapper.classList.remove('hidden');
	} else {
		qcWrapper.classList.add('hidden');
	}

	updateModalPanduanContent(jenis);

	const rules = MODAL_THRESHOLDS[jenis] || MODAL_THRESHOLDS['ziyadah'];
	let kode = 'TL';
	for (let i = 0; i < rules.length; i++) {
		if (rules[i].max === null || kesalahan <= rules[i].max) {
			kode = rules[i].kode;
			break;
		}
	}

	const skor = (MODAL_SKOR_MATRIX[kode] && MODAL_SKOR_MATRIX[kode][kualitas]) ? MODAL_SKOR_MATRIX[kode][kualitas] : 60;

	const recs = MODAL_RECS[jenis] || MODAL_RECS['ziyadah'];
	const recText = recs[kode] ? ` — ${recs[kode].action}` : '';

	document.getElementById('modal-preview-keterangan').innerText = (MODAL_KETERANGAN_LABEL[kode] || kode) + recText;
	document.getElementById('modal-preview-skor').innerText = skor + ' Poin';
}

function bukaModalEdit(data) {
	const modal = document.getElementById('modal-edit-penilaian');
	const form = document.getElementById('form-edit-modal');
	form.action = "<?php echo site_url('penilaian/simpan/'); ?>" + data.id;

	document.getElementById('modal-info-siswa').innerText = data.nama_siswa + " (" + data.nisn + ") • Kelas " + data.nama_kelas;
	document.getElementById('modal-info-materi').innerText = "QS. " + data.surat + " (Juz " + data.juz + ", Ayat " + data.ayat_dari + " - " + data.ayat_sampai + ")";
	document.getElementById('modal-select-jenis').value = data.jenis_setoran || 'ziyadah';
	document.getElementById('modal-input-kesalahan').value = data.jumlah_kesalahan !== undefined ? data.jumlah_kesalahan : 0;
	document.getElementById('modal-select-kualitas').value = data.kualitas_bacaan || 'baik';
	document.getElementById('modal-select-hasil-qc').value = data.hasil_qc || 'layak_tasmi';
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

	modalHitungLive();
	modal.classList.remove('hidden');
}

function tutupModalEdit() {
	const modal = document.getElementById('modal-edit-penilaian');
	const audioPlayer = document.getElementById('modal-audio-player');
	audioPlayer.pause();
	modal.classList.add('hidden');
}
</script>
