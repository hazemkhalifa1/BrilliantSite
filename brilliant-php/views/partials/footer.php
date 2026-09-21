<?php
use App\Services\Repo;
$footer = tarr('footer');
$nav = tarr('nav');
$locale = current_locale();
$contact = Repo::contact();
$quickLinks = [
  ['label' => $nav['home'], 'href' => '/'],
  ['label' => $nav['about'], 'href' => '/about'],
  ['label' => $nav['services'], 'href' => '/services'],
  ['label' => $nav['projects'], 'href' => '/projects'],
  ['label' => $nav['products'], 'href' => '/products'],
  ['label' => $nav['blog'], 'href' => '/blog'],
  ['label' => $nav['contact'], 'href' => '/contact'],
];
$resourceLinks = [
  ['label' => $footer['team'], 'href' => '/team'],
  ['label' => $footer['clients'], 'href' => '/clients'],
];
if (!function_exists('flink')) { function flink(string $href): string { return lnk($href === '/' ? '' : $href); } }
$address = $contact && localized($locale, $contact['address'] ?? '', $contact['addressAr'] ?? null) ? localized($locale, $contact['address'] ?? '', $contact['addressAr'] ?? null) : $footer['contactPrompt'];
?>
<footer class="border-t-4 border-[#BA1A1A] bg-[#111C2D] py-20">
  <div class="container-brilliant grid grid-cols-1 gap-8 md:grid-cols-4 lg:gap-12">
    <div class="flex max-w-sm flex-col items-start gap-8">
      <img src="<?= asset('/img/logo.png') ?>" alt="Brilliant Engineering" class="h-16 w-auto">
      <p class="text-white/60"><?= e($footer['description']) ?></p>
    </div>
    <div class="space-y-6">
      <h4 class="font-headline text-xs font-bold uppercase tracking-[0.3em] text-[#BA1A1A]"><?= e($footer['company']) ?></h4>
      <div class="flex flex-col gap-4">
        <?php foreach (array_slice($quickLinks, 0, 4) as $link): ?>
          <a href="<?= e(flink($link['href'])) ?>" class="text-sm text-white/60 transition-colors hover:text-[#BA1A1A]"><?= e($link['label']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="space-y-6">
      <h4 class="font-headline text-xs font-bold uppercase tracking-[0.3em] text-[#BA1A1A]"><?= e($footer['portal']) ?></h4>
      <div class="flex flex-col gap-4">
        <?php foreach (array_slice($quickLinks, 4) as $link): ?>
          <a href="<?= e(flink($link['href'])) ?>" class="text-sm text-white/60 transition-colors hover:text-[#BA1A1A]"><?= e($link['label']) ?></a>
        <?php endforeach; ?>
        <?php foreach ($resourceLinks as $link): ?>
          <a href="<?= e(flink($link['href'])) ?>" class="text-sm text-white/60 transition-colors hover:text-[#BA1A1A]"><?= e($link['label']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="space-y-6">
      <h4 class="font-headline text-xs font-bold uppercase tracking-[0.3em] text-[#BA1A1A]"><?= e($footer['contact']) ?></h4>
      <div class="flex flex-col gap-4 text-sm text-white/60">
        <p class="whitespace-pre-line leading-relaxed"><?= e($address) ?></p>
        <?php if (!empty($contact['phone1'])): ?><a href="tel:<?= e($contact['phone1']) ?>" class="transition-colors hover:text-[#BA1A1A]"><?= e($contact['phone1']) ?></a><?php endif; ?>
        <?php if (!empty($contact['phone2'])): ?><a href="tel:<?= e($contact['phone2']) ?>" class="transition-colors hover:text-[#BA1A1A]"><?= e($contact['phone2']) ?></a><?php endif; ?>
        <?php if (!empty($contact['email'])): ?><a href="mailto:<?= e($contact['email']) ?>" class="transition-colors hover:text-[#BA1A1A]"><?= e($contact['email']) ?></a><?php endif; ?>
        <?php if (!empty($contact['email2'])): ?><a href="mailto:<?= e($contact['email2']) ?>" class="transition-colors hover:text-[#BA1A1A]"><?= e($contact['email2']) ?></a><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="mt-20 border-t border-white/10">
    <div class="container-brilliant flex flex-col items-center justify-between gap-6 pt-10 md:flex-row">
      <div class="font-headline text-xs uppercase tracking-widest text-white/50"><?= e(str_replace('{year}', (string)date('Y'), $footer['rights'])) ?></div>
      <div class="font-headline text-xs italic uppercase tracking-[0.5em] text-[#BA1A1A]"><?= e($footer['tagline']) ?></div>
    </div>
  </div>
</footer>