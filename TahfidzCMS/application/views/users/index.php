<div class="flex items-center justify-between mb-4">
	<h1 class="text-2xl font-bold"><?php echo $title; ?></h1>
	<a href="<?php echo site_url('users/form'); ?>"
		class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold px-4 py-2 rounded-lg">
		<i class="fa-solid fa-plus mr-1"></i> Tambah User
	</a>
</div>

<div class="flex gap-2 mb-4 text-sm">
	<?php
	$tabs = array('' => 'Semua', 'admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa');
	foreach ($tabs as $val => $label):
		$active = ($role_filter === $val) || ($role_filter === null && $val === '');
	?>
		<a href="<?php echo site_url('users' . ($val ? '?role=' . $val : '')); ?>"
			class="px-3 py-1.5 rounded-lg <?php echo $active ? 'bg-emerald-700 text-white' : 'bg-white border text-gray-600 hover:bg-gray-50'; ?>">
			<?php echo $label; ?>
		</a>
	<?php endforeach; ?>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
	<table class="w-full text-sm">
		<thead class="bg-gray-50 text-gray-500 text-left">
			<tr>
				<th class="px-4 py-3">Nama</th>
				<th class="px-4 py-3">NIP / NISN</th>
				<th class="px-4 py-3">Role</th>
				<th class="px-4 py-3">Status</th>
				<th class="px-4 py-3 text-right">Aksi</th>
			</tr>
		</thead>
		<tbody class="divide-y">
			<?php if (empty($users_list)): ?>
				<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada data.</td></tr>
			<?php endif; ?>

			<?php foreach ($users_list as $u): ?>
				<tr class="hover:bg-gray-50">
					<td class="px-4 py-3 font-medium flex items-center gap-2">
						<?php if (! empty($u->foto)): ?>
							<img src="<?php echo base_url($u->foto); ?>" class="w-8 h-8 rounded-full object-cover">
						<?php else: ?>
							<div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
								<?php echo strtoupper(substr($u->nama, 0, 1)); ?>
							</div>
						<?php endif; ?>
						<?php echo htmlspecialchars($u->nama); ?>
					</td>
					<td class="px-4 py-3 text-gray-500"><?php echo htmlspecialchars($u->username); ?></td>
					<td class="px-4 py-3">
						<span class="px-2 py-0.5 rounded-full text-xs capitalize bg-gray-100 text-gray-600"><?php echo $u->role; ?></span>
					</td>
					<td class="px-4 py-3">
						<?php if ((int) $u->is_active === 1): ?>
							<span class="text-emerald-600 text-xs"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Aktif</span>
						<?php else: ?>
							<span class="text-gray-400 text-xs"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Nonaktif</span>
						<?php endif; ?>
					</td>
					<td class="px-4 py-3 text-right space-x-2">
						<a href="<?php echo site_url('users/reset-password/' . $u->id); ?>"
							onclick="return confirm('Reset password untuk <?php echo htmlspecialchars(addslashes($u->nama)); ?> menjadi default (123456)?');"
							class="text-amber-600 hover:text-amber-700 font-medium hover:underline text-xs" title="Reset ke default: 123456">
							<i class="fa-solid fa-key mr-0.5"></i> Reset Pass
						</a>
						<a href="<?php echo site_url('users/form/' . $u->id); ?>" class="text-emerald-700 font-medium hover:underline text-xs">Edit</a>
						<a href="<?php echo site_url('users/hapus/' . $u->id); ?>"
							onclick="return confirm('Yakin ingin menghapus <?php echo htmlspecialchars(addslashes($u->nama)); ?>?');"
							class="text-red-500 font-medium hover:underline text-xs">Hapus</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
