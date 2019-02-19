<?php

namespace Jawn;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\ErrorHandler;

class Register
{
    public static function exectionTimer(): void
    {
        $start_time = microtime(true);
        register_shutdown_function(function () {
            global $start_time;
            $run_time = (microtime(true) - $start_time);
            echo "Execution took: {$run_time} seconds." . PHP_EOL;
        });
    }

    public static function logger(): void
    {
        $logger = new NewLogger('Rick');
        ErrorHandler::register($logger);
        $logger->pushHandler(new StreamHandler(__DIR__.'/app.log', Logger::DEBUG));
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        //$logger->info('This is a log! ^_^ ');
        //$logger->warning('This is a log warning! ^_^ ');
        //$logger->error('This is a log error! ^_^ ');
    }

}

class NewLogger extends Logger
{
    public function log($level, $message, array $context = [])
    {
        echo "\033[0;31m";
        parent::log($level, $message, $context);
        echo "\033[0m";
    }

    public function debug($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::debug($message, $context);
        echo "\033[0m";
    }

    public function info($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::info($message, $context);
        echo "\033[0m";
    }

    public function notice($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::notice($message, $context);
        echo "\033[0m";
    }

    public function warning($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::info($message, $context);
        echo "\033[0m";
    }

    public function error($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::error($message, $context);
        echo "\033[0m";
    }

    public function critical($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::critical($message, $context);
        echo "\033[0m";
    }

    public function alert($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::alert($message, $context);
        echo "\033[0m";
    }

    public function emergency($message, array $context = [])
    {
        echo "\033[0;31m";
        parent::emergency($message, $context);
        echo "\033[0m";
    }
}
