<?php
declare(strict_types=1);

namespace App;

use App\Controllers\PageController;
use App\Services\I18n;
use App\Services\Schema;
use App\Services\View;

class Kernel
{
    private string $basePath;
    private Router $router;
    private string $locale;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->router = new Router();
    }

    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['_csrf'] = $_SESSION['_csrf'] ?? bin2hex(random_bytes(16));

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $method = request_method();

        $segments = explode('/', trim($path, '/'));
        $first = $segments[0] ?? '';
        $this->locale = in_array($first, config('locales', ['en', 'ar']), true) ? $first : config('default_locale', 'en');
        I18n::setLocale($this->locale);

        Schema::ensure();

        $this->registerRoutes();

        try {
            $result = $this->router->dispatch($method, $path);
            if ($result === false) {
                $this->notFound();
            }
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    public function locale(): string
    {
        return $this->locale;
    }

    private function registerRoutes(): void
    {
        $locale = $this->locale;
        $page = function (string $view) use ($locale) {
            return fn($p) => PageController::page($locale, $view, $p);
        };

        // ---- Public (locale-prefixed) ----
        $this->router->get('/', fn() => redirect('/' . $locale, 301));
        $this->router->get('/{locale:(en|ar)}', fn($p) => PageController::home($p['locale']));
        $this->router->get('/{locale:(en|ar)}/about', $page('about'));
        $this->router->get('/{locale:(en|ar)}/services', $page('services'));
        $this->router->get('/{locale:(en|ar)}/projects', $page('projects'));
        $this->router->get('/{locale:(en|ar)}/projects/{id:\d+}', $page('project'));
        $this->router->get('/{locale:(en|ar)}/products', $page('products'));
        $this->router->get('/{locale:(en|ar)}/products/{id:\d+}', $page('product'));
        $this->router->get('/{locale:(en|ar)}/blog', $page('blog'));
        // Legacy clean-slug → canonical (-2) 301 redirect
        $this->router->get('/{locale:(en|ar)}/blog/what-is-an-hvac-system-and-how-does-it-work', fn($p) => redirect('/' . $p['locale'] . '/blog/what-is-an-hvac-system-and-how-does-it-work-2', 301));
        $this->router->get('/{locale:(en|ar)}/blog/{slug}', $page('blog-post'));
        $this->router->get('/{locale:(en|ar)}/team', $page('team'));
        $this->router->get('/{locale:(en|ar)}/clients', $page('clients'));
        $this->router->get('/{locale:(en|ar)}/contact', $page('contact'));
        $this->router->post('/{locale:(en|ar)}/contact', fn($p) => PageController::submitContact($p['locale']));
        $this->router->get('/{locale:(en|ar)}/login', fn($p) => \App\Controllers\LoginController::show($p['locale']));
        $this->router->get('/login', fn() => redirect('/' . $locale . '/login'));

        // ---- API ----
        $api = \App\Controllers\ApiController::register($this->router);

        // ---- Auth / admin ----
        $this->router->post('/{locale:(en|ar)}/login', fn($p) => \App\Controllers\LoginController::login($p['locale']));
        $this->router->get('/{locale:(en|ar)}/logout', fn($p) => \App\Controllers\LoginController::logout($p['locale']));


        // ---- Admin (auth required) ----
        $adminCls = \App\Controllers\AdminController::class;
        $auth = [fn() => require_admin_login()];
        $this->router->get('/{locale:(en|ar)}/admin', [$adminCls, 'dashboard'], $auth);

        $this->router->get('/{locale:(en|ar)}/admin/settings', [$adminCls, 'settings'], $auth);
        $this->router->post('/{locale:(en|ar)}/admin/settings/{part}', fn($p) => $adminCls::saveSettings($p['part']), $auth);

        $this->router->get('/{locale:(en|ar)}/admin/{entity}/new', fn($p) => $adminCls::form($p['entity']), $auth);
        $this->router->post('/{locale:(en|ar)}/admin/{entity}/store', fn($p) => $adminCls::store($p['entity']), $auth);
        $this->router->get('/{locale:(en|ar)}/admin/{entity}/{id:\d+}', fn($p) => $adminCls::form($p['entity'], (int)$p['id']), $auth);
        $this->router->post('/{locale:(en|ar)}/admin/{entity}/{id:\d+}/update', fn($p) => $adminCls::store($p['entity'], (int)$p['id']), $auth);
        $this->router->post('/{locale:(en|ar)}/admin/{entity}/{id:\d+}/delete', fn($p) => $adminCls::delete($p['entity'], (int)$p['id']), $auth);
        $this->router->post('/{locale:(en|ar)}/admin/{entity}/{id:\d+}/publish', fn($p) => $adminCls::publish($p['entity'], (int)$p['id'], 1), $auth);
        $this->router->post('/{locale:(en|ar)}/admin/{entity}/{id:\d+}/unpublish', fn($p) => $adminCls::publish($p['entity'], (int)$p['id'], 0), $auth);
        $this->router->get('/{locale:(en|ar)}/admin/{entity}', fn($p) => $adminCls::list($p['entity']), $auth);

        // ---- misc ----
        $this->router->get('/robots.txt', fn() => PageController::robots());
        $this->router->get('/{locale:(en|ar)}/robots.txt', fn($p) => PageController::robots());
        $this->router->get('/sitemap.xml', fn() => \App\Controllers\ApiController::sitemapXml());
    }

    private function notFound(): void
    {
        http_response_code(404);
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (str_starts_with($path, '/api')) {
            json_response(['success' => false, 'message' => 'Not found.', 'data' => null, 'statusCode' => 404], 404);
            return;
        }
        View::render('public/404', ['locale' => I18n::locale()], 'layouts/public');
    }

    private function handleError(\Throwable $e): void
    {
        $debug = (bool)config('app.debug', true);
        http_response_code(500);
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if ($debug) {
            $msg = $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();
        } else {
            $msg = 'Internal server error.';
        }
        if (str_starts_with($path, '/api')) {
            json_response(['success' => false, 'message' => $msg, 'data' => null, 'statusCode' => 500], 500);
            return;
        }
        echo '<pre>' . e($msg) . '</pre>';
    }
}
