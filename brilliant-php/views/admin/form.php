<?php
use App\Services\Repo;
/** @var string $entity @var array $cfg @var ?array $item @var array $fieldDefs @var array $options @var ?int $id @var array $brands @var array $categories */
$form = tarr('admin.form');
$allTags = Repo::tags(1, 200)['items'];
$isNew = $id === null;
$action = $isNew ? admin_url('/'.$entity.'/store') : admin_url('/'.$entity.'/'.$id.'/update');
$backLabel = $form['back'] ?? 'Back to {entity}';
$backLabel = str_replace('{entity}', t($cfg['entityKey']), $backLabel);
$entityLabel = t($cfg['entityKey']);
$newTitle = str_replace('{entity}', $entityLabel, $form['newTitle'] ?? 'New');
$editTitle = str_replace('{entity}', $entityLabel, $form['editTitle'] ?? 'Edit');
$createLabel = str_replace('{entity}', $entityLabel, $form['create'] ?? 'Create');
$updateLabel = str_replace('{entity}', $entityLabel, $form['update'] ?? 'Update');

$skip = [];
if ($entity === 'blog') $skip[] = 'slug';
if (!in_array($entity, ['team', 'clients', 'testimonials'], true)) $skip[] = 'order';

$placeholderFor = function (array $f) use ($entity, $form) {
    $name = $f['name'];
    if ($name === 'title') {
        if ($entity === 'projects') return $form['projectTitlePlaceholder'] ?? '';
        if ($entity === 'blog') return $form['postTitlePlaceholder'] ?? '';
        return $form['titlePlaceholder'] ?? '';
    }
    if ($name === 'name') {
        $map = [
            'products' => 'productNamePlaceholder',
            'product-brands' => 'brandNamePlaceholder',
            'service-categories' => 'serviceCategoryNamePlaceholder',
            'product-categories' => 'productCategoryNamePlaceholder',
            'team' => 'teamMemberNamePlaceholder',
            'testimonials' => 'testimonialNamePlaceholder',
        ];
        return isset($map[$entity]) ? ($form[$map[$entity]] ?? '') : '';
    }
    if ($name === 'description') return $form['descriptionPlaceholder'] ?? '';
    if ($name === 'content') return $form['contentPlaceholder'] ?? '';
    if ($name === 'quote') return $form['quotePlaceholder'] ?? '';
    if ($name === 'role') return $form['rolePlaceholder'] ?? '';
    if ($name === 'clientName') return $form['clientNamePlaceholder'] ?? '';
    if ($name === 'jobTitle') return $form['jobTitlePlaceholder'] ?? '';
    if ($name === 'metaTitle') return $form['metaTitlePlaceholder'] ?? '';
    if ($name === 'metaDescription') return $form['metaDescriptionPlaceholder'] ?? '';
    return '';
};

$selectPlaceholder = function (string $name) use ($entity, $form) {
    if ($name === 'categoryId') {
        if ($entity === 'products') return $form['selectCategoryForBrandPlaceholder'] ?? '';
        return $form['selectCategoryPlaceholder'] ?? '';
    }
    if ($name === 'typeId') return $form['selectTypePlaceholder'] ?? '';
    if ($name === 'brandId') return $form['selectBrandPlaceholder'] ?? '';
    return '';
};

$reqMark = function (string $label, bool $req): string {
    return ($req && mb_strpos($label, '*') === false) ? ' *' : '';
};
$selTagIds = array_map(fn($t) => (string)$t['id'], (array)($item['tags'] ?? []));
$productBrandId = $entity === 'products' ? ($item['brandId'] ?? null) : null;
?>
<div class="mx-auto max-w-3xl space-y-6">
  <a href="<?= e(admin_url('/'.$entity)) ?>" class="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-neutral/60 transition-colors hover:text-secondary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
    <?= e($backLabel) ?>
  </a>

  <form method="post" action="<?= e($action) ?>" class="card-brilliant space-y-5 bg-white p-6" data-admin-form>
    <?= csrf_field() ?>
    <h1 class="font-headline text-xl font-bold uppercase"><?= e($isNew ? $newTitle : $editTitle) ?></h1>

    <?php foreach ($fieldDefs as $f): $name = $f['name']; $type = $f['type']; if (in_array($name, $skip, true)) continue; $label = t($f['label'] ?? $name); $req = !empty($f['required']); $val = $f['value'] ?? ''; if ($type === 'richtext' && is_string($val) && $val !== '' && function_exists('looks_like_markdown') && looks_like_markdown($val)) { $val = markdown_to_html($val); } $ph = $placeholderFor($f); ?>

      <?php if ($entity === 'blog' && $name === 'metaTitle'): ?>
        <div class="border-t border-line pt-5">
          <h2 class="font-headline text-sm font-bold uppercase text-neutral/70"><?= e($form['seo'] ?? 'SEO') ?></h2>
          <div class="mt-4 space-y-5">
      <?php endif; ?>
      <?php if ($entity === 'blog' && $name === 'tags'): ?>
        <div class="border-t border-line pt-5">
          <h2 class="font-headline text-sm font-bold uppercase text-neutral/70"><?= e($form['tags'] ?? 'Tags') ?></h2>
      <?php endif; ?>
      <?php if ($entity === 'blog' && $name === 'isPublished'): ?>
        <div class="border-t border-line pt-5">
      <?php endif; ?>

      <?php if ($type === 'checkbox'): ?>
        <label class="flex cursor-pointer items-start gap-3">
          <span class="toggle <?= !empty($f['checked']) ? 'on' : '' ?>">
            <input type="checkbox" name="<?= e($name) ?>" value="1" <?= !empty($f['checked']) ? 'checked' : '' ?>>
            <span class="thumb"></span>
          </span>
          <span>
            <span class="block text-sm font-medium text-neutral"><?= e($label) ?></span>
            <?php if ($name === 'isActive'): ?><span class="mt-0.5 block text-xs text-neutral/60"><?= e(str_replace('{entity}', t($cfg['entityKey']), $form['inactiveHidden'] ?? '')) ?></span><?php elseif ($name === 'isPublished'): ?><span class="mt-0.5 block text-xs text-neutral/60"><?= e($form['publishDescription'] ?? '') ?></span><?php endif; ?>
          </span>
        </label>

      <?php elseif ($type === 'select'): ?>
        <?php if ($entity === 'products' && $name === 'categoryId'): ?>
          <div class="grid gap-5 sm:grid-cols-2">
            <div class="flex w-full flex-col gap-1.5">
              <label for="f_brandId" class="text-sm font-medium text-neutral"><?= e($form['brand'] ?? 'Brand') ?></label>
              <select id="f_brandId" name="brandId" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none" data-brand-filter>
                <option value=""><?= e($form['selectBrandPlaceholder'] ?? 'Select a brand…') ?></option>
                <?php foreach ($brands as $b): ?><option value="<?= e((string)$b['id']) ?>" <?= $productBrandId == $b['id'] ? 'selected' : '' ?>><?= e(lpair($b['name'], $b['nameAr'] ?? null)) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="flex w-full flex-col gap-1.5">
              <label for="f_categoryId" class="text-sm font-medium text-neutral"><?= e($label) ?><?= $reqMark($label, $req) ?></label>
              <select id="f_categoryId" name="categoryId" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none" data-brand-category data-placeholder-brand="<?= e($form['selectCategoryForBrandPlaceholder'] ?? 'Select a category for this brand…') ?>" data-placeholder-all="<?= e($form['selectBrandFirstPlaceholder'] ?? 'Select a brand first to narrow down categories…') ?>">
                <option value=""><?= e($productBrandId ? ($form['selectCategoryForBrandPlaceholder'] ?? '') : ($form['selectBrandFirstPlaceholder'] ?? '')) ?></option>
                <?php foreach ($categories as $c): ?><option value="<?= e((string)$c['id']) ?>" data-brand="<?= e((string)$c['brandId']) ?>" <?= (string)($item['categoryId'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e(lpair($c['name'], $c['nameAr'] ?? null)) ?></option><?php endforeach; ?>
              </select>
            </div>
          </div>
          <p class="text-xs text-neutral/50"><?= e($productBrandId ? ($form['pickCategoryHint'] ?? '') : ($form['pickBrandHint'] ?? '')) ?></p>
        <?php else: ?>
          <div class="flex w-full flex-col gap-1.5">
            <label for="f_<?= e($name) ?>" class="text-sm font-medium text-neutral"><?= e($label) ?><?= $reqMark($label, $req) ?></label>
            <select id="f_<?= e($name) ?>" name="<?= e($name) ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
              <option value=""><?= e($selectPlaceholder($name)) ?></option>
              <?php foreach (($options[$name] ?? []) as $opt): ?><option value="<?= e((string)$opt['id']) ?>" <?= (string)($item[$name] ?? '') === (string)$opt['id'] ? 'selected' : '' ?>><?= e($opt['label']) ?></option><?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

      <?php elseif ($type === 'textarea' || $type === 'richtext'): ?>
        <div class="flex w-full flex-col gap-1.5">
          <label for="f_<?= e($name) ?>" class="text-sm font-medium text-neutral"><?= e($label) ?><?= $reqMark($label, $req) ?></label>
          <textarea id="f_<?= e($name) ?>" name="<?= e($name) ?>" rows="6" placeholder="<?= e($ph) ?>" <?= $type === 'richtext' ? 'data-quill' : '' ?> class="w-full resize-y border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none"><?= e($val) ?></textarea>
        </div>

      <?php elseif ($type === 'image' || $type === 'file'): ?>
        <div class="flex w-full flex-col gap-2">
          <label for="f_<?= e($name) ?>" class="text-sm font-medium text-neutral"><?= e($label) ?></label>
          <div class="flex items-center gap-4">
            <?php if ($val !== ''): ?>
              <div class="relative h-24 w-32 shrink-0 overflow-hidden border border-line bg-white">
                <?php if ($type === 'image'): ?><img src="<?= e(Repo::getImagePath($item ? $item : [], $name)) ?>" alt="<?= e($label) ?>" class="h-full w-full object-contain"><?php else: ?><div class="flex h-full items-center justify-center px-2 text-center text-xs text-neutral/50"><?= e(t('admin.upload.noDocument')) ?></div><?php endif; ?>
                <button type="button" onclick="document.getElementById('f_<?= e($name) ?>').value=''" aria-label="<?= e(t('admin.upload.removeImage')) ?>" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center bg-tertiary text-white transition-colors hover:opacity-90">×</button>
              </div>
            <?php else: ?>
              <div class="flex h-24 w-32 shrink-0 items-center justify-center border border-dashed border-line bg-neutral-light text-center text-xs text-neutral/50"><?= e($type === 'image' ? t('admin.upload.noImage') : t('admin.upload.noDocument')) ?></div>
            <?php endif; ?>
            <button type="button" class="inline-flex items-center gap-2 border border-neutral px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:bg-neutral hover:text-white" data-upload="<?= e($type === 'image' ? 'image/' . $entity : 'document') ?>" data-target="f_<?= e($name) ?>"><?= e($val !== '' ? t('admin.upload.replace') : ($type === 'image' ? t('admin.upload.uploadImage') : t('admin.upload.uploadDocument'))) ?></button>
          </div>
          <div class="flex items-center gap-2">
            <span class="shrink-0 text-xs text-neutral/50"><?= e(t('admin.upload.orPasteUrl')) ?></span>
            <input id="f_<?= e($name) ?>" name="<?= e($name) ?>" value="<?= e($val) ?>" placeholder="https://example.com/image.jpg" class="w-full border border-line bg-white px-3 py-2 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none">
          </div>
        </div>

      <?php elseif ($type === 'tags'): ?>
        <div class="mt-4">
          <label class="text-sm font-medium text-neutral"><?= e($label) ?></label>
          <div class="mt-2 flex flex-wrap gap-2">
            <?php if (count($allTags)): foreach ($allTags as $tag): ?>
              <label class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-1.5 text-sm hover:bg-neutral-light">
                <input type="checkbox" name="tags[]" value="<?= e((string)$tag['id']) ?>" <?= in_array((string)$tag['id'], $selTagIds, true) ? 'checked' : '' ?> class="h-3.5 w-3.5">
                <?= e($tag['name']) ?>
              </label>
            <?php endforeach; else: ?><span class="text-sm text-neutral/50"><?= e(t('admin.tagPicker.empty')) ?></span><?php endif; ?>
          </div>
        </div>

      <?php else: ?>
        <div class="flex w-full flex-col gap-1.5">
          <label for="f_<?= e($name) ?>" class="text-sm font-medium text-neutral"><?= e($label) ?><?= $reqMark($label, $req) ?></label>
          <input id="f_<?= e($name) ?>" name="<?= e($name) ?>" type="<?= $type === 'number' ? 'number' : 'text' ?>" value="<?= e($val) ?>" placeholder="<?= e($ph) ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none" />
        </div>
      <?php endif; ?>

      <?php if ($entity === 'blog' && $name === 'metaDescriptionAr'): ?>
          </div>
        </div>
      <?php endif; ?>
      <?php if ($entity === 'blog' && $name === 'tags'): ?>
        </div>
      <?php endif; ?>
      <?php if ($entity === 'blog' && $name === 'isPublished'): ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>

    <div class="flex items-center justify-end gap-2 border-t border-line pt-5">
      <a href="<?= e(admin_url('/'.$entity)) ?>" class="btn-secondary"><?= e($form['cancel'] ?? 'Cancel') ?></a>
      <button type="submit" class="btn-primary"><?= e($isNew ? $createLabel : $updateLabel) ?></button>
    </div>
  </form>
</div>
