<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$tb = tarr('blog');
$GLOBALS['page_title'] = $tb['metadataTitle'];
$GLOBALS['meta_description'] = $tb['metadataDescription'];

$PAGE_SIZE = 9;
$tags = Repo::tags(1, 50)['items'];

$tagId = (isset($_GET['tagId']) && $_GET['tagId'] !== '' && is_numeric($_GET['tagId'])) ? (int)$_GET['tagId'] : null;
$pageIndex = (isset($_GET['page']) && is_numeric($_GET['page'])) ? max(1, (int)$_GET['page']) : 1;

$result = Repo::blogPosts(true, $tagId, $pageIndex, $PAGE_SIZE);
$posts = $result['items'];
$totalCount = $result['totalCount'];
$totalPages = max(1, (int)ceil($totalCount / $PAGE_SIZE));

$makeUrl = function ($tag, $page) {
  $qs = [];
  if ($tag !== null) $qs[] = 'tagId=' . $tag;
  if ($page > 1) $qs[] = 'page=' . $page;
  return $qs ? lnk('/blog') . '?' . implode('&', $qs) : lnk('/blog');
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
$arrowRightSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
$badgeOutline = 'inline-flex items-center px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide border border-line text-neutral';

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
<div>
<?php
$phEyebrow = $tb['eyebrow'];
$phTitle = $tb['title'];
$phSubtitle = $tb['subtitle'];
$phBackground = 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1920&q=80';
$phCenter = true;
$phParallax = true;
$phOverlay = 'gradient';
require base_path('/views/partials/page-header.php');
?>
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
<section class="py-16 md:py-20">
  <div class="container-brilliant">
    <?php if (count($tags) > 0): ?>
      <div class="flex flex-wrap gap-2">
        <a href="<?= e(lnk('/blog')) ?>" class="<?= $tagId === null ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?> border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors"><?= e($tb['allPosts']) ?></a>
        <?php foreach ($tags as $tag): ?>
          <a href="<?= e($makeUrl($tag['id'], 1)) ?>" class="<?= $tagId === $tag['id'] ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?> border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors"><?= e($tag['name']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (count($posts) > 0): ?>
      <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($posts as $post): ?><?php $renderBlogCard($post, $locale); ?><?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="mt-12 text-center text-neutral/60"><?= e($tb['noPosts']) ?></p>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
      <div class="mt-12 flex items-center justify-center gap-2">
        <a href="<?= e($makeUrl($tagId, $pageIndex - 1)) ?>" class="border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light <?= $pageIndex <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><?= e($tb['previous']) ?></a>
        <span class="px-3 text-sm text-neutral/60"><?= e(t('blog.pageOf', ['current' => $pageIndex, 'total' => $totalPages])) ?></span>
        <a href="<?= e($makeUrl($tagId, $pageIndex + 1)) ?>" class="border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light <?= $pageIndex >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><?= e($tb['next']) ?></a>
      </div>
    <?php endif; ?>
  </div>
</section>
</div>
