<?php
// Simple router for PHP's built-in server, forwarding all requests to public/index.php
// Usage: php -S 0.0.0.0:8000 server.php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the requested file exists in the public directory, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Otherwise, route through Laravel's front controller
require_once __DIR__ . '/public/index.php';
