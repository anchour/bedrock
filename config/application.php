<?php
$root_dir = dirname(__DIR__);
$webroot_dir = $root_dir . '/web';

// Get the environment file.
$env_file = "{$root_dir}/.env.php";

if ( ! file_exists($env_file) ) {
  throw new Exception('No environment file found.');
}
$env_vars = require $env_file;

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
$_ENV['WP_ENV'] = isset($_ENV['WP_ENV'])
  ? $_ENV['WP_ENV']
  : 'development';
define('WP_ENV', $_ENV['WP_ENV'] );

// Get the proper config file...
$env_config = dirname(__FILE__) . '/environments/' . WP_ENV . '.php';

// ... and include it, so we get the proper $_ENV variables.
if ( file_exists($env_config) ) {
  require_once $env_config;
}

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
$table_prefix = $_ENV['TABLE_PREFIX'] ? $_ENV['TABLE_PREFIX'] : 'wp_';

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
