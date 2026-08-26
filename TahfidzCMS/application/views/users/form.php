<h1 class="text-2xl font-bold mb-4"><?php echo $title; ?></h1>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
	<?php echo form_open_multipart('users/simpan', array('class' => 'space-y-4')); ?>

		<?php if ($user): ?>
			<input type="hidden" name="id" value="<?php echo $user->id; ?>">
		<?php endif; ?>

		<div class="grid grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
				<input type="text" name="nama" required value="<?php echo $user ? htmlspecialchars($user->nama) : ''; ?>"
					class="w-full border border-gray-300 rounded-lg px-3 py-2">
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">NIP / NISN (username)</label>
				<input type="text" name="username" required value="<?php echo $user ? htmlspecialchars($user->username) : ''; ?>"
					class="w-full border border-gray-300 rounded-lg px-3 py-2">
			</div>
		</div>

		<div class="grid grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">
					Password <?php echo $user ? '<span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span>' : ''; ?>
				</label>
				<input type="password" name="password" <?php echo $user ? '' : 'required'; ?>
					class="w-full border border-gray-300 rounded-lg px-3 py-2">
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
				<input type="file" name="foto" accept=".jpg,.jpeg,.png"
					class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
			</div>
		</div>

		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
			<select name="role" id="role-select" onchange="toggleRoleFields()" <?php echo $user ? 'disabled' : ''; ?>
				class="w-full border border-gray-300 rounded-lg px-3 py-2">
				<option value="">-- Pilih Role --</option>
				<?php foreach (array('admin', 'guru', 'siswa') as $r): ?>
					<option value="<?php echo $r; ?>" <?php echo ($user && $user->role === $r) ? 'selected' : ''; ?>>
						<?php echo ucfirst($r); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php if ($user): ?>
				<!-- Role tidak boleh diganti setelah dibuat -- ubah struktur (siswa <-> guru)
				     lintas tabel terlalu berisiko lewat form biasa. Hapus & buat baru jika perlu. -->
				<input type="hidden" name="role" value="<?php echo $user->role; ?>">
				<p class="text-xs text-gray-400 mt-1">Role tidak dapat diubah setelah akun dibuat.</p>
			<?php endif; ?>
		</div>

		<!-- Field khusus GURU -->
		<div id="field-guru" class="hidden border-t pt-4">
			<label class="block text-sm font-medium text-gray-700 mb-2">Kelas yang Diampu</label>
			<div class="grid grid-cols-3 gap-2">
				<?php foreach ($kelas_list as $k): ?>
					<label class="flex items-center gap-2 text-sm">
						<input type="checkbox" name="kelas_ids[]" value="<?php echo $k->id; ?>"
							<?php echo in_array($k->id, $kelas_guru_ids) ? 'checked' : ''; ?>>
						<?php echo htmlspecialchars($k->nama_kelas); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Field khusus SISWA -->
		<div id="field-siswa" class="hidden border-t pt-4 grid grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
				<select name="kelas_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
					<option value="">-- Pilih Kelas --</option>
					<?php foreach ($kelas_list as $k): ?>
						<option value="<?php echo $k->id; ?>" <?php echo ($siswa && (int) $siswa->kelas_id === (int) $k->id) ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($k->nama_kelas); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Target Juz</label>
				<input type="number" name="target_juz" min="1" max="30"
					value="<?php echo $siswa ? $siswa->target_juz : 30; ?>"
					class="w-full border border-gray-300 rounded-lg px-3 py-2">
			</div>
		</div>

		<div class="flex gap-3 pt-4">
			<button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-5 py-2 rounded-lg">
				Simpan
			</button>
			<a href="<?php echo site_url('users'); ?>" class="px-5 py-2 rounded-lg border text-gray-600 hover:bg-gray-50">
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
