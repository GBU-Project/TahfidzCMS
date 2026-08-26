<h1 class="text-2xl font-bold mb-4"><?php echo $title; ?></h1>

<div class="grid grid-cols-3 gap-6">
	<div class="col-span-1 bg-white rounded-xl shadow p-5">
		<h2 class="font-semibold mb-3">Tambah Kelas</h2>
		<?php echo form_open('kelas/simpan', array('class' => 'space-y-3')); ?>
			<input type="text" name="nama_kelas" required placeholder="mis. 7A"
				class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
			<button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-2 rounded-lg text-sm">
				Simpan
			</button>
		<?php echo form_close(); ?>
	</div>

	<div class="col-span-2 bg-white rounded-xl shadow overflow-hidden">
		<table class="w-full text-sm">
			<thead class="bg-gray-50 text-gray-500 text-left">
				<tr>
					<th class="px-4 py-3">Nama Kelas</th>
					<th class="px-4 py-3 text-right">Aksi</th>
				</tr>
			</thead>
			<tbody class="divide-y">
				<?php if (empty($kelas_list)): ?>
					<tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">Belum ada kelas.</td></tr>
				<?php endif; ?>

				<?php foreach ($kelas_list as $k): ?>
					<tr class="hover:bg-gray-50">
						<td class="px-4 py-3">
							<?php echo form_open('kelas/simpan', array('class' => 'flex items-center gap-2')); ?>
								<input type="hidden" name="id" value="<?php echo $k->id; ?>">
								<input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($k->nama_kelas); ?>"
									class="border border-gray-200 rounded-lg px-2 py-1 text-sm w-28">
								<button type="submit" class="text-emerald-700 text-xs hover:underline">Update</button>
							<?php echo form_close(); ?>
						</td>
						<td class="px-4 py-3 text-right">
							<a href="<?php echo site_url('kelas/hapus/' . $k->id); ?>"
								onclick="return confirm('Hapus kelas <?php echo htmlspecialchars(addslashes($k->nama_kelas)); ?>?');"
								class="text-red-500 hover:underline text-xs">Hapus</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
