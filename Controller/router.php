<?php
/**
 * Router for PHP Built-in Web Server
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Remove leading slashes and ../ patterns for cleaner matching
$cleanUri = preg_replace('#^/?\.\.?/#', '', $uri);

// Try Controller directory
if (preg_match('#^Controller/(.+\.php)$#', $cleanUri, $matches)) {
    $file = __DIR__ . '/' . $matches[1];
    if (file_exists($file)) {
        require $file;
        return true;
    }
}

// Try Model directory
if (preg_match('#^Model/(.+\.php)$#', $cleanUri, $matches)) {
    $file = __DIR__ . '/../Model/' . $matches[1];
    if (file_exists($file)) {
        require $file;
        return true;
    }
}

// Handle View directory
$file = __DIR__ . '/../View' . $uri;
if (is_dir($file)) {
    $file .= '/index.php';
}

if (file_exists($file)) {
    require $file; // Execute the file right here
    return true; // Indicate that the request has been handled
}

// 404
http_response_code(404);
echo "404 Not Found: " . htmlspecialchars($uri);
return true;