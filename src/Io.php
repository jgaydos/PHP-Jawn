<?php

namespace Jawn;

class Io
{
    public static function extract(
        string $type,
        string $source,
        array $options = []
    ): array {
        $class = 'Jawn\Io\\'.ucfirst($type);
        if (!class_exists($class)) {
            throw new EtlTypeException("$type does not exist.");
        }
        return $class::extract($source, $options);
    }

    public static function load(
        string $type,
        string $target,
        array $data,
        array $options = []
    ): void {
        $class = 'Jawn\Io\\'.ucfirst($type);
        if (!class_exists($class)) {
            throw new EtlTypeException("$type does not exist.");
        }
        $class::load($target, $data, $options);
    }
}
