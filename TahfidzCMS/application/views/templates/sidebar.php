<?php $role = $current_user->role; ?>
<aside class="w-64 bg-emerald-900 text-white flex-shrink-0 min-h-screen p-4">
	<div class="text-xl font-bold mb-6">TahfidzCMS</div>

	<nav class="space-y-1 text-sm">
		<a href="<?php echo site_url('dashboard'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
			<i class="fa-solid fa-house mr-2"></i> Dashboard
		</a>

		<?php if (in_array($role, array('admin', 'guru'), TRUE)): ?>
			<a href="<?php echo site_url('setoran'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
				<i class="fa-solid fa-pen mr-2"></i> Input Setoran
			</a>
			<a href="<?php echo site_url('penilaian'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
				<i class="fa-solid fa-clipboard-check mr-2"></i> Penilaian
			</a>
		<?php endif; ?>

		<a href="<?php echo site_url('riwayat'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
			<i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat
		</a>
		<a href="<?php echo site_url('progress'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
			<i class="fa-solid fa-chart-line mr-2"></i> Progress
		</a>
		<a href="<?php echo site_url('leaderboard'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
			<i class="fa-solid fa-trophy mr-2"></i> Leaderboard
		</a>

		<?php if (in_array($role, array('admin', 'guru'), TRUE)): ?>
			<a href="<?php echo site_url('laporan'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
				<i class="fa-solid fa-file-excel mr-2"></i> Laporan
			</a>
		<?php endif; ?>

		<?php if ($role === 'admin'): ?>
			<a href="<?php echo site_url('users'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
				<i class="fa-solid fa-users-gear mr-2"></i> Kelola Users
			</a>
			<a href="<?php echo site_url('kelas'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
				<i class="fa-solid fa-school mr-2"></i> Kelola Kelas
			</a>
		<?php endif; ?>

		<a href="<?php echo site_url('profile'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">
			<i class="fa-solid fa-user mr-2"></i> Profil
		</a>

		<hr class="border-emerald-800 my-3">

		<a href="<?php echo site_url('logout'); ?>" class="block px-3 py-2 rounded-lg hover:bg-emerald-800 text-red-300">
			<i class="fa-solid fa-right-from-bracket mr-2"></i> Keluar
		</a>
	</nav>
</aside>

<main class="flex-1">
	<header class="bg-white border-b px-6 py-3 flex justify-between items-center">
		<div class="text-sm text-gray-500">
			Selamat datang, <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($current_user->nama); ?></span>
			<span class="ml-2 inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs capitalize"><?php echo $role; ?></span>
		</div>
	</header>
	<div class="p-6">
		<?php if ($this->session->flashdata('success')): ?>
			<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg px-4 py-3">
				<i class="fa-solid fa-circle-check mr-1"></i> <?php echo $this->session->flashdata('success'); ?>
			</div>
		<?php endif; ?>
		<?php if ($this->session->flashdata('error')): ?>
			<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
				<i class="fa-solid fa-circle-exclamation mr-1"></i> <?php echo $this->session->flashdata('error'); ?>
			</div>
		<?php endif; ?>
