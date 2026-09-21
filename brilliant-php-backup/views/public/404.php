<?php
$GLOBALS['meta_head'] = ($GLOBALS['meta_head'] ?? '') . '<meta name="robots" content="noindex, nofollow">' . "\n";
$phEyebrow = t('common.notFound');
$phTitle = '404';
$phSubtitle = t('common.notFound');
require base_path('/views/partials/page-header.php');
?>
<section class="py-24">
  <div class="container-brilliant text-center">
    <a href="<?= e(lnk('')) ?>" class="btn-primary"><?= e(t('nav.home')) ?></a>
  </div>
</section>
