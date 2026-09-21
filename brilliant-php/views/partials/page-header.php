<?php
/** expects: $phTitle, optional $phSubtitle, $phEyebrow, $phBackground */
$phDark = $phDark ?? true;
?>
<section class="relative overflow-hidden pb-16 pt-24 md:pb-20 md:pt-28 <?= $phDark ? 'bg-neutral text-white' : 'border-b border-line bg-white text-neutral' ?>">
  <?php if (!empty($phBackground)): ?>
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= e($phBackground) ?>')"></div>
    <div class="absolute inset-0 bg-neutral/80"></div>
  <?php endif; ?>
  <div class="container-brilliant relative z-10">
    <?php if (!empty($phEyebrow)): ?>
      <span class="font-headline text-xs font-semibold uppercase tracking-widest text-tertiary"><?= e($phEyebrow) ?></span>
    <?php endif; ?>
    <h1 class="mt-3 font-headline text-4xl font-bold uppercase leading-tight md:text-5xl"><?= e($phTitle) ?></h1>
    <span class="mt-4 block h-[3px] w-16 bg-tertiary" aria-hidden="true"></span>
    <?php if (!empty($phSubtitle)): ?>
      <p class="mt-5 max-w-2xl leading-relaxed <?= $phDark ? 'text-white/75' : 'text-neutral/70' ?>"><?= e($phSubtitle) ?></p>
    <?php endif; ?>
  </div>
</section>