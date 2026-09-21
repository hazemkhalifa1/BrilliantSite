<?php
declare(strict_types=1);

namespace App\Services;

class Schema
{
    private static bool $done = false;

    public static function ensure(): void
    {
        if (self::$done) return;
        self::$done = true;

        // Create tables if they do not yet exist.
        $driver = config('db.driver', 'sqlite');
        $file = base_path('/db/schema.' . $driver . '.sql');
        if (is_file($file)) {
            $statements = array_filter(array_map('trim', explode(';', file_get_contents($file))));
            foreach ($statements as $sql) {
                if ($sql === '') continue;
                try {
                    Database::execute($sql);
                } catch (\Throwable $e) {
                    // Ignore "duplicate table" errors on re-run; fail loudly otherwise.
                    if (!preg_match('/already exists|duplicate/i', $e->getMessage())) {
                        throw $e;
                    }
                }
            }
        }

        self::ensureTestimonialsTable();

        self::seed();
    }

    /**
     * Testimonials were added to the product after the initial deployment, so the
     * table is provisioned lazily (or migrated manually by the operator). A bare
     * CREATE TABLE IF NOT EXISTS also protects existing installs that already ran
     * the manual migration.
     */
    private static function ensureTestimonialsTable(): void
    {
        $sql = config('db.driver') === 'mysql'
            ? "CREATE TABLE IF NOT EXISTS testimonials (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                name_ar VARCHAR(255) DEFAULT NULL,
                quote TEXT NOT NULL,
                quote_ar TEXT DEFAULT NULL,
                role VARCHAR(255) DEFAULT NULL,
                role_ar VARCHAR(255) DEFAULT NULL,
                image_path VARCHAR(255) DEFAULT NULL,
                sort_order INT NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id),
                KEY idx_testimonials_active (is_active, sort_order)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            : "CREATE TABLE IF NOT EXISTS testimonials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                name_ar TEXT,
                quote TEXT NOT NULL,
                quote_ar TEXT,
                role TEXT,
                role_ar TEXT,
                image_path TEXT,
                sort_order INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
              )";
        try {
            Database::execute($sql);
        } catch (\Throwable $e) {
            if (!preg_match('/already exists|duplicate/i', $e->getMessage())) {
                throw $e;
            }
        }
    }

    public static function seed(): void
    {
        // Admin user
        $email = config('admin_seed.email', 'admin@brilliant-eng.com');
        $password = config('admin_seed.password');
        if (Database::fetchValue('SELECT id FROM users WHERE email = ?', [$email]) === null) {
            Database::insert('users', [
                'full_name' => 'Brilliant Admin',
                'email' => $email,
                'password_hash' => Auth::hashPassword($password),
                'role' => 'Admin',
                'email_confirmed' => 1,
                'is_active' => 1,
            ]);
        }

        // Default service categories
        if (Database::fetchValue('SELECT COUNT(*) FROM service_categories') == 0) {
            $i = 1;
            foreach (['Civil Construction', 'MEP Works'] as $name) {
                Database::insert('service_categories', ['name' => $name, 'sort_order' => $i, 'is_active' => 1]);
                $i++;
            }
        }

        // Default project types
        if (Database::fetchValue('SELECT COUNT(*) FROM project_types') == 0) {
            foreach (['Commercial', 'Industrial'] as $name) {
                Database::insert('project_types', ['name' => $name, 'is_active' => 1]);
            }
        }

        // Default hero section
        if (Database::fetchValue('SELECT COUNT(*) FROM hero_sections') == 0) {
            Database::insert('hero_sections', ['updated_at' => now()]);
        }

        // Default contact info (single row)
        if (Database::fetchValue('SELECT COUNT(*) FROM contact_info') == 0) {
            Database::insert('contact_info', ['updated_at' => now()]);
        }
    }
}
