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
   * Holds the root path.
   * @var string
   */
  private static $root;

  /**
   * Holds the path to the .env file.
   * @var [type]
   */
  private static $envFilePath;

  public static function addSalts(Event $event)
  {
    // Set the paths, since PHP is stupid and was throwing a syntax error due to the dirname function.
    static::$root = dirname(dirname(__DIR__));
    static::$envFilePath = static::$root . '/.env';

    $composer = $event->getComposer();
    $io = $event->getIO();

    if (!$io->isInteractive()) {
      $generate_salts = $composer->getConfig()->get('generate-salts');
    } else {
      $generate_salts = $io->askConfirmation('<info>Generate salts and append to .env file?</info> [<comment>Y,n</comment>]? ', true);
    }

    if (!$generate_salts) {
      return 1;
    }

    $salts = array_map(function ($key) {
      return sprintf("%s='%s'", $key, Installer::generateSalt());
    }, self::$KEYS);

    if (! file_exists(static::$envFilePath)) {
      static::createEnvFile();
    }

    file_put_contents(static::$envFilePath, implode($salts, "\n"), FILE_APPEND | LOCK_EX);
  }

  /**
   * Create/truncate the .env file to 0.
   * @return resource
   */
  private static function createEnvFile()
  {
    $handle = fopen(static::$envFilePath, 'w');

    fclose($handle);
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
