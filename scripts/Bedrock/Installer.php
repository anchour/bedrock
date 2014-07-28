<?php

namespace Bedrock;

use Composer\Script\Event;

class Installer {
  public static $KEYS = array(
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT'
  );

  /**
   * Holds the keys that are used in .env.php.
   * @var array
   */
  public static $DBKEYS = array(
    'DB_NAME',
    'DB_HOST',
    'DB_USER',
    'DB_PASSWORD',
  );

  /**
   * Holds the root path.
   * @var string
   */
  private static $root;

  /**
   * Holds the path to the .env file.
   * @var string
   */
  private static $envFilePath;

  /**
   * Holds the handle returned by fopen()
   * @var resource
   */
  private static $envFile;

  /**
   * Holds the path to the .env.php file.
   * @var string
   */
  private static $envPhpFilePath;

  /**
   * Holds the handle returned by fopen()
   * @var resource
   */
  private static $envPhpFile;

  public static function addSalts(Event $event)
  {
    // Set the paths, since PHP is stupid and was throwing a syntax error due to the dirname function.
    static::$root = dirname(dirname(__DIR__));
    static::$envFilePath = static::$root . '/.env';
    static::$envPhpFilePath = static::$root . '/.env.php';

    $composer = $event->getComposer();
    $io = $event->getIO();

    if (!$io->isInteractive()) {
      $generate_salts = $composer->getConfig()->get('generate-salts');
    } else {
      $generate_salts = $io->askConfirmation('<info>Generate salts and append to .env file?</info> [<comment>Y,n</comment>]? ', true);
      $generate_env_php = $io->askConfirmation('<info>Generate .env.php file for database details? (This file is generated by Forge when global vars are created.) </info> [<comment>Y,n</comment>]? ', true);
    }

    if ($generate_salts) {
      static::createEnvFile();
    }

    if ($generate_env_php) {
      static::createEnvPhpFile();
    }
  }

  /**
   * Create the .env file and set up the salts.
   *
   * @return void
   */
  private static function createEnvFile()
  {
    // Don't need to re-create the file.
    if (file_exists(static::$envFilePath)) {
      return false;
    }

    $handle = fopen(static::$envFilePath, 'w');

    $salts = array_map(function ($key) {
      return sprintf("%s='%s'", $key, Installer::generateSalt());
    }, self::$KEYS);

    if ( $handle ) {
      fwrite($handle, implode($salts, "\n"));
      fclose($handle);
    }
  }

  /**
   * Create the .env.php file that is created via Laravel Forge. This is done automatically to fix
   * any fatal errors by requiring a .env.php file that doesn't exist when accessing the site.
   * We don't truncate the file because the database details (obviously) shouldn't be wiped
   * any time changes are deployed via Forge (`composer install` is run every time.)
   *
   * @return void
   */
  private static function createEnvPhpFile()
  {
    if (file_exists(static::$envPhpFilePath)) {
      return 1;
    }

    $handle = fopen(static::$envPhpFilePath, 'a');

    $keys = array_map(function($key) {
      return sprintf("'%s' => '%s'", $key, '');
    }, self::$DBKEYS);

    if (strlen(file_get_contents(static::$envPhpFilePath)) === 0 && $handle) {
      fwrite($handle, "<?php return array(\n  " . implode($keys, ",\n  ") . "\n);");
      fclose($handle);
    }
  }

  /**
   * Slightly modified/simpler version of wp_generate_password
   * https://github.com/WordPress/WordPress/blob/cd8cedc40d768e9e1d5a5f5a08f1bd677c804cb9/wp-includes/pluggable.php#L1575
   */
  public static function generateSalt($length = 64) {
    $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $chars .= '!@#$%^&*()';
    $chars .= '-_ []{}<>~`+=,.;:/?|';

    $salt = '';
    for ($i = 0; $i < $length; $i++) {
      $salt .= substr($chars, rand(0, strlen($chars) - 1), 1);
    }

    return $salt;
  }
}
