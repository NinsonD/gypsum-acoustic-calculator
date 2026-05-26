<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public_html');
define('STORAGE_PATH', ROOT_PATH . '/storage');

require APP_PATH . '/core/helpers.php';
require APP_PATH . '/core/Database.php';
require APP_PATH . '/core/Auth.php';
require APP_PATH . '/core/View.php';
require APP_PATH . '/core/Controller.php';
require APP_PATH . '/core/Router.php';
require APP_PATH . '/models/ContentRepository.php';
require APP_PATH . '/controllers/PageController.php';
require APP_PATH . '/controllers/AdminController.php';
require APP_PATH . '/controllers/CalculatorController.php';
require APP_PATH . '/controllers/InquiryController.php';

load_env(ROOT_PATH . '/.env');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

return require APP_PATH . '/config/app.php';
