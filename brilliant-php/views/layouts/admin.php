<?php
$user = \App\Services\Auth::user();
$locale = current_locale();
$dir = $locale === 'ar' ? 'rtl' : 'ltr';
$shell = tarr('admin.shell');
$pageTitle = $GLOBALS['page_title'] ?? t('admin.metadata.defaultTitle');
$currentPath = current_localized_path(); // e.g. /admin/services

$icons = [
  'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
  'services' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>',
  'serviceCategories' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>',
  'projects' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M15.5 3H5a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V8.5L15.5 3Z"/><path d="M15 3v6h6"/></svg>',
  'products' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
  'productBrands' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M5 21h14"/><path d="M6 18h12"/><path d="M5 3h3v9a2 2 0 0 1-2 2H5Z"/><path d="M8 3h3v8a2 2 0 0 1-2 2H8"/><path d="M14 3h3v7a2 2 0 0 1-2 2h-1"/><path d="M17 3h3v6a2 2 0 0 1-2 2h-1"/></svg>',
  'productCategories' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/></svg>',
  'blog' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>',
  'team' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
  'clients' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/></svg>',
  'testimonials' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2Z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/></svg>',
  'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>',
  'logout' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>',
  'menu' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>',
  'close' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>',
  'external' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>',
];

$rawNavItems = [
  ['key' => 'dashboard', 'href' => admin_url(''), 'label' => $shell['dashboard'], 'exact' => true],
  ['key' => 'services', 'href' => admin_url('/services'), 'label' => $shell['services']],
  ['key' => 'serviceCategories', 'href' => admin_url('/service-categories'), 'label' => $shell['serviceCategories']],
  ['key' => 'projects', 'href' => admin_url('/projects'), 'label' => $shell['projects']],
  ['key' => 'products', 'href' => admin_url('/products'), 'label' => $shell['products']],
  ['key' => 'productBrands', 'href' => admin_url('/product-brands'), 'label' => $shell['productBrands']],
  ['key' => 'productCategories', 'href' => admin_url('/product-categories'), 'label' => $shell['productCategories']],
  ['key' => 'blog', 'href' => admin_url('/blog'), 'label' => $shell['blog']],
  ['key' => 'team', 'href' => admin_url('/team'), 'label' => $shell['team']],
  ['key' => 'clients', 'href' => admin_url('/clients'), 'label' => $shell['clients']],
  ['key' => 'testimonials', 'href' => admin_url('/testimonials'), 'label' => $shell['testimonials']],
  ['key' => 'settings', 'href' => admin_url('/settings'), 'label' => $shell['settings']],
];

$navItems = [];
foreach ($rawNavItems as $item) {
    $itemPath = str_replace('/' . $locale . '/admin', '', $item['href']);
    if ($itemPath === '') $itemPath = '/';
    if (!empty($item['exact'])) {
        $active = $currentPath === '/admin';
    } else {
        $active = str_starts_with($currentPath, '/admin' . rtrim($itemPath, '/')) && (rtrim($itemPath, '/') !== '' || $currentPath === '/admin');
    }
    $item['active'] = $active;
    $item['icon'] = $icons[$item['key']] ?? '';
    $navItems[] = $item;
}

$fullName = $user['fullName'] ?? $user['email'] ?? $shell['defaultUserName'] ?? 'Admin';
$email = $user['email'] ?? '';
$initials = '';
$parts = preg_split('/\s+/', trim($fullName));
if (count($parts) >= 2) {
    $initials = mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1);
} else {
    $initials = mb_substr($fullName, 0, 1);
}
$initials = mb_strtoupper($initials);
?>
<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | Admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="<?= asset('/img/favicon.ico') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Space+Grotesk:wght@400;600;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('/css/app.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
<style>:root{--font-inter:'Inter',ui-sans-serif,system-ui,sans-serif;--font-cairo:'Cairo','IBM Plex Sans Arabic',sans-serif;--font-space-grotesk:'Space Grotesk',sans-serif;--font-body:var(--font-inter);--font-headline:var(--font-space-grotesk);}</style>
</head>
<body class="bg-neutral-light font-body text-neutral antialiased">
<div class="min-h-screen bg-neutral-light">
  <aside class="fixed inset-y-0 start-0 z-40 hidden w-60 flex-col bg-neutral text-white lg:flex">
    <div class="flex h-16 items-center border-b border-white/10 px-4">
      <a href="<?= e(admin_url('')) ?>" class="font-headline text-lg font-bold uppercase tracking-wide">Brilliant<span class="text-tertiary">Admin</span></a>
    </div>
    <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
      <?php foreach ($navItems as $item): ?>
        <a href="<?= e($item['href']) ?>" class="flex items-center gap-3 px-3 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors <?= $item['active'] ? 'bg-tertiary text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
          <?= $item['icon'] ?>
          <?= e($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="border-t border-white/10 p-3">
      <a href="<?= e(lnk('/logout')) ?>" class="flex w-full items-center gap-3 px-3 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide text-white/70 transition-colors hover:bg-white/10 hover:text-white">
        <?= $icons['logout'] ?>
        <?= e($shell['signOut']) ?>
      </a>
    </div>
  </aside>

  <div id="mobile-menu" class="fixed inset-0 z-50 hidden lg:hidden">
    <div class="absolute inset-0 bg-black/60" onclick="toggleMobileMenu()" aria-hidden="true"></div>
    <aside class="absolute inset-y-0 start-0 flex w-64 flex-col bg-neutral text-white">
      <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
        <span class="font-headline text-lg font-bold uppercase tracking-wide">Brilliant<span class="text-tertiary">Admin</span></span>
        <button type="button" onclick="toggleMobileMenu()" aria-label="<?= e($shell['closeMenu'] ?? 'Close menu') ?>" class="p-1"><?= $icons['close'] ?></button>
      </div>
      <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
        <?php foreach ($navItems as $item): ?>
          <a href="<?= e($item['href']) ?>" onclick="toggleMobileMenu()" class="flex items-center gap-3 px-3 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors <?= $item['active'] ? 'bg-tertiary text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
            <?= $item['icon'] ?>
            <?= e($item['label']) ?>
          </a>
        <?php endforeach; ?>
      </nav>
      <div class="border-t border-white/10 p-3">
        <a href="<?= e(lnk('/logout')) ?>" class="flex w-full items-center gap-3 px-3 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide text-white/70 transition-colors hover:bg-white/10 hover:text-white">
          <?= $icons['logout'] ?>
          <?= e($shell['signOut']) ?>
        </a>
      </div>
    </aside>
  </div>

  <div class="lg:ps-60">
    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-line bg-white px-4 sm:px-6">
      <div class="flex items-center gap-3">
        <button type="button" onclick="toggleMobileMenu()" aria-label="<?= e($shell['openMenu'] ?? 'Open menu') ?>" class="p-1.5 text-neutral hover:text-tertiary lg:hidden"><?= $icons['menu'] ?></button>
        <a href="<?= e(lnk('')) ?>" target="_blank" class="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-neutral/60 transition-colors hover:text-secondary">
          <?= $icons['external'] ?>
          <?= e($shell['viewSite']) ?>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <div class="hidden text-right sm:block">
          <p class="font-headline text-sm font-semibold"><?= e($fullName) ?></p>
          <?php if ($email !== '' && $email !== $fullName): ?><p class="text-xs text-neutral/60"><?= e($email) ?></p><?php endif; ?>
        </div>
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary font-headline text-sm font-bold text-white"><?= e($initials) ?></div>
      </div>
    </header>
    <main class="p-4 sm:p-6 lg:p-8">
      <?php
        $flash = get_flash();
        if ($flash): foreach ($flash as $f): ?>
          <div class="mb-4 border px-4 py-3 text-sm <?= $f['type']==='success' ? 'border-green-600 bg-green-50 text-green-700' : 'border-tertiary/30 bg-red-50 text-tertiary' ?>"><?= e($f['message']) ?></div>
        <?php endforeach; endif;
      ?>
      <?= $content ?>
    </main>
  </div>
</div>
  <script>window.BS_API_BASE = '/api'; function toggleMobileMenu(){var m=document.getElementById('mobile-menu'); if(m){m.classList.toggle('hidden');}}</script>
  <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
  <script src="<?= asset('/js/admin.js') ?>"></script>
</body>
</html>
