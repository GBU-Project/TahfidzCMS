<?php 
	$role = $current_user->role; 
	$current_segment = $this->uri->segment(1) ?: 'dashboard';
?>

<!-- Backdrop Mobile Overlay -->
<div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden transition-opacity"></div>

<!-- Sidebar Aside -->
<aside id="main-sidebar" class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-emerald-900 text-white flex-shrink-0 min-h-screen p-4 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between">
	<div>
		<div class="flex items-center justify-between mb-6 px-2">
			<div class="flex items-center gap-2.5 min-w-0">
				<?php if (! empty($app_settings['institution_logo']) && file_exists('./' . $app_settings['institution_logo'])): ?>
					<img src="<?php echo base_url($app_settings['institution_logo']); ?>" alt="Logo" class="w-8 h-8 rounded-lg object-contain bg-white/10 p-0.5 flex-shrink-0">
				<?php else: ?>
					<span class="text-2xl flex-shrink-0">📖</span>
				<?php endif; ?>
				<div class="min-w-0">
					<div class="text-base font-bold tracking-tight truncate leading-tight">
						<?php echo htmlspecialchars(isset($app_settings['institution_short_name']) ? $app_settings['institution_short_name'] : 'TahfidzCMS'); ?>
					</div>
					<div class="text-[10px] text-emerald-300 font-medium tracking-wide truncate">Tahfidz Portal</div>
				</div>
			</div>
			<button type="button" onclick="toggleSidebar()" class="md:hidden text-emerald-300 hover:text-white p-1 flex-shrink-0">
				<i class="fa-solid fa-xmark text-lg"></i>
			</button>
		</div>

		<nav class="space-y-1 text-sm font-medium">
			<a href="<?php echo site_url('dashboard'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'dashboard') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
				<i class="fa-solid fa-house w-5 text-center"></i> Dashboard
			</a>

			<?php if (in_array($role, array('admin', 'guru'), TRUE)): ?>
				<a href="<?php echo site_url('setoran'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'setoran') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
					<i class="fa-solid fa-pen w-5 text-center"></i> Input Setoran
				</a>
				<a href="<?php echo site_url('penilaian'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'penilaian') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
					<i class="fa-solid fa-clipboard-check w-5 text-center"></i> Penilaian
				</a>
			<?php endif; ?>

			<a href="<?php echo site_url('riwayat'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'riwayat') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
				<i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> Riwayat
			</a>
			<a href="<?php echo site_url('progress'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'progress') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
				<i class="fa-solid fa-chart-line w-5 text-center"></i> Progress
			</a>
			<a href="<?php echo site_url('leaderboard'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'leaderboard') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
				<i class="fa-solid fa-trophy w-5 text-center"></i> Leaderboard
			</a>

			<?php if (in_array($role, array('admin', 'guru'), TRUE)): ?>
				<a href="<?php echo site_url('laporan'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'laporan') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
					<i class="fa-solid fa-file-excel w-5 text-center"></i> Laporan
				</a>
			<?php endif; ?>

			<?php if ($role === 'admin'): ?>
				<div class="pt-2 pb-1 px-3 text-[11px] font-bold tracking-wider uppercase text-emerald-400">Master Data</div>
				<a href="<?php echo site_url('users'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'users') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
					<i class="fa-solid fa-users-gear w-5 text-center"></i> Kelola Users
				</a>
				<a href="<?php echo site_url('kelas'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'kelas') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
					<i class="fa-solid fa-school w-5 text-center"></i> Kelola Kelas
				</a>
			<?php endif; ?>

			<div class="pt-2 pb-1 px-3 text-[11px] font-bold tracking-wider uppercase text-emerald-400">Pengaturan</div>
			<?php if ($role === 'admin'): ?>
				<a href="<?php echo site_url('settings'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'settings') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
					<i class="fa-solid fa-building-columns w-5 text-center"></i> Identitas Lembaga
				</a>
			<?php endif; ?>
			<a href="<?php echo site_url('profile'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?php echo ($current_segment === 'profile') ? 'bg-emerald-800 text-white shadow-sm font-semibold' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white'; ?>">
				<i class="fa-solid fa-user w-5 text-center"></i> Profil Saya
			</a>
		</nav>
	</div>

	<div class="pt-4 border-t border-emerald-800">
		<a href="<?php echo site_url('logout'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition hover:bg-red-900/50 text-red-300 hover:text-red-200">
			<i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Keluar
		</a>
	</div>
</aside>

<main class="flex-1 flex flex-col min-w-0">
	<header class="bg-white border-b px-4 sm:px-6 py-3 flex justify-between items-center sticky top-0 z-30 shadow-sm">
		<div class="flex items-center gap-3">
			<button type="button" onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100">
				<i class="fa-solid fa-bars text-lg"></i>
			</button>
			<div class="text-sm text-gray-500">
				Halo, <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($current_user->nama); ?></span>
				<span class="ml-2 inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold capitalize border border-emerald-200"><?php echo $role; ?></span>
			</div>
		</div>
		<div class="flex items-center gap-2">
			<a href="<?php echo site_url('profile'); ?>" class="text-gray-400 hover:text-emerald-700 p-1.5 rounded-lg hover:bg-gray-50" title="Profil">
				<i class="fa-solid fa-circle-user text-xl"></i>
			</a>
		</div>
	</header>
	<div class="p-4 sm:p-6 max-w-7xl w-full">
		<?php if ($this->session->flashdata('success')): ?>
			<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
				<i class="fa-solid fa-circle-check text-base"></i>
				<div><?php echo $this->session->flashdata('success'); ?></div>
			</div>
		<?php endif; ?>
		<?php if ($this->session->flashdata('error')): ?>
			<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
				<i class="fa-solid fa-circle-exclamation text-base"></i>
				<div><?php echo $this->session->flashdata('error'); ?></div>
			</div>
		<?php endif; ?>

<script>
function toggleSidebar() {
	const sidebar = document.getElementById('main-sidebar');
	const backdrop = document.getElementById('sidebar-backdrop');
	const isClosed = sidebar.classList.contains('-translate-x-full');

	if (isClosed) {
		sidebar.classList.remove('-translate-x-full');
		backdrop.classList.remove('hidden');
	} else {
		sidebar.classList.add('-translate-x-full');
		backdrop.classList.add('hidden');
	}
}
</script>
