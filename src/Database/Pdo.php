<?php

namespace Jawn\Database;

class Pdo implements \Jawn\Interfaces\DatabaseInterface
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
        $port = $connection->port ?? 1433;
        $database = $connection->database ?? 'master';
        $username = $connection->username ?? '';
        $password = $connection->password ?? '';

        if ($connection->type === 'openedge') {
            $connStr = "JDBC:datadirect:openedge://$host:$port;DatabaseName=$database;";
        } elseif ($connection->type === 'sqlserver') {
            $connStr = "jdbc:sqlserver://$host:$port;databasename=$database;";
        } elseif ($connection->type === 'cubrid') {
            $connStr = "cubrid:dbname=$database;host=$host;port=$port";
        } elseif ($connection->type === 'oracle') {
            $connStr = "jdbc:oracle:thin:$username/$password@$host:$port:$database";
        } elseif ($connection->type === 'mysql') {
            $connStr = "mysql:dbname=$database;$host=$host:$port";
        } elseif ($connection->type === 'pgsql') {
            $connStr = "pgsql:dbname=$database;host=$host:$port";
        } else {
            throw new \DatabaseConnectionException('PDO driver not configured!');
        }

        try {
            $this->_conn = $dbh = new PDO($connStr, $username, $password);
        } catch (PDOException $e) {
            throw new \DatabaseConnectionException($e->getMessage());
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

        try {
            $ofTheKing = [];
            foreach($this->_conn->query($sql) as $row) {
                $ofTheKing[] = $row;
            }
        } catch (PDOException $e) {
            throw new \DatabaseQueryException($e->getMessage());
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
        $this->_conn->exec($sql);
    }
}
