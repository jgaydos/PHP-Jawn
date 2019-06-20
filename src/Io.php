<?php

namespace Jawn;

class Io
{
    /**
     * @param   string  $type
     * @param   string  $source
     * @param   array   $options
     * @return  array
     */
    public static function extract(): array
    {
        $args = func_get_args();

        if (is_string($args[1] ?? [])) {
            $type = $args[0];
            $source = $args[1];
            $options = $args[2] ?? [];
        } else {
            $source = $args[0];
            $type = strtolower(pathinfo($source, PATHINFO_EXTENSION));
            $options = $args[1] ?? [];
        }

        $class = 'Jawn\Io\\'.ucfirst($type);
        if (!class_exists($class)) {
            throw new \IoNotImplementedException("$type does not exist.");
        }
        return $class::extract($source, $options);
    }

    /**
     * @param   string  $type
     * @param   string  $target
     * @param   array   $data
     * @param   array   $options
     * @return  void
     */
    public static function load(): void
    {
        $args = func_get_args();

        if (is_string($args[1])) {
            $type = $args[0];
            $target = $args[1];
            $data = $args[2];
            $options = $args[3] ?? [];
        } else {
            $target = $args[0];
            touch($target);
            $type = strtolower(pathinfo($target, PATHINFO_EXTENSION));
            $data = $args[1];
            $options = $args[2] ?? [];
        }

        $class = 'Jawn\Io\\'.ucfirst($type);
        if (!class_exists($class)) {
            throw new \IoNotImplementedException("$type does not exist.");
        }
        $class::load($target, $data, $options);
    }
}
