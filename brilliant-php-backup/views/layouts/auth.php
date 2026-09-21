<?php
$locale = current_locale();
$dir = $locale === 'ar' ? 'rtl' : 'ltr';
$pageTitle = $GLOBALS['page_title'] ?? t('login.title');
?>
<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | <?= e(t('metadata.siteName')) ?></title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="<?= asset('/img/favicon.ico') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Space+Grotesk:wght@400;600;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('/css/app.css') ?>">
<style>:root{--font-inter:'Inter',ui-sans-serif,system-ui,sans-serif;--font-cairo:'Cairo','IBM Plex Sans Arabic',sans-serif;--font-space-grotesk:'Space Grotesk',sans-serif;}</style>
</head>
<body class="bg-neutral-light font-body text-neutral antialiased">
<?= $content ?>
</body>
</html>
