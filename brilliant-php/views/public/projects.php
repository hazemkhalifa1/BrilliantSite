<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$t = tarr('projects');
$GLOBALS['page_title'] = $t['metadataTitle'];
$GLOBALS['meta_description'] = $t['metadataDescription'];

$PAGE_SIZE = 9;
$HERO_IMAGE = "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";
$heroGridStyle = "background-image:linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px);background-size:32px 32px;";

$rawTypeId = $_GET['typeId'] ?? null;
$typeId = ($rawTypeId !== null && $rawTypeId !== '' && is_numeric($rawTypeId)) ? (int)$rawTypeId : null;
$rawPage = $_GET['page'] ?? null;
$pageIndex = ($rawPage !== null && $rawPage !== '' && is_numeric($rawPage)) ? max(1, (int)$rawPage) : 1;

$types = Repo::projectTypes(true, 1, 100)['items'] ?? [];
$result = Repo::projects($typeId, true, $pageIndex, $PAGE_SIZE);
$projects = $result['items'] ?? [];
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

$chipBase = "border px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-transform hover:-translate-y-0.5";
$chipActive = "border-primary bg-primary text-white";
$chipIdle = "border-line text-neutral hover:bg-neutral-light";
?>
<div>
  <section class="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
    <div class="absolute inset-0" style="<?= e($heroGridStyle) ?>"></div>
    <div class="absolute inset-0">
      <div data-parallax="0.08" class="h-full w-full" style="will-change:transform">
        <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" style="background-image: url('<?= e($HERO_IMAGE) ?>')"></div>
      </div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50"></div>

    <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <span class="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($t['eyebrow']) ?></span>
      <h1 class="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl"><?= e($t['title']) ?></h1>
      <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl"><?= e($t['subtitle']) ?></p>
    </div>
  </section>

  <section class="py-16 md:py-20">
    <div class="container-brilliant">
      <div class="flex flex-wrap gap-2">
        <a href="<?= e(lnk('/projects')) ?>" class="<?= e($chipBase . ($typeId === null ? ' ' . $chipActive : ' ' . $chipIdle)) ?>"><?= e($t['allProjects']) ?></a>
        <?php foreach ($types as $type): ?>
          <a href="<?= e(lnk($makeUrl((int)$type['id'], 1))) ?>" class="<?= e($chipBase . ($typeId !== null && (int)$type['id'] === $typeId ? ' ' . $chipActive : ' ' . $chipIdle)) ?>"><?= e(localized($locale, $type['name'], $type['nameAr'] ?? null)) ?></a>
        <?php endforeach; ?>
      </div>

      <?php if (count($projects) > 0): ?>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <?php foreach ($projects as $index => $project): ?>
            <div class="reveal-on-scroll pop-card h-full" style="transition-delay: <?= ($index % 3) * 80 + 120 ?>ms">
              <a href="<?= e(lnk('/projects/' . $project['id'])) ?>" class="card-brilliant group block h-full overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-tertiary hover:shadow-[6px_6px_0px_0px_#0059bb]">
                <div class="relative aspect-[4/3] overflow-hidden bg-neutral-light">
                  <img src="<?= e(Repo::getImagePath($project, 'imagePath')) ?>" alt="<?= e(localized($locale, $project['title'], $project['titleAr'] ?? null)) ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                </div>
                <div class="p-5">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex items-center bg-tertiary px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide text-white"><?= e($project['typeName'] ?? '') ?></span>
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
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="mt-12 text-center text-neutral/60"><?= e($t['noProjects']) ?></p>
      <?php endif; ?>

      <?php if ($totalPages > 1): ?>
        <div class="mt-12 flex items-center justify-center gap-2">
          <a href="<?= e(lnk($makeUrl($typeId, $pageIndex - 1))) ?>" class="group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light<?= $pageIndex <= 1 ? ' pointer-events-none opacity-40' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 inline h-4 w-4 transition-transform duration-300 group-hover:-translate-x-1"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            <?= e($t['previous']) ?>
          </a>
          <span class="px-3 text-sm text-neutral/60"><?= e(str_replace(['{current}', '{total}'], [(string)$pageIndex, (string)$totalPages], $t['pageOf'])) ?></span>
          <a href="<?= e(lnk($makeUrl($typeId, $pageIndex + 1))) ?>" class="group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light<?= $pageIndex >= $totalPages ? ' pointer-events-none opacity-40' : '' ?>">
            <?= e($t['next']) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 inline h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </section>
</div>