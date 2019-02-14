<?php

namespace Database;

class Pgsql
{
    use \Traits\SqlParamsTrait;

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
        $sql = $this->pgsqlParams($sql);
        $result = @pg_query_params($this->_conn, $sql, $params);
        if(!$result) {
            \Console::danger('...'.pg_last_error($this->_conn));
        }
        $ofTheKing = [];
        while($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
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
        $sql = $this->pgsqlParams($sql);
        pg_query_params($this->_conn, $sql, $params);
    }
}
