<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$tp = tarr('productDetail');
$id = (int)($params['id'] ?? 0);
$product = Repo::product($id);

if (!$product) {
  $GLOBALS['page_title'] = $tp['notFound'];
  $GLOBALS['meta_description'] = $tp['notFound'];
?>
<div>
  <section class="py-24">
    <div class="container-brilliant text-center">
      <h1 class="font-headline text-4xl font-bold uppercase text-neutral"><?= e($tp['notFound']) ?></h1>
      <span class="mt-4 block h-[3px] w-16 bg-[#BA1A1A]" aria-hidden="true"></span>
      <div class="mt-10">
        <a href="<?= e(lnk('/products')) ?>" class="inline-flex items-center gap-2 bg-[#BA1A1A] px-5 py-2.5 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-all hover:opacity-90"><?= e($tp['backToAll']) ?></a>
      </div>
    </div>
  </section>
</div>
<?php
  return;
}

$productNameEn = trim((string)($product['name'] ?? ''));
$productNameAr = trim((string)($product['nameAr'] ?? ''));
$brandName = trim((string)($product['brandName'] ?? ''));
$rawTitle = localized($locale, $productNameEn, $productNameAr !== '' ? $productNameAr : null);
if ($rawTitle === '') $rawTitle = $productNameEn !== '' ? $productNameEn : 'Product';
$GLOBALS['page_title'] = $rawTitle . ($brandName !== '' ? ' | ' . $brandName : '') . ' | Brilliant Engineering';
$rawDesc = localized($locale, $product['description'] ?? '', $product['descriptionAr'] ?? null);
$rawDesc = trim(strip_tags((string)$rawDesc));
if ($rawDesc === '') $rawDesc = $rawTitle;
$rawDesc = preg_replace('/\s+/', ' ', $rawDesc);
$rawDesc = mb_substr($rawDesc, 0, 150);
$GLOBALS['meta_description'] = $rawDesc . ' - Available in Egypt.';

$siteUrl = app_url();
$productName = localized($locale, $product['name'] ?? '', $product['nameAr'] ?? null);
$productDesc = localized($locale, $product['description'] ?? '', $product['descriptionAr'] ?? null);
$backHref = !empty($product['brandId']) ? lnk('/products') . '?brandId=' . $product['brandId'] : lnk('/products');
$backLabel = !empty($product['brandName']) ? t('productDetail.backToBrand', ['brand' => $product['brandName']]) : t('productDetail.backToAll');
$catHref = (!empty($product['brandId']) && !empty($product['categoryId']))
  ? lnk('/products') . '?brandId=' . $product['brandId'] . '&categoryId=' . $product['categoryId']
  : lnk('/products') . '?categoryId=' . $product['categoryId'];

$related = [];
if (!empty($product['brandId'])) {
  $rel = Repo::products($product['brandId'], null, true, null, 1, 12)['items'];
  foreach ($rel as $item) {
    if ($item['id'] !== $product['id']) { $related[] = $item; }
    if (count($related) >= 3) break;
  }
}

$arrowLeftSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>';
$fileTextLg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>';
$fileTextSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>';
$chevronRightSm = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>';
$boxSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-20 w-20 text-neutral/25" aria-hidden="true"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>';
$arrowRightSm = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';

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
<article>
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'Product',
  'name' => $productName,
  'description' => $productDesc ?: null,
  'image' => Repo::getImagePath($product, 'imagePath'),
  'category' => $product['categoryName'] ?? null,
  'brand' => !empty($product['brandName']) ? ['@type' => 'Brand', 'name' => $product['brandName']] : null,
  'url' => $siteUrl . '/products/' . $product['id'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<section class="relative overflow-hidden bg-[#111C2D] py-16 text-white md:py-20">
  <?php if (!empty($product['imagePath'])): ?>
    <div data-parallax="0.08" class="absolute inset-0" style="will-change:transform">
      <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= e(Repo::getImagePath($product, 'imagePath')) ?>')"></div>
    </div>
    <div class="absolute inset-0 bg-[#111C2D]/80"></div>
  <?php endif; ?>
  <div class="container-brilliant relative z-10">
    <a href="<?= e($backHref) ?>" class="inline-flex items-center gap-2 bg-[#BA1A1A] px-5 py-2.5 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-all hover:opacity-90"><?= $arrowLeftSvg ?><?= e($backLabel) ?></a>
    <div class="mt-8 flex flex-wrap items-center gap-2">
      <span class="bg-[#0059BB] border-[#0059BB] px-3 py-1 font-headline text-xs font-semibold uppercase tracking-widest text-white"><?= e($product['categoryName'] ?? t('products.badgeFallback')) ?></span>
      <?php if (!empty($product['brandName'])): ?>
        <span class="bg-[#BA1A1A] px-3 py-1 font-headline text-xs font-semibold uppercase tracking-widest text-white"><?= e($product['brandName']) ?></span>
      <?php endif; ?>
    </div>
    <h1 class="mt-5 max-w-3xl font-headline text-4xl font-bold uppercase leading-tight md:text-5xl"><?= e($productName) ?></h1>
    <span class="mt-5 block h-[3px] w-16 bg-[#BA1A1A]" aria-hidden="true"></span>
  </div>
</section>

<div class="relative" style="background-color:#ffffff;background-image:linear-gradient(to right, rgba(0,0,0,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.04) 1px, transparent 1px);background-size:40px 40px;">
  <div class="container-brilliant py-12 md:py-16">
    <nav aria-label="Breadcrumb" class="mb-10 inline-flex flex-wrap items-center gap-2 border-2 border-black bg-white px-4 py-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60 shadow-[4px_4px_0px_black]">
      <a href="<?= e(lnk('/products')) ?>" class="transition-colors hover:text-[#0059BB] hover:underline"><?= e(t('products.breadcrumb')) ?></a>
      <?php if (!empty($product['categoryName'])): ?>
        <?= $chevronRightSm ?>
        <a href="<?= e($catHref) ?>" class="transition-colors hover:text-[#0059BB] hover:underline"><?= e($product['categoryName']) ?></a>
      <?php endif; ?>
      <?= $chevronRightSm ?>
      <span class="text-neutral "><?= e($productName) ?></span>
    </nav>

    <div class="grid grid-cols-1 items-start gap-12 lg:grid-cols-2">
      <div class="group relative">
        <div class="relative z-10 border-4 border-black bg-white p-4 shadow-[6px_6px_0px_black]">
          <span class="absolute start-4 top-4 z-20 border-2 border-black bg-[#BA1A1A] px-3 py-1 font-headline text-[10px] font-bold uppercase tracking-widest text-white"><?= e($product['brandName'] ?? t('products.badgeFallback')) ?></span>
          <div class="relative aspect-[4/3] w-full overflow-hidden border-2 border-black bg-neutral-light">
            <?php if (!empty($product['imagePath'])): ?>
              <img src="<?= e(Repo::getImagePath($product, 'imagePath')) ?>" alt="<?= e($productName) ?>" loading="eager" class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105 group-hover:grayscale">
            <?php else: ?>
              <div class="flex h-full w-full items-center justify-center"><?= $boxSvg ?></div>
            <?php endif; ?>
          </div>
          <div class="absolute left-0 top-0 h-8 w-8 border-l-4 border-t-4 border-black" aria-hidden="true"></div>
          <div class="absolute bottom-0 right-0 h-8 w-8 border-b-4 border-r-4 border-black" aria-hidden="true"></div>
        </div>
      </div>

      <div class="flex flex-col gap-8">
        <div>
          <h1 class="mb-4 font-headline text-4xl font-bold uppercase leading-none text-neutral md:text-5xl"><?= e($productName) ?></h1>
          <p class="border-s-4 border-[#0059BB] ps-4 text-lg leading-relaxed text-neutral/70"><?= e(localized($locale, ($product['description'] ?? '') !== '' ? $product['description'] : t('productDetail.noDescription'), $product['descriptionAr'] ?? null)) ?></p>
          <?php if (!empty($product['relatedBlogPostId']) && !empty($product['relatedBlogSlug'])): ?>
            <a href="<?= e(lnk('/blog/' . Repo::canonicalSlug($product['relatedBlogSlug']))) ?>" class="mt-6 inline-block border-s-4 border-[#0059BB] bg-white px-5 py-3 text-sm font-semibold text-neutral shadow-[3px_3px_0px_0px_#0059BB] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[5px_5px_0px_0px_#BA1A1A]">
              <?= e($tp['relatedGuide']) ?> <span class="font-bold text-[#BA1A1A]"><?= e(localized($locale, $product['relatedBlogTitle'] ?? '', $product['relatedBlogTitleAr'] ?? null)) ?></span>
            </a>
          <?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="pop-card"><a href="<?= e($backHref) ?>" class="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]">
            <span class="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60"><?= e($tp['brand']) ?></span>
            <span class="font-headline text-lg font-bold uppercase text-neutral md:text-xl"><?= e($product['brandName'] ?? '—') ?></span>
          </a></div>
          <div class="pop-card"><div class="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]">
            <span class="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60"><?= e($tp['category']) ?></span>
            <span class="font-headline text-lg font-bold uppercase text-neutral md:text-xl"><?= e($product['categoryName'] ?? '—') ?></span>
          </div></div>
          <?php if (!empty($product['documentationUrl'])): ?>
            <div class="pop-card"><a href="<?= e(Repo::getImagePath($product, 'documentationUrl')) ?>" target="_blank" rel="noopener noreferrer" class="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]">
              <span class="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60"><?= e($tp['documentation']) ?></span>
              <span class="flex items-center gap-2 font-headline text-lg font-bold uppercase text-[#0059BB] md:text-xl"><?= $fileTextLg ?><?= e($tp['downloadDocs']) ?></span>
            </a></div>
          <?php else: ?>
            <div class="pop-card"><div class="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]">
              <span class="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60"><?= e($tp['documentation']) ?></span>
              <span class="font-headline text-lg font-bold uppercase text-neutral/40 md:text-xl"><?= e($tp['notAvailable']) ?></span>
            </div></div>
          <?php endif; ?>
          <div class="pop-card"><a href="<?= e(lnk('/products')) ?>" class="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]">
            <span class="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60"><?= e($tp['catalog']) ?></span>
            <span class="font-headline text-lg font-bold uppercase text-neutral md:text-xl"><?= e($tp['backToAll']) ?></span>
          </a></div>
        </div>
      </div>
    </div>

    <div class="my-12 w-full border-t-4 border-black" aria-hidden="true"></div>
  </div>

  <?php if (count($related) > 0): ?>
    <section class="bg-neutral-light py-16">
      <div class="container-brilliant">
        <h2 class="font-headline text-xl font-bold uppercase"><?= e(t('productDetail.moreFrom', ['brand' => $product['brandName'] ?? ''])) ?></h2>
        <span class="mt-2 block h-[3px] w-10 bg-[#0059BB]" aria-hidden="true"></span>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <?php foreach ($related as $index => $item): ?>
            <div class="reveal-on-scroll pop-card h-full" style="transition-delay: <?= ($index % 2) * 90 + 100 ?>ms"><?php $renderProductCard($item, $locale); ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>
</article>
