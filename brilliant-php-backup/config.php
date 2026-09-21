<?php
declare(strict_types=1);
/**
 * Brilliant Engineering Co. -- cPanel-compatible PHP port.
 *
 * Non-secret values have sensible defaults. Secrets (MySQL password, JWT signing
 * secret, initial admin password) MUST be supplied as environment variables --
 * the app fails fast with a clear error if a required one is missing, instead of
 * silently falling back to a hardcoded value.
 *
 * cPanel: PHP -> Per-directory settings -> Environment Variables
 * Local:  export VAR=value  (or set them in your IDE / shell profile)
 */

/**
 * Read a required environment variable or throw a clear, actionable error.
 * @param string $key   e.g. 'DB_PASS'
 * @param string $label human-readable description used in the error message
 */
$envRequired = static function (string $key, string $label): string {
    $value = getenv($key);
    if ($value === false || $value === '') {
        throw new RuntimeException(
            "Missing required environment variable {$key} ({$label}). " .
            'Set it via cPanel (PHP -> Per-directory settings -> Environment Variables) ' .
            'or in your local shell before starting the app.'
        );
    }
    return $value;
};

$driver = getenv('DB_DRIVER') ?: 'sqlite';

// MySQL connection credentials are required only when the MySQL driver is used
// (production). SQLite local dev does not need them.
$mysql = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '3306',
    'name' => $driver === 'mysql' ? $envRequired('DB_NAME', 'MySQL database name') : '',
    'user' => $driver === 'mysql' ? $envRequired('DB_USER', 'MySQL database user') : '',
    'pass' => $driver === 'mysql' ? $envRequired('DB_PASS', 'MySQL database password') : '',
];

$jwtSecret = $envRequired('JWT_SECRET', 'JWT signing secret');
$adminPassword = $envRequired('ADMIN_PASSWORD', 'initial admin password');

return [
  'app' => [
    'name' => 'Brilliant Engineering Co.',
    'site_url' => getenv('SITE_URL') ?: 'https://brilliant-eng.com',
    'debug' => getenv('APP_DEBUG') === '1',
    'base_path' => __DIR__,
  ],
  'db' => [
    // 'sqlite' for local dev / quick demo; 'mysql' on cPanel shared hosting.
    'driver' => $driver,
    'mysql' => $mysql,
    'sqlite' => [
      'path' => getenv('SQLITE_PATH') ?: (__DIR__ . '/storage/db.sqlite'),
    ],
  ],
  'jwt' => [
    'secret' => $jwtSecret,
    'issuer' => 'BrilliantEngineering',
    'audience' => 'BrilliantEngineering.Client',
    'expiry_minutes' => 60,
    'refresh_expiry_days' => 7,
  ],
  'admin_seed' => [
    'email' => getenv('ADMIN_EMAIL') ?: 'admin@brilliant-eng.com',
    'password' => $adminPassword,
  ],
  'locales' => ['en', 'ar'],
  'default_locale' => 'en',
  'uploads' => [
    'dir' => __DIR__ . '/uploads',
    'url_base' => '/uploads',
    'max_size' => 5 * 1024 * 1024,
    'image_ext' => ['jpg','jpeg','png','webp'],
    'doc_ext' => ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip'],
  ],
];
