<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Repo;
use App\Services\View;

class PageController
{
    public static function home(string $locale): void
    {
        $hero = Repo::hero();
        $services = Repo::services(null, true, null, 1, 6)['items'];
        $projects = Repo::projects(null, true, 1, 3)['items'];
        $testimonials = Repo::testimonials(true, 1, 6)['items'];
        $GLOBALS['page_title'] = t('home.metadataTitle');
        $metaDescription = t('home.metadataDescription');
        View::render('public/home', compact('locale', 'hero', 'services', 'projects', 'testimonials', 'metaDescription'));
    }

    public static function page(string $locale, string $view, array $params = []): void
    {
        View::render('public/' . $view, ['locale' => $locale, 'params' => $params]);
    }

    public static function submitContact(string $locale): void
    {
        verify_csrf();
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));
        if ($name === '' || $email === '' || $message === '') {
            $_SESSION['_old'] = ['name' => $name, 'email' => $email, 'phone' => $_POST['phone'] ?? '', 'message' => $message];
            flash('error', t('contactForm.validationError'));
            redirect('/' . $locale . '/contact');
        }
        \App\Services\Database::insert('contact_messages', [
            'name' => $name, 'email' => $email, 'phone' => $_POST['phone'] ?? '', 'message' => $message,
        ]);
        $_SESSION['_old'] = [];
        flash('success', t('contactForm.success'));
        redirect('/' . $locale . '/contact');
    }

    public static function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        $base = app_url();
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /api\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /en/admin\n";
        echo "Disallow: /ar/admin\n";
        echo "Disallow: /login\n";
        echo "Disallow: /en/login\n";
        echo "Disallow: /ar/login\n";
        echo "Sitemap: {$base}/sitemap.xml\n";
    }
}
