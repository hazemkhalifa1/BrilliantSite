<?php
$nav = tarr('nav');
$navActive = current_localized_path();
$links = [
  ['label' => $nav['home'], 'href' => '/'],
  ['label' => $nav['about'], 'href' => '/about'],
  ['label' => $nav['services'], 'href' => '/services'],
  ['label' => $nav['projects'], 'href' => '/projects'],
  ['label' => $nav['products'], 'href' => '/products'],
  ['label' => $nav['blog'], 'href' => '/blog'],
  ['label' => $nav['contact'], 'href' => '/contact'],
];
if (!function_exists('navlink')) { function navlink(string $href): string {
    return lnk($href === '/' ? '' : $href);
} }
?>
<header id="siteNav" class="absolute left-0 right-0 top-0 z-50 transition-all duration-300" data-scrolled="false">
  <div class="container-brilliant">
    <div class="flex h-20 w-full items-center justify-between">
      <a href="<?= e(navlink('/')) ?>" class="flex items-center" aria-label="<?= e($nav['homeAria']) ?>">
        <img src="<?= asset('/img/logo.png') ?>" alt="Brilliant Engineering" class="h-12 w-auto">
      </a>
      <nav class="hidden items-center gap-10 lg:flex" aria-label="<?= e($nav['mainNav']) ?>">
        <?php foreach ($links as $link): ?>
          <a href="<?= e(navlink($link['href'])) ?>" class="border-b-2 pb-1 font-headline text-sm uppercase tracking-widest transition-colors <?= $navActive === $link['href'] ? 'border-[#BA1A1A] font-bold text-[#BA1A1A]' : 'border-transparent text-white hover:text-[#BA1A1A]' ?>"><?= e($link['label']) ?></a>
        <?php endforeach; ?>
        <?php require base_path('/views/partials/lang-switcher.php'); ?>
        <a href="<?= e(navlink('/contact')) ?>" class="bg-[#BA1A1A] px-8 py-2.5 font-headline text-sm uppercase tracking-widest text-white transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"><?= e($nav['getQuote']) ?></a>
      </nav>
      <button id="navToggle" class="p-2 text-white lg:hidden" aria-label="<?= e($nav['toggleMenu']) ?>" aria-expanded="false" aria-controls="mobileNav">
        <svg id="navIconOpen" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
        <svg id="navIconClose" class="hidden h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>
  </div>
  <nav id="mobileNav" class="hidden border-t border-white/10 bg-[#111C2D] lg:hidden" aria-label="<?= e($nav['mobileNav']) ?>">
    <div class="flex w-full flex-col px-4 py-4 sm:px-6 lg:px-8">
      <?php foreach ($links as $link): ?>
        <a href="<?= e(navlink($link['href'])) ?>" class="border-b border-white/10 py-3 font-headline text-sm uppercase tracking-widest transition-colors <?= $navActive === $link['href'] ? 'font-bold text-[#BA1A1A]' : 'text-white hover:text-[#BA1A1A]' ?>"><?= e($link['label']) ?></a>
      <?php endforeach; ?>
      <div class="mt-4 inline-block text-center"><?php require base_path('/views/partials/lang-switcher.php'); ?></div>
      <a href="<?= e(navlink('/contact')) ?>" class="mt-4 bg-[#BA1A1A] px-8 py-3 text-center font-headline text-sm uppercase tracking-widest text-white transition-colors hover:opacity-90"><?= e($nav['getQuote']) ?></a>
    </div>
  </nav>
</header>