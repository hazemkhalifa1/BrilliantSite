<?php
use App\Services\Repo;
$locale = $locale ?? current_locale();
$t = tarr('team');
$GLOBALS['page_title'] = $t['metadataTitle'];
$GLOBALS['meta_description'] = $t['metadataDescription'];

$items = Repo::team(true, 1, 100)['items'];
$featured = $items[0] ?? null;
$supporting = array_slice($items, 1);
$tagline = t('footer.tagline');

$initials = function (?string $name): string {
    if ($name === null || trim($name) === '') return '?';
    $parts = array_values(array_filter(preg_split('/\s+/u', trim($name))));
    $parts = array_values(array_filter($parts, fn($p) => !preg_match('/\.$/u', $p)));
    $list = count($parts) > 0 ? $parts : array_values(array_filter(preg_split('/\s+/u', trim($name))));
    $first = mb_substr($list[0] ?? '', 0, 1, 'UTF-8');
    $last = count($list) > 1 ? mb_substr($list[count($list) - 1], 0, 1, 'UTF-8') : '';
    return mb_strtoupper($first . $last, 'UTF-8');
};

$memberName = fn(array $m): string => localized($locale, $m['name'], $m['nameAr'] ?? null);
$memberRole = fn(array $m): string => localized($locale, $m['jobTitle'], $m['jobTitleAr'] ?? null);
$memberDesc = fn(array $m): string => localized($locale, $m['description'] ?? '', $m['descriptionAr'] ?? null);

$renderMedia = function (array $member) use ($initials, $locale): void {
    $name = localized($locale, $member['name'], $member['nameAr'] ?? null);
    $src = Repo::getImagePath($member, 'imagePath');
    if (!empty($member['imagePath'])): ?>
      <img src="<?= e($src) ?>" alt="<?= e($name) ?>" loading="lazy"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
      <div class="neo-fallback" style="display:none"><?= e($initials($name)) ?></div>
    <?php else: ?>
      <div class="neo-fallback"><?= e($initials($name)) ?></div>
    <?php endif;
};

$pillars = [
    ['n' => '01', 't' => localized($locale, 'Precision', 'الدقة'), 'd' => localized($locale, 'Every drawing, joint and handover engineered to exacting standards.', 'كل رسم ووصلة وتسليم مُنفَّذ بمعايير دقيقة.')],
    ['n' => '02', 't' => localized($locale, 'Delivery', 'التسليم'), 'd' => localized($locale, 'We commit to timelines and own them through to completion.', 'نلتزم بالجداول الزمنية ونحترمها حتى الاكتمال.')],
    ['n' => '03', 't' => localized($locale, 'Safety', 'السلامة'), 'd' => localized($locale, 'Zero-compromise safety culture on every site, every day.', 'ثقافة سلامة لا تقبل الحلول الوسط في كل موقع وكل يوم.')],
];

$phEyebrow = $t['eyebrow'];
$phTitle = $t['title'];
$phSubtitle = $t['subtitle'];
require base_path('/views/partials/page-header.php');
?>
<div>
  <!-- Team -->
  <section class="py-16 md:py-24">
    <div class="container-brilliant">
      <?php if (count($items) === 0): ?>
        <div class="neo-empty"><?= e($t['updating']) ?></div>
      <?php else: ?>

        <?php if ($featured): ?>
          <div class="neo-featured reveal-on-scroll">
            <div class="neo-featured__media">
              <?php $renderMedia($featured); ?>
            </div>
            <div class="neo-featured__info">
              <span class="neo-featured__index"><?= e(localized($locale, 'Leadership', 'القيادة')) ?></span>
              <span class="neo-featured__monogram" aria-hidden="true"><?= e($initials($memberName($featured))) ?></span>
              <span class="neo-featured__role"><?= e($memberRole($featured)) ?></span>
              <h2 class="neo-featured__name"><?= e($memberName($featured)) ?></h2>
              <?php if ($memberDesc($featured) !== ''): ?>
                <p class="neo-featured__desc"><?= e($memberDesc($featured)) ?></p>
              <?php endif; ?>
              <p class="monospaced" style="position:relative;margin-top:2rem;font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.45)"><?= e($tagline) ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?php if (count($supporting) > 0): ?>
          <div class="mt-12 grid gap-6 md:grid-cols-2">
            <?php foreach ($supporting as $i => $member): ?>
              <article class="neo-team-card reveal-on-scroll" style="transition-delay: <?= $i * 80 ?>ms">
                <div class="neo-team-card__media">
                  <?php $renderMedia($member); ?>
                </div>
                <div class="neo-team-card__body">
                  <div class="neo-team-card__role"><?= e($memberRole($member)) ?></div>
                  <h3 class="neo-team-card__name"><?= e($memberName($member)) ?></h3>
                  <?php if ($memberDesc($member) !== ''): ?>
                    <p class="neo-team-card__desc"><?= e($memberDesc($member)) ?></p>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </section>

  <!-- How we work -->
  <section class="border-t-4 border-black bg-surface py-16 md:py-24">
    <div class="container-brilliant grid gap-10 lg:grid-cols-12">
      <div class="lg:col-span-4">
        <span class="neo-badge reveal-on-scroll"><?= e(localized($locale, 'How we work', 'كيف نعمل')) ?></span>
        <h2 class="mt-6 font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight text-neutral md:text-5xl reveal-on-scroll"><?= e(localized($locale, 'Standards, not slogans', 'معايير، لا شعارات')) ?></h2>
      </div>
      <div class="lg:col-span-8">
        <div class="neo-pillars">
          <?php foreach ($pillars as $i => $p): ?>
            <div class="neo-pillar reveal-on-scroll" style="transition-delay: <?= $i * 80 ?>ms">
              <div class="neo-pillar__num"><?= e($p['n']) ?></div>
              <div class="neo-pillar__title"><?= e($p['t']) ?></div>
              <p class="neo-pillar__text"><?= e($p['d']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="border-t-4 border-black bg-neutral py-16 text-white md:py-20">
    <div class="container-brilliant flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
      <div>
        <h2 class="font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight md:text-5xl"><?= e(localized($locale, 'Work with a team that owns it', 'اعمل مع فريق يتحمّل المسؤولية')) ?></h2>
        <p class="mt-4 max-w-xl text-white/70"><?= e(localized($locale, 'Bring your next project to a team that treats it like their own.', 'أحضر مشروعك القادم إلى فريق يتعامل معه كما لو كان مشروعه الخاص.')) ?></p>
      </div>
      <a href="<?= e(lnk('/contact')) ?>" class="btn-primary"><?= e(t('nav.getQuote')) ?></a>
    </div>
  </section>
</div>
