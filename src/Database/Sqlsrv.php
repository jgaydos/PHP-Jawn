<?php

namespace Jawn\Database;

use Console;

class Sqlsrv implements \Jawn\Interfaces\DatabaseInterface
{
    use \Jawn\Traits\SqlImportTrait;

    private $_conn;

    /**
     * Constructor creates connection
     *
     * @access  public
     * @param   object  $connection
     */
    public function __construct($connection)
    {
        $host = $connection->host ?? 'localhost';
        $port = $connection->port ?? 5432;
        $database = $connection->database ?? 'master';
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        //Establish the connection
        $this->_conn = sqlsrv_connect(
            $host.', '.$port,
            [
                "Database" => $database,
                "Uid" => $username,
                "PWD" => $password
            ]
        );

        if (!$this->_conn) {
            throw new \DatabaseConnectionException(json_encode(sqlsrv_errors()));
        }
    }

    /**
     * This runs a query...
     *
     * @access  public
     * @param   string  $sql    SQL query
     * @param   array   $params Query parameters
     * @return  array   $ofTheKing    Query results
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = sqlsrv_query($this->_conn, $sql, $params);

        if ($stmt === false) {
            throw new \DatabaseQueryException(json_encode(sqlsrv_errors()));
        }

        $ofTheKing = [];
        while (
            $stmt !== false &&
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)
        ) {
            $ofTheKing[] = $row;
        }
        return $ofTheKing;
    }

    /**
     * This executes a query...
     *
     * @access  public
     * @param   string  $sql    SQL query
     * @param   array   $params Query parameters
     * @return  void
     */
    public function execute($sql, $params = []): void
    {
        $this->query($sql, $params);
    }
}
