<?php
/** expects: $phTitle, optional $phSubtitle, $phEyebrow, $phBackground
 *  optional flags (classic left-aligned hero is the default):
 *    $phCenter    (bool) center content + use Next.js hero sizing
 *    $phParallax  (bool) apply scroll parallax to the background image
 *    $phOverlay   ('flat'|'gradient') overlay style; 'gradient' matches Next.js
 */
$phDark = $phDark ?? true;
$phCenter = $phCenter ?? false;
$phParallax = $phParallax ?? false;
$phOverlay = $phOverlay ?? 'flat';
if ($phCenter) {
  $gridStyle = 'background-image:linear-gradient(to right,rgba(255,255,255,0.07) 1px,transparent 1px),linear-gradient(to bottom,rgba(255,255,255,0.07) 1px,transparent 1px);background-size:32px 32px;';
  $padding = 'pb-24 pt-32 md:pb-32 md:pt-48';
} else {
  $gridStyle = $phDark
    ? 'background-image:linear-gradient(to right,rgba(255,255,255,0.06) 1px,transparent 1px),linear-gradient(to bottom,rgba(255,255,255,0.06) 1px,transparent 1px);background-size:28px 28px;'
    : '';
  $padding = 'pb-16 pt-28 md:pb-24 md:pt-36';
}
?>
<section class="relative overflow-hidden <?= $padding ?> <?= $phDark ? 'bg-neutral text-white' : 'border-b-4 border-black bg-surface text-neutral' ?>">
  <div class="absolute inset-0 <?= $phDark ? '' : 'neo-blueprint' ?>" style="<?= e($gridStyle) ?>" aria-hidden="true"></div>
  <?php if (!empty($phBackground)): ?>
    <?php if ($phParallax): ?>
      <div class="absolute inset-0" data-parallax="0.08">
        <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" style="background-image: url('<?= e($phBackground) ?>')"></div>
      </div>
    <?php else: ?>
      <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= e($phBackground) ?>')"></div>
    <?php endif; ?>
    <?php if ($phOverlay === 'gradient'): ?>
      <div class="absolute inset-0" style="background-image: linear-gradient(to bottom, rgb(17 28 45 / 0.8) 0%, rgb(17 28 45 / 0.5) 100%);"></div>
    <?php else: ?>
      <div class="absolute inset-0 bg-neutral/85"></div>
    <?php endif; ?>
  <?php endif; ?>
  <div class="container-brilliant relative z-10 <?= $phCenter ? 'text-center' : '' ?>">
    <?php if (!empty($phEyebrow)): ?>
      <?php if ($phCenter): ?><span class="neo-badge mb-6"><?= e($phEyebrow) ?></span>
      <?php else: ?><span class="neo-badge"><?= e($phEyebrow) ?></span><?php endif; ?>
    <?php endif; ?>
    <h1 class="<?= $phCenter ? 'mx-auto max-w-4xl text-5xl md:text-7xl' : 'text-4xl md:text-6xl' ?> mt-6 font-headline font-bold uppercase leading-[0.95] tracking-tight"><?= e($phTitle) ?></h1>
    <span class="<?= $phCenter ? 'mx-auto' : '' ?> mt-6 block h-[6px] w-20 bg-tertiary shadow-[3px_3px_0_black]" aria-hidden="true"></span>
    <?php if (!empty($phSubtitle)): ?><p class="<?= $phCenter ? 'mx-auto text-slate-300' : ($phDark ? 'text-white/75' : 'text-neutral/70') ?> mt-6 max-w-2xl text-lg leading-relaxed"><?= e($phSubtitle) ?></p><?php endif; ?>
  </div>
</section>