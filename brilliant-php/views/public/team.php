<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$t = tarr('team');
$GLOBALS['page_title'] = $t['metadataTitle'];
$GLOBALS['meta_description'] = $t['metadataDescription'];

$items = Repo::team(true, 1, 100)['items'];
$featured = $items[0] ?? null;
$supporting = array_slice($items, 1);
$tagline = t('footer.tagline');

$initials = function (?string $name): string {
    if ($name === null || trim($name) === '') return '?';
    $parts = array_values(array_filter(preg_split('/\s+/u', trim($name))));
    $parts = array_values(array_filter($parts, fn($p) => !preg_match('/\.$/u', $p)));
    $list = count($parts) > 0 ? $parts : array_values(array_filter(preg_split('/\s+/u', trim($name))));
    $first = mb_substr($list[0] ?? '', 0, 1, 'UTF-8');
    $last = count($list) > 1 ? mb_substr($list[count($list) - 1], 0, 1, 'UTF-8') : '';
    return mb_strtoupper($first . $last, 'UTF-8');
};

$memberName = fn(array $m): string => localized($locale, $m['name'], $m['nameAr'] ?? null);
$memberRole = fn(array $m): string => localized($locale, $m['jobTitle'], $m['jobTitleAr'] ?? null);
$memberDesc = fn(array $m): string => localized($locale, $m['description'] ?? '', $m['descriptionAr'] ?? null);

$renderMedia = function (array $member) use ($initials, $locale): void {
    $name = localized($locale, $member['name'], $member['nameAr'] ?? null);
    $src = Repo::getImagePath($member, 'imagePath');
    if (!empty($member['imagePath'])): ?>
      <img src="<?= e($src) ?>" alt="<?= e($name) ?>" loading="lazy"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
      <div class="neo-fallback" style="display:none"><?= e($initials($name)) ?></div>
    <?php else: ?>
      <div class="neo-fallback"><?= e($initials($name)) ?></div>
    <?php endif;
};

$pillars = [
    ['n' => '01', 't' => $t['pillarPrecisionTitle'], 'd' => $t['pillarPrecisionText']],
    ['n' => '02', 't' => $t['pillarDeliveryTitle'], 'd' => $t['pillarDeliveryText']],
    ['n' => '03', 't' => $t['pillarSafetyTitle'], 'd' => $t['pillarSafetyText']],
];
?>
<div class="relative">
  <div data-parallax="0.08" class="pointer-events-none absolute inset-0 -z-10">
    <div aria-hidden="true" class="graph-grid h-full w-full opacity-70"></div>
  </div>

  <!-- Hero -->
  <section class="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
    <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px); background-size: 32px 32px;"></div>
    <div class="absolute inset-0">
      <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" data-parallax="0.08" style="background-image: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80');"></div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50"></div>

    <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <span class="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($t['eyebrow']) ?></span>
      <h1 class="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl"><?= e($t['title']) ?></h1>
      <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl"><?= e($t['subtitle']) ?></p>
    </div>
  </section>

  <!-- Team -->
  <section class="py-16 md:py-24">
    <div class="container-brilliant space-y-12 md:space-y-16">
      <?php if (count($items) === 0): ?>
        <div class="neo-empty"><?= e($t['updating']) ?></div>
      <?php else: ?>

        <?php if ($featured): ?>
          <div class="neo-featured reveal-on-scroll" data-from="left">
            <div class="neo-featured__media">
              <?php $renderMedia($featured); ?>
            </div>
            <div class="neo-featured__info">
              <span class="neo-featured__index"><?= e(localized($locale, 'Leadership', 'القيادة')) ?></span>
              <span class="neo-featured__monogram" aria-hidden="true"><?= e($initials($memberName($featured))) ?></span>
              <span class="neo-featured__role"><?= e($memberRole($featured)) ?></span>
              <h2 class="neo-featured__name"><?= e($memberName($featured)) ?></h2>
              <?php if ($memberDesc($featured) !== ''): ?>
                <p class="neo-featured__desc"><?= e($memberDesc($featured)) ?></p>
              <?php endif; ?>
              <p class="monospaced" style="position:relative;margin-top:2rem;font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.45)"><?= e($tagline) ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?php if (count($supporting) > 0): ?>
          <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($supporting as $i => $member): ?>
              <div class="pop-card h-full reveal-on-scroll" style="transition-delay: <?= $i * 80 + 120 ?>ms" data-from="<?= $i % 2 === 0 ? 'left' : 'right' ?>">
                <article class="neo-team-card">
                  <div class="neo-team-card__media">
                    <?php $renderMedia($member); ?>
                  </div>
                  <div class="neo-team-card__body">
                    <div class="neo-team-card__role"><?= e($memberRole($member)) ?></div>
                    <h3 class="neo-team-card__name"><?= e($memberName($member)) ?></h3>
                    <?php if ($memberDesc($member) !== ''): ?>
                      <p class="neo-team-card__desc"><?= e($memberDesc($member)) ?></p>
                    <?php endif; ?>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </section>

  <!-- How we work -->
  <section class="border-t-4 border-black bg-surface py-16 md:py-24">
    <div class="container-brilliant grid gap-10 lg:grid-cols-12">
      <div class="lg:col-span-4">
        <span class="neo-badge reveal-on-scroll"><?= e($t['pillarsEyebrow']) ?></span>
        <h2 class="mt-6 font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight text-neutral md:text-5xl reveal-on-scroll"><?= e($t['pillarsTitle']) ?></h2>
      </div>
      <div class="lg:col-span-8">
        <div class="neo-pillars">
          <?php foreach ($pillars as $i => $p): ?>
            <div class="reveal-on-scroll" style="transition-delay: <?= $i * 80 ?>ms" data-from="<?= $i % 2 === 0 ? 'left' : 'right' ?>">
              <div class="h-full transition-transform duration-300 hover:-translate-y-1.5">
                <div class="pop-card" style="transition-delay: <?= $i * 80 + 120 ?>ms">
                  <div class="neo-pillar">
                    <div class="neo-pillar__num"><?= e($p['n']) ?></div>
                    <div class="neo-pillar__title"><?= e($p['t']) ?></div>
                    <p class="neo-pillar__text"><?= e($p['d']) ?></p>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="border-t-4 border-black bg-neutral py-16 text-white md:py-20">
    <div class="container-brilliant flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
      <div>
        <h2 class="font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight md:text-5xl"><?= e($t['ctaTitle']) ?></h2>
        <p class="mt-4 max-w-xl text-white/70"><?= e($t['ctaText']) ?></p>
      </div>
      <a href="<?= e(lnk('/contact')) ?>" class="btn-cta"><?= e(t('nav.getQuote')) ?></a>
    </div>
  </section>
</div>