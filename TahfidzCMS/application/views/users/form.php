<div class="mb-6">
	<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
	<p class="text-sm text-gray-500">Isi data akun pengguna, santri, atau guru beserta hak aksesnya</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">
	<?php echo form_open_multipart('users/simpan', array('class' => 'space-y-4')); ?>

		<?php if ($user): ?>
			<input type="hidden" name="id" value="<?php echo $user->id; ?>">
		<?php endif; ?>

		<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
				<input type="text" name="nama" required value="<?php echo $user ? htmlspecialchars($user->nama) : ''; ?>"
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>
			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">NIP / NISN (Username) <span class="text-red-500">*</span></label>
				<input type="text" name="username" required value="<?php echo $user ? htmlspecialchars($user->username) : ''; ?>"
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">
					Password <?php echo $user ? '<span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span>' : '<span class="text-red-500">*</span>'; ?>
				</label>
				<input type="password" name="password" <?php echo $user ? '' : 'required'; ?>
					placeholder="<?php echo $user ? 'Ketik jika ingin ganti password' : 'Min. 6 karakter'; ?>"
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>
			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Foto Profil</label>
				<input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp"
					class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
			</div>
		</div>

		<div>
			<label class="block text-xs font-medium text-gray-600 mb-1">Role Akun <span class="text-red-500">*</span></label>
			<select name="role" id="role-select" onchange="toggleRoleFields()" <?php echo $user ? 'disabled' : ''; ?>
				class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
				<option value="">-- Pilih Role --</option>
				<?php foreach (array('admin', 'guru', 'siswa') as $r): ?>
					<option value="<?php echo $r; ?>" <?php echo ($user && $user->role === $r) ? 'selected' : ''; ?>>
						<?php echo ucfirst($r); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php if ($user): ?>
				<input type="hidden" name="role" value="<?php echo $user->role; ?>">
				<p class="text-[11px] text-gray-400 mt-1">Role tidak dapat diubah setelah akun dibuat demi menjaga konsistensi relasi.</p>
			<?php endif; ?>
		</div>

		<!-- Field khusus GURU -->
		<div id="field-guru" class="hidden border-t pt-4">
			<label class="block text-xs font-medium text-gray-600 mb-2">Kelas yang Diampu (Hak Input & Penilaian)</label>
			<div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
				<?php foreach ($kelas_list as $k): ?>
					<label class="flex items-center gap-2 text-sm p-2 rounded-xl bg-gray-50 border border-gray-100 hover:bg-emerald-50/50 cursor-pointer">
						<input type="checkbox" name="kelas_ids[]" value="<?php echo $k->id; ?>"
							class="rounded text-emerald-600 focus:ring-emerald-500"
							<?php echo in_array($k->id, $kelas_guru_ids) ? 'checked' : ''; ?>>
						<span class="text-gray-700 font-medium">Kelas <?php echo htmlspecialchars($k->nama_kelas); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Field khusus SISWA -->
		<div id="field-siswa" class="hidden border-t pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Pilih Rombel / Kelas <span class="text-red-500">*</span></label>
				<select name="kelas_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
					<option value="">-- Pilih Kelas --</option>
					<?php foreach ($kelas_list as $k): ?>
						<option value="<?php echo $k->id; ?>" <?php echo ($siswa && (int) $siswa->kelas_id === (int) $k->id) ? 'selected' : ''; ?>>
							Kelas <?php echo htmlspecialchars($k->nama_kelas); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Target Hafalan (Juz) <span class="text-red-500">*</span></label>
				<input type="number" name="target_juz" min="1" max="30"
					value="<?php echo $siswa ? $siswa->target_juz : 30; ?>"
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>
		</div>

		<div class="flex items-center gap-3 pt-4 border-t">
			<button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-medium px-6 py-2.5 rounded-xl text-sm transition shadow-sm">
				<i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Pengguna
			</button>
			<a href="<?php echo site_url('users'); ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium">
				Batal
			</a>
		</div>
	<?php echo form_close(); ?>
</div>

<script>
function toggleRoleFields() {
	var role = document.getElementById('role-select').value;
	document.getElementById('field-guru').classList.toggle('hidden', role !== 'guru');
	document.getElementById('field-siswa').classList.toggle('hidden', role !== 'siswa');
}
// Jalankan sekali saat halaman dimuat (mis. saat mode edit)
toggleRoleFields();
</script>
