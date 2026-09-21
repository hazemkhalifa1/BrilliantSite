<?php
use App\Services\Repo;
/** @var string $locale */
$locale = $locale ?? current_locale();
$t = tarr('projectDetail');
$project = Repo::project((int)($params['id'] ?? 0));

if ($project === null) {
    $GLOBALS['page_title'] = $t['notFound'];
    $GLOBALS['meta_description'] = $t['notFound'];
} else {
    $GLOBALS['page_title'] = localized($locale, $project['title'] ?? '', $project['titleAr'] ?? null);
    $GLOBALS['meta_description'] = localized($locale, $project['description'] ?? '', $project['descriptionAr'] ?? null);
}
?>
<?php if ($project === null): ?>
<section class="py-16 md:py-20">
  <div class="container-brilliant text-center">
    <h1 class="font-headline text-4xl font-bold uppercase leading-tight"><?= e($t['notFound']) ?></h1>
  </div>
</section>
<?php else: ?>
<article>
  <div class="relative aspect-[21/9] overflow-hidden bg-neutral-light">
    <div class="absolute inset-0" data-parallax="0.06">
      <img src="<?= e(Repo::getImagePath($project, 'imagePath')) ?>" alt="<?= e(localized($locale, $project['title'], $project['titleAr'] ?? null)) ?>" loading="eager" class="h-full w-full object-cover">
    </div>
  </div>

  <div class="container-brilliant py-12 md:py-16">
    <div class="reveal-on-scroll">
      <a href="<?= e(lnk('/projects')) ?>" class="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-secondary transition-colors hover:text-tertiary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
        <?= e($t['allProjects']) ?>
      </a>
    </div>

    <div class="mt-6 flex flex-wrap items-center gap-3 reveal-on-scroll" style="transition-delay: 80ms">
      <span class="inline-flex items-center px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide bg-tertiary text-white"><?= e($project['typeName'] ?? '') ?></span>
      <span class="inline-flex items-center gap-1.5 text-sm text-neutral/60">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
        <?= e((string)$project['year']) ?>
      </span>
      <?php if (!empty($project['clientName'])): ?>
        <span class="inline-flex items-center gap-1.5 text-sm text-neutral/60">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <?= e(localized($locale, $project['clientName'], $project['clientNameAr'] ?? null)) ?>
        </span>
      <?php endif; ?>
    </div>

    <div class="reveal-on-scroll" data-from="up"><h1 class="mt-4 font-headline text-3xl font-bold uppercase leading-tight md:text-5xl"><?= e(localized($locale, $project['title'], $project['titleAr'] ?? null)) ?></h1>
    <span class="mt-4 block h-[3px] w-16 bg-tertiary" aria-hidden="true"></span></div>

    <div class="mt-8 max-w-3xl space-y-4 leading-relaxed text-neutral/80 reveal-on-scroll" data-from="left" style="transition-delay: 140ms">
      <?php $desc = localized($locale, $project['description'] ?? '', $project['descriptionAr'] ?? null); foreach (preg_split('/\n+/', $desc) as $paragraph): ?>
        <?php if (trim($paragraph) !== ''): ?><p><?= e(trim($paragraph)) ?></p><?php endif; ?>
      <?php endforeach; ?>
    </div>

    <div class="mt-12 grid gap-4 sm:grid-cols-3">
      <div class="reveal-on-scroll" style="transition-delay: 100ms">
        <div class="card-brilliant pop-card h-full bg-neutral-light p-6">
          <p class="font-headline text-xs font-semibold uppercase tracking-widest text-neutral/50"><?= e($t['type']) ?></p>
          <p class="mt-2 font-headline text-lg font-bold uppercase"><?= e($project['typeName'] ?? '—') ?></p>
        </div>
      </div>
      <div class="reveal-on-scroll" style="transition-delay: 190ms">
        <div class="card-brilliant pop-card h-full bg-neutral-light p-6">
          <p class="font-headline text-xs font-semibold uppercase tracking-widest text-neutral/50"><?= e($t['client']) ?></p>
          <p class="mt-2 font-headline text-lg font-bold uppercase"><?= e(localized($locale, $project['clientName'], $project['clientNameAr'] ?? null) ?: '—') ?></p>
        </div>
      </div>
      <div class="reveal-on-scroll" style="transition-delay: 280ms">
        <div class="card-brilliant pop-card h-full bg-neutral-light p-6">
          <p class="font-headline text-xs font-semibold uppercase tracking-widest text-neutral/50"><?= e($t['year']) ?></p>
          <p class="mt-2 font-headline text-lg font-bold uppercase"><?= e((string)$project['year']) ?></p>
        </div>
      </div>
    </div>
  </div>
</article>
<?php endif; ?>
