<div class="max-w-4xl mx-auto">
	<div class="flex items-center justify-between mb-6">
		<div>
			<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
			<p class="text-sm text-gray-500">Form input setoran hafalan siswa dengan rekaman audio langsung</p>
		</div>
		<a href="<?php echo site_url('setoran'); ?>" class="text-sm text-gray-500 hover:text-gray-700">
			<i class="fa-solid fa-arrow-left mr-1"></i> Kembali
		</a>
	</div>

	<?php echo form_open_multipart('setoran/simpan', array('id' => 'form-setoran', 'class' => 'space-y-6')); ?>
		
		<!-- Panel 1: Data Santri & Waktu -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
				<i class="fa-solid fa-user-graduate text-emerald-600"></i> Identitas Santri & Waktu
			</h2>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div class="md:col-span-2">
					<label class="block text-sm font-medium text-gray-700 mb-1">Pilih Santri / Siswa <span class="text-red-500">*</span></label>
					<select name="nisn" id="select-siswa" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="">-- Cari & Pilih Siswa --</option>
						<?php foreach ($siswa_list as $s): ?>
							<option value="<?php echo $s->nisn; ?>" data-kelas="<?php echo htmlspecialchars($s->nama_kelas); ?>" data-badge="<?php echo htmlspecialchars($s->badge); ?>">
								<?php echo htmlspecialchars($s->nama); ?> (<?php echo $s->nisn; ?>) — Kelas <?php echo htmlspecialchars($s->nama_kelas); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Kode Setoran</label>
					<input type="text" value="<?php echo $auto_kode; ?>" readonly
						class="w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-500 font-mono">
				</div>

				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
					<input type="date" name="tanggal" required value="<?php echo $default_date; ?>"
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>

				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Waktu <span class="text-red-500">*</span></label>
					<input type="time" name="waktu" required value="<?php echo $default_time; ?>"
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>
			</div>
		</div>

		<!-- Panel 2: Materi Hafalan -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
				<i class="fa-solid fa-book-quran text-emerald-600"></i> Materi Hafalan
			</h2>

			<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Juz <span class="text-red-500">*</span></label>
					<input type="number" name="juz" min="1" max="30" required placeholder="1 - 30"
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>

				<div class="md:col-span-3">
					<label class="block text-sm font-medium text-gray-700 mb-1">Nama Surat <span class="text-red-500">*</span></label>
					<select name="surat" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="">-- Pilih Surat --</option>
						<?php foreach ($daftar_surat as $no => $nama_surat): ?>
							<option value="<?php echo htmlspecialchars($nama_surat); ?>">
								<?php echo $no . '. ' . htmlspecialchars($nama_surat); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="md:col-span-2">
					<label class="block text-sm font-medium text-gray-700 mb-1">Ayat Dari <span class="text-red-500">*</span></label>
					<input type="number" name="ayat_dari" id="ayat_dari" min="1" required placeholder="Contoh: 1"
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>

				<div class="md:col-span-2">
					<label class="block text-sm font-medium text-gray-700 mb-1">Ayat Sampai <span class="text-red-500">*</span></label>
					<input type="number" name="ayat_sampai" id="ayat_sampai" min="1" required placeholder="Contoh: 10"
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>
			</div>
		</div>

		<!-- Panel 3: Penilaian & Evaluasi Sesuai Standar Kriteria -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
				<i class="fa-solid fa-star text-emerald-600"></i> Kriteria Penilaian & Hasil Simak
			</h2>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Jenis Setoran <span class="text-red-500">*</span></label>
					<select name="jenis_setoran" id="select-jenis-setoran" onchange="onJenisSetoranChange()" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<?php foreach ($jenis_setoran_list as $val => $label): ?>
							<option value="<?php echo $val; ?>" <?php echo ($val === 'ziyadah') ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($label); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p id="help-jenis-setoran" class="text-xs text-gray-400 mt-1">Standar kesalahan dihitung per halaman</p>
				</div>

				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kesalahan <span class="text-red-500">*</span></label>
					<input type="number" name="jumlah_kesalahan" id="input-kesalahan" min="0" value="0" required oninput="updateEstimatedPoin()"
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					<p class="text-xs text-gray-400 mt-1">Total salah makhraj, ayat lupa, atau waqaf</p>
				</div>

				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Kualitas Bacaan <span class="text-red-500">*</span></label>
					<select name="kualitas_bacaan" id="select-kualitas" onchange="updateEstimatedPoin()" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="baik" selected>Baik (Makhraj, Tajwid & Sifatul Huruf Baik)</option>
						<option value="kurang_baik">Kurang Baik (Tajwid / Sifat Kurang Pas)</option>
					</select>
				</div>

				<div id="qc-wrapper" class="hidden md:col-span-3">
					<div class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-lg">
						<label class="block text-sm font-medium text-blue-900 mb-1">Keputusan Uji Quality Control <span class="text-red-500">*</span></label>
						<select name="hasil_qc" id="select-hasil-qc" class="w-full bg-white border border-blue-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
							<option value="layak_tasmi">Layak Tasmi' (Lulus Uji & Berhak Maju Tasmi')</option>
							<option value="belum_layak">Belum Layak Tasmi' / Perlu Mengulang</option>
						</select>
						<p class="text-xs text-blue-700 mt-1">Wajib ditentukan saat pengambilan data Quality Control (QC).</p>
					</div>
				</div>

				<!-- Live Result Preview -->
				<div class="md:col-span-3">
					<div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 text-sm flex flex-col sm:flex-row items-center justify-between gap-3">
						<div class="flex items-center gap-3">
							<div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-sm" id="badge-keterangan">
								L
							</div>
							<div>
								<div class="text-xs text-emerald-700 font-medium">Predikat Kelancaran & Rekomendasi:</div>
								<div class="font-bold text-gray-900" id="label-keterangan">Lancar (Tidak Ada Kesalahan)</div>
							</div>
						</div>
						<div class="flex items-center gap-6">
							<div class="text-right">
								<div class="text-xs text-emerald-700 font-medium">Skor Akhir & Poin:</div>
								<div class="font-extrabold text-xl text-emerald-700" id="label-estimasi-poin">100 Poin</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Collapsible Cheat-Sheet / Panduan Standar Rubrik -->
				<div class="md:col-span-3">
					<div class="border border-amber-200 bg-amber-50/50 rounded-xl overflow-hidden text-xs text-gray-700">
						<button type="button" onclick="togglePanduanRubrik()" class="w-full px-4 py-2.5 bg-amber-100/60 hover:bg-amber-100 font-semibold text-amber-900 flex items-center justify-between text-left transition">
							<span class="flex items-center gap-2">
								<i class="fa-solid fa-circle-info text-amber-600"></i> Panduan Standar Kriteria Penilaian & Toleransi Simak
							</span>
							<span id="icon-toggle-panduan" class="text-amber-700">
								<i class="fa-solid fa-chevron-down"></i>
							</span>
						</button>

						<div id="content-panduan-rubrik" class="hidden p-4 space-y-4 border-t border-amber-200">
							<!-- 1. Kriteria Ambang Batas Kesalahan Dinamis -->
							<div>
								<div class="font-bold text-gray-800 mb-1.5 flex items-center gap-1.5">
									<i class="fa-solid fa-ruler-combined text-emerald-600"></i> Ambang Batas Kesalahan (<span id="panduan-judul-jenis" class="text-emerald-700">Ziyadah</span>)
								</div>
								<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2" id="panduan-grid-threshold">
									<!-- Diisi via JS sesuai jenis_setoran -->
								</div>
							</div>

							<!-- 2. Matriks Skor 100 s.d <70 -->
							<div>
								<div class="font-bold text-gray-800 mb-1.5 flex items-center gap-1.5">
									<i class="fa-solid fa-table text-emerald-600"></i> Matriks Nilai & Skor Akhir
								</div>
								<div class="overflow-x-auto">
									<table class="w-full bg-white border border-gray-200 rounded-lg text-left text-[11px]">
										<thead class="bg-gray-100 text-gray-600 border-b">
											<tr>
												<th class="p-2">Predikat Kelancaran</th>
												<th class="p-2 text-center">Makhraj/Tajwid/Sifat BAIK</th>
												<th class="p-2 text-center">Makhraj/Tajwid KURANG BAIK</th>
											</tr>
										</thead>
										<tbody class="divide-y divide-gray-100">
											<tr>
												<td class="p-2 font-semibold text-emerald-800">Lancar (L)</td>
												<td class="p-2 text-center font-bold text-emerald-700 bg-emerald-50/50">100</td>
												<td class="p-2 text-center font-bold text-emerald-700 bg-emerald-50/50">95</td>
											</tr>
											<tr>
												<td class="p-2 font-semibold text-blue-800">Cukup Lancar (CL)</td>
												<td class="p-2 text-center font-bold text-blue-700 bg-blue-50/50">90</td>
												<td class="p-2 text-center font-bold text-blue-700 bg-blue-50/50">85</td>
											</tr>
											<tr>
												<td class="p-2 font-semibold text-amber-800">Kurang Lancar (KL)</td>
												<td class="p-2 text-center font-bold text-amber-700 bg-amber-50/50">80</td>
												<td class="p-2 text-center font-bold text-amber-700 bg-amber-50/50">75</td>
											</tr>
											<tr>
												<td class="p-2 font-semibold text-rose-800">Tidak Lancar (TL)</td>
												<td colspan="2" class="p-2 text-center font-bold text-rose-700 bg-rose-50/50">60 (&lt; 70)</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<!-- 3. Catatan Toleransi & Simak -->
							<div class="bg-white p-3 rounded-lg border border-amber-200/80 space-y-1 text-[11px] text-gray-600">
								<div class="font-bold text-gray-800 mb-1">Catatan Penting Guru Pembimbing / Penguji:</div>
								<p>• <strong>Jenis Kesalahan:</strong> Lupa lanjutan ayat/kata, salah kata, salah harokat, atau salah hukum tajwid.</p>
								<p id="panduan-toleransi-isyarat">• <strong>Toleransi Isyarat:</strong> Apabila diingatkan dengan isyarat lalu santri membenarkan, tidak dihitung kesalahan. Batas isyarat: <strong>maks. 3x</strong> (jika &gt;3x tetap salah maka dihitung kesalahan).</p>
								<p>• <strong>Motivasi:</strong> Pembimbing senantiasa memberikan motivasi dan masukan perbaikan untuk halaqah berikutnya.</p>
							</div>
						</div>
					</div>
				</div>

				<div class="md:col-span-3">
					<label class="block text-sm font-medium text-gray-700 mb-1">Catatan Evaluasi Guru (opsional)</label>
					<textarea name="catatan" rows="2" placeholder="Tuliskan catatan perbaikan makhraj, tajwid atau hafalan jika ada..."
						class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500"></textarea>
				</div>
			</div>
		</div>

		<!-- Panel 4: Rekaman Audio Bukti Setoran -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center justify-between">
				<span class="flex items-center gap-2">
					<i class="fa-solid fa-microphone text-emerald-600"></i> Rekaman Audio Bukti Setoran (Opsional)
				</span>
				<span class="text-xs text-gray-400 font-normal">Maks. 10MB</span>
			</h2>

			<div class="space-y-4">
				<!-- Tab toggle: Rekam Langsung vs Upload File -->
				<div class="flex gap-2 text-sm border-b pb-2">
					<button type="button" id="tab-btn-record" onclick="switchAudioTab('record')"
						class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-medium">
						<i class="fa-solid fa-circle-dot mr-1 text-red-400"></i> Rekam Langsung
					</button>
					<button type="button" id="tab-btn-file" onclick="switchAudioTab('file')"
						class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium">
						<i class="fa-solid fa-upload mr-1"></i> Upload File Audio
					</button>
				</div>

				<!-- Area 1: Rekam via MediaRecorder Browser -->
				<div id="audio-record-section" class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center space-y-3">
					<div class="flex items-center justify-center gap-3">
						<button type="button" id="btn-start-record" onclick="startRecording()"
							class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm">
							<i class="fa-solid fa-microphone"></i> Mulai Rekam
						</button>
						<button type="button" id="btn-stop-record" onclick="stopRecording()" disabled
							class="bg-gray-800 hover:bg-gray-900 disabled:opacity-40 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
							<i class="fa-solid fa-stop"></i> Berhenti
						</button>
					</div>

					<div id="recording-timer" class="text-sm font-mono text-gray-600 hidden">
						<span class="inline-block w-2.5 h-2.5 bg-red-500 rounded-full animate-ping mr-1"></span>
						Durasi: <span id="timer-count">00:00</span>
					</div>

					<div id="audio-preview-container" class="hidden pt-2">
						<p class="text-xs text-gray-500 mb-1">Hasil Rekaman:</p>
						<audio id="audio-preview" controls class="mx-auto h-10 w-full max-w-md"></audio>
					</div>
				</div>

				<!-- Area 2: Upload File Audio -->
				<div id="audio-file-section" class="hidden p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
					<label class="block text-sm text-gray-600">Pilih file audio (.mp3, .wav, .m4a, .ogg)</label>
					<input type="file" name="audio_bukti" id="input-audio-file" accept="audio/*"
						class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
				</div>

				<input type="hidden" name="durasi_audio" id="input-durasi-audio" value="">
			</div>
		</div>

		<div class="flex gap-4 pt-2">
			<button type="submit" id="btn-submit-setoran" class="flex-1 bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 text-white font-semibold py-3 rounded-xl shadow transition-all flex items-center justify-center gap-2">
				<i class="fa-solid fa-check"></i> <span id="btn-submit-text">Simpan Data Setoran</span>
			</button>
			<a href="<?php echo site_url('setoran'); ?>" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 font-medium">
				Batal
			</a>
		</div>
	<?php echo form_close(); ?>
</div>

<script>
// Validasi client-side & Loading State Form Setoran
document.getElementById('form-setoran').addEventListener('submit', function(e) {
	const inputFile = document.getElementById('input-audio-file');
	if (inputFile && inputFile.files && inputFile.files[0]) {
		const fileSizeMB = inputFile.files[0].size / (1024 * 1024);
		if (fileSizeMB > 10) {
			e.preventDefault();
			alert('Ukuran file audio terlalu besar (' + fileSizeMB.toFixed(1) + ' MB). Maksimal ukuran file adalah 10 MB.');
			return false;
		}
	}

	// Disable double submit
	const btnSubmit = document.getElementById('btn-submit-setoran');
	const btnText = document.getElementById('btn-submit-text');
	btnSubmit.disabled = true;
	btnText.innerText = 'Menyimpan Data & Audio...';
	btnSubmit.querySelector('i').className = 'fa-solid fa-spinner fa-spin';
});

// Aturan Bisnis & Ambang Batas Kesalahan
const THRESHOLDS = {
	ziyadah:  [{ max: 0, kode: 'L' }, { max: 1, kode: 'CL' }, { max: 2, kode: 'KL' }, { max: null, kode: 'TL' }],
	murojaah: [{ max: 0, kode: 'L' }, { max: 5, kode: 'CL' }, { max: 10, kode: 'KL' }, { max: null, kode: 'TL' }],
	qc:       [{ max: 0, kode: 'L' }, { max: 1, kode: 'CL' }, { max: 2, kode: 'KL' }, { max: null, kode: 'TL' }]
};

const KETERANGAN_RECOMMENDATIONS = {
	ziyadah: {
		'L':  { title: 'Lancar (L)', desc: 'Tidak Ada Kesalahan (0 salah/hlm)', action: 'Melanjutkan Hafalan', border: 'border-emerald-300 bg-emerald-50' },
		'CL': { title: 'Cukup Lancar (CL)', desc: '1 Kesalahan per Halaman', action: 'Melanjutkan Hafalan', border: 'border-blue-300 bg-blue-50' },
		'KL': { title: 'Kurang Lancar (KL)', desc: '2 Kesalahan per Halaman', action: 'Mengulang / Lanjut Catatan', border: 'border-amber-300 bg-amber-50' },
		'TL': { title: 'Tidak Lancar (TL)', desc: '≥ 3 Kesalahan per Halaman', action: 'Mengulang Hafalan', border: 'border-rose-300 bg-rose-50' }
	},
	murojaah: {
		'L':  { title: 'Lancar (L)', desc: 'Tidak Ada Kesalahan (0 salah/juz)', action: 'Melanjutkan Hafalan', border: 'border-emerald-300 bg-emerald-50' },
		'CL': { title: 'Cukup Lancar (CL)', desc: '1 - 5 Kesalahan dalam 1 Juz', action: 'Melanjutkan Hafalan', border: 'border-blue-300 bg-blue-50' },
		'KL': { title: 'Kurang Lancar (KL)', desc: '6 - 10 Kesalahan dalam 1 Juz', action: 'Mengulang / Lanjut Catatan', border: 'border-amber-300 bg-amber-50' },
		'TL': { title: 'Tidak Lancar (TL)', desc: '≥ 11 Kesalahan dalam 1 Juz', action: 'Mengulang Hafalan', border: 'border-rose-300 bg-rose-50' }
	},
	qc: {
		'L':  { title: 'Lancar (L)', desc: 'Tidak Ada Kesalahan (0 salah/2 hlm)', action: 'Melanjutkan Hafalan', border: 'border-emerald-300 bg-emerald-50' },
		'CL': { title: 'Cukup Lancar (CL)', desc: '1 Kesalahan per 2 Halaman', action: 'Melanjutkan Hafalan', border: 'border-blue-300 bg-blue-50' },
		'KL': { title: 'Kurang Lancar (KL)', desc: '2 Kesalahan per 2 Halaman', action: 'Mengulang / Lanjut Catatan', border: 'border-amber-300 bg-amber-50' },
		'TL': { title: 'Tidak Lancar (TL)', desc: '≥ 3 Kesalahan per 2 Halaman', action: 'Mengulang Hafalan', border: 'border-rose-300 bg-rose-50' }
	}
};

function togglePanduanRubrik() {
	const content = document.getElementById('content-panduan-rubrik');
	const icon = document.getElementById('icon-toggle-panduan');
	if (content.classList.contains('hidden')) {
		content.classList.remove('hidden');
		icon.innerHTML = '<i class="fa-solid fa-chevron-up"></i>';
	} else {
		content.classList.add('hidden');
		icon.innerHTML = '<i class="fa-solid fa-chevron-down"></i>';
	}
}

function updatePanduanContent(jenis) {
	const jenisNamaMap = {
		ziyadah: 'Ziyadah (Hafalan Baru / per Halaman)',
		murojaah: "Muroja'ah (Mengulang Hafalan / per Juz)",
		qc: 'Quality Control (Uji Kelayakan / per 2 Halaman)'
	};
	document.getElementById('panduan-judul-jenis').innerText = jenisNamaMap[jenis] || 'Ziyadah';

	const grid = document.getElementById('panduan-grid-threshold');
	const recs = KETERANGAN_RECOMMENDATIONS[jenis] || KETERANGAN_RECOMMENDATIONS['ziyadah'];
	
	let html = '';
	for (const key in recs) {
		const item = recs[key];
		html += `<div class="p-2.5 rounded-lg border ${item.border}">
			<div class="font-bold text-[11px] text-gray-800">${item.title}</div>
			<div class="text-[10px] text-gray-600 mt-0.5">${item.desc}</div>
			<div class="text-[10px] font-semibold text-emerald-800 mt-1">➔ ${item.action}</div>
		</div>`;
	}
	grid.innerHTML = html;

	const toleransiEl = document.getElementById('panduan-toleransi-isyarat');
	if (jenis === 'qc') {
		toleransiEl.innerHTML = '• <strong>Toleransi Isyarat:</strong> Apabila diingatkan dengan isyarat lalu santri membenarkan, tidak dihitung kesalahan. Batas isyarat: <strong>maks. 2x</strong> (jika &gt;2x tetap salah maka dihitung kesalahan).';
	} else {
		toleransiEl.innerHTML = '• <strong>Toleransi Isyarat:</strong> Apabila diingatkan dengan isyarat lalu santri membenarkan, tidak dihitung kesalahan. Batas isyarat: <strong>maks. 3x</strong> (jika &gt;3x tetap salah maka dihitung kesalahan).';
	}
}

function onJenisSetoranChange() {
	const jenis = document.getElementById('select-jenis-setoran').value;
	const qcWrapper = document.getElementById('qc-wrapper');
	const helpText = document.getElementById('help-jenis-setoran');

	if (jenis === 'qc') {
		qcWrapper.classList.remove('hidden');
		helpText.innerText = 'Standar kesalahan dihitung per 2 halaman';
	} else {
		qcWrapper.classList.add('hidden');
		if (jenis === 'murojaah') {
			helpText.innerText = 'Standar kesalahan dihitung per juz';
		} else {
			helpText.innerText = 'Standar kesalahan dihitung per halaman';
		}
	}
	updatePanduanContent(jenis);
	updateEstimatedPoin();
}

// Kalkulasi estimasi predikat & poin secara real-time di UI
function updateEstimatedPoin() {
	const jenis = document.getElementById('select-jenis-setoran').value;
	let kesalahan = parseInt(document.getElementById('input-kesalahan').value, 10);
	if (isNaN(kesalahan) || kesalahan < 0) kesalahan = 0;

	const kualitas = document.getElementById('select-kualitas').value;

	// Hitung Keterangan
	const rules = THRESHOLDS[jenis] || THRESHOLDS['ziyadah'];
	let kode = 'TL';
	for (let i = 0; i < rules.length; i++) {
		if (rules[i].max === null || kesalahan <= rules[i].max) {
			kode = rules[i].kode;
			break;
		}
	}

	// Hitung Skor
	const skor = (SKOR_MATRIX[kode] && SKOR_MATRIX[kode][kualitas]) ? SKOR_MATRIX[kode][kualitas] : 60;

	// Update UI
	const badgeKeterangan = document.getElementById('badge-keterangan');
	badgeKeterangan.innerText = kode;
	if (kode === 'L') {
		badgeKeterangan.className = 'w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-sm';
	} else if (kode === 'CL') {
		badgeKeterangan.className = 'w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm';
	} else if (kode === 'KL') {
		badgeKeterangan.className = 'w-10 h-10 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold text-lg shadow-sm';
	} else {
		badgeKeterangan.className = 'w-10 h-10 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-lg shadow-sm';
	}

	const recs = KETERANGAN_RECOMMENDATIONS[jenis] || KETERANGAN_RECOMMENDATIONS['ziyadah'];
	const recText = recs[kode] ? ` — ${recs[kode].action}` : '';
	document.getElementById('label-keterangan').innerText = (KETERANGAN_LABELS[kode] || kode) + recText;
	document.getElementById('label-estimasi-poin').innerText = skor + ' Poin';
}

// Inisialisasi saat load
document.addEventListener('DOMContentLoaded', function() {
	onJenisSetoranChange();
});

function switchAudioTab(tab) {
	const recordSection = document.getElementById('audio-record-section');
	const fileSection = document.getElementById('audio-file-section');
	const btnRecord = document.getElementById('tab-btn-record');
	const btnFile = document.getElementById('tab-btn-file');

	if (tab === 'record') {
		recordSection.classList.remove('hidden');
		fileSection.classList.add('hidden');
		btnRecord.className = 'px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-medium';
		btnFile.className = 'px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium';
	} else {
		recordSection.classList.add('hidden');
		fileSection.classList.remove('hidden');
		btnFile.className = 'px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-medium';
		btnRecord.className = 'px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium';
	}
}

// MediaRecorder Logic
let mediaRecorder;
let audioChunks = [];
let recordStartTime;
let timerInterval;

async function startRecording() {
	try {
		const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
		mediaRecorder = new MediaRecorder(stream);
		audioChunks = [];

		mediaRecorder.ondataavailable = (e) => {
			if (e.data.size > 0) {
				audioChunks.push(e.data);
			}
		};

		mediaRecorder.onstop = () => {
			const mimeType = mediaRecorder.mimeType || 'audio/webm';
			const audioBlob = new Blob(audioChunks, { type: mimeType });
			const audioUrl = URL.createObjectURL(audioBlob);
			const preview = document.getElementById('audio-preview');
			preview.src = audioUrl;
			document.getElementById('audio-preview-container').classList.remove('hidden');

			// Tentukan ekstensi file dari mimeType asli
			const ext = mimeType.includes('mp4') ? 'mp4' : (mimeType.includes('ogg') ? 'ogg' : 'webm');

			// Buat file objek dan masukkan ke input file form
			const file = new File([audioBlob], "rekaman_setoran_" + Date.now() + "." + ext, { type: mimeType });
			const container = new DataTransfer();
			container.items.add(file);
			document.getElementById('input-audio-file').files = container.files;
		};

		mediaRecorder.start();
		document.getElementById('btn-start-record').disabled = true;
		document.getElementById('btn-stop-record').disabled = false;
		document.getElementById('recording-timer').classList.remove('hidden');

		recordStartTime = Date.now();
		timerInterval = setInterval(() => {
			const seconds = Math.floor((Date.now() - recordStartTime) / 1000);
			const m = String(Math.floor(seconds / 60)).padStart(2, '0');
			const s = String(seconds % 60).padStart(2, '0');
			document.getElementById('timer-count').innerText = `${m}:${s}`;
			document.getElementById('input-durasi-audio').value = seconds;
		}, 1000);

	} catch (err) {
		alert('Tidak dapat mengakses mikrofon: ' + err.message);
	}
}

function stopRecording() {
	if (mediaRecorder && mediaRecorder.state !== 'inactive') {
		mediaRecorder.stop();
		mediaRecorder.stream.getTracks().forEach(track => track.stop());
		clearInterval(timerInterval);
		document.getElementById('btn-start-record').disabled = false;
		document.getElementById('btn-stop-record').disabled = true;
	}
}
</script>
