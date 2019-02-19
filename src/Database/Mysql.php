<?php

namespace Jawn\Database;

class Mysql implements \Jawn\Interfaces\DatabaseInterface
{
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
        $port = $connection->port ?? 1433;
        $database = $connection->database ?? 'master';
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        //Establish the connection
        $this->_conn = @new \mysqli($host, $username, $password);

        if ($this->_conn->connect_error) {
            throw new \DatabaseConnectionException($this->_conn->connect_error);
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
        $result = $this->_conn->query($sql);

        if (!$result) {
            throw new \DatabaseQueryException($this->_conn->connect_error);
        }

        $ofTheKing = [];
        while ($row = $result->fetch_assoc()) {
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

        if (!$this->_conn->real_query($sql)) {
            throw new \DatabaseQueryException($this->_conn->connect_error);
        }
    }
}
