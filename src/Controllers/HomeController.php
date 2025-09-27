<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\AuthController;
use App\Utils\Router;

class HomeController
{
    /**
     * Show home page
     */
    public function index(): void
    {
        if (AuthController::isLoggedIn()) {
            Router::redirect('/dashboard');
            return;
        }

        Router::redirect('/login');
    }
}