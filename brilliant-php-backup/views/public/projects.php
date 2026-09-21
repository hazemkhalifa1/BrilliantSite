<?php
use App\Services\Repo;
/** @var string $locale */
$locale = $locale ?? current_locale();
$t = tarr('projects');
$GLOBALS['page_title'] = $t['metadataTitle'];
$GLOBALS['meta_description'] = $t['metadataDescription'];

$PAGE_SIZE = 9;
$typeId = (isset($_GET['typeId']) && is_numeric($_GET['typeId'])) ? (int)$_GET['typeId'] : null;
$pageIndex = (isset($_GET['page']) && is_numeric($_GET['page'])) ? max(1, (int)$_GET['page']) : 1;

$types = Repo::projectTypes(true, 1, 100)['items'];
$result = Repo::projects($typeId, true, $pageIndex, $PAGE_SIZE);
$projects = $result['items'];
$totalPages = max(1, (int)ceil($result['totalCount'] / $PAGE_SIZE));

$makeUrl = function (?int $type, int $page): string {
    $params = [];
    if ($type !== null) { $params[] = 'typeId=' . (int)$type; }
    if ($page > 1) { $params[] = 'page=' . (int)$page; }
    $qs = implode('&', $params);
    return $qs ? '/projects?' . $qs : '/projects';
};

$truncate = function (string $text, int $max = 110): string {
    if ($text === '') { return ''; }
    if (mb_strlen($text) <= $max) { return $text; }
    return rtrim(mb_substr($text, 0, $max)) . '…';
};

$phEyebrow = $t['eyebrow'];
$phTitle = $t['title'];
$phSubtitle = $t['subtitle'];
$phBackground = 'https://images.unsplash.com/photo-1517581177682-a085bb7ffb15?auto=format&fit=crop&w=1920&q=80';
?>
<div>
<?php require base_path('/views/partials/page-header.php'); ?>
<section class="py-16 md:py-20">
  <div class="container-brilliant">
    <div class="flex flex-wrap gap-2">
      <a href="<?= e(lnk('/projects')) ?>" class="border px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors<?= $typeId === null ? ' border-primary bg-primary text-white' : ' border-line text-neutral hover:bg-neutral-light' ?>">
        <?= e($t['allProjects']) ?>
      </a>
      <?php foreach ($types as $type): ?>
        <a href="<?= e(lnk($makeUrl($type['id'], 1))) ?>" class="border px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors<?= $typeId !== null && $type['id'] === $typeId ? ' border-primary bg-primary text-white' : ' border-line text-neutral hover:bg-neutral-light' ?>">
          <?= e(localized($locale, $type['name'], $type['nameAr'] ?? null)) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if (count($projects) > 0): ?>
      <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($projects as $project): ?>
          <a href="<?= e(lnk('/projects/' . $project['id'])) ?>" class="card-brilliant group block overflow-hidden transition-colors hover:border-tertiary">
            <div class="relative aspect-[4/3] overflow-hidden bg-neutral-light">
              <img src="<?= e(Repo::getImagePath($project, 'imagePath')) ?>" alt="<?= e(localized($locale, $project['title'], $project['titleAr'] ?? null)) ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide bg-tertiary text-white"><?= e($project['typeName'] ?? '') ?></span>
                <span class="font-headline text-xs uppercase tracking-widest text-neutral/50"><?= e((string)$project['year']) ?></span>
              </div>
              <h3 class="mt-3 font-headline text-lg font-bold uppercase transition-colors group-hover:text-secondary"><?= e(localized($locale, $project['title'], $project['titleAr'] ?? null)) ?></h3>
              <p class="mt-2 text-sm leading-relaxed text-neutral/70"><?= e($truncate(localized($locale, $project['description'], $project['descriptionAr'] ?? null))) ?></p>
              <div class="mt-4 flex items-center gap-4 text-xs text-neutral/60">
                <?php if (!empty($project['clientName'])): ?>
                  <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?= e(localized($locale, $project['clientName'], $project['clientNameAr'] ?? null)) ?>
                  </span>
                <?php endif; ?>
                <span class="inline-flex items-center gap-1.5">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                  <?= e((string)$project['year']) ?>
                </span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="mt-12 text-center text-neutral/60"><?= e($t['noProjects']) ?></p>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
      <div class="mt-12 flex items-center justify-center gap-2">
        <a href="<?= e(lnk($makeUrl($typeId, $pageIndex - 1))) ?>" class="border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light<?= $pageIndex <= 1 ? ' pointer-events-none opacity-40' : '' ?>"><?= e($t['previous']) ?></a>
        <span class="px-3 text-sm text-neutral/60"><?= e(str_replace(['{current}', '{total}'], [(string)$pageIndex, (string)$totalPages], $t['pageOf'])) ?></span>
        <a href="<?= e(lnk($makeUrl($typeId, $pageIndex + 1))) ?>" class="border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light<?= $pageIndex >= $totalPages ? ' pointer-events-none opacity-40' : '' ?>"><?= e($t['next']) ?></a>
      </div>
    <?php endif; ?>
  </div>
</section>
</div>
