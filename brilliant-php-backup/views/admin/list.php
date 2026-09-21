<?php
/** @var string $entity @var array $cfg @var array $items @var array $section @var array $filters @var array $filterOptions @var string $search @var int $page */
$list = tarr('admin.list');
$common = tarr('common');
$base = admin_url('/' . $entity);
$buildUrl = function (array $params) use ($base) {
    $params = array_filter($params, fn($v) => $v !== null && $v !== '');
    $q = http_build_query($params);
    return $q === '' ? $base : $base . '?' . $q;
};
$truncate = function (?string $text, int $max = 70): string {
    $text = trim((string)$text);
    if ($text === '' || mb_strlen($text) <= $max) return $text;
    return mb_substr($text, 0, $max) . '…';
};
$fmtDate = function ($value): string {
    if (!$value) return '—';
    $ts = strtotime((string)$value);
    return $ts ? date('j M Y', $ts) : (string)$value;
};
$badge = function (bool $active, bool $published = false) use ($list, $common) {
    if ($published) {
        return $active
            ? '<span class="inline-flex items-center bg-green-600 px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide text-white">' . e($list['published'] ?? 'Published') . '</span>'
            : '<span class="inline-flex items-center bg-neutral-light px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide text-neutral">' . e($list['drafts'] ?? 'Draft') . '</span>';
    }
    return $active
        ? '<span class="inline-flex items-center bg-green-600 px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide text-white">' . e($common['active']) . '</span>'
        : '<span class="inline-flex items-center bg-neutral-light px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide text-neutral">' . e($common['inactive']) . '</span>';
};
$searchPlaceholder = str_replace('{entity}', mb_strtolower($section['title'] ?? $entity), $list['searchPlaceholder'] ?? 'Search...');
$hasItems = count($items['items']);
$selectedCategory = $filters['categoryId'] ?? null;
$selectedType = $filters['typeId'] ?? null;
$selectedBrand = $filters['brandId'] ?? null;
$selectedStatus = $filters['status'] ?? null;
?>
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-4 border-b border-line pb-5">
    <div>
      <h1 class="font-headline text-2xl font-bold uppercase"><?= e($section['title'] ?? '') ?></h1>
      <p class="mt-1 text-sm text-neutral/60"><?= e($section['description'] ?? '') ?></p>
    </div>
    <a href="<?= e(admin_url('/'.$entity.'/new')) ?>" class="btn-primary !px-4 !py-2">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
      <?= e($section['new'] ?? 'New') ?>
    </a>
  </div>

  <?php if (in_array($entity, ['services', 'products', 'projects', 'product-categories', 'blog'], true)): ?>
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap items-center gap-2">
        <?php if ($entity === 'services' || $entity === 'products'): ?>
          <a href="<?= e($buildUrl(['search' => $search])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= !$selectedCategory ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e($list['all'] ?? 'All') ?></a>
          <?php foreach (($filterOptions['categories'] ?? []) as $cat): ?>
            <a href="<?= e($buildUrl(['categoryId' => $cat['id'], 'search' => $search])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= $selectedCategory == $cat['id'] ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e(lpair($cat['name'], $cat['nameAr'] ?? null)) ?></a>
          <?php endforeach; ?>
        <?php elseif ($entity === 'projects'): ?>
          <a href="<?= e($buildUrl([])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= !$selectedType ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e($list['all'] ?? 'All') ?></a>
          <?php foreach (($filterOptions['types'] ?? []) as $type): ?>
            <a href="<?= e($buildUrl(['typeId' => $type['id']])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= $selectedType == $type['id'] ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e(lpair($type['name'], $type['nameAr'] ?? null)) ?></a>
          <?php endforeach; ?>
        <?php elseif ($entity === 'product-categories'): ?>
          <a href="<?= e($buildUrl([])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= !$selectedBrand ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e($list['all'] ?? 'All') ?></a>
          <?php foreach (($filterOptions['brands'] ?? []) as $brand): ?>
            <a href="<?= e($buildUrl(['brandId' => $brand['id']])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= $selectedBrand == $brand['id'] ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e(lpair($brand['name'], $brand['nameAr'] ?? null)) ?></a>
          <?php endforeach; ?>
        <?php elseif ($entity === 'blog'): ?>
          <a href="<?= e($buildUrl([])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= !$selectedStatus ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e($list['all'] ?? 'All') ?></a>
          <a href="<?= e($buildUrl(['status' => 'published'])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= $selectedStatus === 'published' ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e($list['published'] ?? 'Published') ?></a>
          <a href="<?= e($buildUrl(['status' => 'draft'])) ?>" class="border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors <?= $selectedStatus === 'draft' ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= e($list['drafts'] ?? 'Drafts') ?></a>
        <?php endif; ?>
      </div>

      <?php if ($entity === 'services' || $entity === 'products'): ?>
        <form method="get" action="<?= e($base) ?>" class="flex items-center gap-2">
          <?php if ($selectedCategory): ?><input type="hidden" name="categoryId" value="<?= e((string)$selectedCategory) ?>"><?php endif; ?>
          <input type="search" name="search" value="<?= e($search) ?>" placeholder="<?= e($searchPlaceholder) ?>" class="w-56 border border-line bg-white px-3 py-2 text-sm text-neutral outline-none transition-colors focus:border-secondary">
          <button type="submit" class="border border-primary bg-primary px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-colors hover:bg-neutral"><?= e($list['search'] ?? 'Search') ?></button>
          <?php if ($search !== ''): ?>
            <a href="<?= e($buildUrl(['categoryId' => $selectedCategory])) ?>" class="border border-line px-3 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:bg-neutral-light"><?= e($list['clear'] ?? 'Clear') ?></a>
          <?php endif; ?>
        </form>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="overflow-x-auto border border-line bg-white">
    <table class="w-full min-w-max text-left text-sm">
      <thead>
        <tr class="border-b border-line bg-neutral-light">
          <?php
          $headerKeys = [];
          switch ($entity) {
            case 'services': $headerKeys = ['service', 'category', 'status']; break;
            case 'service-categories': $headerKeys = ['category', 'status']; break;
            case 'projects': $headerKeys = ['project', 'type', 'year', 'status']; break;
            case 'products': $headerKeys = ['product', 'category', 'brand', 'status']; break;
            case 'product-brands': $headerKeys = ['brand', 'status']; break;
            case 'product-categories': $headerKeys = ['category', 'brand', 'status']; break;
            case 'blog': $headerKeys = ['post', 'tags', 'status', 'published']; break;
            case 'team': $headerKeys = ['member', 'order', 'status']; break;
            case 'clients': $headerKeys = ['client', 'order', 'status']; break;
            default: $headerKeys = array_map(fn($c) => $c['key'], $cfg['columns']);
          }
          $headers = array_map(function ($k) use ($section, $cfg) {
              if (isset($section['headers'][$k])) return $section['headers'][$k];
              foreach ($cfg['columns'] as $c) { if ($c['key'] === $k) return t($c['labelKey']); }
              return $k;
          }, $headerKeys);
          foreach ($headers as $h): ?>
            <th class="px-4 py-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral"><?= e($h) ?></th>
          <?php endforeach; ?>
          <th class="px-4 py-3 text-right font-headline text-xs font-semibold uppercase tracking-wide text-neutral"><?= e($list['actions'] ?? 'Actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$hasItems): ?>
          <tr><td class="px-4 py-12 text-center text-neutral/50" colspan="99"><?= e($list['noRecords'] ?? 'No records found.') ?></td></tr>
        <?php else: ?>
          <?php foreach ($items['items'] as $item): ?>
            <tr class="border-b border-line transition-colors last:border-b-0 hover:bg-neutral-light/60">
              <?php if ($entity === 'services'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/services/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['title'], $item['titleAr'] ?? null)) ?></a>
                  <p class="mt-0.5 text-xs text-neutral/50"><?= e($truncate(lpair($item['description'], $item['descriptionAr'] ?? null))) ?></p>
                </td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['categoryName'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php elseif ($entity === 'service-categories'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/service-categories/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['name'], $item['nameAr'] ?? null)) ?></a>
                  <p class="mt-0.5 text-xs text-neutral/50"><?= e($truncate(lpair($item['description'], $item['descriptionAr'] ?? null))) ?></p>
                </td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php elseif ($entity === 'projects'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/projects/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['title'], $item['titleAr'] ?? null)) ?></a>
                  <p class="mt-0.5 text-xs text-neutral/50"><?= e($truncate(lpair($item['description'], $item['descriptionAr'] ?? null))) ?></p>
                </td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['typeName'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['year'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php elseif ($entity === 'products'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/products/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['name'], $item['nameAr'] ?? null)) ?></a>
                  <p class="mt-0.5 text-xs text-neutral/50"><?= e($truncate(lpair($item['description'], $item['descriptionAr'] ?? null))) ?></p>
                </td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['categoryName'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['brandName'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php elseif ($entity === 'product-brands'): ?>
                <td class="px-4 py-3 align-middle">
                  <div class="flex items-center gap-3">
                    <?php if (($item['backgroundImagePath'] ?? '') !== ''): ?>
                      <div class="h-14 w-20 shrink-0 overflow-hidden border border-line bg-white"><img src="<?= e(\App\Services\Repo::getImagePath($item, 'backgroundImagePath')) ?>" alt="<?= e(lpair($item['name'], $item['nameAr'] ?? null)) ?>" class="h-full w-full object-cover"></div>
                    <?php else: ?>
                      <div class="flex h-14 w-20 shrink-0 items-center justify-center border border-dashed border-line bg-neutral-light text-xs text-neutral/50"><?= e(t('admin.upload.noImage')) ?></div>
                    <?php endif; ?>
                    <div>
                      <a href="<?= e(admin_url('/product-brands/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['name'], $item['nameAr'] ?? null)) ?></a>
                      <p class="mt-0.5 text-xs text-neutral/50"><?= e($truncate(lpair($item['description'], $item['descriptionAr'] ?? null), 60)) ?></p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php elseif ($entity === 'product-categories'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/product-categories/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['name'], $item['nameAr'] ?? null)) ?></a>
                </td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['brandName'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php elseif ($entity === 'blog'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/blog/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['title'], $item['titleAr'] ?? null)) ?></a>
                  <p class="mt-0.5 text-xs text-neutral/50">/<?= e($item['slug'] ?? '') ?></p>
                </td>
                <td class="px-4 py-3 align-middle text-neutral/80">
                  <?php $tags = $item['tags'] ?? []; if (count($tags)): ?>
                    <?= e(implode(', ', array_map(fn($t) => $t['name'], $tags))) ?>
                  <?php else: ?>—<?php endif; ?>
                </td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isPublished'], true) ?></td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($fmtDate($item['publishedAt'] ?? null)) ?></td>
              <?php elseif ($entity === 'team'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/team/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['name'], $item['nameAr'] ?? null)) ?></a>
                  <p class="mt-0.5 text-xs text-neutral/50"><?= e(lpair($item['jobTitle'], $item['jobTitleAr'] ?? null) ?: '—') ?></p>
                </td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['order'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php elseif ($entity === 'clients'): ?>
                <td class="px-4 py-3 align-middle">
                  <a href="<?= e(admin_url('/clients/'.$item['id'])) ?>" class="font-semibold text-neutral transition-colors hover:text-secondary"><?= e(lpair($item['name'], $item['nameAr'] ?? null)) ?></a>
                </td>
                <td class="px-4 py-3 align-middle text-neutral/80"><?= e($item['order'] ?? '—') ?></td>
                <td class="px-4 py-3 align-middle"><?= $badge((bool)$item['isActive']) ?></td>
              <?php else: ?>
                <?php foreach ($cfg['columns'] as $col): $k = $col['key']; $v = $item[$k] ?? null; ?>
                  <td class="px-4 py-3 align-middle text-neutral/80"><?= e($v === null ? '—' : (string)$v) ?></td>
                <?php endforeach; ?>
              <?php endif; ?>

              <td class="px-4 py-3 align-middle">
                <div class="flex items-center justify-end gap-2">
                  <?php if ($entity === 'blog'): ?>
                    <form method="post" action="<?= e(admin_url('/blog/'.$item['id'].($item['isPublished']?'/unpublish':'/publish'))) ?>"><?= csrf_field() ?><button class="font-headline text-xs font-semibold uppercase tracking-wide <?= $item['isPublished'] ? 'text-neutral/50 hover:text-neutral' : 'text-green-600 hover:underline' ?>"><?= e($item['isPublished'] ? ($list['unpublish'] ?? 'Unpublish') : ($list['publish'] ?? 'Publish')) ?></button></form>
                  <?php endif; ?>
                  <a href="<?= e(admin_url('/'.$entity.'/'.$item['id'])) ?>" class="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"><?= e($list['edit'] ?? 'Edit') ?></a>
                  <form method="post" action="<?= e(admin_url('/'.$entity.'/'.$item['id'].'/delete')) ?>" onsubmit="return confirm('<?= e($list['deleteConfirm'] ?? 'Delete?') ?>'.replace('{name}', ''));"><?= csrf_field() ?><button class="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"><?= e($list['delete'] ?? 'Delete') ?></button></form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($items['totalCount'] > $items['pageSize']): $totalPages = max(1, (int)ceil($items['totalCount'] / $items['pageSize'])); $pageIdx = (int)$items['pageIndex']; ?>
    <nav aria-label="Pagination" class="flex items-center justify-center gap-1">
      <?php
        $pageParams = [];
        if ($selectedCategory) $pageParams['categoryId'] = $selectedCategory;
        if ($selectedType) $pageParams['typeId'] = $selectedType;
        if ($selectedBrand) $pageParams['brandId'] = $selectedBrand;
        if ($selectedStatus) $pageParams['status'] = $selectedStatus;
        if ($search !== '') $pageParams['search'] = $search;
      ?>
      <a href="<?= e($buildUrl(array_merge($pageParams, ['page' => max(1, $pageIdx - 1)]))) ?>" class="flex h-9 w-9 items-center justify-center border border-line text-neutral transition-colors hover:bg-neutral-light disabled:opacity-40">&lt;</a>
      <?php for ($pi = 1; $pi <= $totalPages; $pi++): ?>
        <a href="<?= e($buildUrl(array_merge($pageParams, ['page' => $pi]))) ?>" class="flex h-9 w-9 items-center justify-center border text-sm font-medium transition-colors <?= $pi === $pageIdx ? 'border-primary bg-primary text-white' : 'border-line text-neutral hover:bg-neutral-light' ?>"><?= $pi ?></a>
      <?php endfor; ?>
      <a href="<?= e($buildUrl(array_merge($pageParams, ['page' => min($totalPages, $pageIdx + 1)]))) ?>" class="flex h-9 w-9 items-center justify-center border border-line text-neutral transition-colors hover:bg-neutral-light">&gt;</a>
    </nav>
  <?php endif; ?>
</div>
