<?php
$root_dir = dirname(__DIR__);
$webroot_dir = $root_dir . '/public';

// Load and set the required $_ENV variables.
Dotenv::load($root_dir);
Dotenv::required([
    'WP_HOME',
    'WP_SITEURL',
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'DB_PASSWORD'
]);

$_ENV['WP_ENV'] = isset($_ENV['WP_ENV'])
  ? $_ENV['WP_ENV']
  : 'development';

define('WP_ENV', $_ENV['WP_ENV']);

// Get the proper config file...
$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

// ... and include it, so we set the proper definitions, etc.
if (file_exists($env_config)) {
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

// Set a table prefix based on the environment variable, if set. If not, fall back to a non-standard
// prefix, just for a little more security.
$table_prefix = isset($_ENV['TABLE_PREFIX']) ? $_ENV['TABLE_PREFIX'] : 'ancb_';

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
