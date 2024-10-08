<?php

require_once __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    // If .env exists, use it for local development
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
// // Load the .env file
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();
?>
