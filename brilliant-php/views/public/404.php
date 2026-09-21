<?php
$GLOBALS['meta_head'] = ($GLOBALS['meta_head'] ?? '') . '<meta name="robots" content="noindex, nofollow">' . "\n";
$nf = t('common.notFound');
$notFoundTitle = localized($locale ?? current_locale(), 'Page Not Found', 'الصفحة غير موجودة');
$notFoundText = localized($locale ?? current_locale(), 'The page you are looking for does not exist or has been moved.', 'الصفحة التي تبحث عنها غير موجودة أو تم نقلها.');
?>
<div>
  <!-- Hero -->
  <section class="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
    <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px); background-size: 32px 32px;"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50"></div>

    <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <span class="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($nf) ?></span>
      <h1 class="mx-auto mb-6 font-headline text-7xl font-bold uppercase leading-tight tracking-tighter text-white md:text-9xl">404</h1>
      <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl"><?= e($notFoundTitle) ?></p>
      <p class="mx-auto mb-10 max-w-xl text-sm leading-relaxed text-white/60"><?= e($notFoundText) ?></p>
      <a href="<?= e(lnk('')) ?>" class="btn-cta"><?= e(t('nav.home')) ?></a>
    </div>
  </section>
</div>