<?php
/**
 * Front Controller - satu-satunya pintu masuk aplikasi.
 * Cocok untuk shared hosting: tidak butuh SSH/Composer/Node, cukup PHP native.
 */

declare(strict_types=1);

define('ROOT_PATH', __DIR__);

require ROOT_PATH . '/app/config/config.php';

$route = $_GET['route'] ?? '';
Router::dispatch($route);
