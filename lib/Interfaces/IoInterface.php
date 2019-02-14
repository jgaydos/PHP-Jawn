<?php

namespace Interfaces;

/**
 * Assure that io classes are posess the same methods.
 */
interface IoInterface
{
    public static function extract(string $path, array $options = []): array;
    public static function load(string $path, array $data, array $options = []): void;
}
