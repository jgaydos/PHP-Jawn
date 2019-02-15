<?php

namespace Jawn\Remote;

/** NOTE THIS HAS NOT BEEN TESTED AND MAY NOT WORK */
class Ftp
{
    /**
     * Get file from remote location
     */
    public function get(string $source, string $target, array $options): void
    {
        $connection = $options['remote'];
        $host = $connection->host ?? 'localhost';
        $port = $connection->port ?? 22;
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        $conn = "ftp://$username:$password@$host";
        file_get_contents($conn.$path);
    }

    /**
     * Put file on remote location
     */
    public function put(string $source, string $target, array $options): void
    {
        $connection = $options['remote'];
        $host = $connection->host ?? 'localhost';
        $port = $connection->port ?? 22;
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        $conn = "ftp://$username:$password@$host";
        file_put_contents($conn.$path);
    }
}
