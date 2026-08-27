<?php if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production'): ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>PHP Error</title>
<style type="text/css">
body { background-color: #f8fafc; margin: 40px; font-family: sans-serif; color: #334155; }
#container { border: 1px solid #fecaca; background: #fff; border-radius: 12px; padding: 24px; max-width: 700px; margin: 0 auto; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
h1 { font-size: 18px; font-weight: bold; color: #b91c1c; margin: 0 0 12px 0; }
p { font-size: 13px; margin: 0 0 6px 0; line-height: 1.5; color: #64748b; }
code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
</style>
</head>
<body>
	<div id="container">
		<h1>A PHP Error was encountered</h1>
		<p><strong>Severity:</strong> <?php echo $severity; ?></p>
		<p><strong>Message:</strong> <?php echo $message; ?></p>
		<p><strong>Filename:</strong> <code><?php echo $filepath; ?></code></p>
		<p><strong>Line Number:</strong> <?php echo $line; ?></p>
	</div>
</body>
</html>
<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Terjadi Kesalahan</title>
<style type="text/css">
body { background-color: #f8fafc; margin: 40px; font-family: sans-serif; color: #334155; }
#container { border: 1px solid #e2e8f0; background: #fff; border-radius: 12px; padding: 24px; max-width: 600px; margin: 0 auto; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
h1 { font-size: 20px; font-weight: bold; color: #1e293b; margin: 0 0 12px 0; }
p { font-size: 14px; margin: 0; line-height: 1.5; color: #64748b; }
</style>
</head>
<body>
	<div id="container">
		<h1>Terjadi Kesalahan Sistem</h1>
		<p>Mohon maaf, terjadi kesalahan pada server. Silakan coba lagi beberapa saat lagi atau hubungi administrator.</p>
	</div>
</body>
</html>
<?php endif; ?>
