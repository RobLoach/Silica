<?php

// Bootstrap the class loader.
require_once __DIR__ . '/../vendor/.composer/autoload.php';

// Create the new Silica Application, reading the configuration from config.yml.
$app = new Silica\Application('../app/config.yml');

// Execute the application.
$app->run();
