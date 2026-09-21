<?php
use App\Services\Repo;

$HOME_IMAGE = "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";
$ABOUT_IMAGE = "https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1200&q=80";
$DEFAULT_METRICS = [["PRECISION_INDEX","99.98%"],["LOAD_THRESHOLD","4500kN/m²"],["GEO_COORDS","40.7128° N, 74.0060° W"]];
/** @var string $locale @var ?array $hero @var array $services @var array $projects */
$siteUrl = app_url();

$contact = Repo::contact();
$socials = Repo::socialLinks(true, 50);
$sameAs = array_values(array_filter(array_map(fn($s) => $s['url'] ?? null, $socials)));
$localPhone = $contact['phone1'] ?? null;
$localEmail = $contact['email'] ?? null;
$localAddress = localized($locale, $contact['address'] ?? '', $contact['addressAr'] ?? null);
$geo = null;
if (!empty($contact['mapEmbedUrl']) && preg_match('/!2d([0-9.\-]+)!3d([0-9.\-]+)/', (string)$contact['mapEmbedUrl'], $gm)) {
    $geo = ['@type' => 'GeoCoordinates', 'latitude' => (float)$gm[2], 'longitude' => (float)$gm[1]];
}

$localBusiness = [
    '@context' => 'https://schema.org',
    '@type' => 'GeneralContractor',
    'name' => t('metadata.company'),
    'url' => $siteUrl,
    'logo' => $siteUrl . '/logo.png',
    'image' => $siteUrl . '/logo.png',
    'foundingDate' => '2009',
    'description' => t('metadata.ogDescription'),
];
if ($localPhone) {
    $localBusiness['telephone'] = $localPhone;
    $localBusiness['contactPoint'] = ['@type' => 'ContactPoint', 'telephone' => $localPhone, 'contactType' => 'customer service', 'availableLanguage' => ['Arabic', 'English']];
}
if ($localEmail) $localBusiness['email'] = $localEmail;
if ($localAddress !== '') {
    $localBusiness['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $localAddress, 'addressLocality' => '6th of October City', 'addressRegion' => 'Giza', 'addressCountry' => 'EG'];
}
if ($geo !== null) $localBusiness['geo'] = $geo;
if ($sameAs !== []) $localBusiness['sameAs'] = $sameAs;

$metrics = (!empty($hero['stats'])) ?
  array_map(function ($s) use ($locale) {
    return [mb_strtoupper(localized($locale, $s['label'] ?? '', $s['labelAr'] ?? null), 'UTF-8'), $s['value'] ?? ''];
  }, $hero['stats']) :
  $DEFAULT_METRICS;

$iconSet = [
  ['bg' => 'bg-secondary', 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7 text-white"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>'],
  ['bg' => 'bg-tertiary', 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7 text-white"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>'],
  ['bg' => 'bg-primary', 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7 text-white"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>'],
];
?>
<div>
<script type="application/ld+json">
<?= json_encode($localBusiness, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<header class="relative flex min-h-[85vh] w-full items-center overflow-hidden border-b-4 border-black">
  <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= e($HOME_IMAGE) ?>')"></div>
  <div class="absolute inset-0 hero-gradient"></div>
  <div class="relative z-10 w-full container-brilliant py-24 md:py-0">
    <div class="reveal-on-scroll max-w-4xl">
      <div class="mb-6 inline-block bg-tertiary px-3 py-1 font-headline text-sm font-bold uppercase tracking-tighter text-white"><?= e(t('home.eyebrow')) ?></div>
      <h1 class="mb-6 font-headline text-4xl font-bold uppercase italic leading-snug text-white sm:text-5xl md:text-6xl">
        <?= e(localized($locale, $hero['headlineTop'] ?? '', $hero['headlineTopAr'] ?? null) ?: t('home.defaultHeadlineTop')) ?>
        <br>
        <span class="mt-2 block text-tertiary md:mt-5"><?= e(localized($locale, $hero['headlineBottom'] ?? '', $hero['headlineBottomAr'] ?? null) ?: t('home.defaultHeadlineBottom')) ?></span>
      </h1>
      <p class="mb-10 max-w-xl border-s-4 border-tertiary py-2 ps-6 text-xl text-white">
        <?= e(localized($locale, $hero['subText'] ?? '', $hero['subTextAr'] ?? null) ?: t('home.defaultSubtext')) ?>
      </p>
      <div class="flex flex-wrap gap-6">
        <a href="<?= e(lnk($hero['primaryBtnUrl'] ?? '/projects')) ?>" class="flex items-center gap-3 whitespace-nowrap border-2 border-black bg-[#BA1A1A] px-10 py-5 font-headline text-lg font-bold uppercase tracking-tighter text-white shadow-[6px_6px_0px_black] transition-all hover:translate-x-[6px] hover:translate-y-[6px] hover:shadow-none">
          <?= e(localized($locale, $hero['primaryBtnText'] ?? '', $hero['primaryBtnTextAr'] ?? null) ?: t('home.defaultPrimaryBtn')) ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-xl"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
        <a href="<?= e(lnk($hero['secondaryBtnUrl'] ?? '/about')) ?>" class="whitespace-nowrap border-2 border-black bg-white px-10 py-5 font-headline text-lg font-bold uppercase tracking-tighter text-black shadow-[6px_6px_0px_black] transition-all hover:translate-x-[6px] hover:translate-y-[6px] hover:shadow-none">
          <?= e(localized($locale, $hero['secondaryBtnText'] ?? '', $hero['secondaryBtnTextAr'] ?? null) ?: t('home.defaultSecondaryBtn')) ?>
        </a>
      </div>
    </div>
  </div>
  <div class="absolute bottom-12 end-4 z-10 hidden lg:block xl:end-12">
    <div class="border-4 border-black bg-white p-6 shadow-[8px_8px_0px_#BA1A1A]">
      <div class="monospaced space-y-2 text-xs font-bold text-black">
        <p class="text-tertiary"><?= e(t('home.metricsLabel')) ?></p>
        <?php foreach ($metrics as $m): ?><p><?= e($m[0]) ?>: <span dir="ltr"><?= e($m[1]) ?></span></p><?php endforeach; ?>
      </div>
    </div>
  </div>
</header>

<?php if (count($services)): ?>
<section class="border-b-4 border-black bg-white py-24">
  <div class="container-brilliant grid grid-cols-1 items-start gap-16 lg:grid-cols-12">
    <div class="lg:col-span-4 lg:sticky lg:top-32">
      <div class="reveal-on-scroll">
        <span class="mb-4 block font-headline text-sm font-bold uppercase tracking-widest text-tertiary"><?= e(t('home.servicesEyebrow')) ?></span>
        <h2 class="mb-6 break-words font-headline text-4xl font-bold uppercase leading-none text-black md:text-5xl"><?= e(t('home.servicesTitle1')) ?><br><?= e(t('home.servicesTitle2')) ?></h2>
        <p class="mb-8 text-lg text-neutral/70"><?= e(t('home.servicesText')) ?></p>
        <div class="monospaced border-s-4 border-black py-2 ps-4 text-sm font-bold text-secondary"><?= e(t('home.servicesEst')) ?></div>
      </div>
    </div>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:col-span-8">
      <?php $i = 0; foreach ($services as $service): $style = $iconSet[$i % 3]; ?>
        <div class="reveal-on-scroll <?= count($services) % 2 === 1 && $i === count($services) - 1 ? 'md:col-span-2' : '' ?>" style="transition-delay: <?= ($i % 3) * 100 ?>ms">
          <div class="brutalist-card h-full bg-white p-8">
            <div class="mb-8 flex h-14 w-14 items-center justify-center border-2 border-black <?= $style['bg'] ?>"><?= $style['svg'] ?></div>
            <h3 class="mb-4 font-headline text-2xl font-bold uppercase text-black"><?= e(localized($locale, $service['title'], $service['titleAr'])) ?></h3>
            <p class="text-neutral/70"><?= e(localized($locale, $service['description'], $service['descriptionAr'])) ?></p>
          </div>
        </div>
      <?php $i++; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="overflow-hidden border-b-4 border-black bg-surface py-24">
  <div class="container-brilliant grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
    <div class="reveal-on-scroll relative order-2 lg:order-1">
      <div class="mx-auto aspect-square max-w-md border-4 border-black bg-cover bg-center shadow-[16px_16px_0px_#0059BB]" style="background-image: url('<?= e($ABOUT_IMAGE) ?>')"></div>
      <div class="absolute -left-6 -top-6 -z-10 h-32 w-32 border-2 border-black bg-tertiary"></div>
    </div>
    <div class="reveal-on-scroll order-1 lg:order-2">
      <span class="monospaced mb-6 block text-sm font-bold uppercase tracking-[0.2em] text-tertiary"><?= e(t('home.philosophyEyebrow')) ?></span>
      <h2 class="mb-8 break-words font-headline text-4xl font-bold uppercase leading-none text-black md:text-5xl"><?= e(t('home.philosophyTitle1')) ?><br><?= e(t('home.philosophyTitle2')) ?></h2>
      <div class="space-y-6">
        <p class="border-s-4 border-black ps-6 text-lg italic text-neutral"><?= e(t('home.philosophyText1')) ?></p>
        <p class="text-neutral/70"><?= e(t('home.philosophyText2')) ?></p>
      </div>
      <div class="mt-12">
        <a href="<?= e(lnk('/about')) ?>" class="group inline-flex items-center gap-4 border-b-4 border-tertiary pb-1 font-headline text-sm font-bold uppercase tracking-tighter text-black">
          <?= e(t('home.readOurStory')) ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:translate-x-2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<?php if (count($projects)): ?>
<section class="border-b-4 border-black bg-white py-24">
  <div class="container-brilliant">
    <div class="reveal-on-scroll mb-16">
      <span class="mb-4 block font-headline text-sm font-bold uppercase tracking-widest text-tertiary"><?= e(t('home.portfolioEyebrow')) ?></span>
      <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
        <h2 class="break-words font-headline text-4xl font-bold uppercase leading-none text-black md:text-5xl"><?= e(t('home.portfolioTitle1')) ?><br><?= e(t('home.portfolioTitle2')) ?></h2>
        <a href="<?= e(lnk('/projects')) ?>" class="inline-flex items-center gap-3 border-b-4 border-black pb-1 font-headline text-sm font-bold uppercase tracking-tighter text-black transition-colors hover:border-tertiary hover:text-tertiary"><?= e(t('home.viewAllProjects')) ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
      </div>
    </div>
    <div class="grid grid-cols-1 gap-10 md:grid-cols-3">
      <?php foreach ($projects as $idx => $project): ?>
        <div class="reveal-on-scroll" style="transition-delay: <?= $idx * 100 ?>ms">
          <a href="<?= e(lnk('/projects/' . $project['id'])) ?>" class="group block border-2 border-black bg-white p-4 transition-colors hover:bg-surface-container">
            <div class="relative mb-6 aspect-[4/3] overflow-hidden border-2 border-black">
              <div class="absolute inset-0 transition-transform duration-700 group-hover:scale-105">
                <img src="<?= e(Repo::getImagePath($project, 'imagePath')) ?>" alt="<?= e(localized($locale, $project['title'], $project['titleAr'])) ?>" loading="lazy" class="h-full w-full object-cover">
              </div>
              <div class="absolute inset-0 bg-primary opacity-0 transition-opacity group-hover:opacity-40"></div>
            </div>
            <div class="flex items-end justify-between gap-4">
              <div>
                <span class="monospaced mb-2 block text-xs font-bold uppercase text-tertiary"><?= e($project['typeName'] ?? t('home.projectFallback')) ?></span>
                <h4 class="font-headline text-2xl font-bold uppercase text-black"><?= e(localized($locale, $project['title'], $project['titleAr'])) ?></h4>
              </div>
              <span class="monospaced shrink-0 border-2 border-black bg-white px-2 py-1 text-xs font-bold text-black"><?= e((string)$project['year']) ?></span>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
</div>
