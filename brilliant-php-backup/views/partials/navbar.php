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
<header class="absolute left-0 right-0 top-0 z-50 border-b-4 border-black bg-secondary">
  <div class="container-brilliant">
    <div class="flex h-20 w-full items-center justify-between">
      <a href="<?= e(navlink('/')) ?>" class="flex items-center" aria-label="<?= e($nav['homeAria']) ?>">
        <span class="flex h-14 items-center border-2 border-black bg-white px-3 shadow-[3px_3px_0_black]">
          <img src="<?= asset('/img/logo.png') ?>" alt="Brilliant Engineering" class="h-10 w-auto">
        </span>
      </a>
      <nav class="hidden items-center gap-8 lg:flex" aria-label="<?= e($nav['mainNav']) ?>">
        <?php foreach ($links as $link): ?>
          <a href="<?= e(navlink($link['href'])) ?>" class="border-b-2 pb-1 font-headline text-sm font-bold uppercase tracking-widest transition-colors <?= $navActive === $link['href'] ? 'border-[#BA1A1A] text-white' : 'border-transparent text-white/80 hover:text-white' ?>"><?= e($link['label']) ?></a>
        <?php endforeach; ?>
        <?php require base_path('/views/partials/lang-switcher.php'); ?>
        <a href="<?= e(navlink('/contact')) ?>" class="border-2 border-black bg-[#BA1A1A] px-7 py-2.5 font-headline text-sm font-bold uppercase tracking-widest text-white shadow-[4px_4px_0_black] transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none"><?= e($nav['getQuote']) ?></a>
      </nav>
      <button class="border-2 border-black bg-white p-2 text-black lg:hidden" id="navToggle" aria-label="<?= e($nav['toggleMenu']) ?>" aria-expanded="false">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>
  <nav class="hidden border-t-4 border-black bg-secondary lg:hidden" id="mobileNav" aria-label="<?= e($nav['mobileNav']) ?>">
    <div class="flex w-full flex-col px-4 py-4 sm:px-6 lg:px-8">
      <?php foreach ($links as $link): ?>
        <a href="<?= e(navlink($link['href'])) ?>" class="border-b border-white/20 py-3 font-headline text-sm font-bold uppercase tracking-widest transition-colors <?= $navActive === $link['href'] ? 'text-white' : 'text-white/80 hover:text-white' ?>"><?= e($link['label']) ?></a>
      <?php endforeach; ?>
      <div class="mt-4 inline-block text-center"><?php require base_path('/views/partials/lang-switcher.php'); ?></div>
      <a href="<?= e(navlink('/contact')) ?>" class="mt-4 border-2 border-black bg-[#BA1A1A] px-8 py-3 text-center font-headline text-sm font-bold uppercase tracking-widest text-white shadow-[4px_4px_0_black]"><?= e($nav['getQuote']) ?></a>
    </div>
  </nav>
</header>
