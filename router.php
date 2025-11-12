<?php

/**
 * Simple PHP Router Script
 * This script handles routing for web servers with .htaccess support
 * All requests (except static files) are routed through index.php to this file
 * 
 * For development with PHP built-in server, you can still use:
 * php -S localhost:8000 router.php
 * 
 * For production with Apache/Nginx, use the .htaccess file to route to index.php
 * 
 * Supports non-webroot placement (subdirectories)
 */

// Detect base path for non-webroot installations
function getBasePath()
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Remove trailing slash unless it's root
    return $scriptDir === '/' ? '' : $scriptDir;
}

$basePath = getBasePath();

// Get the requested URI and remove base path
$fullUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove base path from URI to get the route
if ($basePath && strpos($fullUri, $basePath) === 0) {
    $uri = substr($fullUri, strlen($basePath));
} else {
    $uri = $fullUri;
}

// Ensure URI starts with /
if (empty($uri) || $uri[0] !== '/') {
    $uri = '/' . ltrim($uri, '/');
}

// Remove query string and decode URL
$uri = urldecode($uri);

// Helper function to generate URLs with base path
function url($path)
{
    global $basePath;
    return $basePath . $path;
}

// Basic routing logic
switch ($uri) {
    case '/':
        // Redirect root to /home
        header('Location: home');
        exit;

    case '/home':
        echo "<!DOCTYPE html><html><head>";
        echo "<title>PHP Router Demo</title>";
        echo "<link rel='stylesheet' href='" . url('/assets/style.css') . "'>";
        echo "</head><body><div class='container'>";
        echo "<h1>Welcome to PHP Router</h1>";
        echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
        echo "<p>Base path: <code>" . htmlspecialchars($basePath ?: '/') . "</code></p>";
        echo "<p>Available routes:</p>";
        echo "<ul>";
        echo "<li><a href='" . url('/hello') . "'>GET /hello</a> - Page with static image</li>";
        echo "<li><a href='" . url('/hello?name=PHP') . "'>GET /hello?name=PHP</a> - Hello with name parameter</li>";
        echo "<li><a href='" . url('/about') . "'>GET /about</a> - Page with static image and CSS</li>";
        echo "<li><a href='" . url('/api/status') . "'>GET /api/status</a> - JSON API endpoint</li>";
        echo "<li><a href='" . url('/assets/demo-image.svg') . "'>Direct image access</a></li>";
        echo "<li><a href='" . url('/assets/docs.php') . "'><strong>📖 Dual Compatibility Documentation</strong></a> - Complete technical guide</li>";
        echo "</ul>";
        echo "<h2>Dual Support Architecture</h2>";
        echo "<div class='image-demo'>";
        echo "<img src='" . url('/assets/dual-support-diagram.svg') . "' alt='PHP Router Dual Support Architecture Diagram' style='max-width: 100%; height: auto;' />";
        echo "<p><em>This router supports both PHP built-in server (development) and Apache with .htaccess (production)</em></p>";
        echo "</div>";
        echo "</div></body></html>";
        break;

    case '/hello':
        // Get query parameters
        $name = $_GET['name'] ?? 'World';
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); // Sanitize input

        echo "<!DOCTYPE html><html><head>";
        echo "<title>Hello - PHP Router Demo</title>";
        echo "<link rel='stylesheet' href='" . url('/assets/style.css') . "'>";
        echo "</head><body><div class='container'>";
        echo "<h1>Hello {$name}!</h1>";

        if (isset($_GET['name'])) {
            echo "<p>Welcome, <strong>{$name}</strong>! This greeting was personalized using the query parameter <code>?name={$name}</code>.</p>";
        } else {
            echo "<p>This is the hello route with a static image demonstration.</p>";
            echo "<p>Try adding a name parameter: <a href='" . url('/hello?name=PHP') . "'>/hello?name=PHP</a></p>";
        }

        echo "<div class='image-demo'>";
        echo "<img src='" . url('/assets/demo-image.svg') . "' alt='PHP Router Demo Image' />";
        echo "<p><em>This SVG image is served as a static file</em></p>";
        echo "</div>";
        echo "<p>The image above is loaded from <code>" . url('/assets/demo-image.svg') . "</code></p>";

        // Show query parameters if any
        if (!empty($_GET)) {
            echo "<h3>Query Parameters:</h3>";
            echo "<ul>";
            foreach ($_GET as $key => $value) {
                $key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                echo "<li><code>{$key}</code> = <code>{$value}</code></li>";
            }
            echo "</ul>";
        }

        echo "<a href='" . url('/home') . "'>← Back to home</a>";
        echo "</div></body></html>";
        break;

    case '/about':
        echo "<!DOCTYPE html><html><head>";
        echo "<title>About - PHP Router Demo</title>";
        echo "<link rel='stylesheet' href='" . url('/assets/style.css') . "'>";
        echo "</head><body><div class='container'>";
        echo "<h1>About</h1>";
        echo "<p>This is a simple PHP router demonstration with static file serving.</p>";
        echo "<div class='image-demo'>";
        echo "<img src='" . url('/assets/demo-image.svg') . "' alt='PHP Router Demo Image' />";
        echo "<p><em>Same image, different page!</em></p>";
        echo "</div>";
        echo "<h3>Technical Details:</h3>";
        echo "<ul>";
        echo "<li><strong>Server:</strong> PHP " . PHP_VERSION . "</li>";
        echo "<li><strong>Static Files:</strong> CSS and SVG served directly</li>";
        echo "<li><strong>Router:</strong> Custom PHP routing with fallback to static files</li>";
        echo "<li><strong>Base Path:</strong> <code>" . htmlspecialchars($basePath ?: '/') . "</code></li>";
        echo "</ul>";
        echo "<p>The styling you see is loaded from <code>" . url('/assets/style.css') . "</code></p>";
        echo "<a href='" . url('/home') . "'>← Back to home</a>";
        echo "</div></body></html>";
        break;

    case '/api/status':
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'timestamp' => time(),
            'method' => $method,
            'php_version' => PHP_VERSION,
            'base_path' => $basePath,
            'full_uri' => $fullUri,
            'route_uri' => $uri
        ]);
        break;

    default:
        // Check if it's a static file (needed for PHP built-in server compatibility)
        $file = __DIR__ . $uri;
        if (is_file($file)) {
            return false; // Let PHP's built-in server handle static files
        }

        // 404 for unknown routes (static files are handled by .htaccess in production)
        http_response_code(404);
        echo "<!DOCTYPE html><html><head>";
        echo "<title>404 - Not Found</title>";
        echo "<link rel='stylesheet' href='" . url('/assets/style.css') . "'>";
        echo "</head><body><div class='container'>";
        echo "<h1>404 - Not Found</h1>";
        echo "<p>The requested route '{$uri}' was not found.</p>";
        echo "<p>Full URI: <code>" . htmlspecialchars($fullUri) . "</code></p>";
        echo "<p>Base path: <code>" . htmlspecialchars($basePath ?: '/') . "</code></p>";
        echo "<p>Make sure you're using the correct URL format.</p>";
        echo "<a href='" . url('/home') . "'>← Back to home</a>";
        echo "</div></body></html>";
        break;
}
