<?php
/** @var string $title @var array $dash @var array $sections @var ?array $contact */
$icons = [
  'services' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>',
  'projects' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M15.5 3H5a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V8.5L15.5 3Z"/><path d="M15 3v6h6"/></svg>',
  'products' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
  'blog' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>',
  'team' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
  'clients' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/></svg>',
];
$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-neutral/40"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
$quickActions = [
  ['ent' => 'services', 'label' => $dash['newService'] ?? 'New Service'],
  ['ent' => 'projects', 'label' => $dash['newProject'] ?? 'New Project'],
  ['ent' => 'products', 'label' => $dash['newProduct'] ?? 'New Product'],
  ['ent' => 'blog', 'label' => $dash['newBlogPost'] ?? 'New Blog Post'],
  ['ent' => 'team', 'label' => $dash['newTeamMember'] ?? 'New Team Member'],
  ['ent' => 'clients', 'label' => $dash['newClient'] ?? 'New Client'],
];
?>
<div class="space-y-8">
  <div class="flex flex-wrap items-center justify-between gap-4 border-b border-line pb-5">
    <div>
      <h1 class="font-headline text-2xl font-bold uppercase"><?= e($dash['title'] ?? 'Dashboard') ?></h1>
      <p class="mt-1 text-sm text-neutral/60"><?= e($dash['description'] ?? '') ?></p>
    </div>
    <a href="<?= e(admin_url('/settings')) ?>" class="btn-secondary !px-4 !py-2"><?= e($dash['siteSettings'] ?? 'Settings') ?></a>
  </div>

  <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($sections as $s): ?>
      <a href="<?= e($s['href']) ?>" class="card-brilliant group flex items-center justify-between bg-white p-6 transition-colors hover:border-secondary">
        <div class="flex items-center gap-4">
          <div class="flex h-11 w-11 items-center justify-center bg-neutral-light text-secondary">
            <?= $icons[$s['key']] ?? '' ?>
          </div>
          <div>
            <p class="font-headline text-2xl font-bold text-neutral"><?= e((string)$s['count']) ?></p>
            <p class="font-headline text-xs font-semibold uppercase tracking-wide text-neutral/60"><?= e($s['label']) ?></p>
          </div>
        </div>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-neutral/40 transition-transform group-hover:translate-x-1 group-hover:text-secondary"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
    <?php endforeach; ?>
  </section>

  <div class="grid gap-4 lg:grid-cols-3">
    <div class="card-brilliant bg-white p-6 lg:col-span-2">
      <div class="flex items-center justify-between">
        <h2 class="font-headline text-lg font-bold uppercase"><?= e($dash['quickActions'] ?? 'Quick Actions') ?></h2>
      </div>
      <div class="mt-5 grid gap-3 sm:grid-cols-2">
        <?php foreach ($quickActions as $qa): ?>
          <a href="<?= e(admin_url('/' . $qa['ent'] . '/new')) ?>" class="inline-flex items-center justify-between border border-line px-4 py-3 text-sm text-neutral transition-colors hover:border-secondary hover:bg-neutral-light">
            <?= e($qa['label']) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-neutral/40"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card-brilliant bg-neutral p-6 text-white">
      <h2 class="font-headline text-lg font-bold uppercase"><?= e($dash['contactInfo'] ?? 'Contact Info') ?></h2>
      <span class="mt-2 block h-[3px] w-8 bg-tertiary" aria-hidden="true"></span>
      <dl class="mt-5 space-y-3 text-sm">
        <div>
          <dt class="text-white/50"><?= e($dash['phone'] ?? 'Phone') ?></dt>
          <dd class="mt-0.5"><?= e($contact['phone1'] ?? '—') ?></dd>
        </div>
        <div>
          <dt class="text-white/50"><?= e($dash['email'] ?? 'Email') ?></dt>
          <dd class="mt-0.5"><?= e($contact['email'] ?? '—') ?></dd>
        </div>
        <div>
          <dt class="text-white/50"><?= e($dash['address'] ?? 'Address') ?></dt>
          <dd class="mt-0.5 leading-relaxed"><?= e($contact['address'] ?? '—') ?></dd>
        </div>
      </dl>
      <a href="<?= e(admin_url('/settings')) ?>" class="mt-6 inline-flex items-center gap-2 border border-white/25 px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors hover:border-tertiary hover:text-tertiary">
        <?= e($dash['editContact'] ?? 'Edit Contact') ?>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
    </div>
  </div>

  <div class="flex items-center justify-end gap-2 text-xs text-neutral/50">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
    <span><?= e($dash['settingsNote'] ?? 'Hero, contact and social links are managed in Settings.') ?></span>
  </div>
</div>
