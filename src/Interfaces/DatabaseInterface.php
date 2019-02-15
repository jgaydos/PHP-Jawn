<?php

namespace Jawn\Interfaces;

/**
 * Assure that database classes are posess the same methods.
 */
interface DatabaseInterface
{
    public function query(string $sql, array $params = []): array;
    public function execute(string $sql, array $params = []): void;
}
