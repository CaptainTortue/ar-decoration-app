<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Page d'accueil — landing page
Route::get('/', function () {
    // Si l'utilisateur est déjà connecté, rediriger vers le bon panel
    if (Auth::check()) {
        return Auth::user()->is_admin
            ? redirect('/admin')
            : redirect('/dashboard');
    }
    return view('welcome');
})->name('home');

// Rediriger l'ancienne route /login vers le panel utilisateur Filament
// (évite les 404 pour les liens directs et les redirections du middleware auth)
Route::redirect('/login', '/dashboard/login', 301)->name('login');
Route::redirect('/register', '/dashboard/register', 301)->name('register');

require __DIR__.'/settings.php';
