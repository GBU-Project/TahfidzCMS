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

	<form method="post" action="<?php echo site_url('setoran/simpan'); ?>" enctype="multipart/form-data" id="form-setoran" class="space-y-6">
		
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

		<!-- Panel 3: Penilaian & Evaluasi Awal -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
				<i class="fa-solid fa-star text-emerald-600"></i> Penilaian & Catatan
			</h2>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Nilai Tajwid & Makhraj <span class="text-red-500">*</span></label>
					<select name="nilai" id="select-nilai" onchange="updateEstimatedPoin()" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="A">A (Sangat Baik / Mumtaz - 100 Poin)</option>
						<option value="B" selected>B (Baik / Jayyid - 75 Poin)</option>
						<option value="C">C (Cukup / Maqbul - 50 Poin)</option>
					</select>
				</div>

				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Status Kelancaran <span class="text-red-500">*</span></label>
					<select name="status" id="select-status" onchange="updateEstimatedPoin()" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
						<option value="Lancar" selected>Lancar (+20 Poin)</option>
						<option value="Cukup">Cukup (+10 Poin)</option>
						<option value="Perlu Perbaikan">Perlu Perbaikan (+0 Poin)</option>
					</select>
				</div>

				<div class="md:col-span-2">
					<div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm flex items-center justify-between">
						<span>Estimasi Perolehan Poin Setoran:</span>
						<span id="label-estimasi-poin" class="font-bold text-lg">+95 Poin</span>
					</div>
				</div>

				<div class="md:col-span-2">
					<label class="block text-sm font-medium text-gray-700 mb-1">Catatan Evaluasi Guru (opsional)</label>
					<textarea name="catatan" rows="2" placeholder="Tuliskan catatan perbaikan makhraj atau tajwid jika ada..."
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
			<button type="submit" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-xl shadow transition-all">
				<i class="fa-solid fa-check mr-1"></i> Simpan Data Setoran
			</button>
			<a href="<?php echo site_url('setoran'); ?>" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 font-medium">
				Batal
			</a>
		</div>
	</form>
</div>

<script>
// Kalkulasi estimasi poin secara real-time di UI
function updateEstimatedPoin() {
	const nilai = document.getElementById('select-nilai').value;
	const status = document.getElementById('select-status').value;

	let base = 0;
	if (nilai === 'A') base = 100;
	else if (nilai === 'B') base = 75;
	else if (nilai === 'C') base = 50;

	let bonus = 0;
	if (status === 'Lancar') bonus = 20;
	else if (status === 'Cukup') bonus = 10;
	else if (status === 'Perlu Perbaikan') bonus = 0;

	const total = base + bonus;
	document.getElementById('label-estimasi-poin').innerText = '+' + total + ' Poin';
}

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
			// CATATAN PENTING: browser (Chrome/Firefox) merekam via MediaRecorder
			// dalam format WebM (codec Opus) secara default — BUKAN mp3 asli.
			// Sebelumnya kode ini memberi label palsu "audio/mp3" & ekstensi
			// ".mp3" pada data WebM, yang membuat validasi MIME di server
			// (CI3 Upload library mencocokkan ekstensi vs MIME asli file)
			// menolak upload di kebanyakan browser. Sekarang label dibuat jujur
			// sesuai isi sebenarnya (audio/webm), dan server (Upload_handler)
			// sudah disesuaikan untuk menerima ekstensi .webm.
			const mimeType = mediaRecorder.mimeType || 'audio/webm';
			const audioBlob = new Blob(audioChunks, { type: mimeType });
			const audioUrl = URL.createObjectURL(audioBlob);
			const preview = document.getElementById('audio-preview');
			preview.src = audioUrl;
			document.getElementById('audio-preview-container').classList.remove('hidden');

			// Tentukan ekstensi file dari mimeType asli, bukan diklaim sepihak
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
