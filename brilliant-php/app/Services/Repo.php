<?php
declare(strict_types=1);

namespace App\Services;

class Repo
{
    // ---- generic helpers ----
    public static function camelRow(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $out[self::camel($k)] = $v;
        }
        return $out;
    }

    public static function camel(string $k): string
    {
        return lcfirst(str_replace('_', '', ucwords($k, '_')));
    }

    public static function intify(array $row): array
    {
        foreach (['id','categoryId','typeId','year','brandId','order','relatedBlogPostId'] as $f) {
            if (isset($row[$f]) && is_numeric($row[$f])) $row[$f] = (int)$row[$f];
        }
        foreach (['isActive','isPublished'] as $f) {
            if (isset($row[$f])) $row[$f] = (bool)$row[$f];
        }
        return $row;
    }

    public static function paged(string $countSql, string $dataSql, array $params, int $pageIndex, int $pageSize): array
    {
        $total = (int)Database::fetchValue($countSql, $params);
        $offset = ($pageIndex - 1) * $pageSize;
        $items = Database::fetchAll($dataSql . ' LIMIT ' . (int)$pageSize . ' OFFSET ' . (int)$offset, $params);
        return ['items' => $items, 'totalCount' => $total, 'pageIndex' => $pageIndex, 'pageSize' => $pageSize];
    }

    public static function activeClause(?bool $onlyActive, string $prefix = ''): array
    {
        $where = '';
        $params = [];
        if ($onlyActive === true) {
            $where = ' AND ' . ($prefix !== '' ? $prefix : '') . 'is_active = 1';
        }
        return [$where, $params];
    }

    // ---- hero ----
    public static function hero(): ?array
    {
        $row = Database::fetchOne('SELECT * FROM hero_sections ORDER BY id ASC LIMIT 1');
        if ($row === null) return null;
        $row = self::camelRow($row);
        $stats = Database::fetchAll('SELECT * FROM hero_stats WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
        $row['stats'] = array_map(fn($s) => self::intify(self::camelRow($s, )), $stats);
        return $row;
    }

    // ---- contact ----
    public static function contact(): ?array
    {
        $row = Database::fetchOne('SELECT * FROM contact_info ORDER BY id ASC LIMIT 1');
        return $row === null ? null : self::camelRow($row);
    }

    // ---- social ----
    public static function socialLinks(?bool $onlyActive = null, int $pageSize = 100): array
    {
        [$where, $params] = self::activeClause($onlyActive);
        $rows = Database::fetchAll('SELECT * FROM social_links WHERE 1=1' . $where . ' ORDER BY sort_order ASC, id ASC LIMIT ' . (int)$pageSize, $params);
        return array_map(fn($r) => self::intify(self::camelRow($r)), $rows);
    }

    // ---- services ----
    public static function services(?int $categoryId = null, ?bool $onlyActive = null, ?string $search = null, int $pageIndex = 1, int $pageSize = 10): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        if ($categoryId !== null) { $where .= ' AND s.category_id = ?'; $params[] = $categoryId; }
        if ($onlyActive === true) { $where .= ' AND s.is_active = 1'; }
        if ($search !== null && $search !== '') { $where .= ' AND (s.title LIKE ? OR s.title_ar LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
        $count = 'SELECT COUNT(*) FROM services s' . $where;
        $data = 'SELECT s.*, sc.name AS category_name, rb.title AS related_blog_title, rb.title_ar AS related_blog_title_ar, rb.slug AS related_blog_slug FROM services s LEFT JOIN service_categories sc ON sc.id = s.category_id LEFT JOIN blog_posts rb ON rb.id = s.related_blog_post_id' . $where . ' ORDER BY s.sort_order ASC, s.id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function service(int $id): ?array
    {
        $row = Database::fetchOne('SELECT s.*, sc.name AS category_name, rb.title AS related_blog_title, rb.title_ar AS related_blog_title_ar, rb.slug AS related_blog_slug FROM services s LEFT JOIN service_categories sc ON sc.id = s.category_id LEFT JOIN blog_posts rb ON rb.id = s.related_blog_post_id WHERE s.id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    public static function serviceCategories(?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 100): array
    {
        [$where, $params] = self::activeClause($onlyActive, '');
        $count = 'SELECT COUNT(*) FROM service_categories WHERE 1=1' . $where;
        $data = 'SELECT * FROM service_categories WHERE 1=1' . $where . ' ORDER BY sort_order ASC, id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function serviceCategory(int $id): ?array
    {
        $row = Database::fetchOne('SELECT * FROM service_categories WHERE id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    // ---- projects ----
    public static function projects(?int $typeId = null, ?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 10): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        if ($typeId !== null) { $where .= ' AND p.type_id = ?'; $params[] = $typeId; }
        if ($onlyActive === true) { $where .= ' AND p.is_active = 1'; }
        $count = 'SELECT COUNT(*) FROM projects p' . $where;
        $data = 'SELECT p.*, pt.name AS type_name FROM projects p LEFT JOIN project_types pt ON pt.id = p.type_id' . $where . ' ORDER BY p.sort_order ASC, p.id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function project(int $id): ?array
    {
        $row = Database::fetchOne('SELECT p.*, pt.name AS type_name FROM projects p LEFT JOIN project_types pt ON pt.id = p.type_id WHERE p.id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    public static function projectTypes(?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 100): array
    {
        [$where, $params] = self::activeClause($onlyActive);
        $count = 'SELECT COUNT(*) FROM project_types WHERE 1=1' . $where;
        $data = 'SELECT * FROM project_types WHERE 1=1' . $where . ' ORDER BY id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r, )), $result['items']);
        return $result;
    }

    public static function projectType(int $id): ?array
    {
        $row = Database::fetchOne('SELECT * FROM project_types WHERE id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    // ---- products ----
    public static function products(?int $brandId = null, ?int $categoryId = null, ?bool $onlyActive = null, ?string $search = null, int $pageIndex = 1, int $pageSize = 10): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        if ($brandId !== null) { $where .= ' AND pb.id = ?'; $params[] = $brandId; }
        if ($categoryId !== null) { $where .= ' AND pc.id = ?'; $params[] = $categoryId; }
        if ($onlyActive === true) { $where .= ' AND p.is_active = 1'; }
        if ($search !== null && $search !== '') { $where .= ' AND (p.name LIKE ? OR p.name_ar LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
        $from = ' FROM products p LEFT JOIN product_categories pc ON pc.id = p.category_id LEFT JOIN product_brands pb ON pb.id = pc.brand_id LEFT JOIN blog_posts rb ON rb.id = p.related_blog_post_id';
        $count = 'SELECT COUNT(*) ' . $from . $where;
        $data = 'SELECT p.*, pc.name AS category_name, pc.brand_id, pb.name AS brand_name, rb.title AS related_blog_title, rb.title_ar AS related_blog_title_ar, rb.slug AS related_blog_slug ' . $from . $where . ' ORDER BY p.sort_order ASC, p.id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function product(int $id): ?array
    {
        $row = Database::fetchOne('SELECT p.*, pc.name AS category_name, pc.brand_id, pb.name AS brand_name, rb.title AS related_blog_title, rb.title_ar AS related_blog_title_ar, rb.slug AS related_blog_slug FROM products p LEFT JOIN product_categories pc ON pc.id = p.category_id LEFT JOIN product_brands pb ON pb.id = pc.brand_id LEFT JOIN blog_posts rb ON rb.id = p.related_blog_post_id WHERE p.id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    public static function productBrands(?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 100): array
    {
        [$where, $params] = self::activeClause($onlyActive);
        $count = 'SELECT COUNT(*) FROM product_brands WHERE 1=1' . $where;
        $data = 'SELECT * FROM product_brands WHERE 1=1' . $where . ' ORDER BY sort_order ASC, id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $items = [];
        foreach ($result['items'] as $camel) {
            $item = self::intify(self::camelRow($camel));
            $cats = self::productCategoriesByBrand($item['id'], null, 100);
            $item['categories'] = $cats['items'];
            $items[] = $item;
        }
        $result['items'] = $items;
        return $result;
    }

    public static function productBrand(int $id): ?array
    {
        $row = Database::fetchOne('SELECT * FROM product_brands WHERE id = ?', [$id]);
        if ($row === null) return null;
        $item = self::intify(self::camelRow($row));
        $item['categories'] = self::productCategoriesByBrand($id, null, 100)['items'];
        return $item;
    }

    public static function productCategories(?int $brandId = null, ?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 100): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        if ($brandId !== null) { $where .= ' AND pc.brand_id = ?'; $params[] = $brandId; }
        if ($onlyActive === true) { $where .= ' AND pc.is_active = 1'; }
        $count = 'SELECT COUNT(*) FROM product_categories pc' . $where;
        $data = 'SELECT pc.*, pb.name AS brand_name FROM product_categories pc LEFT JOIN product_brands pb ON pb.id = pc.brand_id' . $where . ' ORDER BY pc.sort_order ASC, pc.id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function productCategory(int $id): ?array
    {
        $row = Database::fetchOne('SELECT pc.*, pb.name AS brand_name FROM product_categories pc LEFT JOIN product_brands pb ON pb.id = pc.brand_id WHERE pc.id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    private static function productCategoriesByBrand(int $brandId, ?bool $onlyActive, int $pageSize): array
    {
        $where = ' WHERE pc.brand_id = ?';
        $params = [$brandId];
        if ($onlyActive === true) { $where .= ' AND pc.is_active = 1'; }
        $data = 'SELECT pc.*, pb.name AS brand_name FROM product_categories pc LEFT JOIN product_brands pb ON pb.id = pc.brand_id' . $where . ' ORDER BY pc.sort_order ASC, pc.id ASC LIMIT ' . (int)$pageSize;
        $rows = Database::fetchAll($data, $params);
        return ['items' => array_map(fn($r) => self::intify(self::camelRow($r)), $rows), 'totalCount' => count($rows)];
    }

    // ---- blog ----
    public static function blogPosts(?bool $publishedOnly = null, ?int $tagId = null, int $pageIndex = 1, int $pageSize = 10, ?int $excludeId = null): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        if ($publishedOnly === true) { $where .= ' AND b.is_published = 1'; }
        elseif ($publishedOnly === false) { $where .= ' AND b.is_published = 0'; }
        if ($tagId !== null) {
            $where .= ' AND EXISTS (SELECT 1 FROM blog_post_tags bpt WHERE bpt.blog_post_id = b.id AND bpt.tag_id = ?)';
            $params[] = $tagId;
        }
        if ($excludeId !== null) {
            $where .= ' AND b.id != ?';
            $params[] = $excludeId;
        }
        $count = 'SELECT COUNT(*) FROM blog_posts b' . $where;
        $data = 'SELECT * FROM blog_posts b' . $where . ' ORDER BY b.sort_order ASC, b.id DESC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::mapBlog(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function blogPost(int $id): ?array
    {
        $row = Database::fetchOne('SELECT * FROM blog_posts WHERE id = ?', [$id]);
        return $row === null ? null : self::mapBlog(self::camelRow($row));
    }

    /**
     * Published blog posts reduced to [id,label] pairs for admin <select> dropdowns.
     */
    public static function blogPostChoices(): array
    {
        $rows = self::blogPosts(true, null, 1, 1000)['items'];
        return array_map(
            fn($r) => ['id' => (int)$r['id'], 'label' => lpair($r['title'] ?? '', $r['titleAr'] ?? null)],
            $rows
        );
    }

    public static function blogPostBySlug(string $slug): ?array
    {
        $slug = (string)$slug;
        $row = Database::fetchOne('SELECT * FROM blog_posts WHERE slug = ?', [$slug]);

        if ($row === null) {
            // Legacy auto-suffixed slugs ("…-2") and their plain aliases resolve
            // to the same article so old indexed links never 404.
            $clean = (string)preg_replace('/-\d+$/', '', $slug);
            $row = ($clean !== $slug)
                ? Database::fetchOne('SELECT * FROM blog_posts WHERE slug = ? OR slug LIKE ? ORDER BY id ASC LIMIT 1', [$clean, $clean . '-%'])
                : Database::fetchOne('SELECT * FROM blog_posts WHERE slug LIKE ? ORDER BY id ASC LIMIT 1', [$slug . '-%']);
        }

        return $row === null ? null : self::mapBlog(self::camelRow($row));
    }

    /**
     * Canonical URL slug for a blog post. Strips the legacy auto-appended
     * numeric suffix ("…-2", "…-3") so articles always surface under their
     * clean slug regardless of what is stored in the database.
     */
    public static function canonicalSlug(string $slug): string
    {
        $slug = (string)$slug;
        $clean = (string)preg_replace('/-\d+$/', '', $slug);
        return $clean !== '' ? $clean : $slug;
    }

    private static function mapBlog(array $row): array
    {
        $row = self::intify($row);
        $tags = Database::fetchAll(
            'SELECT t.id, t.name, t.slug FROM tags t INNER JOIN blog_post_tags bpt ON bpt.tag_id = t.id WHERE bpt.blog_post_id = ? ORDER BY t.name ASC',
            [$row['id']]
        );
        $row['tags'] = array_map(fn($t) => ['id' => $t['id'], 'name' => $t['name'], 'slug' => $t['slug']], $tags);
        return $row;
    }

    public static function tags(int $pageIndex = 1, int $pageSize = 100): array
    {
        $count = 'SELECT COUNT(*) FROM tags';
        $data = 'SELECT * FROM tags ORDER BY name ASC';
        $result = self::paged($count, $data, [], $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => ['id' => (int)$r['id'], 'name' => $r['name'], 'slug' => $r['slug']], $result['items']);
        return $result;
    }

    // ---- team / clients ----
    public static function team(?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 100): array
    {
        [$where, $params] = self::activeClause($onlyActive);
        $count = 'SELECT COUNT(*) FROM team_members WHERE 1=1' . $where;
        $data = 'SELECT * FROM team_members WHERE 1=1' . $where . ' ORDER BY sort_order ASC, id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function teamMember(int $id): ?array
    {
        $row = Database::fetchOne('SELECT * FROM team_members WHERE id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    public static function clients(?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 100): array
    {
        [$where, $params] = self::activeClause($onlyActive);
        $count = 'SELECT COUNT(*) FROM clients WHERE 1=1' . $where;
        $data = 'SELECT * FROM clients WHERE 1=1' . $where . ' ORDER BY sort_order ASC, id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intify(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function client(int $id): ?array
    {
        $row = Database::fetchOne('SELECT * FROM clients WHERE id = ?', [$id]);
        return $row === null ? null : self::intify(self::camelRow($row));
    }

    // ---- testimonials ----
    public static function testimonials(?bool $onlyActive = null, int $pageIndex = 1, int $pageSize = 100): array
    {
        [$where, $params] = self::activeClause($onlyActive);
        $count = 'SELECT COUNT(*) FROM testimonials WHERE 1=1' . $where;
        $data = 'SELECT * FROM testimonials WHERE 1=1' . $where . ' ORDER BY sort_order ASC, id ASC';
        $result = self::paged($count, $data, $params, $pageIndex, $pageSize);
        $result['items'] = array_map(fn($r) => self::intifyTestimonial(self::camelRow($r)), $result['items']);
        return $result;
    }

    public static function testimonial(int $id): ?array
    {
        $row = Database::fetchOne('SELECT * FROM testimonials WHERE id = ?', [$id]);
        return $row === null ? null : self::intifyTestimonial(self::camelRow($row));
    }

    private static function intifyTestimonial(array $row): array
    {
        $row = self::intify($row);
        if (isset($row['sortOrder'])) {
            $row['order'] = $row['sortOrder'];
            unset($row['sortOrder']);
        }
        return $row;
    }

    // ---- misc ----
    public static function getImagePath(?array $item, string $field): string
    {
        $path = (string)($item[$field] ?? '');
        if ($path === '') return '/img/placeholder.svg';
        if (preg_match('#^https?://#i', $path)) return $path;

        // Legacy Next.js data stored media under /images/... — the PHP port
        // stores uploads under /uploads/images/... . Map old paths transparently.
        if (str_starts_with($path, '/images/')) {
            $path = '/uploads' . $path;
        }

        return asset($path);
    }

    public static function siteStats(): array
    {
        return [
            'projects' => (int)Database::fetchValue('SELECT COUNT(*) FROM projects'),
            'products' => (int)Database::fetchValue('SELECT COUNT(*) FROM products'),
            'blogPosts' => (int)Database::fetchValue('SELECT COUNT(*) FROM blog_posts'),
            'teamMembers' => (int)Database::fetchValue('SELECT COUNT(*) FROM team_members'),
            'services' => (int)Database::fetchValue('SELECT COUNT(*) FROM services'),
            'clients' => (int)Database::fetchValue('SELECT COUNT(*) FROM clients'),
            'testimonials' => (int)Database::fetchValue('SELECT COUNT(*) FROM testimonials'),
        ];
    }

    public static function contactMessages(): array
    {
        return Database::fetchAll('SELECT * FROM contact_messages ORDER BY id DESC LIMIT 200');
    }
}
