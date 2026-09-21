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

        self::seed();
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
