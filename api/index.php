<?php
// filepath: /c:/xampp/htdocs/php_api/public/index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload classes using Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Define the base path
define('BASE_PATH', dirname(__DIR__));

// Load the core application
require_once BASE_PATH . '/api/core/App.php';

// Initialize the application
$app = new App();

?>