<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$tp = tarr('products');
$tf = tarr('productFilters');
$GLOBALS['page_title'] = $tp['metadataTitle'];
$GLOBALS['meta_description'] = $tp['metadataDescription'];

$brands = Repo::productBrands(true, 1, 100)['items'];
$categories = [];
foreach ($brands as $brand) {
  foreach (($brand['categories'] ?? []) as $cat) {
    $categories[] = $cat;
  }
}
$PAGE_SIZE = 12;

$brandId = (isset($_GET['brandId']) && $_GET['brandId'] !== '' && is_numeric($_GET['brandId'])) ? (int)$_GET['brandId'] : null;
$categoryId = (isset($_GET['categoryId']) && $_GET['categoryId'] !== '' && is_numeric($_GET['categoryId'])) ? (int)$_GET['categoryId'] : null;
$pageIndex = (isset($_GET['page']) && is_numeric($_GET['page'])) ? max(1, (int)$_GET['page']) : 1;

$selectedBrand = null;
foreach ($brands as $brand) {
  if ($brand['id'] === $brandId) { $selectedBrand = $brand; break; }
}
$isProductsView = (bool)($selectedBrand || $categoryId);

$makeUrl = function ($brandId, $categoryId, $page) {
  $qs = [];
  if ($brandId) $qs[] = 'brandId=' . $brandId;
  if ($categoryId) $qs[] = 'categoryId=' . $categoryId;
  if ($page > 1) $qs[] = 'page=' . $page;
  return $qs ? lnk('/products') . '?' . implode('&', $qs) : lnk('/products');
};

$checkboxClass = 'h-4 w-4 rounded-none border-2 border-black bg-white text-black focus:ring-0 focus:ring-offset-0 checked:bg-black';
$boxSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-16 w-16 text-neutral/25" aria-hidden="true"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>';
$arrowLeftSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>';
$arrowRightSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
$arrowRightSm = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
$chevronLeftSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m15 18-6-6 6-6"/></svg>';
$chevronRightSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m9 18 6-6-6-6"/></svg>';
$fileTextSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>';
$slidersSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-[#BA1A1A]" aria-hidden="true"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg>';

$renderProductCard = function ($product, $locale) use ($fileTextSvg, $arrowRightSm, $boxSvg) {
?>
<article class="brutalist-card group relative flex flex-col bg-white">
  <div class="absolute left-0 top-0 z-10 h-2 w-full border-b-2 border-black bg-[#BA1A1A]" aria-hidden="true"></div>
  <div class="relative aspect-square w-full overflow-hidden border-b-4 border-black bg-neutral-light">
    <?php if (!empty($product['imagePath'])): ?>
      <img src="<?= e(Repo::getImagePath($product, 'imagePath')) ?>" alt="<?= e(localized($locale, $product['name'], $product['nameAr'] ?? null)) ?>" loading="lazy" class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105">
    <?php else: ?>
      <div class="flex h-full w-full items-center justify-center"><?= $boxSvg ?></div>
    <?php endif; ?>
    <div class="absolute left-4 top-4 z-20 flex flex-col gap-2">
      <?php if (!empty($product['brandName'])): ?>
        <span class="border-2 border-black bg-[#0059BB] px-3 py-1 font-headline text-[10px] font-bold uppercase tracking-widest text-white"><?= e($product['brandName']) ?></span>
      <?php endif; ?>
      <?php if (!empty($product['categoryName'])): ?>
        <span class="flex items-center gap-1 border-2 border-black bg-white px-3 py-1 font-headline text-[10px] font-bold uppercase tracking-widest text-black"><?= e($product['categoryName']) ?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class="flex flex-1 flex-col p-6">
    <div class="mb-4 flex items-start justify-between gap-2">
      <h3 class="pr-2 font-headline text-xl font-bold uppercase leading-tight text-neutral"><?= e(localized($locale, $product['name'], $product['nameAr'] ?? null)) ?></h3>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 shrink-0 text-neutral transition-colors group-hover:text-[#0059BB]" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </div>
    <p class="mb-6 line-clamp-2 text-sm leading-relaxed text-neutral/70"><?= e(localized($locale, ($product['description'] ?? '') !== '' ? $product['description'] : t('productDetail.noDescription'), $product['descriptionAr'] ?? null)) ?></p>
    <div class="mt-auto flex flex-col gap-2">
      <?php if (!empty($product['relatedBlogPostId']) && !empty($product['relatedBlogSlug'])): ?>
        <a href="<?= e(lnk('/blog/' . Repo::canonicalSlug($product['relatedBlogSlug']))) ?>" class="flex w-full items-center justify-center gap-2 border-s-4 border-[#0059BB] bg-white py-3 font-headline text-xs font-bold uppercase tracking-widest text-neutral transition-colors hover:bg-[#0059BB] hover:text-white"><?= e(t('productDetail.relatedGuide')) ?> <?= e(localized($locale, $product['relatedBlogTitle'] ?? '', $product['relatedBlogTitleAr'] ?? null)) ?></a>
      <?php endif; ?>
      <a href="<?= e(lnk('/products/' . $product['id'])) ?>" class="flex w-full items-center justify-center gap-2 border-2 border-black bg-white py-3 font-headline text-xs font-bold uppercase tracking-widest text-neutral transition-colors hover:bg-[#0059BB] hover:text-white"><?= e(t('common.viewSpecs')) ?><?= $arrowRightSm ?></a>
      <?php if (!empty($product['documentationUrl'])): ?>
        <a href="<?= e(Repo::getImagePath($product, 'documentationUrl')) ?>" target="_blank" rel="noopener noreferrer" class="flex w-full items-center justify-center gap-2 border-2 border-[#BA1A1A] bg-[#BA1A1A] py-3 font-headline text-xs font-bold uppercase tracking-widest text-white transition-opacity hover:opacity-90"><?= $fileTextSvg ?><?= e(t('productDetail.downloadDocs')) ?></a>
      <?php endif; ?>
    </div>
  </div>
</article>
<?php
};
?>
<div>
<?php if (!$isProductsView): ?>
  <section class="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
    <div class="absolute inset-0" style="background-image:linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px);background-size:32px 32px;"></div>
    <div class="absolute inset-0">
      <div data-parallax="0.08" class="h-full w-full" style="will-change:transform">
        <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" style="background-image: url('<?= e('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80') ?>')"></div>
      </div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50"></div>

    <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <span class="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($tp['eyebrow']) ?></span>
      <h1 class="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl"><?= e($tp['title']) ?></h1>
      <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl"><?= e($tp['subtitle']) ?></p>
    </div>
  </section>
  <section class="py-16 md:py-24">
    <div class="container-brilliant">
      <?php if (count($brands) > 0): ?>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <?php foreach ($brands as $index => $brand): ?>
            <?php $brandImage = $brand['backgroundImagePath'] ?? null; ?>
            <div class="reveal-on-scroll pop-card h-full" style="transition-delay: <?= ($index % 2) * 70 + 100 ?>ms">
            <a href="<?= e($makeUrl($brand['id'], null, 1)) ?>" class="group block h-full">
              <div class="overflow-hidden border-2 border-black bg-white shadow-[4px_4px_0px_black] transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:border-[#BA1A1A] hover:shadow-[6px_6px_0px_#BA1A1A]">
                <div class="relative aspect-[16/10] bg-neutral-light">
                  <?php if ($brandImage): ?>
                    <img src="<?= e(Repo::getImagePath($brand, 'backgroundImagePath')) ?>" alt="<?= e(localized($locale, $brand['name'], $brand['nameAr'] ?? null)) ?>" loading="lazy" class="absolute inset-0 h-full w-full object-contain transition-transform duration-300 group-hover:scale-105">
                  <?php else: ?>
                    <div class="flex h-full w-full items-center justify-center"><?= $boxSvg ?></div>
                  <?php endif; ?>
                  <span class="absolute end-3 top-3 bg-[#0059BB] px-2 py-0.5 font-headline text-[10px] font-bold uppercase tracking-widest text-white"><?= e($tp['viewProducts']) ?></span>
                </div>
                <div class="p-4">
                  <h3 class="font-headline text-lg font-bold uppercase tracking-tight"><?= e(localized($locale, $brand['name'], $brand['nameAr'] ?? null)) ?></h3>
                  <?php if (!empty($brand['description'])): ?>
                    <p class="mt-1 line-clamp-2 text-sm text-neutral/70"><?= e(localized($locale, $brand['description'], $brand['descriptionAr'] ?? null)) ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-center text-neutral/60"><?= e($tp['noBrands']) ?></p>
      <?php endif; ?>
    </div>
  </section>
<?php else: ?>
  <?php
  $result = Repo::products($selectedBrand ? $selectedBrand['id'] : null, $categoryId, true, null, $pageIndex, $PAGE_SIZE);
  $products = $result['items'];
  $totalPages = max(1, (int)ceil($result['totalCount'] / $PAGE_SIZE));
  $paginationClass = 'flex h-12 w-12 items-center justify-center border-2 border-black bg-white text-neutral shadow-[4px_4px_0px_black] transition-all hover:bg-neutral hover:text-white active:translate-x-[2px] active:translate-y-[2px] active:shadow-none';
  $brandTitle = $selectedBrand ? localized($locale, $selectedBrand['name'], $selectedBrand['nameAr'] ?? null) : $tp['titleFallback'];
  $visibleCategories = $selectedBrand ? array_values(array_filter($categories, function ($c) use ($selectedBrand) { return $c['brandId'] === $selectedBrand['id']; })) : $categories;
  $qBrand = $selectedBrand ? $selectedBrand['id'] : null;
  ?>
  <section class="relative overflow-hidden bg-[#111C2D] py-24 text-white md:py-28">
    <div data-parallax="0.08" class="absolute inset-0" style="will-change:transform">
      <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')"></div>
    </div>
    <div class="absolute inset-0 bg-[#111C2D]/80"></div>
    <div class="container-brilliant relative z-10">
      <a href="<?= e(lnk('/products')) ?>" class="inline-flex items-center gap-2 bg-[#BA1A1A] px-5 py-2.5 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-all hover:opacity-90"><?= $arrowLeftSvg ?><?= e($tp['backToAll']) ?></a>
      <h1 class="mt-6 font-headline text-4xl font-bold uppercase tracking-tight md:text-5xl"><?= e($brandTitle) ?></h1>
      <?php if ($selectedBrand && !empty($selectedBrand['description'])): ?>
        <p class="mt-4 max-w-2xl text-lg text-white/70"><?= e(localized($locale, $selectedBrand['description'], $selectedBrand['descriptionAr'] ?? null)) ?></p>
      <?php endif; ?>
    </div>
  </section>

  <section class="py-12 md:py-16">
    <div class="container-brilliant">
      <div class="grid grid-cols-1 gap-8 md:grid-cols-12">
        <aside class="h-fit border-2 border-black bg-white p-6 shadow-[4px_4px_0px_black] md:col-span-3">
          <form method="get" action="<?= e(lnk('/products')) ?>">
            <h2 class="mb-6 flex items-center justify-between border-b-2 border-black pb-4 font-headline text-xl font-bold uppercase"><?= e($tf['filters']) ?><?= $slidersSvg ?></h2>
            <div class="mb-8">
              <h3 class="mb-4 flex items-center gap-2 font-headline text-xs font-bold uppercase tracking-widest text-neutral/60"><span class="inline-block h-2 w-2 bg-[#BA1A1A]" aria-hidden="true"></span><?= e($tf['categories']) ?></h3>
              <div class="space-y-3 font-headline text-xs font-semibold uppercase tracking-wide">
                <?php if (count($visibleCategories) > 0): ?>
                  <?php foreach ($visibleCategories as $c): ?>
                    <label class="group flex cursor-pointer items-center gap-3">
                      <input type="checkbox" name="categoryId" value="<?= e($c['id']) ?>" <?= $categoryId === $c['id'] ? 'checked' : '' ?> class="<?= e($checkboxClass) ?>">
                      <span class="text-neutral transition-colors group-hover:text-[#BA1A1A]"><?= e(localized($locale, $c['name'], $c['nameAr'] ?? null)) ?></span>
                    </label>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="text-xs font-normal normal-case text-neutral/50"><?= e($tf['noCategories']) ?></p>
                <?php endif; ?>
              </div>
            </div>
            <div class="mb-8">
              <h3 class="mb-4 flex items-center gap-2 font-headline text-xs font-bold uppercase tracking-widest text-neutral/60"><span class="inline-block h-2 w-2 bg-[#BA1A1A]" aria-hidden="true"></span><?= e($tf['brands']) ?></h3>
              <div class="space-y-3 font-headline text-xs font-semibold uppercase tracking-wide">
                <?php foreach ($brands as $b): ?>
                  <label class="group flex cursor-pointer items-center gap-3">
                    <input type="checkbox" name="brandId" value="<?= e($b['id']) ?>" <?= $selectedBrand && $selectedBrand['id'] === $b['id'] ? 'checked' : '' ?> class="<?= e($checkboxClass) ?>">
                    <span class="text-neutral transition-colors group-hover:text-[#BA1A1A]"><?= e(localized($locale, $b['name'], $b['nameAr'] ?? null)) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            <button type="submit" class="w-full border-2 border-black bg-black py-3 font-headline text-xs font-bold uppercase tracking-widest text-white transition-all hover:bg-[#BA1A1A] active:translate-x-[2px] active:translate-y-[2px]"><?= e($tf['apply']) ?></button>
          </form>
        </aside>

        <div class="md:col-span-9">
          <?php if (count($products) > 0): ?>
            <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-3">
              <?php foreach ($products as $product): ?><?php $renderProductCard($product, $locale); ?><?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="mt-12 text-center text-neutral/60"><?= e($tp['noProducts']) ?></p>
          <?php endif; ?>

          <?php if ($totalPages > 1): ?>
            <div class="mt-16 flex flex-wrap justify-center gap-4">
              <a href="<?= e($makeUrl($qBrand, $categoryId, $pageIndex - 1)) ?>" aria-label="<?= e($tp['prevPage']) ?>" class="<?= e($paginationClass) ?> <?= $pageIndex <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><?= $chevronLeftSvg ?></a>
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a href="<?= e($makeUrl($qBrand, $categoryId, $p)) ?>" <?= $p === $pageIndex ? 'aria-current="page"' : '' ?> class="<?= $p === $pageIndex ? 'flex h-12 w-12 items-center justify-center border-2 font-headline text-sm font-bold transition-all border-black bg-black text-white shadow-[4px_4px_0px_black]' : e($paginationClass) ?>"><?= e($p) ?></a>
              <?php endfor; ?>
              <a href="<?= e($makeUrl($qBrand, $categoryId, $pageIndex + 1)) ?>" aria-label="<?= e($tp['nextPage']) ?>" class="<?= e($paginationClass) ?> <?= $pageIndex >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><?= $chevronRightSvg ?></a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>
</div>
