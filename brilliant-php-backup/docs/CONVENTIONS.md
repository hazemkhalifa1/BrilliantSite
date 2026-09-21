# Brilliant PHP Public Page Conventions

The site is a PHP port of the Next.js app at C:/Users/Ahmed/Downloads/Brilliant-Eng-Website/BrilliantSite/brilliant-frontend.
Public pages live in C:/Users/Ahmed/Downloads/Brilliant-Eng-Website/brilliant-php/views/public/<name>.php.
Each page is a SELF-CONTAINED view (no controller needed). It is rendered via the layouts/public layout (which already outputs
<html> head, navbar, footer, scripts). Use the already-working exemplars:
  - views/public/home.php      (hero + sections, uses Repo)
  - views/public/contact.php   (uses page-header partial + Repo + i18n)

## View contract
When the view is required, these are set in scope:
  - $locale  (string, 'en' or 'ar')
  - $params  (array; contains route params e.g. 'id', 'slug' for detail pages; empty otherwise)
For query strings use $_GET (e.g. page, categoryId, typeId, brandId).

## Data access
Use \App\Services\Repo (call as Repo::method()). It returns camelCase arrays matching the frontend TS types:
  - Repo::services($categoryId, $onlyActive, $search, $page, $size) -> ['items'=>[...], totalCount, pageIndex, pageSize]
  - Repo::serviceCategories($onlyActive,$page,$size)
  - Repo::service($id)
  - Repo::projects($typeId,$onlyActive,$page,$size); Repo::project($id); Repo::projectTypes($onlyActive,$page,$size)
  - Repo::productBrands($onlyActive,$page,$size) (each brand has 'categories'); Repo::product($id)
  - Repo::products($brandId,$categoryId,$onlyActive,$search,$page,$size)
  - Repo::blogPosts($publishedOnly,$tagId,$page,$size); Repo::blogPostBySlug($slug); Repo::tags(...)
  - Repo::team($onlyActive,$page,$size)['items']; Repo::clients($onlyActive,$page,$size)['items']
  - Repo::socialLinks($onlyActive,$size)
Item field names are camelCase: e.g. title, titleAr, description, descriptionAr, imagePath, categoryName, typeName, year, slug.
Image path strings that start with a '/' should be turned into a URL via Repo::getImagePath($item,'fieldName').
Localize bilingual fields with: localized($locale, $item['title'], $item['titleAr'] ?? null).

## i18n
NOT in objects. Use:
  - t('namespace.key') -> scalar string (params: t('key',['x'=>1]) replaces "{x}")
  - tarr('namespace') -> associative array for a whole section.
See the namespace docs: metadata, common, nav, footer, home, about, services, serviceBreakdown, projects, projectDetail,
products, productDetail, productFilters, blog, blogPost, team, clients, contact, contactForm, login.

## Helpers available
  - e($str) escape; asset('/path') absolute URL; lnk('/path') locale-prefixed internal link (use for all internal links)
  - t(), tarr(), localized(), Repo::getImagePath()
  - Set $GLOBALS['page_title'], $GLOBALS['meta_description'] (and optional 'meta_keywords') for SEO.

## Page header partial
Set these vars then require it:      $phEyebrow, $phTitle, $phSubtitle, $phBackground (string URL or ''), optional $phDark (bool).
    require base_path('/views/partials/page-header.php');

## Critical rules
- Port the exact className strings from the TSX. The compiled CSS (public/css/app.css) was generated from the original TSX, so
  identical classes render identically. Do NOT invent new classes.
- Use <img> (not next/image). For dynamic site images call Repo::getImagePath.
- Pagination component: build prev/next/numbers as plain HTML links with ?page=N (see Pagination.tsx for styling).
- After writing the file, run:  <php> -l <file>  to confirm no syntax errors (php at C:/Users/Ahmed/Downloads/Brilliant-Eng-Website/tools/php/php.exe).
- Do NOT modify Kernel.php, controllers, Repo, or any file outside views/public/*.php (create only the named view file).
- Keep the top of the file minimal (1 <?php block) and set $GLOBALS['page_title'] early.
