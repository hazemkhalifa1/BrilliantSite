<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$tb = tarr('blog');
$GLOBALS['page_title'] = $tb['metadataTitle'];
$GLOBALS['meta_description'] = $tb['metadataDescription'];

$PAGE_SIZE = 9;
$HERO_IMAGE = "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";
$heroGridStyle = "background-image:linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px);background-size:32px 32px;";

$tags = Repo::tags(1, 50)['items'] ?? [];

$rawTagId = $_GET['tagId'] ?? null;
$tagId = ($rawTagId !== null && $rawTagId !== '' && is_numeric($rawTagId)) ? (int)$rawTagId : null;
$rawPage = $_GET['page'] ?? null;
$pageIndex = ($rawPage !== null && $rawPage !== '' && is_numeric($rawPage)) ? max(1, (int)$rawPage) : 1;

$result = Repo::blogPosts(true, $tagId, $pageIndex, $PAGE_SIZE);
$posts = $result['items'] ?? [];
$totalCount = $result['totalCount'] ?? 0;
$totalPages = max(1, (int)ceil($totalCount / $PAGE_SIZE));

$makeUrl = function ($tag, $page) {
  $qs = [];
  if ($tag !== null) $qs[] = 'tagId=' . $tag;
  if ($page > 1) $qs[] = 'page=' . $page;
  return $qs ? '/blog?' . implode('&', $qs) : '/blog';
};

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

$calendarSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>';
$arrowRightSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
$badgeOutline = 'inline-flex items-center px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide border border-line text-neutral';

$renderBlogCard = function ($post, $locale) use ($calendarSvg, $arrowRightSvg, $badgeOutline, $fmtDate, $truncateText) {
  $title = localized($locale, $post['title'] ?? '', $post['titleAr'] ?? null);
  $excerpt = localized($locale, !empty($post['metaDescription']) ? $post['metaDescription'] : strip_html(markdown_safe_html($post['content'] ?? '')), !empty($post['metaDescriptionAr']) ? $post['metaDescriptionAr'] : ($post['contentAr'] ?? null));
  $excerpt = $truncateText($excerpt, 120);
?>
<article class="card-brilliant group flex flex-col overflow-hidden transition-colors hover:border-tertiary">
  <a href="<?= e(lnk('/blog/' . Repo::canonicalSlug($post['slug']))) ?>" class="relative block aspect-[16/9] overflow-hidden bg-neutral-light">
    <img src="<?= e(Repo::getImagePath($post, 'coverImagePath')) ?>" alt="<?= e($title) ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
  </a>
  <div class="flex flex-1 flex-col p-5">
    <div class="flex items-center gap-3">
      <span class="inline-flex items-center gap-1.5 text-xs text-neutral/60"><?= $calendarSvg ?><?= e($fmtDate($post['publishedAt'] ?? null)) ?></span>
      <?php if (!empty($post['tags']) && count($post['tags']) > 0): ?>
        <span class="<?= e($badgeOutline) ?>"><?= e($post['tags'][0]['name']) ?></span>
      <?php endif; ?>
    </div>
    <a href="<?= e(lnk('/blog/' . Repo::canonicalSlug($post['slug']))) ?>" class="mt-3 font-headline text-lg font-bold uppercase leading-snug transition-colors group-hover:text-secondary"><?= e($title) ?></a>
    <p class="mt-2 flex-1 text-sm leading-relaxed text-neutral/70"><?= e($excerpt) ?></p>
    <a href="<?= e(lnk('/blog/' . Repo::canonicalSlug($post['slug']))) ?>" class="group mt-4 inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-secondary transition-colors hover:text-tertiary"><?= e(t('common.readMore')) ?><?= $arrowRightSvg ?></a>
  </div>
</article>
<?php
};

$chipBase = "border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-transform hover:-translate-y-0.5";
$chipActive = "border-primary bg-primary text-white";
$chipIdle = "border-line text-neutral hover:bg-neutral-light";
?>
<div>
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => t('nav.home'), 'item' => url($locale)],
    ['@type' => 'ListItem', 'position' => 2, 'name' => $tb['title'], 'item' => url($locale . '/blog')],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<section class="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
  <div class="absolute inset-0" style="<?= e($heroGridStyle) ?>"></div>
  <div class="absolute inset-0">
    <div data-parallax="0.08" class="h-full w-full" style="will-change:transform">
      <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" style="background-image: url('<?= e($HERO_IMAGE) ?>')"></div>
    </div>
  </div>
  <div class="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50"></div>

  <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
    <span class="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($tb['eyebrow']) ?></span>
    <h1 class="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl"><?= e($tb['title']) ?></h1>
    <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl"><?= e($tb['subtitle']) ?></p>
  </div>
</section>

<section class="py-16 md:py-20">
  <div class="container-brilliant">
    <?php if (count($tags) > 0): ?>
      <div class="flex flex-wrap gap-2">
        <a href="<?= e(lnk('/blog')) ?>" class="<?= e($chipBase . ($tagId === null ? ' ' . $chipActive : ' ' . $chipIdle)) ?>"><?= e($tb['allPosts']) ?></a>
        <?php foreach ($tags as $tag): ?>
          <a href="<?= e(lnk($makeUrl($tag['id'], 1))) ?>" class="<?= e($chipBase . ($tagId === $tag['id'] ? ' ' . $chipActive : ' ' . $chipIdle)) ?>"><?= e($tag['name']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (count($posts) > 0): ?>
      <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($posts as $index => $post): ?>
          <div class="reveal-on-scroll pop-card h-full" style="transition-delay: <?= ($index % 3) * 80 + 120 ?>ms"><?php $renderBlogCard($post, $locale); ?></div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="mt-12 text-center text-neutral/60"><?= e($tb['noPosts']) ?></p>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
      <div class="mt-12 flex items-center justify-center gap-2">
        <a href="<?= e(lnk($makeUrl($tagId, $pageIndex - 1))) ?>" class="group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light<?= $pageIndex <= 1 ? ' pointer-events-none opacity-40' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 inline h-4 w-4 transition-transform duration-300 group-hover:-translate-x-1"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
          <?= e($tb['previous']) ?>
        </a>
        <span class="px-3 text-sm text-neutral/60"><?= e(t('blog.pageOf', ['current' => $pageIndex, 'total' => $totalPages])) ?></span>
        <a href="<?= e(lnk($makeUrl($tagId, $pageIndex + 1))) ?>" class="group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light<?= $pageIndex >= $totalPages ? ' pointer-events-none opacity-40' : '' ?>">
          <?= e($tb['next']) ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 inline h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>
</div>