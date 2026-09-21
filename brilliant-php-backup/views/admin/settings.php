<?php
/** @var ?array $hero @var ?array $contact @var array $socials @var array $tags */
$s = tarr('admin.settings');
$badgeActive = '<span class="inline-flex items-center bg-green-600 px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide text-white">' . e(t('common.active')) . '</span>';
$badgeInactive = '<span class="inline-flex items-center bg-neutral-light px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide text-neutral">' . e(t('common.inactive')) . '</span>';
$trashIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';
?>
<div class="space-y-8">
  <div class="border-b border-line pb-5">
    <h1 class="font-headline text-2xl font-bold uppercase"><?= e($s['title']) ?></h1>
    <p class="mt-1 text-sm text-neutral/60"><?= e($s['description']) ?></p>
  </div>

  <div class="grid items-start gap-8 xl:grid-cols-2">
    <!-- Hero -->
    <section class="card-brilliant space-y-8 bg-white p-6">
      <div>
        <h2 class="font-headline text-lg font-bold uppercase"><?= e($s['hero']['title']) ?></h2>
        <p class="mt-1 text-sm text-neutral/60"><?= e($s['hero']['description']) ?></p>
      </div>

      <form method="post" action="<?= e(admin_url('/settings/hero')) ?>" class="space-y-5">
        <?= csrf_field() ?>
        <div class="grid gap-5 sm:grid-cols-2">
          <?php
          $heroFields = [
            ['headlineTop', 'headlineTop', 'text'],
            ['headlineTopAr', 'headlineTopAr', 'text'],
            ['headlineBottom', 'headlineBottom', 'text'],
            ['headlineBottomAr', 'headlineBottomAr', 'text'],
          ];
          foreach ($heroFields as $hf): [$key, $lbl] = $hf; ?>
            <div class="flex w-full flex-col gap-1.5">
              <label class="text-sm font-medium text-neutral"><?= e($s['hero'][$lbl] ?? $key) ?></label>
              <input name="<?= e($key) ?>" value="<?= e($hero[$key] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none"/>
            </div>
          <?php endforeach; ?>
          <?php $subFields = [['subText','subText'],['subTextAr','subTextAr']]; foreach ($subFields as $sf): ?>
            <div class="flex w-full flex-col gap-1.5 sm:col-span-2">
              <label class="text-sm font-medium text-neutral"><?= e($s['hero'][$sf[1]] ?? $sf[0]) ?></label>
              <textarea name="<?= e($sf[0]) ?>" rows="3" class="w-full resize-y border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none"><?= e($hero[$sf[0]] ?? '') ?></textarea>
            </div>
          <?php endforeach; ?>
          <?php
          $btnFields = [
            ['primaryBtnText','primaryBtnText'], ['primaryBtnTextAr','primaryBtnTextAr'], ['primaryBtnUrl','primaryBtnUrl'],
            ['secondaryBtnText','secondaryBtnText'], ['secondaryBtnTextAr','secondaryBtnTextAr'], ['secondaryBtnUrl','secondaryBtnUrl'],
          ];
          foreach ($btnFields as $bf): ?>
            <div class="flex w-full flex-col gap-1.5">
              <label class="text-sm font-medium text-neutral"><?= e($s['hero'][$bf[1]] ?? $bf[0]) ?></label>
              <input name="<?= e($bf[0]) ?>" value="<?= e($hero[$bf[0]] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none"/>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="flex justify-end">
          <button class="btn-primary"><?= e($s['hero']['save']) ?></button>
        </div>
      </form>

      <div class="border-t border-line pt-8">
        <h3 class="font-headline text-base font-bold uppercase"><?= e($s['hero']['stats']) ?></h3>
        <p class="mt-1 text-sm text-neutral/60"><?= e($s['hero']['statsDescription']) ?></p>

        <div class="mt-5 overflow-x-auto border border-line">
          <table class="w-full min-w-max text-left text-sm">
            <thead>
              <tr class="border-b border-line bg-neutral-light">
                <th class="px-4 py-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral"><?= e($s['hero']['value'] ?? 'Value') ?></th>
                <th class="px-4 py-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral"><?= e($s['hero']['label'] ?? 'Label') ?></th>
                <th class="px-4 py-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral"><?= e(t('common.order')) ?></th>
                <th class="px-4 py-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral"><?= e(t('common.status')) ?></th>
                <th class="px-4 py-3 text-right font-headline text-xs font-semibold uppercase tracking-wide text-neutral"><?= e(t('admin.list.actions')) ?></th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($hero['stats'] ?? [])): foreach ($hero['stats'] as $st): ?>
                <tr class="border-b border-line last:border-b-0 hover:bg-neutral-light/60">
                  <td class="px-4 py-3 align-middle"><span class="font-semibold text-neutral"><?= e($st['statValue']) ?></span></td>
                  <td class="px-4 py-3 align-middle text-neutral/80"><?= e(lpair($st['label'], $st['labelAr'] ?? null)) ?></td>
                  <td class="px-4 py-3 align-middle text-neutral/80"><?= e((string)($st['sortOrder'] ?? 0)) ?></td>
                  <td class="px-4 py-3 align-middle"><?= $st['isActive'] ? $badgeActive : $badgeInactive ?></td>
                  <td class="px-4 py-3 align-middle">
                    <div class="flex justify-end">
                      <form method="post" action="<?= e(admin_url('/settings/hero')) ?>">
                        <?= csrf_field() ?><input type="hidden" name="action" value="delete-stat"><input type="hidden" name="id" value="<?= e((string)$st['id']) ?>">
                        <button aria-label="<?= e($s['hero']['deleteStat'] ?? 'Delete stat') ?>" class="inline-flex h-8 w-8 items-center justify-center border border-line text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"><?= $trashIcon ?></button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td class="px-4 py-12 text-center text-neutral/50" colspan="5"><?= e($s['hero']['noStats']) ?></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <form method="post" action="<?= e(admin_url('/settings/hero')) ?>" class="mt-5 grid gap-4 border border-line bg-neutral-light p-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
          <?= csrf_field() ?><input type="hidden" name="action" value="add-stat">
          <div class="flex w-full flex-col gap-1.5">
            <label class="text-sm font-medium text-neutral"><?= e($s['hero']['value'] ?? 'Value') ?></label>
            <input name="stat_value" placeholder="<?= e($s['hero']['statValuePlaceholder'] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
          </div>
          <div class="flex w-full flex-col gap-1.5">
            <label class="text-sm font-medium text-neutral"><?= e($s['hero']['label'] ?? 'Label') ?></label>
            <input name="label" placeholder="<?= e($s['hero']['statLabelPlaceholder'] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
          </div>
          <div class="flex w-full flex-col gap-1.5 sm:col-span-2">
            <label class="text-sm font-medium text-neutral"><?= e($s['hero']['labelAr'] ?? 'Label (Arabic)') ?></label>
            <input name="labelAr" placeholder="<?= e($s['hero']['arPlaceholder'] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
          </div>
          <div class="pb-1">
            <label class="flex cursor-pointer items-start gap-3">
              <span class="toggle on"><input type="checkbox" name="is_active" value="1" checked><span class="thumb"></span></span>
              <span><span class="block text-sm font-medium text-neutral"><?= e(t('common.active')) ?></span></span>
            </label>
          </div>
          <button class="btn-secondary !py-2.5 sm:col-span-2"><?= e($s['hero']['add']) ?></button>
        </form>
      </div>
    </section>

    <div class="space-y-8">
      <!-- Contact -->
      <section class="card-brilliant space-y-6 bg-white p-6">
        <div>
          <h2 class="font-headline text-lg font-bold uppercase"><?= e($s['contact']['title']) ?></h2>
          <p class="mt-1 text-sm text-neutral/60"><?= e($s['contact']['description']) ?></p>
        </div>
        <form method="post" action="<?= e(admin_url('/settings/contact')) ?>" class="space-y-5">
          <?= csrf_field() ?>
          <div class="grid gap-5 sm:grid-cols-2">
            <?php foreach (['phone1'=>'primaryPhone','phone2'=>'secondaryPhone','email'=>'email','email2'=>'secondaryEmail'] as $k=>$lbl): ?>
              <div class="flex w-full flex-col gap-1.5">
                <label class="text-sm font-medium text-neutral"><?= e($s['contact'][$lbl] ?? $k) ?></label>
                <input name="<?= e($k) ?>" value="<?= e($contact[$k] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none"/>
              </div>
            <?php endforeach; ?>
            <?php foreach (['address'=>'address','addressAr'=>'addressAr'] as $k=>$lbl): ?>
              <div class="flex w-full flex-col gap-1.5 sm:col-span-2">
                <label class="text-sm font-medium text-neutral"><?= e($s['contact'][$lbl] ?? $k) ?></label>
                <textarea name="<?= e($k) ?>" rows="2" class="w-full resize-y border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none"><?= e($contact[$k] ?? '') ?></textarea>
              </div>
            <?php endforeach; ?>
            <div class="flex w-full flex-col gap-1.5 sm:col-span-2">
              <label class="text-sm font-medium text-neutral"><?= e($s['contact']['mapEmbedUrl']) ?></label>
              <input name="mapEmbedUrl" value="<?= e($contact['mapEmbedUrl'] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none"/>
              <p class="text-xs text-neutral/60"><?= e($s['contact']['mapHint'] ?? '') ?></p>
            </div>
          </div>
          <div class="flex justify-end">
            <button class="btn-primary"><?= e($s['contact']['save']) ?></button>
          </div>
        </form>
      </section>

      <!-- Social -->
      <section class="card-brilliant space-y-6 bg-white p-6">
        <div>
          <h2 class="font-headline text-lg font-bold uppercase"><?= e($s['social']['title']) ?></h2>
          <p class="mt-1 text-sm text-neutral/60"><?= e($s['social']['description']) ?></p>
        </div>

        <?php if (count($socials)): ?>
          <ul class="divide-y divide-line border border-line">
            <?php foreach ($socials as $so): ?>
              <li class="flex items-center justify-between gap-3 px-4 py-3">
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-neutral"><?= e($so['platform']) ?></span>
                    <?= $so['isActive'] ? $badgeActive : $badgeInactive ?>
                  </div>
                  <a href="<?= e($so['url']) ?>" target="_blank" rel="noopener noreferrer" class="block truncate text-xs text-neutral/50 hover:text-secondary"><?= e($so['url']) ?></a>
                </div>
                <form method="post" action="<?= e(admin_url('/settings/social')) ?>">
                  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e((string)$so['id']) ?>">
                  <button aria-label="<?= e($s['social']['delete'] ?? 'Delete') ?>" class="inline-flex h-8 w-8 shrink-0 items-center justify-center border border-line text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"><?= $trashIcon ?></button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="py-6 text-center text-sm text-neutral/50"><?= e($s['social']['empty']) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= e(admin_url('/settings/social')) ?>" class="space-y-4 border border-line bg-neutral-light p-4">
          <?= csrf_field() ?>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex w-full flex-col gap-1.5">
              <label class="text-sm font-medium text-neutral"><?= e($s['social']['platform']) ?></label>
              <input name="platform" placeholder="<?= e($s['social']['platformPlaceholder'] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
            </div>
            <div class="flex w-full flex-col gap-1.5">
              <label class="text-sm font-medium text-neutral"><?= e($s['social']['url']) ?></label>
              <input name="url" type="url" placeholder="https://…" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
            </div>
          </div>
          <div class="flex w-full flex-col gap-1.5">
            <label class="text-sm font-medium text-neutral"><?= e($s['social']['iconClass']) ?></label>
            <input name="iconClass" placeholder="fab fa-linkedin" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
          </div>
          <div class="flex items-center justify-between gap-4">
            <label class="flex cursor-pointer items-start gap-3">
              <span class="toggle on"><input type="checkbox" name="is_active" value="1" checked><span class="thumb"></span></span>
              <span><span class="block text-sm font-medium text-neutral"><?= e(t('common.active')) ?></span></span>
            </label>
            <button class="btn-secondary"><?= e($s['social']['add']) ?></button>
          </div>
        </form>
      </section>

      <!-- Tags -->
      <section class="card-brilliant space-y-6 bg-white p-6">
        <div>
          <h2 class="font-headline text-lg font-bold uppercase"><?= e($s['tags']['title']) ?></h2>
          <p class="mt-1 text-sm text-neutral/60"><?= e($s['tags']['description']) ?></p>
        </div>

        <?php if (count($tags)): ?>
          <ul class="divide-y divide-line border border-line">
            <?php foreach ($tags as $tag): ?>
              <li class="flex items-center justify-between gap-3 px-4 py-3">
                <div>
                  <span class="font-semibold text-neutral"><?= e($tag['name']) ?></span>
                  <p class="text-xs text-neutral/50">/<?= e($tag['slug']) ?></p>
                </div>
                <form method="post" action="<?= e(admin_url('/settings/tags')) ?>">
                  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e((string)$tag['id']) ?>">
                  <button aria-label="<?= e($s['tags']['delete'] ?? 'Delete') ?>" class="inline-flex h-8 w-8 items-center justify-center border border-line text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"><?= $trashIcon ?></button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="py-6 text-center text-sm text-neutral/50"><?= e($s['tags']['empty']) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= e(admin_url('/settings/tags')) ?>" class="flex flex-col gap-3 sm:flex-row sm:items-end">
          <?= csrf_field() ?>
          <div class="flex w-full flex-col gap-1.5">
            <label class="text-sm font-medium text-neutral"><?= e($s['tags']['tagName']) ?></label>
            <input name="name" placeholder="<?= e($s['tags']['tagNamePlaceholder'] ?? '') ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral focus:border-secondary focus:outline-none">
          </div>
          <button class="btn-secondary"><?= e($s['tags']['add']) ?></button>
        </form>
      </section>
    </div>
  </div>
</div>
