<h1 class="text-2xl font-bold mb-4"><?php echo $title; ?></h1>

<div class="bg-white rounded-xl shadow p-6">
	<p class="text-gray-600">
		Login berhasil sebagai <strong><?php echo htmlspecialchars($current_user->nama); ?></strong>
		(role: <strong><?php echo $current_user->role; ?></strong>).
	</p>
	<p class="text-sm text-gray-400 mt-2">
		Statistik dashboard (total siswa, setoran, top siswa, chart) akan
		diisi pada Fase 4 sesuai blueprint.
	</p>
</div>
