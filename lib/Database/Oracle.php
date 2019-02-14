<?php

namespace Database;

class Oracle
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
        $port = $connection->port ?? 1521;
        $service = $connection->service ?? 'XE';
        $database = $connection->database ?? 'master';
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        //Establish the connection
        $dsn = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$service)))";
        $this->_conn = oci_connect($username, $password, $dsn);
    }

    /**
     * This runs a query...
     *
     * @access  public
     * @param   string  $sql    SQL query
     * @param   array   $params Query parameters
     * @param   bool    $errors Show/Stop on errors or not
     * @return  array   $ofTheKing    Query results
     */
    public function query(string $sql, array $params = []): array
    {
        $sql = $this->params($sql, $params);
        $stid = oci_parse($this->_conn, $sql);
        oci_execute($stid);
        $ofTheKing = [];
        while ($row = oci_fetch_assoc($stid)) {
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
    public function execute(string $sql, array $params = []): void
    {
        $sql = $this->params($sql, $params);
        $stid = oci_parse($this->_conn, $sql);
        oci_execute($stid);
    }
}
