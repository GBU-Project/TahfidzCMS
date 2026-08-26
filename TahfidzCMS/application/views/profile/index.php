<div class="max-w-3xl mx-auto">
	<div class="mb-6">
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Kelola informasi data akun dan kata sandi Anda</p>
	</div>

	<form method="post" action="<?php echo site_url('profile/update'); ?>" enctype="multipart/form-data" class="space-y-6">
		
		<!-- Kartu Informasi Pengguna -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
			<h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
				<i class="fa-solid fa-user-circle text-emerald-600"></i> Informasi Pribadi
			</h2>

			<div class="flex flex-col sm:flex-row items-center gap-6 mb-6">
				<div class="relative">
					<div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 border-2 border-emerald-500 flex items-center justify-center text-gray-400 text-3xl font-bold">
						<?php if (! empty($user->foto) && file_exists('./' . $user->foto)): ?>
							<img src="<?php echo base_url($user->foto); ?>" alt="Foto" class="w-full h-full object-cover">
						<?php else: ?>
							<i class="fa-solid fa-user"></i>
						<?php endif; ?>
					</div>
				</div>

				<div class="flex-1 space-y-1 text-center sm:text-left">
					<div class="text-sm font-medium text-gray-700">Foto Profil</div>
					<input type="file" name="foto" accept="image/jpeg,image/png,image/webp"
						class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
					<p class="text-[11px] text-gray-400">Format: JPG, PNG, atau WEBP. Maks 2MB.</p>
				</div>
			</div>

			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<div>
					<label class="block text-xs font-medium text-gray-600 mb-1">Username / NIP / NISN</label>
					<input type="text" value="<?php echo htmlspecialchars($user->username); ?>" readonly
						class="w-full bg-gray-100 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-500 font-mono">
				</div>

				<div>
					<label class="block text-xs font-medium text-gray-600 mb-1">Role Akses</label>
					<input type="text" value="<?php echo ucfirst($user->role); ?>" readonly
						class="w-full bg-gray-100 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-500">
				</div>

				<div class="sm:col-span-2">
					<label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
					<input type="text" name="nama" required value="<?php echo htmlspecialchars($user->nama); ?>"
						class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>

				<?php if ($siswa_detail): ?>
					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1">Kelas</label>
						<input type="text" value="Kelas <?php echo htmlspecialchars($siswa_detail->nama_kelas); ?>" readonly
							class="w-full bg-gray-100 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-500">
					</div>

					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1">Badge Saat Ini</label>
						<input type="text" value="<?php echo htmlspecialchars($siswa_detail->badge); ?> (<?php echo number_format($siswa_detail->total_poin); ?> pts)" readonly
							class="w-full bg-gray-100 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-500 font-semibold">
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Kartu Ganti Password -->
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
			<h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
				<i class="fa-solid fa-key text-emerald-600"></i> Ganti Password
			</h2>
			<p class="text-xs text-gray-400 mb-4">Kosongkan kolom di bawah jika Anda tidak berniat mengubah password.</p>

			<div class="space-y-4">
				<div>
					<label class="block text-xs font-medium text-gray-600 mb-1">Password Lama</label>
					<input type="password" name="password_lama" placeholder="Ketik password saat ini..."
						class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1">Password Baru</label>
						<input type="password" name="password_baru" placeholder="Minimal 6 karakter"
							class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					</div>

					<div>
						<label class="block text-xs font-medium text-gray-600 mb-1">Ulangi Password Baru</label>
						<input type="password" name="konfirmasi_password" placeholder="Ketik ulang password baru..."
							class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					</div>
				</div>
			</div>
		</div>

		<!-- Tombol Simpan -->
		<div class="flex justify-end gap-3">
			<button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-xl transition shadow-sm text-sm">
				<i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan
			</button>
		</div>

	</form>
</div>
