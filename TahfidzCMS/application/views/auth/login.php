<?php
	$brand_name = isset($settings['institution_name']) && $settings['institution_name'] ? $settings['institution_name'] : 'TahfidzCMS';
	$short_name = isset($settings['institution_short_name']) && $settings['institution_short_name'] ? $settings['institution_short_name'] : 'TahfidzCMS';
	$tagline    = isset($settings['institution_tagline']) && $settings['institution_tagline'] ? $settings['institution_tagline'] : "Sistem Monitoring Hafalan Al-Qur'an";
	$logo_url   = (! empty($settings['institution_logo']) && file_exists('./' . $settings['institution_logo'])) ? base_url($settings['institution_logo']) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Masuk - <?php echo htmlspecialchars($short_name); ?></title>
	<?php if ($logo_url): ?>
		<link rel="icon" type="image/png" href="<?php echo $logo_url; ?>">
	<?php else: ?>
		<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📖</text></svg>">
	<?php endif; ?>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-between antialiased">

	<!-- Header Link Back to Landing -->
	<header class="p-4 sm:p-6 max-w-7xl mx-auto w-full flex justify-between items-center">
		<a href="<?php echo site_url(); ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-800 hover:text-emerald-950 transition">
			<i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
		</a>
		<span class="text-xs text-slate-400 font-medium">Portal Tahfidz</span>
	</header>

	<main class="flex items-center justify-center px-4 py-8 flex-1">
		<div class="bg-white shadow-xl shadow-slate-200/60 rounded-3xl p-8 sm:p-10 w-full max-w-md border border-slate-100">
			
			<!-- Brand Header -->
			<div class="text-center mb-8">
				<div class="flex justify-center mb-4">
					<?php if ($logo_url): ?>
						<img src="<?php echo $logo_url; ?>" alt="Logo" class="h-16 w-auto max-w-[140px] object-contain rounded-xl shadow-sm">
					<?php else: ?>
						<div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-3xl shadow-md shadow-emerald-600/20">
							📖
						</div>
					<?php endif; ?>
				</div>
				<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
					<?php echo htmlspecialchars($short_name); ?>
				</h1>
				<p class="text-xs text-slate-500 mt-1 line-clamp-2">
					<?php echo htmlspecialchars($tagline); ?>
				</p>
			</div>

			<?php if (! empty($error)): ?>
				<div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
					<i class="fa-solid fa-circle-exclamation text-sm flex-shrink-0"></i>
					<div><?php echo $error; ?></div>
				</div>
			<?php endif; ?>

			<?php echo form_open('login', array('class' => 'space-y-4')); ?>
				<div>
					<label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIP / NISN</label>
					<div class="relative">
						<input type="text" name="username" required placeholder="Masukkan NIP atau NISN..."
							class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
						<i class="fa-solid fa-id-card absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
					</div>
				</div>
				<div>
					<label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
					<div class="relative">
						<input type="password" name="password" required placeholder="Masukkan kata sandi..."
							class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
						<i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
					</div>
				</div>
				<button type="submit"
					class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl py-3 text-sm shadow-md shadow-emerald-600/20 hover:shadow-lg transition">
					Masuk ke Sistem
				</button>
			<?php echo form_close(); ?>
		</div>
	</main>

	<footer class="text-center py-4 text-xs text-slate-400 border-t border-slate-100 bg-white">
		&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($brand_name); ?>.
	</footer>

</body>
</html>

