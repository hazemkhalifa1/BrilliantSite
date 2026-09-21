<?php
/** @var string $content */
use App\Services\Repo;
$locale = current_locale();
$dir = $locale === 'ar' ? 'rtl' : 'ltr';
$pageTitle = $GLOBALS['page_title'] ?? ($pageTitle ?? null);
$metaDescription = $GLOBALS['meta_description'] ?? ($metaDescription ?? null);
$metaKeywords = $GLOBALS['meta_keywords'] ?? ($metaKeywords ?? null);
$metaHead = $GLOBALS['meta_head'] ?? ($metaHead ?? '');
$siteName = t('metadata.siteName');
$fullTitle = $pageTitle && $pageTitle !== '' ? $pageTitle . ' | ' . $siteName : t('metadata.title');
$localizedPath = current_localized_path();
$pathSuffix = $localizedPath === '/' ? '' : $localizedPath;
$canonicalUrl = app_url() . '/' . $locale . $pathSuffix;
$alternateEn = app_url() . '/en' . $pathSuffix;
$alternateAr = app_url() . '/ar' . $pathSuffix;
$ogTitle = $fullTitle;
$ogDescription = $metaDescription ?: t('metadata.description');
$ogImage = $GLOBALS['og_image'] ?? asset('/img/logo.png');
$ogType = $GLOBALS['og_type'] ?? 'website';
$ogLocale = $locale === 'ar' ? 'ar_EG' : 'en_US';
$ogLocaleAlternate = $locale === 'ar' ? 'en_US' : 'ar_EG';
?>
<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>" class="">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<?php if ($metaDescription): ?><meta name="description" content="<?= e($metaDescription) ?>"><?php endif; ?>
<?php if ($metaKeywords): ?><meta name="keywords" content="<?= e($metaKeywords) ?>"><?php endif; ?>
<link rel="canonical" href="<?= e($canonicalUrl) ?>">
<link rel="alternate" hreflang="en" href="<?= e($alternateEn) ?>">
<link rel="alternate" hreflang="ar" href="<?= e($alternateAr) ?>">
<link rel="alternate" hreflang="x-default" href="<?= e($alternateEn) ?>">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($ogTitle) ?>">
<meta property="og:description" content="<?= e($ogDescription) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:url" content="<?= e($canonicalUrl) ?>">
<meta property="og:type" content="<?= e($ogType) ?>">
<meta property="og:locale" content="<?= e($ogLocale) ?>">
<meta property="og:locale:alternate" content="<?= e($ogLocaleAlternate) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($ogTitle) ?>">
<meta name="twitter:description" content="<?= e($ogDescription) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">
<link rel="icon" href="<?= asset('/img/favicon.ico') ?>">
<link rel="icon" type="image/png" href="<?= asset('/img/logo.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('/css/app.css') ?>">
<style>
:root{
  --font-inter:'Inter',ui-sans-serif,system-ui,-apple-system,'Segoe UI',sans-serif;
  --font-cairo:'Cairo','IBM Plex Sans Arabic','Tajawal','Segoe UI',Tahoma,Arial,sans-serif;
  --font-space-grotesk:'Space Grotesk',ui-sans-serif,system-ui,'Segoe UI',sans-serif;
  --font-body:var(--font-inter);
  --font-headline:var(--font-space-grotesk);
}
</style>
<?= $metaHead ?>
</head>
<body class="bg-white font-body text-neutral antialiased overflow-x-hidden">
<div class="flex min-h-screen flex-col">
  <?php require base_path('/views/partials/navbar.php'); ?>
  <main class="flex-1"><?= $content ?></main>
  <?php require base_path('/views/partials/footer.php'); ?>
</div>
<script src="<?= asset('/js/app.js') ?>"></script>
</body>
</html>
