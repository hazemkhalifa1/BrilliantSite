<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$a = tarr('about');
$GLOBALS['page_title'] = $a['metadataTitle'];
$GLOBALS['meta_description'] = $a['metadataDescription'];

// data: find founder from active team (matches /team?onlyActive=true&pageSize=50)
$teamItems = Repo::team(true, 1, 50)['items'];
$founder = null;
foreach ($teamItems as $member) {
    if (mb_stripos((string)($member['name'] ?? ''), 'ayman') !== false) { $founder = $member; break; }
}
if ($founder === null && count($teamItems)) $founder = $teamItems[0];

$initials = function (?string $name): string {
    if ($name === null || trim($name) === '') return '?';
    $parts = array_values(array_filter(preg_split('/\s+/u', trim($name))));
    $parts = array_slice($parts, 0, 2);
    $out = '';
    foreach ($parts as $p) { $out .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8'); }
    return $out;
};

// next-intl t.rich: escape text, wrap <strong> with class
$rich = function (string $text): string {
    $parts = preg_split('/(<strong>.*?<\/strong>)/s', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $out = '';
    foreach ($parts as $part) {
        if (preg_match('/^<strong>(.*?)<\/strong>$/s', $part, $m)) {
            $out .= '<strong class="text-[#111C2D]">' . e($m[1]) . '</strong>';
        } else {
            $out .= e($part);
        }
    }
    return $out;
};

$icons = [
    'factory'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M17 18h1"/><path d="M12 18h1"/><path d="M7 18h1"/></svg>',
    'building'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>',
    'warehouse'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M22 8.35V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8.35"/><path d="M2 8.35 12 4l10 4.35"/><path d="M6 12h12"/><path d="M6 16h12"/><path d="M6 20h12"/></svg>',
    'bird'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M16 7h.01"/><path d="M3.4 18H12a8 8 0 0 0 8-8V7a4 4 0 0 0-7.28-2.3L2 20"/><path d="m20 7 2 .5-2 .5"/><path d="M10 18v3"/><path d="M14 17.75V21"/><path d="M7 18a6 6 0 0 0 3.84-10.61"/></svg>',
    'briefcase'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>',
    'target'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
    'gem'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><path d="M6 3h12l4 6-10 13L2 9Z"/><path d="M11 3 8 9l4 13 4-13-3-6"/><path d="M2 9h20"/></svg>',
    'handshake'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/></svg>',
    'arrowright' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>',
    'quote'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto h-12 w-12 text-[#0059BB]"><path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/><path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/></svg>',
];

$INDUSTRIES = [
    ['icon' => $icons['factory'],   'title' => $a['industryManufacturing']],
    ['icon' => $icons['building'],  'title' => $a['industryCommercial']],
    ['icon' => $icons['warehouse'], 'title' => $a['industryWarehousing']],
    ['icon' => $icons['bird'],      'title' => $a['industryPoultry']],
    ['icon' => $icons['briefcase'], 'title' => $a['industryOffices']],
];

$PILLARS = [
    ['icon' => $icons['target'],    'title' => $a['pillarPrecisionTitle'],   'text' => $a['pillarPrecisionText']],
    ['icon' => $icons['gem'],       'title' => $a['pillarInnovationTitle'],  'text' => $a['pillarInnovationText']],
    ['icon' => $icons['handshake'], 'title' => $a['pillarPartnershipTitle'], 'text' => $a['pillarPartnershipText']],
];

$STATS = [
    ['value' => '2009', 'label' => $a['statEstablished']],
    ['value' => '74+',  'label' => $a['statClients']],
    ['value' => '24+',  'label' => $a['statExperience']],
    ['value' => '100%', 'label' => $a['statTurnkey']],
];

$gridStyle = 'background-image: linear-gradient(to right, rgba(17,28,45,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(17,28,45,0.05) 1px, transparent 1px); background-size: 40px 40px;';
?>
<div>
  <!-- Hero -->
  <section class="relative flex min-h-[520px] items-center overflow-hidden bg-[#111C2D]">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="absolute inset-0 bg-[#111C2D]/80"></div>
    <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(to right, rgba(255,255,255,0.06) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.06) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="container-brilliant relative z-10 py-24">
      <div class="max-w-3xl">
        <span class="mb-6 inline-block bg-[#BA1A1A] px-4 py-1 font-headline text-sm uppercase tracking-tighter text-white"><?= e($a['heroEyebrow']) ?></span>
        <h1 class="font-headline text-6xl uppercase italic leading-tight tracking-tighter text-white md:text-7xl"><?= e($a['heroTitle']) ?> <span class="not-italic text-[#BA1A1A]"><?= e($a['heroAccent']) ?></span></h1>
        <p class="mt-8 max-w-xl border-s-4 border-[#0059BB] ps-8 text-lg leading-relaxed text-slate-300"><?= e($a['heroSubtitle']) ?></p>
      </div>
    </div>
    <div class="absolute bottom-12 end-12 hidden font-headline text-4xl uppercase tracking-widest text-[#0059BB] opacity-70 lg:block"><?= e($a['est']) ?></div>
  </section>

  <!-- Who We Are -->
  <section class="bg-white py-24 md:py-32">
    <div class="container-brilliant">
      <div class="reveal-on-scroll">
        <div class="flex items-center gap-4">
          <div class="h-1 w-12 bg-[#0059BB]"></div>
          <span class="font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D]"><?= e($a['whoWeAre']) ?></span>
        </div>
        <h2 class="mt-6 font-headline text-4xl uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl"><?= e($a['whoWeAreTitle1']) ?> <span class="italic text-[#BA1A1A]"><?= e($a['whoWeAreTitleAccent']) ?></span></h2>
      </div>
      <div class="reveal-on-scroll mt-10 space-y-6 text-lg leading-relaxed text-[#4C4546]" style="transition-delay: 100ms">
        <p><?= e($a['p1']) ?></p>
        <p><?= $rich($a['p2']) ?></p>
        <p><?= $rich($a['p3']) ?></p>
      </div>
    </div>
  </section>

  <!-- Industries We Serve -->
  <section class="border-y-2 border-[#111C2D] bg-[#F9F9FF] py-24 md:py-32" style="<?= $gridStyle ?>">
    <div class="container-brilliant">
      <div class="reveal-on-scroll">
        <div class="mx-auto mb-16 max-w-2xl text-center">
          <h2 class="font-headline text-4xl uppercase tracking-tighter text-[#111C2D] md:text-5xl"><?= e($a['industries']) ?> <span class="italic text-[#BA1A1A]"><?= e($a['industriesAccent']) ?></span></h2>
          <div class="mx-auto mt-6 h-1 w-24 bg-[#0059BB]"></div>
          <p class="mt-6 text-lg text-[#4C4546]"><?= e($a['industriesSubtitle']) ?></p>
        </div>
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($INDUSTRIES as $index => $industry): ?>
          <div class="reveal-on-scroll" style="transition-delay: <?= $index * 80 ?>ms">
            <div class="group flex h-full flex-col border-2 border-[#111C2D] bg-white p-8 transition-transform duration-300 hover:-translate-y-2">
              <div class="mb-6 flex h-14 w-14 items-center justify-center bg-[#0059BB] text-white transition-colors group-hover:bg-[#BA1A1A]"><?= $industry['icon'] ?></div>
              <h3 class="font-headline text-xl font-bold uppercase leading-snug tracking-tighter text-[#111C2D]"><?= e($industry['title']) ?></h3>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="flex h-full flex-col justify-center border-2 border-[#BA1A1A] bg-[#BA1A1A] p-8">
          <h3 class="font-headline text-xl font-bold uppercase leading-snug tracking-tighter text-white"><?= e($a['andMore']) ?></h3>
          <a href="<?= e(lnk('/contact')) ?>" class="mt-6 inline-flex items-center gap-2 font-headline text-sm font-bold uppercase tracking-widest text-white transition-opacity hover:opacity-80"><?= e($a['discussProject']) ?> <?= $icons['arrowright'] ?></a>
        </div>
      </div>
    </div>
  </section>

  <!-- What Sets Us Apart -->
  <section class="relative bg-[#111C2D] py-24 md:py-32">
    <div class="absolute inset-0 bg-cover bg-center opacity-25" style="background-image: url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="absolute inset-0 bg-[#111C2D]/75"></div>
    <div class="container-brilliant relative z-10">
      <div class="reveal-on-scroll">
        <div class="mb-16 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
          <div>
            <div class="flex items-center gap-4">
              <div class="h-1 w-12 bg-[#0059BB]"></div>
              <span class="font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($a['apartEyebrow']) ?></span>
            </div>
            <h2 class="mt-6 font-headline text-4xl uppercase leading-tight tracking-tighter text-white md:text-5xl"><?= e($a['apartTitle1']) ?> <span class="italic text-[#BA1A1A]"><?= e($a['apartTitleAccent']) ?></span><?= e($a['apartTitle2']) ?></h2>
          </div>
        </div>
      </div>
      <div class="reveal-on-scroll" style="transition-delay: 100ms">
        <p class="mb-14 max-w-3xl text-lg leading-relaxed text-slate-300"><?= e($a['apartText']) ?></p>
      </div>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <?php foreach ($PILLARS as $index => $pillar): ?>
          <div class="reveal-on-scroll" style="transition-delay: <?= $index * 100 ?>ms">
            <div class="group flex h-full flex-col border-2 border-white/20 bg-white/5 p-10 transition-colors hover:border-[#BA1A1A]">
              <div class="mb-8 flex h-16 w-16 items-center justify-center bg-[#0059BB] text-white"><?= $pillar['icon'] ?></div>
              <h3 class="font-headline text-2xl uppercase italic tracking-tighter text-white"><?= e($pillar['title']) ?></h3>
              <p class="mt-4 leading-relaxed text-slate-300"><?= e($pillar['text']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Stats + Mission -->
  <section class="bg-white py-24 md:py-32">
    <div class="container-brilliant">
      <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <?php foreach ($STATS as $index => $stat): ?>
          <div class="reveal-on-scroll" style="transition-delay: <?= $index * 80 ?>ms">
            <div class="border-2 border-[#111C2D] bg-white p-8 text-center shadow-[4px_4px_0px_0px_#111C2D]">
              <p class="font-headline text-4xl font-bold md:text-5xl <?= $index === count($STATS) - 1 ? 'text-[#BA1A1A]' : 'text-[#0059BB]' ?>"><span dir="ltr"><?= e($stat['value']) ?></span></p>
              <p class="mt-2 font-headline text-xs font-bold uppercase tracking-widest text-[#4C4546]"><?= e($stat['label']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="reveal-on-scroll" style="transition-delay: 150ms">
        <div class="mx-auto mt-24 max-w-4xl text-center">
          <?= $icons['quote'] ?>
          <h2 class="mt-8 font-headline text-4xl uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl"><?= e($a['mission']) ?></h2>
          <p class="mt-8 text-2xl font-light italic leading-relaxed text-[#4C4546]"><?= e($a['missionText']) ?></p>
        </div>
      </div>
    </div>
  </section>

  <!-- Founder -->
  <section class="border-y-2 border-[#111C2D] bg-[#F9F9FF] py-24 md:py-32">
    <div class="container-brilliant">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <div class="reveal-on-scroll">
          <div class="relative overflow-hidden border-2 border-[#111C2D] bg-[#111C2D] text-white shadow-[6px_6px_0px_0px_#0059BB]">
            <div class="absolute right-4 top-4 font-headline text-8xl italic leading-none text-white opacity-10"><?= e($founder ? $initials(localized($locale, $founder['name'], $founder['nameAr'] ?? null)) : 'AG') ?></div>
            <div class="relative z-10 flex flex-col md:flex-row">
              <?php if ($founder && !empty($founder['imagePath'])): ?>
                <div class="w-full shrink-0 md:w-72">
                  <img src="<?= e(Repo::getImagePath($founder, 'imagePath')) ?>" alt="<?= e(localized($locale, $founder['name'], $founder['nameAr'] ?? null)) ?>" loading="lazy" class="h-64 w-full object-cover md:h-full" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                  <div style="display:none" class="h-64 w-full items-center justify-center bg-[#0059BB] font-headline text-6xl font-bold text-white md:h-full"><?= e($founder ? $initials(localized($locale, $founder['name'], $founder['nameAr'] ?? null)) : 'AG') ?></div>
                </div>
              <?php endif; ?>
              <div class="p-8 md:p-12">
                <h3 class="font-headline text-4xl uppercase leading-tight tracking-tighter"><?= e($founder ? localized($locale, $founder['name'], $founder['nameAr'] ?? null) : $a['founderName']) ?></h3>
                <p class="mt-4 font-headline text-sm font-bold uppercase tracking-widest text-[#BA1A1A]"><?= e($founder ? localized($locale, $founder['jobTitle'], $founder['jobTitleAr'] ?? null) : $a['founderTitle']) ?></p>
                <p class="mt-6 leading-relaxed text-slate-300"><?= e($a['founderText']) ?></p>
              </div>
            </div>
          </div>
        </div>
        <div class="reveal-on-scroll" style="transition-delay: 100ms">
          <div>
            <div class="flex items-center gap-4">
              <div class="h-1 w-12 bg-[#0059BB]"></div>
              <span class="font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D]"><?= e($a['leadership']) ?></span>
            </div>
            <h2 class="mt-6 font-headline text-4xl uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl"><?= e($a['leadershipTitle1']) ?> <span class="italic text-[#BA1A1A]"><?= e($a['leadershipTitleAccent']) ?></span></h2>
            <p class="mt-6 text-lg leading-relaxed text-[#4C4546]"><?= e($a['leadershipText']) ?></p>
            <a href="<?= e(lnk('/team')) ?>" class="mt-10 inline-flex items-center gap-2 border-2 border-[#0059BB] px-10 py-3 font-headline text-sm font-bold uppercase tracking-widest text-[#0059BB] transition-all hover:-translate-x-1 hover:-translate-y-1 hover:bg-[#0059BB] hover:text-white hover:shadow-[4px_4px_0px_0px_#111C2D]"><?= e($a['viewFullTeam']) ?> <?= $icons['arrowright'] ?></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="relative py-40" style="<?= $gridStyle ?>">
    <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="container-brilliant relative z-10 text-center">
      <div class="reveal-on-scroll">
        <h2 class="font-headline text-5xl uppercase italic leading-none tracking-tighter text-[#111C2D] md:text-7xl"><?= e($a['ctaTitle1']) ?> <span class="not-italic text-[#BA1A1A]"><?= e($a['ctaTitleAccent']) ?></span></h2>
        <p class="mx-auto mt-8 max-w-2xl text-xl leading-relaxed text-[#4C4546]"><?= e($a['ctaText']) ?></p>
        <div class="mt-12 flex flex-wrap justify-center gap-8">
          <a href="<?= e(lnk('/contact')) ?>" class="bg-[#BA1A1A] px-12 py-5 font-headline text-sm uppercase tracking-widest text-white transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#111C2D]"><?= e($a['startConsultation']) ?></a>
          <a href="<?= e(lnk('/services')) ?>" class="border-2 border-[#0059BB] px-12 py-5 font-headline text-sm uppercase tracking-widest text-[#0059BB] transition-all hover:bg-[#0059BB] hover:text-white"><?= e($a['exploreServices']) ?></a>
        </div>
      </div>
    </div>
  </section>
</div>
