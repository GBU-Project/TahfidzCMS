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
								<img src="<?php echo base_url($u->foto); ?>" class="w-9 h-9 rounded-xl object-cover border border-gray-200">
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
							<a href="<?php echo site_url('users/reset-password/' . $u->id); ?>"
								onclick="return confirm('Reset kata sandi untuk <?php echo htmlspecialchars(addslashes($u->nama)); ?> kembali ke sandi awal?');"
								class="text-amber-600 hover:text-amber-800 hover:bg-amber-50 px-2 py-1 rounded-lg font-medium text-xs transition" title="Reset Sandi">
								<i class="fa-solid fa-key mr-0.5"></i> Reset
							</a>
							<a href="<?php echo site_url('users/form/' . $u->id); ?>" class="text-emerald-700 hover:text-emerald-900 hover:bg-emerald-50 px-2 py-1 rounded-lg font-medium text-xs transition">
								<i class="fa-solid fa-pen-to-square mr-0.5"></i> Edit
							</a>
							<a href="<?php echo site_url('users/hapus/' . $u->id); ?>"
								onclick="return confirm('Yakin ingin menghapus <?php echo htmlspecialchars(addslashes($u->nama)); ?>?');"
								class="text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded-lg font-medium text-xs transition">
								<i class="fa-solid fa-trash mr-0.5"></i> Hapus
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
