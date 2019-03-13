<?php

namespace Jawn;

/**
 * Basket class (static)
 *
 * Creates source & target connections
 */
class Basket
{
    private static $_default_database;
    public static $_databases = [];
    private static $_default_remote;
    public static $_remotes = [];
    private static $_project_dir;

    /**
     * Return database connection object
     */
    public static function database(string $name = '')
    {
        if ($name === '' && self::$_default_database !== '') {
            $name = self::$_default_database;
        }
        if (isset(self::$_databases[$name])) {
            $class = 'Jawn\Database\\'.ucfirst(strtolower(self::$_databases[$name]->driver));
            return new $class(self::$_databases[$name]);
        }
        throw new \BasketConfigException("Database connection {$name} not found.");
    }

    /**
     * Return remote connection object
     */
    public static function remote(string $name = '')
    {
        if ($name === '' && self::$_default_remote !== '') {
            $name = self::$_default_remote;
        }
        if (isset(self::$_remotes[$name])) {
            $class = 'Jawn\Remote\\'.ucfirst(strtolower(self::$_remotes[$name]->protocol));
            return new $class(self::$_remotes[$name]);
        }
        throw new \BasketConfigException("Remote connection {$name} not found.");
    }

    /**
     * Return project dir for current host
     * @param   string  $name   Project name
     * @return  string
     */
    public static function projectDir(string $name = ''): string
    {
        if (self::$_project_dir === '') {
            throw new \BasketConfigException('Project dir not set for ' . gethostname());
        }
        if ($name !== '') {
            return rtrim(self::$_project_dir, '/').'/'.rtrim($name, '/');
        }
        return rtrim(self::$_project_dir, '/');
    }

    /**
     * Load json config file
     * @param   string  $location   Location of config
     * @return  void
     */
    public static function load(string $location): void
    {
        if (!file_exists($location)) {
            throw new \BasketConfigException('Config not found.');
        }

        $config = json_decode(file_get_contents($location));

        // set defaults
        self::$_default_database = $config->database->default ?? '';
        self::$_default_remote = $config->remote->default ?? '';

        foreach ($config->database->connections ?? [] as $key => $item) {
            $class = 'Jawn\Database\\'. ucfirst(strtolower($item->driver));
            if (!class_exists($class)) {
                throw new \BasketConfigException("Database driver {$item->driver} does not exist.");
            }

            self::$_databases[$key] = $item;
        }

        foreach ($config->remote->connections ?? [] as $key => $item) {
            $class = 'Jawn\Remote\\' . ucfirst(strtolower($item->protocol));
            if (!class_exists($class)) {
                throw new \BasketConfigException("Remote protocol {$item->protocol} does not exist.");
            }

            self::$_remotes[$key] = $item;
        }

        // set project dir
        self::$_project_dir = '';
        foreach ($config->project_dirs ?? [] as $key => $item) {
            if ($key !== gethostname()) {
                continue;
            }
            self::$_project_dir = $item;
            break;
        }
    }
}
