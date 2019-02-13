<?php

/**
 * Useful (or not) console tools
 */
class Console
{
    private static $colors = [
        'black'  => 30,
        'red'    => 31,
        'green'  => 32,
        'yellow' => 33,
        'blue'   => 34,
        'purple' => 35,
        'cyan'   => 36,
        'white'  => 37
    ];

    private static $verbose = true;

    /**
     * This is used to echo the progress of a task on the command line.
     * Pass in the current row that you are on and the number of rows
     * that need to be processed and this will echo out
     * a progress bar like this
     *
     * @param      $totalDone - The number of rows that have been processed so far
     * @param      $total     - The total number of rows to be processed
     * @param bool $last      - If the process has been completed
     * @param bool $steps     - How wide the process bar should be
     */
    public static function bar(int $done, int $total, int $steps = 60): void
    {
        $red = "\033[41m \033[0m";
        $blue = "\033[44m \033[0m";

        if ($done === $total) {
            $display = "Completed. {" . str_repeat($blue, $steps + 1) . "} 100%\n";
        } else {
            $toGo = floor((1 - ($done / $total)) * $steps);
            $progressBar = str_repeat($red, $steps - $toGo);
            $emptySpace = str_repeat(' ', $toGo);
            $display = "Running... {{$progressBar}{$red}{$emptySpace}} "
                . (round(1-($toGo/$steps),2)*100).'%';
        }
        echo "$display\r";
    }

    public static function table(array ...$data): void
    {
        foreach ($data as $set) {
            $table = new jc21\CliTable();
            $keys = array_keys($set[key($set)] ?? []);

            foreach ($keys ?? [] as $key)
                $table->addField($key, $key, 'grey');

            $table->setTableColor('red');
            $table->setHeaderColor('cyan');

            $table->injectData($set);
            $table->display();
            self::reset();
        }
    }

    private static function text(string $color, string $str): void
    {
        if (self::$verbose) {
            echo "\033[0;".self::$colors[$color]."m";
            echo $str;
            if ($str != '') self::reset();
        }
    }

    /**
     * Turn on our off console output
     */
    public static function verbose(bool $verbose = true): void
    {
        self::$verbose = $verbose;
    }

    /**
     * Write out newline
     */
    public static function EOL(string $eol = PHP_EOL): void
    {
        self::text('white', $eol);
    }

    /**
     * Write out white text
     */
    public static function log(string $str, string $eol = PHP_EOL): void
    {
        self::text('white', $str . $eol);
    }

    /**
     * Write out blue text
     */
    public static function info (string $str, string $eol = PHP_EOL): void
    {
        self::text('blue', $str.$eol);
    }

    /**
     * Write out yellow text
     */
    public static function warning(string $str, string $eol = PHP_EOL): void
    {
        self::text('yellow', $str.$eol);
    }

    /**
     * Write out red text and exit
     */
    public static function danger(string $str, string $eol = PHP_EOL): void
    {
        self::text('red', $str.$eol);
        exit(1);
    }

    /**
     * Write out green text
     */
    public static function success(string $str, string $eol = PHP_EOL): void
    {
        self::text('green', $str.$eol);
    }

    /**
     * Write out message, read and return user input
     */
    public static function prompt(string $str, string $end = ': '): string
    {
        return readline($str.$end);
    }

    /**
     * Write out message, silently read and return user input
     */
    public static function password(string $str, string $end = ': '): string
    {
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            Console::danger("Can't invoke bash");
        }
        $command = "/usr/bin/env bash -c 'read -s -p \""
            . addslashes($str.$end)
            . "\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        Console::EOL();
        return $password;
    }

    /**
     * Clears console
     */
    public static function clear(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
    }

    /**
     * Resets console color
     */
    public static function reset(): void
    {
        echo "\033[0m";
    }

    /**
     * Usage: ->transform('command', ['command' => 'ls'])
     *
     * @access public
     * @param   array   $options
     * @return  void
     */
    public static function command(string $command): void
    {
        shell_exec($command);
    }

    /**
     * Usage: ->transform('command', ['commands' => ['ls', 'echo 1']])
     *
     * @access public
     * @param   array   $options
     * @return  void
     */
    public static function commands(array $commands): void
    {
        foreach ($commands as $command) {
            self::command($command);
        }
    }

    /**
     * Get a cli argument
     * Supported formats
     * --argv value
     * --argv=value
     */
    public function get(string $name): string
    {
        global $argv;
        foreach ($argv as $key => $value) {
            if ("--$name=" === substr($value, 0, strlen("--$name="))) {
                return explode('=', $value, 2)[1];
            } elseif ("--$name" === $value) {
                if (isset($argv[($key + 1)])) {
                    return $argv[($key + 1)];
                }
                Console::danger("Argument --$name has no value.");
            }
        }
        Console::danger("Argument --$name does not exist.");
    }

    /**
     * Check if cli argument is set
     */
    public static function has(string $name): string
    {
        global $argv;
        foreach ($argv as $key => $value) {
            if ("--$name=" === substr($value, 0, strlen("--$name="))) {
                return true;
            } elseif ("--$name" === $value) {
                return true;
            }
        }
        return false;
    }
}
