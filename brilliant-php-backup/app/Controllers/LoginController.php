<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Auth;
use App\Services\View;

class LoginController
{
    public static function show(string $locale): void
    {
        if (Auth::check()) {
            redirect('/' . $locale . '/admin');
        }
        View::render('login', ['locale' => $locale], 'layouts/auth');
    }

    public static function login(string $locale): void
    {
        verify_csrf();
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $user = Auth::attempt($email, $password);
        if ($user === null) {
            $_SESSION['_old'] = ['email' => $email];
            flash('error', t('login.signInFailed'));
            redirect('/' . $locale . '/login');
        }
        Auth::loginAs($user);
        redirect('/' . $locale . '/admin');
    }

    public static function logout(string $locale): void
    {
        Auth::logout();
        redirect('/' . $locale . '/login');
    }
}
