<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Router;
use App\Services\Auth;
use App\Services\Database;
use App\Services\Jwt;
use App\Services\Repo;
use App\Services\Response;

class ApiController
{
    /** @var array<string,array{table:string,reorder:bool,list:string,get:string}> */
    private static array $entityConfig = [
        'services'          => ['table' => 'services',          'reorder' => true,  'list' => 'services', 'get' => 'service'],
        'service-categories'=> ['table' => 'service_categories','reorder' => true,  'list' => 'serviceCategories', 'get' => 'serviceCategory'],
        'project-types'     => ['table' => 'project_types',     'reorder' => false, 'list' => 'projectTypes', 'get' => 'projectType'],
        'projects'          => ['table' => 'projects',          'reorder' => true,  'list' => 'projects', 'get' => 'project'],
        'product-brands'    => ['table' => 'product_brands',    'reorder' => false, 'list' => 'productBrands', 'get' => 'productBrand'],
        'product-categories'=> ['table' => 'product_categories','reorder' => false, 'list' => 'productCategories', 'get' => 'productCategory'],
        'products'          => ['table' => 'products',          'reorder' => false, 'list' => 'products', 'get' => 'product'],
        'blog'              => ['table' => 'blog_posts',        'reorder' => false, 'list' => 'blogPosts', 'get' => 'blogPost'],
        'tags'              => ['table' => 'tags',              'reorder' => false, 'list' => 'tags', 'get' => null],
        'clients'           => ['table' => 'clients',           'reorder' => true,  'list' => 'clients', 'get' => 'client'],
        'team'              => ['table' => 'team_members',      'reorder' => true,  'list' => 'team', 'get' => 'teamMember'],
        'testimonials'      => ['table' => 'testimonials',      'reorder' => true,  'list' => 'testimonials', 'get' => 'testimonial'],
        'social-links'      => ['table' => 'social_links',      'reorder' => true,  'list' => 'socialLinks', 'get' => null],
    ];

    public static function register(Router $router): void
    {
        // auth
        $router->post('/api/auth/login', fn() => self::login());
        $router->post('/api/auth/refresh', fn() => self::refresh());

        // hero (specific)
        $router->get('/api/hero', fn() => Response::ok(Repo::hero()));
        $router->put('/api/hero', fn() => self::updateHero());
        $router->get('/api/hero/stats', fn() => self::heroStats());
        $router->get('/api/hero/stats/{id:\d+}', fn($p) => self::heroStat((int)$p['id']));
        $router->post('/api/hero/stats', fn() => self::createHeroStat());
        $router->put('/api/hero/stats/{id:\d+}', fn($p) => self::updateHeroStat((int)$p['id']));
        $router->delete('/api/hero/stats/{id:\d+}', fn($p) => self::deleteHeroStat((int)$p['id']));
        $router->put('/api/hero/stats/reorder', fn() => self::reorderHeroStats());

        // contact
        $router->get('/api/contact', fn() => Response::ok(Repo::contact()));
        $router->put('/api/contact', fn() => self::updateContact());
        $router->post('/api/contact/messages', fn() => self::storeContactMessage());

        // meta + sitemap
        $router->get('/api/meta/{page}', fn($p) => self::meta($p['page']));
        $router->get('/api/sitemap', fn() => Response::ok(self::sitemapItems()));

        // upload
        $router->post('/api/upload/image/{entity}', fn($p) => self::uploadImage($p['entity']));
        $router->post('/api/upload/document', fn() => self::uploadDocument());

        // generic entity CRUD
        foreach (self::$entityConfig as $key => $cfg) {
            $router->get('/api/' . $key, fn($p) => self::listEntity($key, $p));
            $router->get('/api/' . $key . '/{id:\d+}', fn($p) => self::getEntity($key, (int)$p['id']));
            $router->post('/api/' . $key, fn() => self::createEntity($key));
            $router->put('/api/' . $key . '/{id:\d+}', fn($p) => self::updateEntity($key, (int)$p['id']));
            $router->delete('/api/' . $key . '/{id:\d+}', fn($p) => self::deleteEntity($key, (int)$p['id']));
            if ($cfg['reorder']) {
                $router->put('/api/' . $key . '/reorder', fn() => self::reorderEntity($key));
            }
            // bulk (products, product-brands, product-categories, service-categories, services, tags)
            if ($key === 'products' || $key === 'tags') $router->post('/api/' . $key . '/Bulk', fn() => self::createBulk($key));
            if ($key === 'products' || $key === 'product-brands' || $key === 'product-categories' || $key === 'service-categories' || $key === 'services') $router->put('/api/' . $key . '/Bulk', fn() => self::updateBulk($key));
        }

        // blog publish/unpublish
        $router->post('/api/blog/{id:\d+}/publish', fn($p) => self::publishBlog((int)$p['id'], true));
        $router->post('/api/blog/{id:\d+}/unpublish', fn($p) => self::publishBlog((int)$p['id'], false));
    }

    // -------------------- auth --------------------
    private static function login(): void
    {
        $body = json_body();
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';
        $user = Auth::attempt((string)$email, (string)$password);
        if ($user === null) {
            Response::fail(401, 'Invalid email or password.');
        }
        Response::ok(self::authResponse($user), 'Login successful.');
    }

    private static function refresh(): void
    {
        $body = json_body();
        $token = (string)($body['refreshToken'] ?? '');
        $payload = Jwt::validateRefreshToken($token);
        if ($payload === null) {
            Response::fail(401, 'Invalid or expired refresh token.');
        }
        $user = Auth::findById((int)$payload['sub']);
        if ($user === null) {
            Response::fail(401, 'Invalid refresh token.');
        }
        Response::ok(self::authResponse($user), 'Token refreshed successfully.');
    }

    private static function authResponse(array $user): array
    {
        $roles = [$user['role'] ?? 'Admin'];
        $made = Jwt::makeToken((int)$user['id'], $user['email'], $roles);
        return [
            'token' => $made['token'],
            'refreshToken' => Jwt::makeRefreshToken((int)$user['id']),
            'expiresAt' => $made['expires_at'],
            'email' => $user['email'],
            'fullName' => $user['full_name'] ?? null,
            'roles' => $roles,
        ];
    }

    // -------------------- hero --------------------
    private static function updateHero(): void
    {
        Auth::apiRequireAdmin();
        $body = json_body();
        $data = self::snakeBody($body, 'hero_sections');
        $row = Database::fetchOne('SELECT id FROM hero_sections ORDER BY id ASC LIMIT 1');
        $id = $row ? (int)$row['id'] : 0;
        if ($id) Database::update('hero_sections', $data, ['id' => $id]);
        else { Database::insert('hero_sections', $data); $id = Database::lastInsertId(); }
        Response::ok(Repo::hero(), 'Hero section updated successfully.');
    }

    private static function heroStats(): void
    {
        $onlyActive = isset($_GET['onlyActive']) ? (bool)$_GET['onlyActive'] : null;
        $pageIndex = (int)($_GET['pageIndex'] ?? 1);
        $pageSize = (int)($_GET['pageSize'] ?? 10);
        $rows = Database::fetchAll('SELECT * FROM hero_stats ORDER BY sort_order ASC, id ASC');
        $items = array_map(fn($r) => self::camelInt($r), $rows);
        Response::ok(['items' => $items, 'totalCount' => count($items), 'pageIndex' => $pageIndex, 'pageSize' => $pageSize]);
    }

    private static function heroStat(int $id): void
    {
        $row = Database::fetchOne('SELECT * FROM hero_stats WHERE id = ?', [$id]);
        if ($row === null) Response::fail(404, 'Hero stat not found.');
        Response::ok(self::camelInt($row));
    }

    private static function createHeroStat(): void
    {
        Auth::apiRequireAdmin();
        $body = json_body();
        $id = Database::insert('hero_stats', self::snakeBody($body, 'hero_stats'));
        Response::created(self::camelInt(Database::fetchOne('SELECT * FROM hero_stats WHERE id = ?', [$id])));
    }

    private static function updateHeroStat(int $id): void
    {
        Auth::apiRequireAdmin();
        $body = json_body();
        if (($body['id'] ?? null) && (int)$body['id'] !== $id) Response::fail(400, 'Route id and body id must match.');
        Database::update('hero_stats', self::snakeBody($body, 'hero_stats'), ['id' => $id]);
        Response::ok(self::camelInt(Database::fetchOne('SELECT * FROM hero_stats WHERE id = ?', [$id])), 'Hero stat updated successfully.');
    }

    private static function deleteHeroStat(int $id): void
    {
        Auth::apiRequireAdmin();
        Database::delete('hero_stats', ['id' => $id]);
        Response::ok(true, 'Hero stat deleted successfully.');
    }

    private static function reorderHeroStats(): void
    {
        Auth::apiRequireAdmin();
        self::applyReorder('hero_stats', json_body());
        Response::ok(true, 'Hero stats reordered successfully.');
    }

    // -------------------- contact --------------------
    private static function updateContact(): void
    {
        Auth::apiRequireAdmin();
        $body = json_body();
        $data = self::snakeBody($body, 'contact_info');
        $row = Database::fetchOne('SELECT id FROM contact_info ORDER BY id ASC LIMIT 1');
        if ($row) Database::update('contact_info', $data, ['id' => (int)$row['id']]);
        else Database::insert('contact_info', $data);
        Response::ok(Repo::contact(), 'Contact info updated successfully.');
    }

    private static function storeContactMessage(): void
    {
        $body = json_body();
        $name = trim((string)($body['name'] ?? ''));
        $email = trim((string)($body['email'] ?? ''));
        $message = trim((string)($body['message'] ?? ''));
        if ($name === '' || $email === '' || $message === '') {
            Response::fail(400, 'Please fill in your name, email and message.');
        }
        $id = Database::insert('contact_messages', [
            'name' => $name, 'email' => $email, 'phone' => $body['phone'] ?? '', 'message' => $message,
        ]);
        Response::ok(['id' => $id], 'Message sent successfully.');
    }

    // -------------------- meta / sitemap --------------------
    private static function meta(string $page): void
    {
        $base = app_url();
        $titles = [
            'home' => t('metadata.title'), 'about' => t('about.metadataTitle'), 'services' => t('services.metadataTitle'),
            'projects' => t('projects.metadataTitle'), 'products' => t('products.metadataTitle'), 'blog' => t('blog.metadataTitle'),
            'team' => t('team.metadataTitle'), 'clients' => t('clients.metadataTitle'), 'contact' => t('contact.metadataTitle'),
        ];
        $descs = [
            'home' => t('home.metadataDescription'), 'about' => t('about.metadataDescription'), 'services' => t('services.metadataDescription'),
            'projects' => t('projects.metadataDescription'), 'products' => t('products.metadataDescription'), 'blog' => t('blog.metadataDescription'),
            'team' => t('team.metadataDescription'), 'clients' => t('clients.metadataDescription'), 'contact' => t('contact.metadataDescription'),
        ];
        Response::ok([
            'title' => $titles[$page] ?? t('metadata.title'),
            'description' => $descs[$page] ?? t('metadata.description'),
            'keywords' => t('metadata.keywords.general'),
            'canonicalUrl' => $base . '/' . current_locale() . ($page === 'home' ? '' : '/' . $page),
            'ogImage' => app_url() . '/img/logo.png', 'ogType' => 'website',
        ]);
    }

    private static function sitemapItems(): array
    {
        try {
            $base = rtrim(app_url(), '/');
            $static = ['', '/about', '/services', '/projects', '/products', '/blog', '/team', '/clients', '/contact'];
            $items = [];
            // Use Repo with large limit (5000) - original working method, no raw SQL with updated_at (column does not exist in BaseEntity)
            $allProducts = Repo::products(null, null, true, null, 1, 5000)['items'];
            $allProjects = Repo::projects(null, true, 1, 5000)['items'];
            $allBlogs = Repo::blogPosts(true, null, 1, 5000)['items'];
            foreach (config('locales') as $loc) {
                foreach ($static as $route) {
                    $items[] = ['url' => $base . '/' . $loc . $route, 'lastModified' => now(), 'changeFrequency' => 'weekly', 'priority' => $route === '' ? 1 : 0.7];
                }
                foreach ($allProjects as $p) {
                    $lm = $p['createdAt'] ?? $p['created_at'] ?? now();
                    $items[] = ['url' => $base . '/' . $loc . '/projects/' . $p['id'], 'lastModified' => $lm, 'changeFrequency' => 'monthly', 'priority' => 0.5];
                }
                foreach ($allProducts as $p) {
                    $lm = $p['createdAt'] ?? $p['created_at'] ?? now();
                    $items[] = ['url' => $base . '/' . $loc . '/products/' . $p['id'], 'lastModified' => $lm, 'changeFrequency' => 'monthly', 'priority' => 0.5];
                }
                $seenBlog = [];
                foreach ($allBlogs as $b) {
                    $bs = Repo::canonicalSlug($b['slug']);
                    if (isset($seenBlog[$bs])) continue;
                    $seenBlog[$bs] = true;
                    $lm = $b['publishedAt'] ?? $b['published_at'] ?? $b['createdAt'] ?? $b['created_at'] ?? now();
                    $items[] = ['url' => $base . '/' . $loc . '/blog/' . $bs, 'lastModified' => $lm, 'changeFrequency' => 'monthly', 'priority' => 0.5];
                }
            }
            return $items;
        } catch (\Throwable $e) {
            error_log('[sitemapItems] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            // Fallback to static routes only to keep sitemap working
            $base = rtrim(app_url(), '/');
            $items = [];
            foreach (config('locales') as $loc) {
                foreach (['', '/about', '/services', '/projects', '/products', '/blog', '/team', '/clients', '/contact'] as $route) {
                    $items[] = ['url' => $base . '/' . $loc . $route, 'lastModified' => now(), 'changeFrequency' => 'weekly', 'priority' => $route === '' ? 1 : 0.7];
                }
            }
            return $items;
        }
    }

    public static function sitemapXml(): void
    {
        try {
            header('Content-Type: application/xml; charset=utf-8');
            $items = self::sitemapItems();
            echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
            foreach ($items as $it) {
                $lm = gmdate('Y-m-d', strtotime($it['lastModified']));
                echo '<url><loc>' . htmlspecialchars($it['url']) . '</loc><lastmod>' . $lm . '</lastmod><changefreq>' . $it['changeFrequency'] . '</changefreq><priority>' . $it['priority'] . '</priority></url>
';
            }
            echo '</urlset>';
        } catch (\Throwable $e) {
            error_log('[sitemapXml] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Sitemap generation failed. Check error log.';
        }
    }

    // -------------------- upload --------------------
    private static function uploadImage(string $entity): void
    {
        Auth::apiRequireAdmin();
        if (!preg_match('/^[a-zA-Z0-9_&\-]+$/', $entity)) Response::fail(400, 'Invalid entity folder.');
        $folder = strtolower($entity);
        $file = self::requireUpload();
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = config('uploads.image_ext');
        if (!in_array($ext, $allowed, true)) Response::fail(400, 'Only JPG, JPEG, PNG and WEBP images are allowed.');
        $rel = self::saveUpload($file, 'images/' . $folder, $ext);
        Response::created($rel, 'Image uploaded successfully.');
    }

    private static function uploadDocument(): void
    {
        Auth::apiRequireAdmin();
        $file = self::requireUpload();
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, config('uploads.doc_ext'), true)) Response::fail(400, 'This document type is not allowed.');
        $rel = self::saveUpload($file, 'docs', $ext);
        Response::created($rel, 'Document uploaded successfully.');
    }

    private static function requireUpload(): array
    {
        if (empty($_FILES['file'])) {
            Response::fail(400, 'No file was received. It may be larger than the server upload limit.');
        }
        $file = $_FILES['file'];
        if (!isset($file['error']) || is_array($file['error'])) {
            Response::fail(400, 'Invalid upload request.');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $messages = [
                UPLOAD_ERR_INI_SIZE   => 'The file is larger than the server allows (upload_max_filesize).',
                UPLOAD_ERR_FORM_SIZE  => 'The file is larger than this form allows.',
                UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded. Please retry.',
                UPLOAD_ERR_NO_FILE    => 'No file was received.',
                UPLOAD_ERR_NO_TMP_DIR => 'The server upload temp folder is missing.',
                UPLOAD_ERR_CANT_WRITE => 'The server could not write the uploaded file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the upload.',
            ];
            Response::fail(400, $messages[$file['error']] ?? ('Upload failed (error code ' . $file['error'] . ').'));
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            Response::fail(400, 'Invalid uploaded file.');
        }
        return $file;
    }

    private static function saveUpload(array $file, string $sub, string $ext): string
    {
        $max = (int)config('uploads.max_size', 5 * 1024 * 1024);
        if ($file['size'] > $max) Response::fail(400, 'File exceeds the maximum allowed size of 5 MB.');
        $dir = config('uploads.dir') . '/' . $sub;
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            Response::fail(500, 'Could not create the upload folder: ' . $sub);
        }
        if (!is_writable($dir)) {
            Response::fail(500, 'The upload folder is not writable: ' . $sub);
        }
        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            Response::fail(500, 'Could not save the uploaded file. Check the uploads folder permissions.');
        }
        return '/uploads/' . $sub . '/' . $name;
    }

    // -------------------- generic entity CRUD --------------------
    private static function listEntity(string $key, array $p): void
    {
        $cfg = self::$entityConfig[$key];
        $page = (int)($_GET['pageIndex'] ?? 1);
        $size = (int)($_GET['pageSize'] ?? 10);
        $onlyActive = isset($_GET['onlyActive']) ? (bool)$_GET['onlyActive'] : null;
        switch ($key) {
            case 'services': $r = Repo::services(isset($_GET['categoryId']) ? (int)$_GET['categoryId'] : null, $onlyActive, ($_GET['search'] ?? null) ?: null, $page, $size); break;
            case 'service-categories': $r = Repo::serviceCategories($onlyActive, $page, $size); break;
            case 'project-types': $r = Repo::projectTypes($onlyActive, $page, $size); break;
            case 'projects': $r = Repo::projects(isset($_GET['typeId']) ? (int)$_GET['typeId'] : null, $onlyActive, $page, $size); break;
            case 'product-brands': $r = Repo::productBrands($onlyActive, $page, $size); break;
            case 'product-categories': $r = Repo::productCategories(isset($_GET['brandId']) ? (int)$_GET['brandId'] : null, $onlyActive, $page, $size); break;
            case 'products': $r = Repo::products(isset($_GET['brandId']) ? (int)$_GET['brandId'] : null, isset($_GET['categoryId']) ? (int)$_GET['categoryId'] : null, $onlyActive, ($_GET['search'] ?? null) ?: null, $page, $size); break;
            case 'blog': $r = Repo::blogPosts(isset($_GET['publishedOnly']) ? (bool)$_GET['publishedOnly'] : null, isset($_GET['tagId']) ? (int)$_GET['tagId'] : null, $page, $size); break;
            case 'tags': $r = Repo::tags($page, $size); break;
            case 'clients': $r = Repo::clients($onlyActive, $page, $size); break;
            case 'team': $r = Repo::team($onlyActive, $page, $size); break;
            case 'testimonials': $r = Repo::testimonials($onlyActive, $page, $size); break;
            case 'social-links': $r = ['items' => Repo::socialLinks($onlyActive, 100), 'totalCount' => count(Repo::socialLinks($onlyActive, 100)), 'pageIndex' => $page, 'pageSize' => $size]; break;
            default: Response::fail(404, 'Unknown entity.');
        }
        Response::ok($r);
    }

    private static function getEntity(string $key, int $id): void
    {
        $method = self::$entityConfig[$key]['get'];
        $row = $method ? Repo::$method($id) : null;
        if ($row === null) Response::fail(404, 'Not found.');
        Response::ok($row);
    }

    private static function createEntity(string $key): void
    {
        Auth::apiRequireAdmin();
        $cfg = self::$entityConfig[$key];
        $body = json_body();
        $data = self::snakeBody($body, $cfg['table']);
        $id = Database::insert($cfg['table'], $data);
        if ($cfg['table'] === 'blog_posts') self::setBlogTags($id, $body['tags'] ?? []);
        self::respondStoredEntity($key, $id, true);
    }

    private static function updateEntity(string $key, int $id): void
    {
        Auth::apiRequireAdmin();
        $cfg = self::$entityConfig[$key];
        $body = json_body();
        if (isset($body['id']) && (int)$body['id'] !== $id) Response::fail(400, 'Route id and body id must match.');
        $data = self::snakeBody($body, $cfg['table']);
        Database::update($cfg['table'], $data, ['id' => $id]);
        if ($cfg['table'] === 'blog_posts') self::setBlogTags($id, $body['tags'] ?? []);
        self::respondStoredEntity($key, $id, false);
    }

    private static function deleteEntity(string $key, int $id): void
    {
        Auth::apiRequireAdmin();
        Database::delete(self::$entityConfig[$key]['table'], ['id' => $id]);
        Response::ok(true, 'Deleted successfully.');
    }

    private static function reorderEntity(string $key): void
    {
        Auth::apiRequireAdmin();
        self::applyReorder(self::$entityConfig[$key]['table'], json_body());
        Response::ok(true, 'Reordered successfully.');
    }

    private static function createBulk(string $key): void
    {
        Auth::apiRequireAdmin();
        $cfg = self::$entityConfig[$key];
        $list = json_body();
        if (!is_array($list) || !array_is_list($list)) $list = $list['items'] ?? [];
        $results = [];
        foreach ($list as $item) {
            $results[] = $cfg['table'] === 'tags'
                ? self::camelInt(Database::fetchOne('SELECT id,name,slug FROM tags WHERE id = ?', [Database::insert('tags', self::snakeBody((array)$item, 'tags'))]))
                : self::getEntityData($key, Database::insert($cfg['table'], self::snakeBody((array)$item, $cfg['table'])));
        }
        Response::created($results ?: $list);
    }

    private static function updateBulk(string $key): void
    {
        Auth::apiRequireAdmin();
        $cfg = self::$entityConfig[$key];
        $list = json_body();
        if (!is_array($list) || !array_is_list($list)) $list = $list['items'] ?? [];
        $results = [];
        foreach ($list as $item) {
            $item = (array)$item;
            $id = (int)($item['id'] ?? 0);
            if ($id) Database::update($cfg['table'], self::snakeBody($item, $cfg['table']), ['id' => $id]);
            $results[] = self::getEntityData($key, $id);
        }
        Response::ok($results, 'Updated successfully.');
    }

    private static function respondStoredEntity(string $key, int $id, bool $created): void
    {
        $data = self::getEntityData($key, $id);
        if ($created) Response::created($data); else Response::ok($data, 'Updated successfully.');
    }

    private static function getEntityData(string $key, int $id): mixed
    {
        $method = self::$entityConfig[$key]['get'];
        if ($method) return Repo::$method($id);
        $table = self::$entityConfig[$key]['table'];
        return self::camelInt(Database::fetchOne('SELECT * FROM ' . $table . ' WHERE id = ?', [$id]));
    }

    private static function publishBlog(int $id, bool $publish): void
    {
        Auth::apiRequireAdmin();
        $data = ['is_published' => $publish ? 1 : 0];
        if ($publish) $data['published_at'] = now();
        Database::update('blog_posts', $data, ['id' => $id]);
        Response::ok(Repo::blogPost($id), 'Blog post ' . ($publish ? 'published' : 'unpublished') . ' successfully.');
    }

    // -------------------- helpers --------------------
    private static function snakeBody(array $body, string $table): array
    {
        $cols = Database::columns($table);
        $data = [];
        foreach ($body as $k => $v) {
            if (is_int($k)) continue;
            $snake = snake_case((string)$k);
            if (in_array($snake, $cols, true) && !in_array($snake, ['id', 'created_at'], true)) {
                $data[$snake] = $v;
            }
        }
        foreach (['is_active', 'is_published'] as $b) {
            if (array_key_exists($b, $data)) $data[$b] = $data[$b] ? 1 : 0;
        }
        return $data;
    }

    private static function setBlogTags(int $postId, mixed $tags): void
    {
        Database::delete('blog_post_tags', ['blog_post_id' => $postId]);
        if (!is_array($tags)) return;
        foreach ($tags as $tagId) {
            if (is_numeric($tagId)) {
                Database::insert('blog_post_tags', ['blog_post_id' => $postId, 'tag_id' => (int)$tagId], null);
            }
        }
    }

    private static function applyReorder(string $table, array $body): void
    {
        $items = $body['items'] ?? (array_is_list($body) ? $body : []);
        if (!is_array($items)) return;
        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['id'], $item['order'])) continue;
            Database::update($table, ['sort_order' => (int)$item['order']], ['id' => (int)$item['id']]);
        }
    }

    private static function camelInt(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $ck = Repo::camel((string)$k);
            if ($ck === 'sortOrder') $ck = 'order';
            if ($ck === 'statValue') $ck = 'value';
            $out[$ck] = $v;
        }
        foreach (['id','order','year','categoryId','typeId','brandId'] as $f) {
            if (isset($out[$f]) && is_numeric($out[$f])) $out[$f] = (int)$out[$f];
        }
        foreach (['isActive','isPublished'] as $f) {
            if (isset($out[$f])) $out[$f] = (bool)$out[$f];
        }
        return $out;
    }
}
