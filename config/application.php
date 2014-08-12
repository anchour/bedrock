<?php
$root_dir = dirname(__DIR__);
$webroot_dir = $root_dir . '/public';

// Get the .env file set up by the Bedrock Installer. Dotenv::load automatically searches for
// the .env file, so no need to set up the .env path.
Dotenv::load($root_dir);

// Get the environment file set up by Forge.
$env_file = "{$root_dir}/.env";
$env_php_file = "{$root_dir}/.env.php";

if ( ! file_exists($env_file) ) {
  throw new Exception('No environment file found.');
}

$env_vars = [];

/**
 * If the .env.php file exists, include that file so we get the .env.php database array.
 * Note that these values will be overridden by any values set within .env,
 * so really only salts should be set within .env for any sites
 * That are deployed via Forge.
 */
if ( file_exists($env_php_file) )
{
  $env_vars = require $env_php_file;
}

/**
 * Get the .env configuration file. Only set the $_ENV var from .env.php
 * if the environment variable isn't already set. This allows .env
 * to manage both DB_HOST, USER, etc. variables, so local
 * dev. environments do not need to use .env.php
 */
if (is_array($env_vars) && count($env_vars) > 0) {
  foreach($env_vars as $key => $var) {
    if ( ! isset($_ENV[$key]) )
      putenv("{$key}={$var}");
      $_ENV[$key] = $var;
      $_SERVER[$key] = $var;
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
