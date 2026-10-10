<?php

declare(strict_types=1);

/*
 * Local:
 * $routes  - routing table defined by H-Project
 * $method  - HTTP method extracted from $_SERVER
 * $path    - request path extracted from $_SERVER
 * $handler - resolved local handler file
 *
 * System / PHP:
 * $_SERVER - PHP superglobal containing server and request information
 * PHP built-in functions / language constructs used:
 *
 * parse_url($url, $component)
 * PHP built-in.
 * Takes a URL/URI string and optionally a requested component.
 * Returns the requested URL component. We request PHP_URL_PATH.
 *
 * isset($value)
 * PHP language construct.
 * Takes a variable or array element.
 * Returns true if it exists and is not null, otherwise false.
 *
 * http_response_code($code)
 * PHP built-in.
 * Takes an integer HTTP status code.
 * Sets the HTTP response status. Returns the current/previous response code.
 *
 * header($header)
 * PHP built-in.
 * Takes an HTTP header string.
 * Adds/replaces a header in the HTTP response.
 *
 * json_encode($value)
 * PHP built-in.
 * Takes a PHP value/array/object.
 * Returns its JSON string representation, or false on failure.
 *
 * is_file($filename)
 * PHP built-in.
 * Takes a filesystem path.
 * Returns true if the path exists and is a regular file.
 *
 * pathinfo($path, $flags)
 * PHP built-in.
 * Takes a filesystem path and optional requested component.
 * Returns path information. We request PATHINFO_EXTENSION.
 *
 * readfile($filename)
 * PHP built-in.
 * Takes a filesystem path.
 * Outputs the file directly and returns the number of bytes read, or false.
 *
 * require $filename
 * PHP language construct.
 * Takes a PHP file path.
 * Loads and executes that PHP file. Fatal error if it cannot be loaded.
 *
 * exit
 * PHP language construct.
 * Immediately terminates execution of the current PHP script.
 *
 * __DIR__
 * PHP magic constant.
 * Returns the absolute directory containing the current PHP file.
 *
 * PHP_URL_PATH
 * PHP predefined constant used by parse_url().
 * Requests only the path component of the URI.
 *
 * PATHINFO_EXTENSION
 * PHP predefined constant used by pathinfo().
 * Requests only the file extension.
 */

$routes =
[
    'GET' =>
    [
        '/' => __DIR__ . '/../public/index.html',
        '/api/status' => __DIR__ . '/../src/status.php'
    ],

    'POST' =>
    [
        // '/api/chat' => __DIR__ . '/../src/chat.php'
    ]
];

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!isset($routes[$method]))
{
    http_response_code(405);
    header('Content-Type: application/json');

    echo json_encode(
    [
        'error' => 'Method not allowed'
    ]);

    exit;
}

if (!isset($routes[$method][$path]))
{
    http_response_code(404);
    header('Content-Type: application/json');

    echo json_encode(
    [
        'error' => 'Route not found'
    ]);

    exit;
}

$handler = $routes[$method][$path];

if (!is_file($handler))
{
    http_response_code(500);
    header('Content-Type: application/json');

    echo json_encode(
    [
        'error' => 'Route handler missing'
    ]);

    exit;
}

$extension = pathinfo($handler, PATHINFO_EXTENSION);

if ($extension === 'html')
{
    header('Content-Type: text/html; charset=utf-8');
    readfile($handler);

    exit;
}

require $handler;