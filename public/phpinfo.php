<?php
// Simple PHP diagnostic file - bypasses Laravel entirely

header('Content-Type: application/json');

$checks = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
    'current_dir' => __DIR__,
    'parent_dir' => dirname(__DIR__),
];

// Check if storage directories exist and are writable
$storageDirs = [
    'storage' => dirname(__DIR__) . '/storage',
    'storage/framework' => dirname(__DIR__) . '/storage/framework',
    'storage/framework/views' => dirname(__DIR__) . '/storage/framework/views',
    'storage/framework/sessions' => dirname(__DIR__) . '/storage/framework/sessions',
    'storage/framework/cache' => dirname(__DIR__) . '/storage/framework/cache',
    'storage/logs' => dirname(__DIR__) . '/storage/logs',
    'bootstrap/cache' => dirname(__DIR__) . '/bootstrap/cache',
];

foreach ($storageDirs as $name => $path) {
    $checks['dir_' . str_replace('/', '_', $name)] = [
        'exists' => file_exists($path),
        'is_dir' => is_dir($path),
        'writable' => is_writable($path),
        'permissions' => file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A',
    ];
}

// Check if .env exists
$envPath = dirname(__DIR__) . '/.env';
$checks['env_file'] = [
    'exists' => file_exists($envPath),
    'readable' => is_readable($envPath),
];

// If .env exists, check for APP_KEY
if (file_exists($envPath) && is_readable($envPath)) {
    $envContent = file_get_contents($envPath);
    preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches);
    $appKey = $matches[1] ?? '';
    $checks['app_key'] = [
        'set' => !empty($appKey),
        'length' => strlen($appKey),
        'starts_with_base64' => strpos($appKey, 'base64:') === 0,
    ];
}

// Check database file
$dbPath = dirname(__DIR__) . '/database/database.sqlite';
$checks['database'] = [
    'exists' => file_exists($dbPath),
    'readable' => is_readable($dbPath),
    'writable' => is_writable($dbPath),
    'size' => file_exists($dbPath) ? filesize($dbPath) : 0,
];

// Try to load Laravel's autoloader to see if that works
try {
    require dirname(__DIR__) . '/vendor/autoload.php';
    $checks['autoloader'] = 'OK';
} catch (Exception $e) {
    $checks['autoloader'] = 'FAILED: ' . $e->getMessage();
}

// Check for route cache files
$routeCachePath = dirname(__DIR__) . '/bootstrap/cache/routes-v7.php';
$checks['route_cache_file'] = [
    'path' => $routeCachePath,
    'exists' => file_exists($routeCachePath),
];

// Check all cache files
$cacheDir = dirname(__DIR__) . '/bootstrap/cache';
$cacheFiles = glob($cacheDir . '/*.php');
$checks['cache_files'] = array_map('basename', $cacheFiles ?: []);

// Check web.php routes file
$webRoutesPath = dirname(__DIR__) . '/routes/web.php';
$checks['web_routes_file'] = [
    'path' => $webRoutesPath,
    'exists' => file_exists($webRoutesPath),
    'readable' => is_readable($webRoutesPath),
    'size' => file_exists($webRoutesPath) ? filesize($webRoutesPath) : 0,
];

// Try to bootstrap Laravel and handle a request
try {
    $app = require dirname(__DIR__) . '/bootstrap/app.php';
    $checks['bootstrap'] = 'OK';

    // Try to get the kernel
    try {
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $checks['kernel'] = 'OK';

        // Create a request
        $request = Illuminate\Http\Request::create('/debug', 'GET');

        // Actually handle the request (this will load routes)
        try {
            // Capture output to prevent it from being sent
            ob_start();
            $response = $kernel->handle($request);
            ob_end_clean();

            $checks['response_status'] = $response->getStatusCode();
            $checks['response_content_length'] = strlen($response->getContent());

            // If error, capture the content
            if ($response->getStatusCode() >= 400) {
                $content = $response->getContent();
                // Try to decode as JSON
                $decoded = json_decode($content, true);
                if ($decoded) {
                    $checks['error_details'] = $decoded;
                } else {
                    // Extract error message from HTML
                    if (preg_match('/<h1[^>]*>([^<]+)<\/h1>/', $content, $matches)) {
                        $checks['error_title'] = $matches[1];
                    }
                    if (preg_match('/<div[^>]*class="[^"]*message[^"]*"[^>]*>([^<]+)<\/div>/i', $content, $matches)) {
                        $checks['error_message'] = trim($matches[1]);
                    }
                    // For debug mode, try to extract the actual error
                    if (preg_match('/class="exception_message">([^<]+)</', $content, $matches)) {
                        $checks['exception_message'] = trim($matches[1]);
                    }
                    if (preg_match('/class="exception_title">([^<]+)</', $content, $matches)) {
                        $checks['exception_title'] = trim($matches[1]);
                    }
                }
            }

            // Now check routes after request handling
            $router = $app->make('router');
            $routes = $router->getRoutes();
            $checks['routes_after_handle'] = $routes->count();

            $routeList = [];
            foreach ($routes as $route) {
                $routeList[] = $route->uri();
            }
            $checks['route_list'] = array_slice($routeList, 0, 20); // Limit to first 20

            $kernel->terminate($request, $response);

        } catch (Exception $e) {
            $checks['handle_failed'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }

    } catch (Exception $e) {
        $checks['kernel'] = 'FAILED: ' . $e->getMessage();
    }
} catch (Exception $e) {
    $checks['bootstrap'] = 'FAILED: ' . $e->getMessage();
}

echo json_encode($checks, JSON_PRETTY_PRINT);
