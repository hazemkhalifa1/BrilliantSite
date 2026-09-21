<?php
$locale = current_locale();
$other = other_locale();
?>
<div class="flex items-center gap-2 text-sm uppercase tracking-widest text-white">
  <a href="<?= e(locale_switch_href('en')) ?>" class="font-headline transition-colors hover:text-[#BA1A1A] <?= $locale === 'en' ? 'font-bold text-[#BA1A1A]' : '' ?>">EN</a>
  <span class="opacity-40" aria-hidden="true">|</span>
  <a href="<?= e(locale_switch_href('ar')) ?>" class="font-headline transition-colors hover:text-[#BA1A1A] <?= $locale === 'ar' ? 'font-bold text-[#BA1A1A]' : '' ?>">AR</a>
</div>
