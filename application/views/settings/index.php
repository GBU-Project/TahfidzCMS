<div class="max-w-4xl mx-auto">
	<div class="mb-6">
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Sesuaikan logo, nama lembaga, nama brand, dan tagline untuk Public Landing Page & Dashboard</p>
	</div>

	<?php echo form_open_multipart('settings/update', array('id' => 'form-settings', 'class' => 'space-y-6', 'onsubmit' => 'handleSubmit(event)')); ?>
		
		<!-- Panel: Identitas Lembaga -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
			<h2 class="text-base font-bold text-gray-800 mb-6 pb-3 border-b flex items-center gap-2">
				<i class="fa-solid fa-building-columns text-emerald-600"></i> Identitas Lembaga & Branding
			</h2>

			<!-- Section: Upload & Preview Logo -->
			<div class="mb-8 p-5 bg-gray-50/75 rounded-2xl border border-gray-100 flex flex-col sm:flex-row items-center gap-6">
				<div class="flex-shrink-0 text-center">
					<div class="text-xs font-semibold text-gray-500 mb-2">Logo Saat Ini</div>
					<div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white border-2 border-dashed border-emerald-300 p-2 flex items-center justify-center overflow-hidden shadow-sm">
						<?php if (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])): ?>
							<img id="logo-preview" src="<?php echo base_url($settings['institution_logo']); ?>" alt="Logo Lembaga" class="max-w-full max-h-full object-contain">
						<?php else: ?>
							<div id="logo-preview-placeholder" class="text-center text-emerald-600">
								<span class="text-3xl block mb-1">📖</span>
								<span class="text-[10px] font-bold text-gray-400">Default Logo</span>
							</div>
							<img id="logo-preview" src="" alt="Preview Logo" class="max-w-full max-h-full object-contain hidden">
						<?php endif; ?>
					</div>
				</div>

				<div class="flex-1 space-y-2 text-center sm:text-left">
					<label class="block text-sm font-semibold text-gray-800">Unggah Logo Baru</label>
					<p class="text-xs text-gray-500">Pilih berkas logo resmi lembaga (transparan lebih disarankan).</p>
					
					<div class="mt-2">
						<input type="file" name="institution_logo" id="institution_logo" accept="image/png,image/jpeg,image/webp" onchange="previewSelectedLogo(this)"
							class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
					</div>
					<div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-gray-400 mt-1">
						<span><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Format: <strong>PNG, JPG, WEBP</strong></span>
						<span><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Maksimal: <strong>2MB</strong></span>
					</div>
					<div id="logo-error" class="text-xs text-red-600 font-medium hidden"></div>
				</div>
			</div>

			<!-- Input Fields Form -->
			<div class="space-y-5">
				<div>
					<label for="institution_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
						Nama Lengkap Lembaga / Pesantren / Sekolah <span class="text-red-500">*</span>
					</label>
					<input type="text" name="institution_name" id="institution_name" required
						value="<?php echo htmlspecialchars($settings['institution_name']); ?>"
						placeholder="Contoh: Yayasan Pesantren Pertanian Darul Falah"
						class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
					<p class="text-[11px] text-gray-400 mt-1">Ditampilkan pada Hero Landing Page, Kop Laporan, dan Footer resmi.</p>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
					<div>
						<label for="institution_short_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
							Nama Singkat / Nama Brand <span class="text-red-500">*</span>
						</label>
						<input type="text" name="institution_short_name" id="institution_short_name" required
							value="<?php echo htmlspecialchars($settings['institution_short_name']); ?>"
							placeholder="Contoh: Darul Falah"
							class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
						<p class="text-[11px] text-gray-400 mt-1">Ditampilkan pada Navbar, Sidebar Header, dan Title Bar.</p>
					</div>

					<div>
						<label for="institution_tagline" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
							Tagline / Deskripsi Singkat
						</label>
						<input type="text" name="institution_tagline" id="institution_tagline"
							value="<?php echo htmlspecialchars($settings['institution_tagline']); ?>"
							placeholder="Contoh: Sistem Monitoring Hafalan Al-Qur'an"
							class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
						<p class="text-[11px] text-gray-400 mt-1">Slogan atau deskripsi di bawah nama institusi.</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Action Buttons -->
		<div class="flex items-center justify-between pt-2">
			<a href="<?php echo site_url('dashboard'); ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-medium transition">
				<i class="fa-solid fa-arrow-left mr-1.5"></i> Ke Dashboard
			</a>

			<button type="submit" id="btn-submit"
				class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl transition shadow-sm text-sm inline-flex items-center gap-2">
				<i class="fa-solid fa-floppy-disk"></i>
				<span id="btn-text">Simpan Perubahan</span>
			</button>
		</div>

	<?php echo form_close(); ?>
</div>

<script>
function previewSelectedLogo(input) {
	const file = input.files[0];
	const errorDiv = document.getElementById('logo-error');
	const previewImg = document.getElementById('logo-preview');
	const placeholder = document.getElementById('logo-preview-placeholder');

	errorDiv.classList.add('hidden');
	errorDiv.textContent = '';

	if (! file) return;

	// Validasi ukuran (maks 2MB = 2097152 bytes)
	if (file.size > 2097152) {
		errorDiv.textContent = 'Ukuran file melebihi batas 2MB. Silakan pilih file yang lebih kecil.';
		errorDiv.classList.remove('hidden');
		input.value = '';
		return;
	}

	// Validasi tipe
	const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
	if (! allowedTypes.includes(file.type)) {
		errorDiv.textContent = 'Format file tidak didukung. Harap unggah file PNG, JPG, atau WEBP.';
		errorDiv.classList.remove('hidden');
		input.value = '';
		return;
	}

	const reader = new FileReader();
	reader.onload = function(e) {
		previewImg.src = e.target.result;
		previewImg.classList.remove('hidden');
		if (placeholder) {
			placeholder.classList.add('hidden');
		}
	};
	reader.readAsDataURL(file);
}

function handleSubmit(event) {
	const btn = document.getElementById('btn-submit');
	const btnText = document.getElementById('btn-text');

	btn.disabled = true;
	btn.classList.add('opacity-75', 'cursor-not-allowed');
	btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...';
}
</script>
