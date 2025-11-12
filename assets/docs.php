<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Router Dual Compatibility Documentation</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .doc-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }

        .section {
            margin: 30px 0;
            padding: 20px;
            border-left: 4px solid #3498db;
            background: #f8f9fa;
        }

        .dev-section {
            border-left-color: #27ae60;
            background: #e8f8f5;
        }

        .prod-section {
            border-left-color: #f39c12;
            background: #fef9e7;
        }

        .code-block {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            white-space: pre;
        }

        .shell-block {
            background: #1e1e1e;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            white-space: pre;
            border-left: 4px solid #00ff00;
        }

        .inline-code {
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .comparison-table th,
        .comparison-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .comparison-table th {
            background: #34495e;
            color: white;
        }

        .comparison-table tr:nth-child(even) {
            background: #f2f2f2;
        }

        .flow-diagram {
            text-align: center;
            margin: 20px 0;
        }

        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .file-tree {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            font-family: 'Courier New', monospace;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="doc-container">
        <h1>PHP Router Dual Compatibility Documentation</h1>

        <p>This PHP router is designed with <strong>dual compatibility</strong> to work seamlessly in both development
            and production environments without code changes.</p>

        <div class="flow-diagram">
            <img src="dual-support-diagram.svg" alt="Dual Support Architecture"
                style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px;">
        </div>

        <div class="section">
            <h2>🎯 Core Concept</h2>
            <p>The router automatically detects its environment and adapts its behavior:</p>
            <ul>
                <li><strong>Development:</strong> Works with PHP's built-in development server</li>
                <li><strong>Production:</strong> Works with Apache + .htaccess for clean URLs</li>
                <li><strong>Same Code:</strong> No modifications needed between environments</li>
            </ul>
        </div>

        <div class="section dev-section">
            <h2>🔧 Development Environment</h2>
            <h3>Setup</h3>
            <pre class="shell-block"># Start the PHP built-in development server
php -S localhost:8000 router.php</pre>

            <h3>How it Works</h3>
            <p>In development mode, the router uses PHP's built-in server capabilities:</p>
            <ul>
                <li>All requests are routed through <span class="inline-code">router.php</span></li>
                <li>Static files are detected using <span class="inline-code">is_file()</span></li>
                <li>When a static file is requested, <span class="inline-code">return false;</span> lets PHP serve it
                    directly</li>
                <li>Dynamic routes are handled by the switch statement</li>
            </ul>

            <h3>Static File Handling</h3>
            <pre class="code-block">// Check if it's a static file (needed for PHP built-in server compatibility)
$file = __DIR__ . $uri;
if (is_file($file)) {
    return false; // Let PHP's built-in server handle static files
}</pre>

            <div class="success">
                <strong>✅ Benefits:</strong> Quick setup, no server configuration, perfect for rapid development and
                testing.
            </div>
        </div>

        <div class="section prod-section">
            <h2>🚀 Production Environment</h2>
            <h3>Setup Requirements</h3>
            <ul>
                <li>Apache web server with <span class="inline-code">mod_rewrite</span> enabled</li>
                <li><span class="inline-code">AllowOverride All</span> in virtual host configuration</li>
                <li>.htaccess file in document root</li>
            </ul>

            <h3>Apache Configuration</h3>
            <pre class="shell-block"># Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# In your virtual host or apache2.conf:
&lt;Directory /var/www/html&gt;
    AllowOverride All
    Require all granted
&lt;/Directory&gt;</pre>

            <h3>.htaccess Configuration</h3>
            <pre class="code-block">RewriteEngine On

# If the requested file doesn't exist AND the requested directory doesn't exist
# then route the request to router.php (for dynamic routes)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ router.php [QSA,L]

# For all other cases (when files or directories DO exist)
# serve them as-is without rewriting (static files, existing directories)
RewriteRule ^(.*)$ - [QSA,L]</pre>

            <h3>How it Works</h3>
            <p>In production mode, Apache handles the routing:</p>
            <ul>
                <li>Apache checks if the requested file exists</li>
                <li>If it exists (static file), Apache serves it directly</li>
                <li>If it doesn't exist, .htaccess routes to <span class="inline-code">router.php</span></li>
                <li>The <span class="inline-code">return false;</span> code never executes in production</li>
            </ul>

            <div class="success">
                <strong>✅ Benefits:</strong> Better performance, proper web server handling, production-ready clean
                URLs.
            </div>
        </div>

        <div class="section">
            <h2>📊 Environment Comparison</h2>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>Aspect</th>
                        <th>Development (PHP Server)</th>
                        <th>Production (Apache + .htaccess)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Server Command</strong></td>
                        <td><span class="inline-code">php -S localhost:8000 router.php</span></td>
                        <td>Apache virtual host</td>
                    </tr>
                    <tr>
                        <td><strong>Static File Handling</strong></td>
                        <td>PHP checks file existence, returns false</td>
                        <td>Apache serves directly via .htaccess</td>
                    </tr>
                    <tr>
                        <td><strong>URL Routing</strong></td>
                        <td>All requests to router.php</td>
                        <td>Only non-existing files to router.php</td>
                    </tr>
                    <tr>
                        <td><strong>Performance</strong></td>
                        <td>Good for development</td>
                        <td>Optimized for production</td>
                    </tr>
                    <tr>
                        <td><strong>Configuration</strong></td>
                        <td>Zero configuration</td>
                        <td>Requires mod_rewrite + .htaccess</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>📁 File Structure</h2>
            <pre class="file-tree">project-root/
├── router.php          # Main router logic (works in both environments)
├── index.php          # Entry point for .htaccess (optional)
├── .htaccess          # Apache rewrite rules
├── assets/
│   ├── style.css      # Stylesheet
│   ├── demo-image.svg # Demo image
│   └── docs.php       # This documentation
└── README.md          # Project documentation</pre>
        </div>

        <div class="section">
            <h2>🔄 Request Flow</h2>

            <h3>Development Environment Flow</h3>
            <ol>
                <li>Browser requests <span class="inline-code">/hello</span></li>
                <li>PHP built-in server routes to <span class="inline-code">router.php</span></li>
                <li>Router checks if it's a static file</li>
                <li>Not a static file → processes route in switch statement</li>
                <li>Returns HTML response</li>
            </ol>

            <h3>Production Environment Flow</h3>
            <ol>
                <li>Browser requests <span class="inline-code">/hello</span></li>
                <li>Apache checks if <span class="inline-code">/hello</span> file exists</li>
                <li>File doesn't exist → .htaccess routes to <span class="inline-code">router.php</span></li>
                <li>Router processes route in switch statement</li>
                <li>Returns HTML response</li>
            </ol>
        </div>

        <div class="section">
            <h2>🛠 Available Routes</h2>
            <ul>
                <li><strong><span class="inline-code">/home</span></strong> - Home page with route listing and
                    architecture diagram</li>
                <li><strong><span class="inline-code">/hello</span></strong> - Hello page with optional name parameter
                </li>
                <li><strong><span class="inline-code">/about</span></strong> - About page with technical details</li>
                <li><strong><span class="inline-code">/api/status</span></strong> - JSON API endpoint</li>
                <li><strong><span class="inline-code">/assets/*</span></strong> - Static files (CSS, images, etc.)</li>
            </ul>
        </div>

        <div class="section">
            <h2>🚨 Troubleshooting</h2>

            <h3>Common Issues</h3>

            <div class="warning">
                <strong>⚠️ 404 on static files in production:</strong>
                <p>Check that Apache has <span class="inline-code">mod_rewrite</span> enabled and <span
                        class="inline-code">AllowOverride All</span> is set for your directory.</p>
            </div>

            <div class="warning">
                <strong>⚠️ Routes not working in production:</strong>
                <p>Verify .htaccess file permissions (644) and that it's in the correct directory (document root).</p>
            </div>

            <div class="warning">
                <strong>⚠️ 500 Internal Server Error:</strong>
                <p>Check Apache error logs: <span class="inline-code">tail -f /var/log/apache2/error.log</span></p>
            </div>

            <h3>Testing Both Environments</h3>
            <pre class="shell-block"># Test development server
php -S localhost:8000 router.php
curl http://localhost:8000/hello

# Test production (assuming files in /var/www/html/)
curl http://localhost/hello</pre>
        </div>

        <div class="section">
            <h2>✨ Key Benefits</h2>
            <ul>
                <li><strong>Zero Code Changes:</strong> Same router.php works in both environments</li>
                <li><strong>Seamless Development:</strong> Quick setup with PHP built-in server</li>
                <li><strong>Production Ready:</strong> Clean URLs and proper static file handling</li>
                <li><strong>Automatic Detection:</strong> Environment-aware behavior</li>
                <li><strong>Performance Optimized:</strong> Static files served efficiently in production</li>
            </ul>
        </div>

        <div class="section">
            <h2>🎓 Learning More</h2>
            <p>This dual compatibility pattern is useful for:</p>
            <ul>
                <li>Building prototypes that can scale to production</li>
                <li>Teaching PHP routing concepts</li>
                <li>Creating portable PHP applications</li>
                <li>Understanding web server request handling</li>
            </ul>
        </div>

        <p style="text-align: center; margin-top: 50px;">
            <a href="../home">← Back to Router Demo</a>
        </p>
    </div>
</body>

</html>