<?php

/**
 * Basket class (static)
 *
 * Creates source & target connections
 */
class Basket
{
    private static $instance;

    private static $default_database;
    public static $databases = [];
    private static $default_remote;
    public static $remotes = [];
    private static $project_dir;

    /**
     * Return database connection object
     */
    public static function database(string $name = '')
    {
        if ($name === '' && self::$default_database !== '') {
            $name = self::$default_database;
        }
        if (isset(self::$databases[$name])) {
            $class = 'Database\\'.ucfirst(strtolower(self::$databases[$name]->driver));
            return new $class(self::$databases[$name]);
        }
        Console::danger("Database connection {$name} not found!");
    }

    /**
     * Return remote connection object
     */
    public static function remote(string $name = '')
    {
        if ($name === '' && self::$default_remote !== '') {
            $name = self::$default_remote;
        }
        if (isset(self::$remotes[$name])) {
            $class = 'Remote\\'.ucfirst(strtolower(self::$remotes[$name]->protocol));
            return new $class(self::$remotes[$name]);
        }
        Console::danger("Remote connection {$name} not found!");
    }

    /**
     * Return project dir for current host
     */
    public static function projectDir(string $name = ''): string
    {
        if (self::$project_dir === '') {
            Console::danger('Project dir not set for ' . gethostname());
        }
        if ($name !== '') {
            return rtrim(self::$project_dir, '/').'/'.rtrim($name, '/');
        }
        return rtrim(self::$project_dir, '/');
    }

    /**
     * Load json config file
     */
    public static function load(string $location): void
    {
        if (!file_exists($location)) {
            Console::danger('...Config not found!');
        }

        $config = json_decode(file_get_contents($location));

        // set defaults
        self::$default_database = $config->database->default ?? '';
        self::$default_remote = $config->remote->default ?? '';

        foreach ($config->database->connections ?? [] as $key => $item) {
            $class = 'Database\\'. ucfirst(strtolower($item->driver));
            if (!class_exists($class)) {
                Console::danger("Database driver {$item->driver} does not exist!");
            }

            self::$databases[$key] = $item;
        }

        foreach ($config->remote->connections ?? [] as $key => $item) {
            $class = 'Remote\\' . ucfirst(strtolower($item->protocol));
            if (!class_exists($class)) {
                Console::danger("Remote protocol {$item->protocol} does not exist!");
            }

            self::$remotes[$key] = $item;
        }

        // set project dir
        self::$project_dir = '';
        foreach ($config->project_dirs ?? [] as $key => $item) {
            if ($key !== gethostname()) {
                continue;
            }
            self::$project_dir = $item;
            break;
        }
    }
}
