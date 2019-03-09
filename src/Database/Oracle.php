<?php

namespace Jawn\Database;

class Oracle implements \Jawn\Interfaces\DatabaseInterface
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
        $port = $connection->port ?? 1521;
        $database = $connection->database ?? $connection->service ?? 'XE';
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        //Establish the connection
        $dsn = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$database)))";
        $this->_conn = \oci_connect($username, $password, $dsn);

        if (!$this->_conn) {
            throw new \DatabaseConnectionException(oci_error());
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
        $stid = oci_parse($this->_conn, $sql);

        if (!oci_execute($stid)) {
            throw new \DatabaseQueryException(oci_error());
        }

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

        if (!oci_execute($stid)) {
            throw new \DatabaseQueryException(oci_error());
        }

    }
}
