<?php
use App\Services\Repo;

$locale = $locale ?? current_locale();
$s = tarr('services');
$sb = tarr('serviceBreakdown');
$GLOBALS['page_title'] = $s['metadataTitle'];
$GLOBALS['meta_description'] = $s['metadataDescription'];

$HERO_IMAGE = "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";
$FALLBACK_IMAGE = $HERO_IMAGE;
$heroGridStyle = "background-image:linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px);background-size:32px 32px;";
$surfaceGridStyle = "background-image:linear-gradient(to right, rgba(17,28,45,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(17,28,45,0.05) 1px, transparent 1px);background-size:32px 32px;";

$rawCategoryId = $_GET['categoryId'] ?? null;
$categoryId = null;
if ($rawCategoryId !== null && $rawCategoryId !== '' && is_numeric($rawCategoryId)) {
    $categoryId = (int)$rawCategoryId;
}

$categoryItems = Repo::serviceCategories(true, 1, 100)['items'] ?? [];
$serviceItems = Repo::services($categoryId, true, null, 1, 100)['items'] ?? [];

$activeCategory = null;
foreach ($categoryItems as $cat) {
    if ((int)$cat['id'] === $categoryId) { $activeCategory = $cat; break; }
}

$chipBase = "border-2 px-5 py-2 font-headline text-sm font-bold uppercase tracking-widest transition-colors";
$chipActive = "border-[#111C2D] bg-[#0059BB] text-white";
$chipIdle = "border-[#111C2D] text-[#111C2D] hover:bg-[#BA1A1A] hover:text-white";
$pad2 = fn(int $n): string => str_pad((string)$n, 2, '0', STR_PAD_LEFT);
?>
<div>
  <section class="relative overflow-hidden bg-[#111C2D] pb-24 pt-32 md:pb-32 md:pt-48">
    <div class="absolute inset-0" style="<?= e($heroGridStyle) ?>"></div>
    <div class="absolute inset-0">
      <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" style="background-image: url('<?= e($HERO_IMAGE) ?>')"></div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-[#111C2D]/80 to-[#111C2D]/50"></div>

    <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <span class="mb-6 inline-block bg-[#BA1A1A] px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e(str_replace('{year}', (string)date('Y'), $s['eyebrow'])) ?></span>
      <h1 class="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase italic leading-tight tracking-tighter text-white md:text-7xl">
        <?= e($s['title1']) ?> <span class="not-italic text-[#BA1A1A]"><?= e($s['titleAccent']) ?></span>
      </h1>
      <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl">
        <?= e($s['subtitle']) ?>
      </p>
      <a href="#core" class="inline-flex items-center gap-2 border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[6px_6px_0px_0px_#0059BB] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_#0059BB]">
        <?= e($s['exploreCapabilities']) ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
      </a>
    </div>
  </section>

  <?php if (count($serviceItems) > 0): ?>
  <section id="core" class="bg-white py-24 md:py-32">
    <div class="container-brilliant">
      <div class="mb-16 text-center">
        <h2 class="font-headline text-4xl font-bold uppercase tracking-tighter text-[#111C2D] md:text-5xl">
          <?= e($sb['title1']) ?> <span class="italic text-[#BA1A1A]"><?= e($sb['titleAccent']) ?></span>
        </h2>
        <div class="mx-auto mt-6 h-1 w-24 bg-[#0059BB]"></div>
        <p class="mx-auto mt-6 max-w-2xl text-lg text-[#4C4546]">
          <?= e($sb['subtitle']) ?>
        </p>
      </div>

      <?php if (count($categoryItems) > 0): ?>
      <div class="mb-16 flex flex-wrap justify-center gap-2">
        <a href="<?= e(lnk('/services')) ?>" class="<?= e($chipBase . ($categoryId === null ? ' ' . $chipActive : ' ' . $chipIdle)) ?>"><?= e($sb['allServices']) ?></a>
        <?php foreach ($categoryItems as $cat): ?>
          <a href="<?= e(lnk('/services?categoryId=' . $cat['id'])) ?>" class="<?= e($chipBase . ((int)$cat['id'] === $categoryId ? ' ' . $chipActive : ' ' . $chipIdle)) ?>"><?= e(localized($locale, $cat['name'], $cat['nameAr'] ?? null)) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($activeCategory && !empty($activeCategory['description'])): ?>
      <div class="mx-auto mb-16 max-w-2xl border-l-4 border-[#BA1A1A] bg-white p-6 shadow-[4px_4px_0px_0px_#111C2D]">
        <p class="font-headline text-sm font-bold uppercase tracking-widest text-[#BA1A1A]"><?= e(localized($locale, $activeCategory['name'], $activeCategory['nameAr'] ?? null)) ?></p>
        <p class="mt-2 text-lg leading-relaxed text-[#4C4546]"><?= e(localized($locale, $activeCategory['description'], $activeCategory['descriptionAr'] ?? null)) ?></p>
      </div>
      <?php endif; ?>

      <div class="space-y-24 md:space-y-32">
        <?php foreach ($serviceItems as $index => $service): ?>
          <div class="flex flex-col items-center gap-10 md:flex-row md:gap-16<?= $index % 2 === 1 ? ' md:flex-row-reverse' : '' ?>">
            <div class="reveal-on-scroll w-full md:w-1/2">
              <div class="relative overflow-hidden border-2 border-[#111C2D] bg-[#111C2D] shadow-[6px_6px_0px_0px_#BA1A1A]">
                <?php if (!empty($service['iconPath'])): ?>
                  <div class="h-72 w-full md:h-96">
                    <img src="<?= e(Repo::getImagePath($service, 'iconPath')) ?>" alt="<?= e(localized($locale, $service['title'], $service['titleAr'] ?? null)) ?>" loading="eager" class="h-full w-full object-cover">
                  </div>
                <?php else: ?>
                  <div class="h-72 w-full bg-cover bg-center opacity-40 mix-blend-luminosity md:h-96" style="background-image: url('<?= e($FALLBACK_IMAGE) ?>')"></div>
                <?php endif; ?>
                <div class="absolute bottom-4 left-4 flex items-center gap-2 border-2 border-[#111C2D] bg-[#111C2D] px-3 py-1.5 font-mono text-xs font-bold uppercase tracking-widest text-white">
                  <span class="inline-block h-2 w-2 bg-[#BA1A1A]" aria-hidden="true"></span>
                  SYS_ACTIVE // SRV_<?= e($pad2($index + 1)) ?>
                </div>
              </div>
            </div>

            <div class="reveal-on-scroll w-full md:w-1/2" style="transition-delay: 100ms">
              <span class="mb-4 inline-block border-2 border-[#111C2D] bg-[#0059BB] px-3 py-1 font-headline text-xs font-bold uppercase tracking-widest text-white"><?= e(str_replace('{number}', $pad2($index + 1), $sb['sector'])) ?></span>
              <?php if (!empty($service['categoryName'])): ?>
                <span class="mb-4 ml-2 inline-block border-2 border-[#111C2D] bg-[#BA1A1A] px-3 py-1 font-headline text-xs font-bold uppercase tracking-widest text-white"><?= e($service['categoryName']) ?></span>
              <?php endif; ?>
              <h2 class="font-headline text-4xl font-bold uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl"><?= e(localized($locale, $service['title'], $service['titleAr'] ?? null)) ?></h2>
              <p class="mt-6 text-lg leading-relaxed text-[#4C4546]"><?= e(localized($locale, $service['description'], $service['descriptionAr'] ?? null)) ?></p>
              <a href="<?= e(lnk('/contact')) ?>" class="mt-8 inline-flex items-center gap-2 bg-[#BA1A1A] px-8 py-3.5 font-headline text-sm font-bold uppercase tracking-widest text-white transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_#111C2D]">
                <?= e($sb['requestService']) ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php else: ?>
  <section class="bg-white py-24 md:py-32">
    <div class="container-brilliant text-center">
      <p class="text-lg text-[#4C4546]"><?= e($s['noServices']) ?></p>
    </div>
  </section>
  <?php endif; ?>

  <section class="bg-[#F9F9FF] py-24 md:py-32" style="<?= e($surfaceGridStyle) ?>">
    <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
      <div class="reveal-on-scroll">
        <div class="border-2 border-[#111C2D] bg-white p-10 text-center shadow-[6px_6px_0px_0px_#111C2D] md:p-14">
          <h2 class="font-headline text-4xl font-bold uppercase italic leading-none tracking-tighter text-[#111C2D] md:text-6xl">
            <?= e($s['ctaTitle1']) ?> <span class="not-italic text-[#BA1A1A]"><?= e($s['ctaTitleAccent']) ?></span>
          </h2>
          <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-[#4C4546]">
            <?= e($s['ctaText']) ?>
          </p>
          <a href="<?= e(lnk('/contact')) ?>" class="mt-10 inline-flex items-center gap-3 bg-[#BA1A1A] px-10 py-4 font-headline text-base font-bold uppercase tracking-widest text-white shadow-[4px_4px_0px_0px_#111C2D] transition-transform hover:-translate-x-1 hover:-translate-y-1">
            <?= e($s['initiateProtocol']) ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>
  </section>
</div>
