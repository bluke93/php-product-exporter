<?php

// API Configuration
define('BASE_URL', getenv('BASE_URL') ?: '');
define('API_PATH', getenv('API_PATH') ?: '');
define('API_VERSION', getenv('API_VERSION') ?: '');
define('API_ENDPOINT', getenv('API_ENDPOINT') ?: '');

// Database Configuration
define('DB_CONNECTION', getenv('DB_CONNECTION') ?: '');

// Store Configuration
define('STORE_ID', getenv('STORE_ID') ?: '');

// API Authentication
define('HEADER_APIKEY_KEY', getenv('HEADER_APIKEY_KEY') ?: '');
define('HEADER_APIKEY_VALUE', getenv('HEADER_APIKEY_VALUE') ?: '');

// Application Settings
define('MAX_RETRIES', getenv('MAX_RETRIES') ?: 3);

// File System Paths
define('IMAGES_PATH', getenv('IMAGES_PATH') ?: '');
define('CONFIG_PATH', getenv('CONFIG_PATH') ?: '');

?>