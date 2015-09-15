<?php

namespace Bedrock;

use Composer\Script\Event;

class Installer {
    public static $saltKeys = array(
        'AUTH_KEY',
        'SECURE_AUTH_KEY',
        'LOGGED_IN_KEY',
        'NONCE_KEY',
        'AUTH_SALT',
        'SECURE_AUTH_SALT',
        'LOGGED_IN_SALT',
        'NONCE_SALT'
    );

    public static $dbKeys = [
        'DB_NAME',
        'DB_HOST',
        'DB_USER',
        'DB_PASSWORD',
        'WP_ENV',
        'WP_HOME',
        'WP_SITEURL',
    ];

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

    public static function addSalts(Event $event)
    {
        static::$root = dirname(dirname(__DIR__));
        static::$envFilePath = static::$root . '/.env';

        $io = $event->getIO();

        // Only add new/regenerate salts if we are doing a fresh install and specify "yes" to the question.
        // This way the salts aren't cleared whenever a `composer install` is done on deploy.
        if ($io->isInteractive()) {
            $generate_salts = $io->askConfirmation('<info>Generate salts and append to .env file?</info> [<comment>Y,n</comment>]? ', true);
        }

        // No need to continue - exit.
        if (! $generate_salts) {
            return;
        }

        // Open the file for writing.
        $handle = fopen(static::$envFilePath, 'a');

        $keys = array_map(function($key) {
            return sprintf("%s='%s'", $key, Installer::generateSalt());
        }, self::$saltKeys);

        if ($handle) {
            fwrite($handle, implode($keys, "\n"));
            fclose($handle);
        }
    }

    /**
     * Create the .env file and set up the salts.
     *
     * @return void
     */
    private static function createEnvFile()
    {
        if (file_exists(static::$envFilePath)) {
            return false;
        }
    }

    /**
     * Slightly modified/simpler version of wp_generate_password
     *
     * @link <https://github.com/WordPress/WordPress/blob/cd8cedc40d768e9e1d5a5f5a08f1bd677c804cb9/wp-includes/pluggable.php#L1575>
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
