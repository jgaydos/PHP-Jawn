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
    public static function extract(
        string $type,
        string $source,
        array $options = []
    ): array {
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
    public static function load(
        string $type,
        string $target,
        array $data,
        array $options = []
    ): void {
        $class = 'Jawn\Io\\'.ucfirst($type);
        if (!class_exists($class)) {
            throw new \IoNotImplementedException("$type does not exist.");
        }
        $class::load($target, $data, $options);
    }
}
