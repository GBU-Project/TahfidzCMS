<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
	<div>
		<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
		<p class="text-sm text-gray-500">Kelola akun Super Admin, Dewan Guru, dan Santri / Siswa</p>
	</div>
	<a href="<?php echo site_url('users/form'); ?>"
		class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm self-start sm:self-auto">
		<i class="fa-solid fa-plus"></i> Tambah Pengguna
	</a>
</div>

<div class="flex flex-wrap gap-2 mb-6 text-sm">
	<?php
	$tabs = array('' => 'Semua Akun', 'admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa');
	foreach ($tabs as $val => $label):
		$active = ($role_filter === $val) || ($role_filter === null && $val === '');
	?>
		<a href="<?php echo site_url('users' . ($val ? '?role=' . $val : '')); ?>"
			class="px-4 py-2 rounded-xl text-xs font-semibold transition <?php echo $active ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
			<?php echo $label; ?>
		</a>
	<?php endforeach; ?>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
	<div class="overflow-x-auto">
		<table class="w-full text-sm">
			<thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-left font-medium">
				<tr>
					<th class="px-5 py-3.5">Nama & Profil</th>
					<th class="px-5 py-3.5">NIP / NISN</th>
					<th class="px-5 py-3.5">Role</th>
					<th class="px-5 py-3.5">Status Akun</th>
					<th class="px-5 py-3.5 text-right">Aksi</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100">
				<?php if (empty($users_list)): ?>
					<tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 italic">Belum ada data akun pada kategori ini.</td></tr>
				<?php endif; ?>

				<?php foreach ($users_list as $u): ?>
					<tr class="hover:bg-gray-50/50 transition">
						<td class="px-5 py-3.5 font-medium flex items-center gap-3">
							<?php if (! empty($u->foto)): ?>
								<img src="<?php echo htmlspecialchars(base_url($u->foto)); ?>" class="w-9 h-9 rounded-xl object-cover border border-gray-200">
							<?php else: ?>
								<div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
									<?php echo strtoupper(substr($u->nama, 0, 1)); ?>
								</div>
							<?php endif; ?>
							<div>
								<div class="font-semibold text-gray-800"><?php echo htmlspecialchars($u->nama); ?></div>
							</div>
						</td>
						<td class="px-5 py-3.5 text-gray-500 font-mono text-xs"><?php echo htmlspecialchars($u->username); ?></td>
						<td class="px-5 py-3.5">
							<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize <?php echo $u->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($u->role === 'guru' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'); ?>">
								<?php echo $u->role; ?>
							</span>
						</td>
						<td class="px-5 py-3.5">
							<?php if ((int) $u->is_active === 1): ?>
								<span class="inline-flex items-center text-emerald-600 text-xs font-semibold">
									<i class="fa-solid fa-circle text-[7px] mr-1.5"></i> Aktif
								</span>
							<?php else: ?>
								<span class="inline-flex items-center text-gray-400 text-xs">
									<i class="fa-solid fa-circle text-[7px] mr-1.5"></i> Nonaktif
								</span>
							<?php endif; ?>
						</td>
						<td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
							<?php if ($u->role === 'siswa'): ?>
								<?php echo form_open('users/generate-rapor-token/' . $u->id, array('id' => 'regen-form-' . $u->id)); ?>
								<?php echo form_close(); ?>
								<?php if (! empty($u->access_token)): ?>
									<button type="button" title="Bagikan Rapor ke Orangtua"
										onclick="bukaShareRapor('<?php echo htmlspecialchars(addslashes($u->nama)); ?>', '<?php echo htmlspecialchars(site_url('rapor/' . $u->access_token)); ?>', <?php echo (int) $u->id; ?>)"
										class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded-lg font-medium text-xs transition">
										<i class="fa-solid fa-share-nodes mr-0.5"></i> Rapor
									</button>
								<?php else: ?>
									<button type="button" title="Buat Tautan Rapor untuk Orangtua"
										onclick="if(confirm('Buat tautan rapor untuk orangtua ' + '<?php echo htmlspecialchars(addslashes($u->nama)); ?>' + '?')) document.getElementById('regen-form-<?php echo (int) $u->id; ?>').submit();"
										class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded-lg font-medium text-xs transition">
										<i class="fa-solid fa-link mr-0.5"></i> Buat Rapor
									</button>
								<?php endif; ?>
							<?php endif; ?>
							<?php echo form_open('users/reset-password/' . $u->id, array('class' => 'inline', 'onsubmit' => "return confirm('Reset kata sandi untuk " . htmlspecialchars(addslashes($u->nama)) . " kembali ke sandi awal?');")); ?>
								<button type="submit" class="text-amber-600 hover:text-amber-800 hover:bg-amber-50 px-2 py-1 rounded-lg font-medium text-xs transition" title="Reset Sandi">
									<i class="fa-solid fa-key mr-0.5"></i> Reset
								</button>
							<?php echo form_close(); ?>
							<a href="<?php echo site_url('users/form/' . $u->id); ?>" class="text-emerald-700 hover:text-emerald-900 hover:bg-emerald-50 px-2 py-1 rounded-lg font-medium text-xs transition">
								<i class="fa-solid fa-pen-to-square mr-0.5"></i> Edit
							</a>
							<?php echo form_open('users/hapus/' . $u->id, array('class' => 'inline', 'onsubmit' => "return confirm('Yakin ingin menghapus " . htmlspecialchars(addslashes($u->nama)) . "?');")); ?>
								<button type="submit" class="text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded-lg font-medium text-xs transition">
									<i class="fa-solid fa-trash mr-0.5"></i> Hapus
								</button>
							<?php echo form_close(); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<!-- Modal Share Rapor -->
<div id="modal-share-rapor" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
	<div class="bg-white rounded-2xl shadow-lg max-w-sm w-full p-6">
		<div class="flex items-center justify-between mb-4">
			<h3 class="text-base font-bold text-gray-800">Bagikan Rapor — <span id="share-nama-siswa"></span></h3>
			<button type="button" onclick="tutupShareRapor()" class="text-gray-400 hover:text-gray-600">
				<i class="fa-solid fa-xmark text-lg"></i>
			</button>
		</div>

		<div id="share-qr-container" class="flex justify-center mb-4 bg-gray-50 rounded-xl p-4"></div>

		<label class="block text-xs font-medium text-gray-600 mb-1">Tautan Rapor</label>
		<div class="flex gap-2 mb-4">
			<input id="share-link-input" type="text" readonly
				class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-mono text-gray-600">
			<button type="button" onclick="copyShareLink()"
				class="bg-emerald-700 hover:bg-emerald-800 text-white px-3 rounded-xl text-xs font-medium transition shrink-0">
				<i class="fa-solid fa-copy"></i>
			</button>
		</div>

		<p class="text-xs text-gray-400 mb-4">
			Bagikan tautan atau kode QR ini ke orangtua/wali santri. Tautan bersifat privat —
			regenerasi jika dianggap sudah tersebar ke pihak yang tidak seharusnya.
		</p>

		<button type="button" onclick="regenerasiRapor()" class="w-full text-center text-xs font-medium text-amber-700 hover:text-amber-800 hover:bg-amber-50 py-2 rounded-xl transition">
			<i class="fa-solid fa-rotate mr-1"></i> Regenerasi Tautan (link lama tidak akan berfungsi lagi)
		</button>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let shareCurrentUserId = null;

function bukaShareRapor(nama, url, userId) {
	shareCurrentUserId = userId;
	document.getElementById('share-nama-siswa').innerText = nama;
	document.getElementById('share-link-input').value = url;

	const qrContainer = document.getElementById('share-qr-container');
	qrContainer.innerHTML = '';
	new QRCode(qrContainer, { text: url, width: 160, height: 160, colorDark: '#065f46' });

	const modal = document.getElementById('modal-share-rapor');
	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function tutupShareRapor() {
	const modal = document.getElementById('modal-share-rapor');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
}

function copyShareLink() {
	const input = document.getElementById('share-link-input');
	input.select();
	navigator.clipboard.writeText(input.value).then(function () {
		alert('Tautan berhasil disalin.');
	}).catch(function () {
		document.execCommand('copy');
	});
}

function regenerasiRapor() {
	if (! shareCurrentUserId) return;
	if (! confirm('Yakin ingin regenerasi tautan? Tautan lama tidak akan berfungsi lagi.')) return;
	document.getElementById('regen-form-' + shareCurrentUserId).submit();
}
</script>
