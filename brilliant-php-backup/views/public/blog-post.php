<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$tb = tarr('blogPost');
$slug = (string)($params['slug'] ?? '');
$post = Repo::blogPostBySlug($slug);

if (!$post) {
  $GLOBALS['page_title'] = $tb['notFound'];
  $GLOBALS['meta_description'] = $tb['notFound'];
?>
<div>
  <section class="py-24">
    <div class="container-brilliant text-center">
      <h1 class="font-headline text-4xl font-bold uppercase text-neutral"><?= e($tb['notFound']) ?></h1>
      <span class="mt-4 block h-[3px] w-16 bg-tertiary" aria-hidden="true"></span>
      <div class="mt-10">
        <a href="<?= e(lnk('/blog')) ?>" class="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-secondary transition-colors hover:text-tertiary"><?= e($tb['allPosts']) ?></a>
      </div>
    </div>
  </section>
</div>
<?php
  return;
}

$postTitle = localized($locale, !empty($post['metaTitle']) ? $post['metaTitle'] : $post['title'], !empty($post['metaTitleAr']) ? $post['metaTitleAr'] : ($post['titleAr'] ?? null));
$postDesc = localized($locale, $post['metaDescription'] ?? '', $post['metaDescriptionAr'] ?? null);
$displayTitle = localized($locale, $post['title'] ?? '', $post['titleAr'] ?? null);
$rawContent = localized($locale, $post['content'] ?? '', $post['contentAr'] ?? null);
$bodyHtml = clean_article_html((string)$rawContent, (string)$displayTitle);
$minutes = reading_minutes((string)$rawContent);
$GLOBALS['page_title'] = $postTitle;
$GLOBALS['meta_description'] = $postDesc;
$GLOBALS['og_image'] = Repo::getImagePath($post, 'coverImagePath');
$GLOBALS['og_type'] = 'article';

$siteUrl = app_url();
$postUrl = $siteUrl . '/' . $locale . '/blog/' . $post['slug'];
$blogUrl = $siteUrl . '/' . $locale . '/blog';
$homeUrl = $siteUrl . '/' . $locale;
$firstTagId = !empty($post['tags']) && isset($post['tags'][0]['id']) ? $post['tags'][0]['id'] : null;
$related = [];
if ($firstTagId) {
  $rel = Repo::blogPosts(true, $firstTagId, 1, 3)['items'];
  foreach ($rel as $item) {
    if ($item['slug'] !== $post['slug']) { $related[] = $item; }
    if (count($related) >= 3) break;
  }
}
if (count($related) < 3) {
  $latest = Repo::blogPosts(true, null, 1, 3)['items'];
  foreach ($latest as $item) {
    if ($item['slug'] !== $post['slug'] && !in_array($item['slug'], array_column($related, 'slug'), true)) {
      $related[] = $item;
    }
    if (count($related) >= 3) break;
  }
}

$fmtDate = function ($v) use ($locale) {
  if (!$v) return '—';
  $ts = strtotime($v);
  if ($ts === false) return (string)$v;
  return date($locale === 'ar' ? 'j F Y' : 'j M Y', $ts);
};
$truncateText = function ($s, $max = 120) {
  if ($s === null || $s === '') return '';
  if (mb_strlen($s) <= $max) return $s;
  return rtrim(mb_substr($s, 0, $max)) . '…';
};

$arrowLeftSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 rtl:rotate-180"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>';
$calendarSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>';
$clockSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>';
$arrowRightSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
$badgeOutline = 'inline-flex items-center px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide border border-line text-neutral';
$heroTag = 'inline-flex items-center border border-white/25 px-3 py-1 font-headline text-xs font-semibold uppercase tracking-widest text-white/90';

$renderBlogCard = function ($post, $locale) use ($calendarSvg, $arrowRightSvg, $badgeOutline, $fmtDate, $truncateText) {
  $title = localized($locale, $post['title'] ?? '', $post['titleAr'] ?? null);
  $excerpt = localized($locale, !empty($post['metaDescription']) ? $post['metaDescription'] : strip_tags($post['content'] ?? ''), !empty($post['metaDescriptionAr']) ? $post['metaDescriptionAr'] : ($post['contentAr'] ?? null));
  $excerpt = $truncateText($excerpt, 120);
?>
<article class="card-brilliant group flex flex-col overflow-hidden transition-colors hover:border-tertiary">
  <a href="<?= e(lnk('/blog/' . $post['slug'])) ?>" class="relative block aspect-[16/9] overflow-hidden bg-neutral-light">
    <img src="<?= e(Repo::getImagePath($post, 'coverImagePath')) ?>" alt="<?= e($title) ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
  </a>
  <div class="flex flex-1 flex-col p-5">
    <div class="flex items-center gap-3">
      <span class="inline-flex items-center gap-1.5 text-xs text-neutral/60"><?= $calendarSvg ?><?= e($fmtDate($post['publishedAt'] ?? null)) ?></span>
      <?php if (!empty($post['tags']) && count($post['tags']) > 0): ?>
        <span class="<?= e($badgeOutline) ?>"><?= e($post['tags'][0]['name']) ?></span>
      <?php endif; ?>
    </div>
    <a href="<?= e(lnk('/blog/' . $post['slug'])) ?>" class="mt-3 font-headline text-lg font-bold uppercase leading-snug transition-colors group-hover:text-secondary"><?= e($title) ?></a>
    <p class="mt-2 flex-1 text-sm leading-relaxed text-neutral/70"><?= e($excerpt) ?></p>
    <a href="<?= e(lnk('/blog/' . $post['slug'])) ?>" class="mt-4 inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-secondary transition-colors hover:text-tertiary"><?= e(t('common.readMore')) ?><?= $arrowRightSvg ?></a>
  </div>
</article>
<?php
};
?>
<article>
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'Article',
  'headline' => $displayTitle,
  'description' => $postDesc !== '' ? $postDesc : null,
  'image' => Repo::getImagePath($post, 'coverImagePath'),
  'datePublished' => $post['publishedAt'] ?? null,
  'dateModified' => ($post['publishedAt'] ?? $post['createdAt'] ?? null),
  'inLanguage' => $locale === 'ar' ? 'ar-EG' : 'en',
  'url' => $postUrl,
  'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $postUrl],
  'author' => ['@type' => 'Organization', 'name' => 'Brilliant Engineering Co.'],
  'publisher' => ['@type' => 'Organization', 'name' => 'Brilliant Engineering Co.', 'logo' => ['@type' => 'ImageObject', 'url' => $siteUrl . '/logo.png']],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => t('nav.home'), 'item' => $homeUrl],
    ['@type' => 'ListItem', 'position' => 2, 'name' => t('nav.blog'), 'item' => $blogUrl],
    ['@type' => 'ListItem', 'position' => 3, 'name' => $displayTitle, 'item' => $postUrl],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<div id="readingProgress" aria-hidden="true" class="pointer-events-none fixed inset-x-0 top-0 z-[85] h-[3px] bg-transparent">
  <div class="h-full bg-tertiary" style="width: 0%"></div>
</div>

<header class="relative flex min-h-[64vh] w-full items-center overflow-hidden bg-neutral md:min-h-[72vh]">
  <div class="absolute inset-0" data-parallax="0.1">
    <img src="<?= e(Repo::getImagePath($post, 'coverImagePath')) ?>" alt="<?= e($displayTitle) ?>" loading="eager" class="h-full w-full object-cover">
  </div>
  <div class="absolute inset-0" style="background-image: linear-gradient(to bottom, rgb(17 28 45 / 0.8) 0%, rgb(17 28 45 / 0.6) 50%, rgb(17 28 45 / 0.9) 100%);"></div>

  <div class="container-brilliant relative z-10 w-full py-32 md:py-40">
    <div class="mx-auto max-w-3xl text-center">
      <a href="<?= e(lnk('/blog')) ?>" class="inline-flex items-center gap-2 font-headline text-xs font-semibold uppercase tracking-[0.25em] text-white/70 transition-colors hover:text-white"><?= $arrowLeftSvg ?><?= e($tb['allPosts']) ?></a>

      <div class="mt-8 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm text-white/80">
        <span class="inline-flex items-center gap-1.5"><?= $calendarSvg ?><?= e($fmtDate($post['publishedAt'] ?? null)) ?></span>
        <?php if ($minutes > 0): ?>
          <span class="h-1 w-1 rounded-full bg-white/40" aria-hidden="true"></span>
          <span class="inline-flex items-center gap-1.5"><?= $clockSvg ?><?= e(t('blogPost.readingTime', ['count' => $minutes])) ?></span>
        <?php endif; ?>
      </div>

      <h1 class="mt-7 font-headline text-4xl font-bold uppercase leading-[1.05] tracking-tighter text-white md:text-5xl lg:text-6xl"><?= e($displayTitle) ?></h1>
      <span class="mx-auto mt-8 block h-1 w-24 bg-tertiary" aria-hidden="true"></span>

      <?php if (!empty($post['tags']) && count(array_filter($post['tags'])) > 0): ?>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
          <?php foreach ($post['tags'] as $tag): ?>
            <span class="<?= e($heroTag) ?>"><?= e($tag['name']) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="container-brilliant py-16 md:py-24">
  <div class="mx-auto max-w-3xl">
    <div class="article-body"><?= $bodyHtml ?></div>
  </div>
</div>

<?php if (count($related) > 0): ?>
<section class="border-t border-line bg-neutral-light py-16 md:py-20">
  <div class="container-brilliant">
    <h2 class="font-headline text-2xl font-bold uppercase tracking-tight"><?= e($tb['relatedPosts']) ?></h2>
    <span class="mt-3 block h-[3px] w-12 bg-tertiary" aria-hidden="true"></span>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($related as $item): ?><?php $renderBlogCard($item, $locale); ?><?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
</article>