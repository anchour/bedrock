<?php
$root_dir = dirname(__DIR__);
$webroot_dir = $root_dir . '/web';

$files = scandir($root_dir);

// Get the available config file. I don't like this implementation.
foreach ($files as $key => $file) {
  if ( substr($file, 0, 4) === '.env' && substr($file, ( strlen($file) - 4 ), 4) === '.php' ) {
    $files[$key] = $file;
  } else {
    unset($files[$key]);
  }
}

// Get the environment file.
$env_file = array_shift($files);

if ( ! $env_file ) {
  throw new Exception('No environment file found.');
}

$env_config = $root_dir . '/' . $env_file;
$env_vars = require $env_config;

// Get the .env configuration file.
if (is_array($env_vars) && count($env_vars) > 0) {
  foreach($env_vars as $key => $var) {
    $_ENV[$key] = $var;
  }
}

/**
 * Set up our global environment constant and load its config first
 * Default: development
 */
define('WP_ENV', $_ENV['WP_ENV'] ? $_ENV['WP_ENV'] : 'development');

/**
 * Custom Content Directory
 */
define('CONTENT_DIR', '/app');
define('WP_CONTENT_DIR', $webroot_dir . CONTENT_DIR);
define('WP_CONTENT_URL', WP_HOME . CONTENT_DIR);

/**
 * DB settings
 */
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix = 'wp_';

/**
 * WordPress Localized Language
 * Default: English
 *
 * A corresponding MO file for the chosen language must be installed to app/languages
 */
define('WPLANG', '');

/**
 * Authentication Unique Keys and Salts
 */
define('AUTH_KEY',         getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY'));
define('NONCE_KEY',        getenv('NONCE_KEY'));
define('AUTH_SALT',        getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT'));
define('NONCE_SALT',       getenv('NONCE_SALT'));

/**
 * Custom Settings
 */
define('AUTOMATIC_UPDATER_DISABLED', true);
define('DISABLE_WP_CRON', true);
define('DISALLOW_FILE_EDIT', true);

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
  define('ABSPATH', $webroot_dir . '/wp/');
}
