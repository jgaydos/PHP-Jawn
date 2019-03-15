<?php

namespace Jawn\Database;

class Pgsql implements \Jawn\Interfaces\DatabaseInterface
{
    use \Jawn\Traits\SqlImportTrait;
    use \Jawn\Traits\SqlParamsTrait;

    private $_conn;

    /**
     * Constructor creates connection
     *
     * @access  public
     * @param   object  $connection
     */
    public function __construct(object $connection)
    {
        $host = $connection->host ?? 'localhost';
        $port = $connection->port ?? 5432;
        $database = $connection->database ?? 'master';
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        //Establish the connection
        $dsn = "host=$host port=$port dbname=$database user=$username password=$password";
        $this->_conn = pg_connect($dsn);
        if (!$this->_conn) {
            throw new \DatabaseConnectionException('Connection failed');
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
        $sql = $this->params($sql, $params);
        $result = pg_query($this->_conn, $sql);

        if (!$result) {
            throw new \DatabaseQueryException(pg_last_error($this->_conn));
        }

        $ofTheKing = [];
        while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
            $ofTheKing[] = $row;
        }
        return $ofTheKing;
    }

    /**
     * This runs a query...
     *
     * @access  public
     * @param   string  $sql    SQL query
     * @param   array   $params Query parameters
     * @return  void
     */
    public function execute(string $sql, array $params = []): void
    {
        $sql = $this->params($sql, $params);

        if (!pg_query($this->_conn, $sql)) {
            throw new \DatabaseQueryException(pg_last_error($this->_conn));
        }
    }
}
