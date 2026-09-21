<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$c = tarr('clients');
$GLOBALS['page_title'] = $c['metadataTitle'];
$GLOBALS['meta_description'] = $c['metadataDescription'];

$items = Repo::clients(true, 1, 100)['items'];

$initials = function (?string $name): string {
    if ($name === null || trim($name) === '') return '?';
    $parts = array_values(array_filter(preg_split('/\s+/u', trim($name))));
    $parts = array_slice($parts, 0, 2);
    $out = '';
    foreach ($parts as $p) { $out .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8'); }
    return $out;
};
$clientName = function (array $client) use ($locale): string {
    return localized($locale, $client['name'], $client['nameAr'] ?? null);
};

$pillars = [
    ['n' => '01', 't' => $c['pillarTrustedTitle'], 'd' => $c['pillarTrustedText']],
    ['n' => '02', 't' => $c['pillarRigorTitle'], 'd' => $c['pillarRigorText']],
    ['n' => '03', 't' => $c['pillarPartnersTitle'], 'd' => $c['pillarPartnersText']],
];
$heroImage = 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80';
?>
<div>
  <!-- Hero -->
  <section class="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
    <div class="absolute inset-0" style="background-image:linear-gradient(to right,rgba(255,255,255,0.07) 1px,transparent 1px),linear-gradient(to bottom,rgba(255,255,255,0.07) 1px,transparent 1px);background-size:32px 32px;" aria-hidden="true"></div>
    <div class="absolute inset-0">
      <div data-parallax="0.08" class="h-full w-full" style="will-change:transform">
        <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" style="background-image: url('<?= e($heroImage) ?>')"></div>
      </div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50"></div>

    <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <span class="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($c['eyebrow']) ?></span>
      <h1 class="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl"><?= e($c['title']) ?></h1>
      <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl"><?= e($c['subtitle']) ?></p>
    </div>
  </section>

  <!-- Clients grid -->
  <section class="py-16 md:py-24">
    <div class="container-brilliant">
      <?php if (count($items) > 0): ?>
        <div class="neo-clients reveal-on-scroll">
          <?php foreach ($items as $i => $client): $name = $clientName($client); ?>
            <div class="neo-client reveal-on-scroll" style="transition-delay: <?= ($i % 4) * 60 ?>ms">
              <span class="neo-client__index" aria-hidden="true"><?= e(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
              <?php if (!empty($client['logoPath'])): ?>
                <img class="neo-client__logo" src="<?= e(Repo::getImagePath($client, 'logoPath')) ?>" alt="<?= e($name) ?>" loading="lazy">
              <?php else: ?>
                <span class="neo-client__initials"><?= e($initials($name)) ?></span>
              <?php endif; ?>
              <span class="neo-client__name"><?= e($name) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="neo-empty reveal-on-scroll">
          <svg class="h-10 w-10 text-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
          <p class="font-headline text-2xl font-bold uppercase tracking-tight text-on-surface"><?= e($c['updating']) ?></p>
          <p class="mx-auto max-w-md text-sm leading-relaxed text-on-surface-variant"><?= e($c['emptySubtitle'] ?? '') ?></p>
          <a href="<?= e(lnk('/contact')) ?>" class="btn-cta mt-1"><?= e(t('nav.getQuote')) ?></a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Why partner -->
  <section class="border-t-4 border-black bg-surface py-16 md:py-24">
    <div class="container-brilliant grid gap-10 lg:grid-cols-12">
      <div class="lg:col-span-4">
        <span class="neo-badge reveal-on-scroll"><?= e($c['whyEyebrow'] ?? '') ?></span>
        <h2 class="mt-6 font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight text-neutral md:text-5xl reveal-on-scroll"><?= e($c['whyTitle'] ?? '') ?></h2>
      </div>
      <div class="lg:col-span-8">
        <div class="neo-pillars">
          <?php foreach ($pillars as $i => $p): ?>
            <div class="h-full reveal-on-scroll <?= $i % 2 === 0 ? 'reveal-from-left' : 'reveal-from-right' ?>" style="transition-delay: <?= $i * 80 ?>ms">
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
    <div class="container-brilliant reveal-on-scroll">
      <div class="flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
        <div>
          <h2 class="font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight md:text-5xl"><?= e($c['ctaTitle'] ?? '') ?></h2>
          <p class="mt-4 max-w-xl text-white/70"><?= e($c['ctaText'] ?? '') ?></p>
        </div>
        <a href="<?= e(lnk('/contact')) ?>" class="btn-cta"><?= e(t('nav.getQuote')) ?></a>
      </div>
    </div>
  </section>
</div>