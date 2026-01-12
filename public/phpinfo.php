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
    $appKey = trim($matches[1] ?? '');
    $checks['app_key'] = [
        'set' => !empty($appKey),
        'raw_length' => strlen($appKey),
        'starts_with_base64' => strpos($appKey, 'base64:') === 0,
        'first_10_chars' => substr($appKey, 0, 10),
        'last_5_chars' => substr($appKey, -5),
    ];

    // If base64, decode and check actual key length
    if (strpos($appKey, 'base64:') === 0) {
        $base64Part = substr($appKey, 7); // Remove 'base64:' prefix
        $checks['app_key']['base64_part_length'] = strlen($base64Part);
        $decoded = base64_decode($base64Part, true);
        if ($decoded !== false) {
            $checks['app_key']['decoded_length'] = strlen($decoded);
            $checks['app_key']['decoded_valid'] = strlen($decoded) === 32 ? 'OK (32 bytes)' : 'WRONG (' . strlen($decoded) . ' bytes, expected 32)';
        } else {
            $checks['app_key']['decoded_length'] = 'DECODE_FAILED';
        }
    }
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

// Check environment variable (might override .env file)
$envAppKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? null);
if ($envAppKey) {
    $checks['env_var_app_key'] = [
        'source' => 'ENVIRONMENT VARIABLE (overrides .env!)',
        'length' => strlen($envAppKey),
        'first_10' => substr($envAppKey, 0, 10),
        'last_5' => substr($envAppKey, -5),
        'matches_env_file' => ($envAppKey === $appKey),
    ];
} else {
    $checks['env_var_app_key'] = 'NOT SET (good - uses .env file)';
}

// Check for config cache that might have old APP_KEY
$configCachePath = dirname(__DIR__) . '/bootstrap/cache/config.php';
$checks['config_cache_exists'] = file_exists($configCachePath);
if (file_exists($configCachePath)) {
    $cachedConfig = require $configCachePath;
    $cachedKey = $cachedConfig['app']['key'] ?? '';
    $checks['cached_app_key'] = [
        'length' => strlen($cachedKey),
        'first_10' => substr($cachedKey, 0, 10),
        'last_5' => substr($cachedKey, -5),
        'matches_env_file' => ($cachedKey === $appKey),
    ];
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

            // Debug: check what key Laravel is actually using
            try {
                $configKey = $app->make('config')->get('app.key');
                $checks['laravel_config_app_key'] = [
                    'length' => strlen($configKey ?? ''),
                    'first_10' => substr($configKey ?? '', 0, 10),
                    'last_5' => substr($configKey ?? '', -5),
                    'is_base64' => strpos($configKey ?? '', 'base64:') === 0,
                ];

                // Decode and check actual bytes
                if (strpos($configKey ?? '', 'base64:') === 0) {
                    $base64Part = substr($configKey, 7);
                    $decoded = base64_decode($base64Part, true);
                    $checks['laravel_key_decoded'] = [
                        'base64_length' => strlen($base64Part),
                        'decoded_bytes' => $decoded !== false ? strlen($decoded) : 'DECODE_FAILED',
                    ];
                }
            } catch (Exception $e2) {
                $checks['config_debug_error'] = $e2->getMessage();
            }
        }

    } catch (Exception $e) {
        $checks['kernel'] = 'FAILED: ' . $e->getMessage();
    }
} catch (Exception $e) {
    $checks['bootstrap'] = 'FAILED: ' . $e->getMessage();
}

echo json_encode($checks, JSON_PRETTY_PRINT);
