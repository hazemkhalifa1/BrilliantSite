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
    ['n' => '01', 't' => localized($locale, 'Trusted Delivery', 'تسليم موثوق'), 'd' => localized($locale, 'Every engagement is a partnership — on time, on spec, no surprises.', 'كل مشروع شراكة — في الوقت المحدد ووفق المواصفات ودون مفاجآت.')],
    ['n' => '02', 't' => localized($locale, 'Technical Rigor', 'دقة تقنية'), 'd' => localized($locale, 'Engineering discipline behind every decision, from sketch to handover.', 'انضباط هندسي خلف كل قرار، من المخطط إلى التسليم.')],
    ['n' => '03', 't' => localized($locale, 'Long-Term Partners', 'شركاء طويلو الأمد'), 'd' => localized($locale, 'Clients who start once stay for the next project — and the one after.', 'العملاء الذين يبدأون معنا يبقون للمشروع التالي — والذي يليه.')],
];

$phEyebrow = $c['eyebrow'];
$phTitle = $c['title'];
$phSubtitle = $c['subtitle'];
$phBackground = 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80';
$phCenter = true;
$phParallax = true;
$phOverlay = 'gradient';
require base_path('/views/partials/page-header.php');
?>
<div>
  <!-- Clients grid -->
  <section class="py-16 md:py-24">
    <div class="container-brilliant">
      <?php if (count($items) > 0): ?>
        <div class="neo-clients">
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
          <svg class="neo-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="40" height="40" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
          <p class="neo-empty__title"><?= e($c['updating']) ?></p>
          <p class="neo-empty__text"><?= e($c['emptySubtitle'] ?? '') ?></p>
          <a href="<?= e(lnk('/contact')) ?>" class="btn-primary mt-1"><?= e(t('nav.getQuote')) ?></a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Why partner -->
  <section class="border-t-4 border-black bg-surface py-16 md:py-24">
    <div class="container-brilliant grid gap-10 lg:grid-cols-12">
      <div class="lg:col-span-4">
        <span class="neo-badge reveal-on-scroll"><?= e(localized($locale, 'Why Brilliant', 'لماذا بريليانت')) ?></span>
        <h2 class="mt-6 font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight text-neutral md:text-5xl reveal-on-scroll"><?= e(localized($locale, 'Built to be relied on', 'مبنية لتُعتمد عليها')) ?></h2>
      </div>
      <div class="lg:col-span-8">
        <div class="neo-pillars">
          <?php foreach ($pillars as $i => $p): ?>
            <div class="neo-pillar reveal-on-scroll" style="transition-delay: <?= $i * 80 ?>ms">
              <div class="neo-pillar__num"><?= e($p['n']) ?></div>
              <div class="neo-pillar__title"><?= e($p['t']) ?></div>
              <p class="neo-pillar__text"><?= e($p['d']) ?></p>
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
        <h2 class="font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight md:text-5xl"><?= e(localized($locale, "Let's build together", 'لنبنِ معًا')) ?></h2>
        <p class="mt-4 max-w-xl text-white/70"><?= e(localized($locale, 'Partner with a contractor that delivers — safely, precisely, on time.', 'شارك مقاولًا يسلّم — بأمان ودقة وفي الوقت المحدد.')) ?></p>
      </div>
      <a href="<?= e(lnk('/contact')) ?>" class="btn-primary"><?= e(t('nav.getQuote')) ?></a>
    </div>
  </section>
</div>