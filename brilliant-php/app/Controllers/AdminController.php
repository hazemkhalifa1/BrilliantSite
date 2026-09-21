<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Auth;
use App\Services\Database;
use App\Services\Repo;
use App\Services\View;

class AdminController
{
    private static array $config = [
        'services' => ['table'=>'services','list'=>'services','get'=>'service','titleKey'=>'admin.sections.services.title','descKey'=>'admin.sections.services.description','newKey'=>'admin.sections.services.new','entityKey'=>'admin.sections.services.entity',
            'columns'=>[['labelKey'=>'admin.sections.services.headers.service','key'=>'title'],['labelKey'=>'admin.sections.services.headers.category','key'=>'categoryName'],['labelKey'=>'admin.sections.services.headers.order','key'=>'order'],['labelKey'=>'admin.sections.services.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'title','label'=>'admin.form.title','type'=>'text','required'=>true],['name'=>'titleAr','label'=>'admin.form.titleAr','type'=>'text'],['name'=>'description','label'=>'admin.form.description','type'=>'textarea'],['name'=>'descriptionAr','label'=>'admin.form.descriptionAr','type'=>'textarea'],['name'=>'iconPath','label'=>'admin.form.iconImage','type'=>'image'],['name'=>'categoryId','label'=>'admin.form.category','type'=>'select','options'=>'serviceCategories','required'=>true],['name'=>'relatedBlogPostId','label'=>'admin.form.relatedBlogPost','type'=>'select','options'=>'blogPosts'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'service-categories' => ['table'=>'service_categories','list'=>'serviceCategories','get'=>'serviceCategory','titleKey'=>'admin.sections.serviceCategories.title','descKey'=>'admin.sections.serviceCategories.description','newKey'=>'admin.sections.serviceCategories.new','entityKey'=>'admin.sections.serviceCategories.entity',
            'columns'=>[['labelKey'=>'admin.sections.serviceCategories.headers.category','key'=>'name'],['labelKey'=>'admin.sections.serviceCategories.headers.order','key'=>'order'],['labelKey'=>'admin.sections.serviceCategories.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'name','label'=>'admin.form.name','type'=>'text','required'=>true],['name'=>'nameAr','label'=>'admin.form.nameAr','type'=>'text'],['name'=>'description','label'=>'admin.form.description','type'=>'textarea'],['name'=>'descriptionAr','label'=>'admin.form.descriptionAr','type'=>'textarea'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'projects' => ['table'=>'projects','list'=>'projects','get'=>'project','titleKey'=>'admin.sections.projects.title','descKey'=>'admin.sections.projects.description','newKey'=>'admin.sections.projects.new','entityKey'=>'admin.sections.projects.entity',
            'columns'=>[['labelKey'=>'admin.sections.projects.headers.project','key'=>'title'],['labelKey'=>'admin.sections.projects.headers.type','key'=>'typeName'],['labelKey'=>'admin.sections.projects.headers.year','key'=>'year'],['labelKey'=>'admin.sections.projects.headers.order','key'=>'order'],['labelKey'=>'admin.sections.projects.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'title','label'=>'admin.form.projectTitle','type'=>'text','required'=>true],['name'=>'titleAr','label'=>'admin.form.titleAr','type'=>'text'],['name'=>'description','label'=>'admin.form.description','type'=>'textarea'],['name'=>'descriptionAr','label'=>'admin.form.descriptionAr','type'=>'textarea'],['name'=>'imagePath','label'=>'admin.form.projectImage','type'=>'image'],['name'=>'clientName','label'=>'admin.form.clientName','type'=>'text'],['name'=>'clientNameAr','label'=>'admin.form.clientNameAr','type'=>'text'],['name'=>'year','label'=>'admin.form.year','type'=>'number','required'=>true],['name'=>'typeId','label'=>'admin.form.projectType','type'=>'select','options'=>'projectTypes','required'=>true],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'products' => ['table'=>'products','list'=>'products','get'=>'product','titleKey'=>'admin.sections.products.title','descKey'=>'admin.sections.products.description','newKey'=>'admin.sections.products.new','entityKey'=>'admin.sections.products.entity',
            'columns'=>[['labelKey'=>'admin.sections.products.headers.product','key'=>'name'],['labelKey'=>'admin.sections.products.headers.category','key'=>'categoryName'],['labelKey'=>'admin.sections.products.headers.brand','key'=>'brandName'],['labelKey'=>'admin.sections.products.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'name','label'=>'admin.form.name','type'=>'text','required'=>true],['name'=>'nameAr','label'=>'admin.form.nameAr','type'=>'text'],['name'=>'description','label'=>'admin.form.description','type'=>'textarea'],['name'=>'descriptionAr','label'=>'admin.form.descriptionAr','type'=>'textarea'],['name'=>'imagePath','label'=>'admin.form.productImage','type'=>'image'],['name'=>'documentationUrl','label'=>'admin.form.documentationFile','type'=>'file'],['name'=>'categoryId','label'=>'admin.form.category','type'=>'select','options'=>'productCategories','required'=>true],['name'=>'relatedBlogPostId','label'=>'admin.form.relatedBlogPost','type'=>'select','options'=>'blogPosts'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'product-brands' => ['table'=>'product_brands','list'=>'productBrands','get'=>'productBrand','titleKey'=>'admin.sections.productBrands.title','descKey'=>'admin.sections.productBrands.description','newKey'=>'admin.sections.productBrands.new','entityKey'=>'admin.sections.productBrands.entity',
            'columns'=>[['labelKey'=>'admin.sections.productBrands.headers.brand','key'=>'name'],['labelKey'=>'admin.sections.productBrands.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'name','label'=>'admin.form.brand','type'=>'text','required'=>true],['name'=>'nameAr','label'=>'admin.form.nameAr','type'=>'text'],['name'=>'description','label'=>'admin.form.description','type'=>'textarea'],['name'=>'descriptionAr','label'=>'admin.form.descriptionAr','type'=>'textarea'],['name'=>'backgroundImagePath','label'=>'admin.form.backgroundImage','type'=>'image'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'product-categories' => ['table'=>'product_categories','list'=>'productCategories','get'=>'productCategory','titleKey'=>'admin.sections.productCategories.title','descKey'=>'admin.sections.productCategories.description','newKey'=>'admin.sections.productCategories.new','entityKey'=>'admin.sections.productCategories.entity',
            'columns'=>[['labelKey'=>'admin.sections.productCategories.headers.category','key'=>'name'],['labelKey'=>'admin.sections.productCategories.headers.brand','key'=>'brandName'],['labelKey'=>'admin.sections.productCategories.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'name','label'=>'admin.form.name','type'=>'text','required'=>true],['name'=>'nameAr','label'=>'admin.form.nameAr','type'=>'text'],['name'=>'brandId','label'=>'admin.form.brand','type'=>'select','options'=>'productBrands','required'=>true],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'blog' => ['table'=>'blog_posts','list'=>'blogPosts','get'=>'blogPost','titleKey'=>'admin.sections.blog.title','descKey'=>'admin.sections.blog.description','newKey'=>'admin.sections.blog.new','entityKey'=>'admin.sections.blog.entity','publish'=>true,
            'columns'=>[['labelKey'=>'admin.sections.blog.headers.post','key'=>'title'],['labelKey'=>'admin.sections.blog.headers.tags','key'=>'tags'],['labelKey'=>'admin.sections.blog.headers.order','key'=>'order'],['labelKey'=>'admin.sections.blog.headers.published','key'=>'isPublished']],
            'fields'=>[['name'=>'title','label'=>'admin.form.title','type'=>'text','required'=>true],['name'=>'titleAr','label'=>'admin.form.titleAr','type'=>'text'],['name'=>'content','label'=>'admin.form.content','type'=>'richtext','required'=>true],['name'=>'contentAr','label'=>'admin.form.contentAr','type'=>'richtext'],['name'=>'coverImagePath','label'=>'admin.form.coverImage','type'=>'image'],['name'=>'slug','label'=>'Slug','type'=>'text'],['name'=>'metaTitle','label'=>'admin.form.metaTitle','type'=>'text'],['name'=>'metaTitleAr','label'=>'admin.form.metaTitleAr','type'=>'text'],['name'=>'metaDescription','label'=>'admin.form.metaDescription','type'=>'textarea'],['name'=>'metaDescriptionAr','label'=>'admin.form.metaDescriptionAr','type'=>'textarea'],['name'=>'tags','label'=>'admin.form.tags','type'=>'tags'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isPublished','label'=>'admin.form.publish','type'=>'checkbox']]],
        'team' => ['table'=>'team_members','list'=>'team','get'=>'teamMember','titleKey'=>'admin.sections.team.title','descKey'=>'admin.sections.team.description','newKey'=>'admin.sections.team.new','entityKey'=>'admin.sections.team.entity',
            'columns'=>[['labelKey'=>'admin.sections.team.headers.member','key'=>'name'],['labelKey'=>'admin.sections.team.headers.order','key'=>'order'],['labelKey'=>'admin.sections.team.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'name','label'=>'admin.form.name','type'=>'text','required'=>true],['name'=>'nameAr','label'=>'admin.form.nameAr','type'=>'text'],['name'=>'jobTitle','label'=>'admin.form.jobTitle','type'=>'text'],['name'=>'jobTitleAr','label'=>'admin.form.jobTitleAr','type'=>'text'],['name'=>'description','label'=>'admin.form.description','type'=>'textarea'],['name'=>'descriptionAr','label'=>'admin.form.descriptionAr','type'=>'textarea'],['name'=>'imagePath','label'=>'admin.form.photo','type'=>'image'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'clients' => ['table'=>'clients','list'=>'clients','get'=>'client','titleKey'=>'admin.sections.clients.title','descKey'=>'admin.sections.clients.description','newKey'=>'admin.sections.clients.new','entityKey'=>'admin.sections.clients.entity',
            'columns'=>[['labelKey'=>'admin.sections.clients.headers.client','key'=>'name'],['labelKey'=>'admin.sections.clients.headers.order','key'=>'order'],['labelKey'=>'admin.sections.clients.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'name','label'=>'admin.form.name','type'=>'text','required'=>true],['name'=>'nameAr','label'=>'admin.form.nameAr','type'=>'text'],['name'=>'logoPath','label'=>'admin.form.logo','type'=>'image'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
        'testimonials' => ['table'=>'testimonials','list'=>'testimonials','get'=>'testimonial','titleKey'=>'admin.sections.testimonials.title','descKey'=>'admin.sections.testimonials.description','newKey'=>'admin.sections.testimonials.new','entityKey'=>'admin.sections.testimonials.entity',
            'columns'=>[['labelKey'=>'admin.sections.testimonials.headers.client','key'=>'name'],['labelKey'=>'admin.sections.testimonials.headers.role','key'=>'role'],['labelKey'=>'admin.sections.testimonials.headers.order','key'=>'order'],['labelKey'=>'admin.sections.testimonials.headers.status','key'=>'isActive']],
            'fields'=>[['name'=>'name','label'=>'admin.form.name','type'=>'text','required'=>true],['name'=>'nameAr','label'=>'admin.form.nameAr','type'=>'text'],['name'=>'quote','label'=>'admin.form.quote','type'=>'textarea','required'=>true],['name'=>'quoteAr','label'=>'admin.form.quoteAr','type'=>'textarea'],['name'=>'role','label'=>'admin.form.role','type'=>'text'],['name'=>'roleAr','label'=>'admin.form.roleAr','type'=>'text'],['name'=>'imagePath','label'=>'admin.form.photo','type'=>'image'],['name'=>'order','label'=>'admin.form.order','type'=>'number'],['name'=>'isActive','label'=>'admin.form.active','type'=>'checkbox']]],
    ];

    private static function cfg(string $key): ?array
    {
        return self::$config[$key] ?? null;
    }

    public static function dashboard(): void
    {
        $dash = tarr('admin.dashboard');
        $title = $dash['title'] ?? 'Dashboard';
        $GLOBALS['page_title'] = $title;
        $stats = Repo::siteStats();
        $contact = Repo::contact();
        $labels = tarr('admin.dashboard.labels');
        $sections = [
            ['key'=>'services','label'=>$labels['services'],'href'=>admin_url('/services'),'count'=>$stats['services']],
            ['key'=>'projects','label'=>$labels['projects'],'href'=>admin_url('/projects'),'count'=>$stats['projects']],
            ['key'=>'products','label'=>$labels['products'],'href'=>admin_url('/products'),'count'=>$stats['products']],
            ['key'=>'blog','label'=>$labels['blog'],'href'=>admin_url('/blog'),'count'=>$stats['blogPosts']],
            ['key'=>'team','label'=>$labels['team'],'href'=>admin_url('/team'),'count'=>$stats['teamMembers']],
            ['key'=>'clients','label'=>$labels['clients'],'href'=>admin_url('/clients'),'count'=>$stats['clients']],
            ['key'=>'testimonials','label'=>$labels['testimonials'],'href'=>admin_url('/testimonials'),'count'=>$stats['testimonials']],
        ];
        View::render('admin/dashboard', compact('title','dash','sections','contact'), 'layouts/admin');
    }

    public static function list(string $entity): void
    {
        $cfg = self::cfg($entity);
        if ($cfg === null) self::notFound();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $size = 20;

        $filters = [];
        $search = trim((string)($_GET['search'] ?? ''));
        if ($search !== '') $filters['search'] = $search;
        $map = [
            'services' => 'categoryId',
            'projects' => 'typeId',
            'products' => 'categoryId',
            'product-categories' => 'brandId',
        ];
        if (isset($map[$entity])) {
            $val = $_GET[$map[$entity]] ?? null;
            if ($val !== null && $val !== '' && ctype_digit((string)$val)) $filters[$map[$entity]] = (int)$val;
        }
        if ($entity === 'blog') {
            $status = $_GET['status'] ?? null;
            if ($status === 'published') $filters['status'] = 'published';
            elseif ($status === 'draft') $filters['status'] = 'draft';
        }

        $items = self::adminListItems($entity, $page, $size, $filters);
        $filterOptions = self::filterOptions($entity);
        $GLOBALS['page_title'] = t($cfg['titleKey']);
        $section = tarr('admin.sections.' . self::i18nKey($entity));
        View::render('admin/list', compact('entity','cfg','items','section','filters','filterOptions','search','page'), 'layouts/admin');
    }

    private static function filterOptions(string $entity): array
    {
        switch ($entity) {
            case 'services':
                return ['categories' => Repo::serviceCategories(null, 1, 1000)['items']];
            case 'products':
                return ['categories' => Repo::productCategories(null, null, 1, 1000)['items']];
            case 'projects':
                return ['types' => Repo::projectTypes(null, 1, 1000)['items']];
            case 'product-categories':
                return ['brands' => Repo::productBrands(null, 1, 1000)['items']];
        }
        return [];
    }

    private static function i18nKey(string $entity): string
    {
        $map = ['service-categories'=>'serviceCategories','product-brands'=>'productBrands','product-categories'=>'productCategories'];
        return $map[$entity] ?? $entity;
    }

    public static function form(string $entity, ?int $id = null): void
    {
        $cfg = self::cfg($entity);
        if ($cfg === null) self::notFound();
        $item = null;
        if ($id !== null) {
            $method = $cfg['get'];
            $item = Repo::$method($id);
            if ($item === null) self::notFound();
        }
        $fieldDefs = self::enrichFields($entity, $item);
        $options = self::selectOptions($fieldDefs);
        $brands = [];
        $categories = [];
        if ($entity === 'products') {
            $brands = Repo::productBrands(null, 1, 1000)['items'];
            $categories = Repo::productCategories(null, null, 1, 1000)['items'];
        }
        $GLOBALS['page_title'] = $id === null ? t('admin.form.newTitle', ['entity' => t($cfg['entityKey'])]) : t('admin.form.editTitle', ['entity' => t($cfg['entityKey'])]);
        View::render('admin/form', compact('entity','cfg','item','fieldDefs','fieldValues','options','id','brands','categories'), 'layouts/admin');
    }

    public static function store(string $entity, ?int $id = null): void
    {
        Auth::check() ?: redirect(lnk('/login'));
        verify_csrf();
        $cfg = self::cfg($entity);
        if ($cfg === null) self::notFound();
        $data = self::collect($entity, $cfg);
        $data = array_intersect_key($data, array_flip(Database::columns($cfg['table'])));
        unset($data['id']);
        if ($cfg['table'] === 'blog_posts') {
            $rawSlug = trim((string)($_POST['slug'] ?? ''));
            if ($rawSlug !== '') {
                // An explicit slug wins; ignore the current row so re-saving a post
                // with its own slug never turns it into "<slug>-2".
                $data['slug'] = \App\Services\Slugger::unique('blog_posts', 'slug', $rawSlug, $id ?? 0);
            } elseif ($id === null) {
                // Only generate a slug when creating; keep the existing slug on edit.
                $data['slug'] = \App\Services\Slugger::unique('blog_posts', 'slug', slugify($data['title'] ?? 'post'));
            }
        }
        if ($id !== null) {
            Database::update($cfg['table'], $data, ['id' => $id]);
            $savedId = $id;
        } else {
            $savedId = Database::insert($cfg['table'], $data);
        }
        if ($cfg['table'] === 'blog_posts') {
            self::syncBlogTags($savedId, $_POST['tags'] ?? null);
            if (!empty($data['is_published'])) ping_search_engines();
        }
        flash('success', 'Saved.');
        redirect(admin_url('/' . $entity));
    }

    public static function delete(string $entity, int $id): void
    {
        verify_csrf();
        $cfg = self::cfg($entity);
        if ($cfg === null) self::notFound();
        Database::delete($cfg['table'], ['id' => $id]);
        flash('success', 'Deleted.');
        redirect(admin_url('/' . $entity));
    }

    public static function publish(string $entity, int $id, int $publish): void
    {
        verify_csrf();
        Database::update('blog_posts', ['is_published' => $publish ? 1 : 0, 'published_at' => $publish ? now() : null], ['id' => $id]);
        if ($publish) ping_search_engines();
        flash('success', $publish ? 'Published.' : 'Unpublished.');
        redirect(admin_url('/' . $entity));
    }

    // ---- settings ----
    public static function settings(): void
    {
        $GLOBALS['page_title'] = t('admin.settings.title');
        $hero = Repo::hero();
        $contact = Repo::contact();
        $socials = Repo::socialLinks(false, 100);
        $tags = Repo::tags(1, 100)['items'];
        View::render('admin/settings', compact('hero','contact','socials','tags'), 'layouts/admin');
    }

    public static function saveSettings(string $part): void
    {
        verify_csrf();
        $action = $_POST['action'] ?? 'save';
        if ($part === 'hero') {
            if ($action === 'delete-stat') {
                Database::delete('hero_stats', ['id' => (int)($_POST['id'] ?? 0)]);
            } elseif ($action === 'add-stat') {
                $value = trim($_POST['stat_value'] ?? '');
                $label = trim($_POST['label'] ?? '');
                if ($value !== '' && $label !== '') {
                    $next = (int)Database::fetchValue('SELECT COALESCE(MAX(sort_order),0)+1 FROM hero_stats');
                    Database::insert('hero_stats', ['stat_value'=>$value,'label'=>$label,'label_ar'=>$_POST['labelAr']??'','sort_order'=>$next,'is_active'=>isset($_POST['is_active']) ? 1 : 0]);
                }
            } else {
                $data = self::collectHero($_POST);
                $row = Database::fetchOne('SELECT id FROM hero_sections ORDER BY id ASC LIMIT 1');
                if ($row) Database::update('hero_sections', $data, ['id' => (int)$row['id']]);
                else Database::insert('hero_sections', $data);
            }
        } elseif ($part === 'contact') {
            if ($action === 'save') {
                $data = self::collectContact($_POST);
                $row = Database::fetchOne('SELECT id FROM contact_info ORDER BY id ASC LIMIT 1');
                if ($row) Database::update('contact_info', $data, ['id' => (int)$row['id']]);
                else Database::insert('contact_info', $data);
            }
        } elseif ($part === 'social') {
            if ($action === 'delete') {
                Database::delete('social_links', ['id' => (int)($_POST['id'] ?? 0)]);
            } else {
                $platform = trim($_POST['platform'] ?? '');
                $url = trim($_POST['url'] ?? '');
                if ($platform !== '' && $url !== '') {
                    $next = (int)Database::fetchValue('SELECT COALESCE(MAX(sort_order),0)+1 FROM social_links');
                    Database::insert('social_links', ['platform'=>$platform,'url'=>$url,'icon_class'=>$_POST['iconClass']??'','sort_order'=>$next,'is_active'=>isset($_POST['is_active']) ? 1 : 0]);
                }
            }
        } elseif ($part === 'tags') {
            if ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                Database::delete('blog_post_tags', ['tag_id' => $id]);
                Database::delete('tags', ['id' => $id]);
            } else {
                $name = trim($_POST['name'] ?? '');
                if ($name !== '') {
                    $slug = \App\Services\Slugger::unique('tags', 'slug', slugify($name));
                    Database::insert('tags', ['name'=>$name,'slug'=>$slug]);
                }
            }
        }
        flash('success', 'Saved.');
        redirect(admin_url('/settings'));
    }

    // ---- helpers ----
    private static function collect(string $entity, array $cfg): array
    {
        $fields = $cfg['fields'];
        $data = [];
        foreach ($fields as $f) {
            $name = $f['name'];
            if ($name === 'tags') continue;
            $type = $f['type'] ?? 'text';
            $val = $_POST[$name] ?? null;
            if ($type === 'checkbox') { $data[snake_case($name)] = isset($_POST[$name]) ? 1 : 0; continue; }
            if ($val === null) continue;
            if (in_array($type, ['number'])) { $data[snake_case($name)] = $val === '' ? null : (int)$val; continue; }
            if ($type === 'select') { $data[snake_case($name)] = $val === '' ? null : $val; continue; }
            $data[snake_case($name)] = $val;
        }
        return $data;
    }

    private static function adminListItems(string $entity, int $page, int $size, array $filters = []): array
    {
        switch ($entity) {
            case 'services':
                return Repo::services($filters['categoryId'] ?? null, null, $filters['search'] ?? null, $page, $size);
            case 'service-categories': return Repo::serviceCategories(null, $page, $size);
            case 'projects': return Repo::projects($filters['typeId'] ?? null, null, $page, $size);
            case 'products':
                return Repo::products(null, $filters['categoryId'] ?? null, null, $filters['search'] ?? null, $page, $size);
            case 'product-brands': return Repo::productBrands(null, $page, $size);
            case 'product-categories': return Repo::productCategories($filters['brandId'] ?? null, null, $page, $size);
            case 'blog':
                $publishedOnly = ($filters['status'] ?? null) === 'published' ? true : (($filters['status'] ?? null) === 'draft' ? false : null);
                return Repo::blogPosts($publishedOnly, null, $page, $size);
            case 'team': return Repo::team(null, $page, $size);
            case 'clients': return Repo::clients(null, $page, $size);
            case 'testimonials': return Repo::testimonials(null, $page, $size);
        }
        return ['items'=>[], 'totalCount'=>0, 'pageIndex'=>$page, 'pageSize'=>$size];
    }

    private static function enrichFields(string $entity, ?array $item): array
    {
        $fields = self::cfg($entity)['fields'];
        foreach ($fields as &$f) {
            $name = $f['name'];
            if ($name === 'tags') { $f['value'] = $item['tags'] ?? []; continue; }
            $f['value'] = $item[$name] ?? '';
            if (($f['type'] ?? '') === 'checkbox') $f['checked'] = !empty($item[$name]);
        }
        unset($f);
        return $fields;
    }

    private static function selectOptions(array $fieldDefs): array
    {
        $opts = [];
        foreach ($fieldDefs as $f) {
            if (($f['type'] ?? '') !== 'select') continue;
            $method = $f['options'];
            if ($method === 'blogPosts') {
                $opts[$f['name']] = Repo::blogPostChoices();
                continue;
            }
            $rows = ($method === 'productCategories')
                ? Repo::productCategories(null, true, 1, 1000)['items']
                : Repo::$method(true, 1, 1000)['items'];
            $opts[$f['name']] = array_map(fn($r) => ['id'=>(int)$r['id'],'label'=> lpair($r['name'] ?? $r['title'] ?? '', $r['nameAr'] ?? $r['titleAr'] ?? null)], $rows);
        }
        return $opts;
    }

    private static function syncBlogTags(int $postId, mixed $tags): void
    {
        Database::delete('blog_post_tags', ['blog_post_id' => $postId]);
        if (!is_array($tags)) return;
        foreach ($tags as $id) {
            if (is_numeric($id)) Database::insert('blog_post_tags', ['blog_post_id'=>$postId,'tag_id'=>(int)$id]);
        }
    }

    private static function collectHero(array $post): array
    {
        $map = ['headlineTop'=>'headline_top','headlineTopAr'=>'headline_top_ar','headlineBottom'=>'headline_bottom','headlineBottomAr'=>'headline_bottom_ar','subText'=>'sub_text','subTextAr'=>'sub_text_ar','primaryBtnText'=>'primary_btn_text','primaryBtnTextAr'=>'primary_btn_text_ar','primaryBtnUrl'=>'primary_btn_url','secondaryBtnText'=>'secondary_btn_text','secondaryBtnTextAr'=>'secondary_btn_text_ar','secondaryBtnUrl'=>'secondary_btn_url'];
        $data = [];
        foreach ($map as $k => $col) $data[$col] = $post[$k] ?? '';
        return $data;
    }

    private static function collectContact(array $post): array
    {
        $map = ['phone1'=>'phone1','phone2'=>'phone2','email'=>'email','email2'=>'email2','address'=>'address','addressAr'=>'address_ar','mapEmbedUrl'=>'map_embed_url'];
        $data = [];
        foreach ($map as $k => $col) $data[$col] = $post[$k] ?? '';
        return $data;
    }

    private static function notFound(): void
    {
        http_response_code(404);
        echo 'Not found.';
        exit;
    }
}
