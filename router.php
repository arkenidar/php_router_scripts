<?php

/**
 * Simple PHP Router Script
 * This script handles routing for the PHP built-in development server
 */

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string and decode URL
$uri = urldecode($uri);

// Basic routing logic
switch ($uri) {
    case '/':
        echo "<!DOCTYPE html><html><head>";
        echo "<title>PHP Router Demo</title>";
        echo "<link rel='stylesheet' href='/assets/style.css'>";
        echo "</head><body><div class='container'>";
        echo "<h1>Welcome to PHP Router</h1>";
        echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
        echo "<p>Available routes:</p>";
        echo "<ul>";
        echo "<li><a href='/hello'>GET /hello</a> - Page with static image</li>";
        echo "<li><a href='/hello?name=PHP'>GET /hello?name=PHP</a> - Hello with name parameter</li>";
        echo "<li><a href='/about'>GET /about</a> - Page with static image and CSS</li>";
        echo "<li><a href='/api/status'>GET /api/status</a> - JSON API endpoint</li>";
        echo "<li><a href='/assets/demo-image.svg'>Direct image access</a></li>";
        echo "</ul>";
        echo "</div></body></html>";
        break;

    case '/hello':
        // Get query parameters
        $name = $_GET['name'] ?? 'World';
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); // Sanitize input

        echo "<!DOCTYPE html><html><head>";
        echo "<title>Hello - PHP Router Demo</title>";
        echo "<link rel='stylesheet' href='/assets/style.css'>";
        echo "</head><body><div class='container'>";
        echo "<h1>Hello {$name}!</h1>";

        if (isset($_GET['name'])) {
            echo "<p>Welcome, <strong>{$name}</strong>! This greeting was personalized using the query parameter <code>?name={$name}</code>.</p>";
        } else {
            echo "<p>This is the hello route with a static image demonstration.</p>";
            echo "<p>Try adding a name parameter: <a href='/hello?name=PHP'>/hello?name=PHP</a></p>";
        }

        echo "<div class='image-demo'>";
        echo "<img src='/assets/demo-image.svg' alt='PHP Router Demo Image' />";
        echo "<p><em>This SVG image is served as a static file</em></p>";
        echo "</div>";
        echo "<p>The image above is loaded from <code>/assets/demo-image.svg</code></p>";

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

        echo "<a href='/'>← Back to home</a>";
        echo "</div></body></html>";
        break;

    case '/about':
        echo "<!DOCTYPE html><html><head>";
        echo "<title>About - PHP Router Demo</title>";
        echo "<link rel='stylesheet' href='/assets/style.css'>";
        echo "</head><body><div class='container'>";
        echo "<h1>About</h1>";
        echo "<p>This is a simple PHP router demonstration with static file serving.</p>";
        echo "<div class='image-demo'>";
        echo "<img src='/assets/demo-image.svg' alt='PHP Router Demo Image' />";
        echo "<p><em>Same image, different page!</em></p>";
        echo "</div>";
        echo "<h3>Technical Details:</h3>";
        echo "<ul>";
        echo "<li><strong>Server:</strong> PHP " . PHP_VERSION . "</li>";
        echo "<li><strong>Static Files:</strong> CSS and SVG served directly</li>";
        echo "<li><strong>Router:</strong> Custom PHP routing with fallback to static files</li>";
        echo "</ul>";
        echo "<p>The styling you see is loaded from <code>/assets/style.css</code></p>";
        echo "<a href='/'>← Back to home</a>";
        echo "</div></body></html>";
        break;

    case '/api/status':
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'timestamp' => time(),
            'method' => $method,
            'php_version' => PHP_VERSION
        ]);
        break;

    default:
        // Check if it's a static file
        $file = __DIR__ . $uri;
        if (is_file($file)) {
            return false; // Let PHP's built-in server handle static files
        }

        // 404 for other routes
        http_response_code(404);
        echo "<h1>404 - Not Found</h1>";
        echo "<p>The requested route '{$uri}' was not found.</p>";
        echo "<a href='/'>← Back to home</a>";
        break;
}
