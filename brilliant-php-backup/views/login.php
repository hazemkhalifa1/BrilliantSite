<?php
$locale = $locale ?? current_locale();
$login = tarr('login');
$GLOBALS['page_title'] = $login['title'];
$flash = get_flash();
?>
<main class="flex min-h-screen items-center justify-center bg-neutral-light px-4 py-12">
  <div class="w-full max-w-md">
    <div class="card-brilliant bg-white p-8 md:p-10">
      <div class="flex flex-col items-center text-center">
        <div class="flex h-12 w-12 items-center justify-center bg-primary text-white">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <img src="<?= asset('/img/logo.png') ?>" alt="Brilliant Engineering" class="relative mt-6 h-10 w-56 object-contain">
        <h1 class="mt-6 font-headline text-xl font-bold uppercase"><?= e($login['title']) ?></h1>
        <p class="mt-1 text-sm text-neutral/60"><?= e($login['description']) ?></p>
      </div>
      <div class="mt-8">
        <?php if ($flash): foreach ($flash as $f): ?>
          <div class="mb-4 bg-red-50 px-3 py-2.5 text-sm text-tertiary"><?= e($f['message']) ?></div>
        <?php endforeach; endif; ?>
        <form method="post" action="<?= e(lnk('/login')) ?>" class="space-y-4">
          <?= csrf_field() ?>
          <div class="flex w-full flex-col gap-1.5">
            <label for="email" class="text-sm font-medium text-neutral"><?= e($login['email']) ?></label>
            <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" placeholder="<?= e($login['emailPlaceholder']) ?>" required class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none">
          </div>
          <div class="flex w-full flex-col gap-1.5">
            <label for="password" class="text-sm font-medium text-neutral"><?= e($login['password']) ?></label>
            <input id="password" type="password" name="password" placeholder="<?= e($login['emailPlaceholder']) ?>" required class="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none">
          </div>
          <button type="submit" class="inline-flex w-full items-center justify-center gap-2 bg-tertiary px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:opacity-90"><?= e($login['signIn']) ?></button>
        </form>
      </div>
      <p class="mt-6 text-center text-xs text-neutral/50">
        <a href="<?= e(lnk('')) ?>" class="transition-colors hover:text-secondary"><?= e($login['backToWebsite']) ?></a>
      </p>
    </div>
  </div>
</main>
