<div class="mb-6">
	<h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
	<p class="text-sm text-gray-500">Kelola daftar master data kelas dan rombongan belajar</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
	<div class="md:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
		<h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
			<i class="fa-solid fa-plus-circle text-emerald-600"></i> Tambah Kelas Baru
		</h2>
		<?php echo form_open('kelas/simpan', array('class' => 'space-y-4')); ?>
			<div>
				<label class="block text-xs font-medium text-gray-600 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
				<input type="text" name="nama_kelas" required placeholder="Contoh: 7A / 8B / 9C"
					class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-emerald-500">
			</div>
			<button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-xl text-sm transition shadow-sm">
				<i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Kelas
			</button>
		<?php echo form_close(); ?>
	</div>

	<div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
		<div class="p-4 border-b border-gray-100 flex items-center justify-between">
			<h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
				<i class="fa-solid fa-school text-emerald-600"></i> Daftar Kelas Terdaftar
			</h2>
			<span class="text-xs text-gray-400 font-medium"><?php echo count($kelas_list); ?> Kelas</span>
		</div>
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-left font-medium">
					<tr>
						<th class="px-5 py-3.5">Nama Kelas</th>
						<th class="px-5 py-3.5 text-right">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-100">
					<?php if (empty($kelas_list)): ?>
						<tr><td colspan="2" class="px-5 py-8 text-center text-gray-400 italic">Belum ada data kelas yang ditambahkan.</td></tr>
					<?php endif; ?>

					<?php foreach ($kelas_list as $k): ?>
						<tr class="hover:bg-gray-50/50 transition">
							<td class="px-5 py-3.5">
								<?php echo form_open('kelas/simpan', array('class' => 'flex items-center gap-2')); ?>
									<input type="hidden" name="id" value="<?php echo $k->id; ?>">
									<input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($k->nama_kelas); ?>"
										class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm font-semibold text-gray-800 w-32 focus:bg-white focus:ring-2 focus:ring-emerald-500">
									<button type="submit" class="text-emerald-700 hover:text-emerald-800 text-xs font-semibold px-2 py-1 rounded bg-emerald-50 hover:bg-emerald-100 transition">Update</button>
								<?php echo form_close(); ?>
							</td>
							<td class="px-5 py-3.5 text-right">
								<a href="<?php echo site_url('kelas/hapus/' . $k->id); ?>"
									onclick="return confirm('Hapus kelas <?php echo htmlspecialchars(addslashes($k->nama_kelas)); ?>?');"
									class="text-red-500 hover:text-red-700 hover:bg-red-50 px-2.5 py-1.5 rounded-lg text-xs font-medium transition">
									<i class="fa-solid fa-trash mr-1"></i> Hapus
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
