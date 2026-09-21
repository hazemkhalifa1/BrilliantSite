<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$contact = Repo::contact();
$socials = Repo::socialLinks(true, 50);
$c = tarr('contact');
$cf = tarr('contactForm');
$GLOBALS['page_title'] = $c['metadataTitle'];
$GLOBALS['meta_description'] = $c['metadataDescription'];
$flash = get_flash();
?>
<div>
  <!-- Hero -->
  <section class="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
    <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px); background-size: 32px 32px;"></div>
    <div class="absolute inset-0">
      <div class="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity" data-parallax="0.08" style="background-image: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80');"></div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50"></div>

    <div class="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <span class="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white"><?= e($c['eyebrow']) ?></span>
      <h1 class="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl"><?= e($c['title']) ?></h1>
      <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl"><?= e($c['subtitle']) ?></p>
    </div>
  </section>

  <section class="py-16 md:py-20">
    <div class="container-brilliant grid gap-10 lg:grid-cols-2">
      <div class="reveal-on-scroll" data-from="left">
        <form method="post" action="<?= e(lnk('/contact')) ?>" class="card-brilliant p-6 md:p-8">
      <?= csrf_field() ?>
      <h2 class="font-headline text-xl font-bold uppercase"><?= e($cf['sendMessage']) ?></h2>
      <span class="mt-2 block h-[3px] w-10 bg-tertiary" aria-hidden="true"></span>
      <?php if ($flash): foreach ($flash as $f): ?>
        <div class="mt-4 px-3 py-2.5 text-sm <?= $f['type']==='success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-tertiary' ?>"><?= e($f['message']) ?></div>
      <?php endforeach; endif; ?>
      <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="flex w-full flex-col gap-1.5">
          <label for="cf-name" class="text-sm font-medium text-neutral"><?= e($cf['fullName']) ?></label>
          <input id="cf-name" name="name" value="<?= e(old('name')) ?>" placeholder="<?= e($cf['namePlaceholder']) ?>" required class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none">
        </div>
        <div class="flex w-full flex-col gap-1.5">
          <label for="cf-email" class="text-sm font-medium text-neutral"><?= e($cf['email']) ?></label>
          <input id="cf-email" type="email" name="email" value="<?= e(old('email')) ?>" placeholder="<?= e($cf['emailPlaceholder']) ?>" required class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none">
        </div>
      </div>
      <div class="mt-4">
        <div class="flex w-full flex-col gap-1.5">
          <label for="cf-phone" class="text-sm font-medium text-neutral"><?= e($cf['phone']) ?></label>
          <input id="cf-phone" name="phone" value="<?= e(old('phone')) ?>" placeholder="<?= e($cf['phonePlaceholder']) ?>" class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none">
        </div>
      </div>
      <div class="mt-4 flex flex-col gap-1.5">
        <label for="cf-message" class="text-sm font-medium text-neutral"><?= e($cf['message']) ?></label>
        <textarea id="cf-message" name="message" rows="5" placeholder="<?= e($cf['messagePlaceholder']) ?>" required class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none"><?= e(old('message')) ?></textarea>
      </div>
      <button type="submit" class="mt-6 inline-flex items-center justify-center gap-2 bg-tertiary px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:opacity-90"><?= e($cf['sendButton']) ?></button>
      </form>
      </div>

      <div class="reveal-on-scroll" data-from="right" style="transition-delay: 120ms">
        <div class="card-brilliant bg-neutral p-8 text-white">
        <h2 class="font-headline text-xl font-bold uppercase"><?= e($c['contactInfo']) ?></h2>
        <span class="mt-2 block h-[3px] w-10 bg-tertiary" aria-hidden="true"></span>
        <ul class="mt-6 space-y-5">
          <li class="flex items-start gap-4">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 shrink-0 text-tertiary"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <div>
              <p class="text-sm text-white/60"><?= e($c['phone']) ?></p>
              <p class="mt-0.5"><?= e($contact['phone1'] ?? '—') ?></p>
              <?php if (!empty($contact['phone2'])): ?><p class="mt-0.5"><?= e($contact['phone2']) ?></p><?php endif; ?>
            </div>
          </li>
          <li class="flex items-start gap-4">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 shrink-0 text-tertiary"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            <div>
              <p class="text-sm text-white/60"><?= e($c['email']) ?></p>
              <a href="mailto:<?= e($contact['email'] ?? '') ?>" class="mt-0.5 block transition-colors hover:text-secondary"><?= e($contact['email'] ?? '—') ?></a>
              <?php if (!empty($contact['email2'])): ?><a href="mailto:<?= e($contact['email2']) ?>" class="mt-0.5 block transition-colors hover:text-secondary"><?= e($contact['email2']) ?></a><?php endif; ?>
            </div>
          </li>
          <li class="flex items-start gap-4">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 shrink-0 text-tertiary"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <div>
              <p class="text-sm text-white/60"><?= e($c['address']) ?></p>
              <p class="mt-0.5"><?= e(localized($locale, $contact['address'] ?? '', $contact['addressAr'] ?? null) ?: '—') ?></p>
            </div>
          </li>
        </ul>
        <?php if (count($socials)): ?>
        <div class="mt-8 border-t border-white/15 pt-6">
          <p class="font-headline text-sm font-semibold uppercase tracking-widest text-white/60"><?= e($c['followUs']) ?></p>
          <div class="mt-3 flex flex-wrap gap-2">
            <?php foreach ($socials as $social): ?>
              <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" class="border border-white/25 px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-colors hover:border-tertiary hover:text-tertiary"><?= e($social['platform']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <?php if (!empty($contact['mapEmbedUrl'])): ?>
  <section class="pb-16 md:pb-20">
    <div class="container-brilliant">
      <div class="reveal-on-scroll">
        <div class="aspect-[21/9] w-full overflow-hidden border border-line">
          <iframe src="<?= e($contact['mapEmbedUrl']) ?>" title="<?= e($c['mapTitle']) ?>" class="h-full w-full" loading="lazy" referrerPolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>
</div>
