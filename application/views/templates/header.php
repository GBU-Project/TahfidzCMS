<?php
	$brand_title = isset($app_settings['institution_short_name']) && $app_settings['institution_short_name'] ? $app_settings['institution_short_name'] : 'TahfidzCMS';
	$page_title = isset($title) ? htmlspecialchars($title) . ' - ' . htmlspecialchars($brand_title) : htmlspecialchars($brand_title) . ' - Monitoring Hafalan';
	$meta_desc = isset($app_settings['institution_tagline']) && $app_settings['institution_tagline'] ? $app_settings['institution_tagline'] : "Sistem Monitoring Hafalan Al-Qur'an dan Penilaian Tajwid Santri";
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
	<?php if (! empty($app_settings['institution_logo']) && file_exists('./' . $app_settings['institution_logo'])): ?>
		<link rel="icon" type="image/png" href="<?php echo htmlspecialchars(base_url($app_settings['institution_logo'])); ?>">
	<?php else: ?>
		<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📖</text></svg>">
	<?php endif; ?>
	<title><?php echo $page_title; ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
	<div class="flex min-h-screen relative overflow-x-hidden">
