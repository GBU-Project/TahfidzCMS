<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title>Login - TahfidzCMS</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-emerald-50 min-h-screen flex items-center justify-center">

	<div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-sm">
		<h1 class="text-2xl font-bold text-emerald-800 mb-1">TahfidzCMS</h1>
		<p class="text-sm text-gray-500 mb-6">Monitoring Hafalan Al-Qur'an</p>

		<?php if (! empty($error)): ?>
			<div class="bg-red-50 text-red-700 text-sm rounded-lg px-3 py-2 mb-4">
				<?php echo $error; ?>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo site_url('login'); ?>" class="space-y-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">NIP / NISN</label>
				<input type="text" name="username" required
					class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
				<input type="password" name="password" required
					class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
			</div>
			<button type="submit"
				class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-lg py-2 transition">
				Masuk
			</button>
		</form>
	</div>

</body>
</html>
