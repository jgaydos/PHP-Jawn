<?php

namespace Jawn\Database;

/**
 * Class for using PHP JDBC Java bridge
 */
class Jdbc implements \Jawn\Interfaces\DatabaseInterface
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
        $agentHost = $connection->agent->host ?? 'localhost';
        $agentPort = $connection->agent->port ?? '4444';

        if ($connection->type === 'openedge') {
            $connStr = "JDBC:datadirect:openedge://$host:$port;DatabaseName=$database;";
        } elseif ($connection->type === 'sqlserver') {
            $connStr = "jdbc:sqlserver://$host:$port;databasename=$database;";
        } elseif ($connection->type === 'oracle') {
            $connStr = "jdbc:oracle:thin:$username/$password@$host:$port:$database";
        } else {
            throw new \DatabaseQueryException('JDBC driver not configured.');

            Console::danger();
        }

        $this->_conn = new \PJBridge($agentHost, $agentPort);
        $result = $this->_conn->connect($connStr, $username, $password);

        if (!$result) {
            throw new \DatabaseQueryException('Failed to connect.');
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
        $cursor = $this->_conn->exec($sql);

        if (!$cursor) {
            throw new \DatabaseQueryException('Query failed.');
        }

        $ofTheKing = [];
        while ($row = $this->_conn->fetch_array($cursor)) {
            $ofTheKing[] = $row;
        }
        return $ofTheKing;

        $this->_conn->free_result($cursor);
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
        $this->query($sql, $params);
    }
}
