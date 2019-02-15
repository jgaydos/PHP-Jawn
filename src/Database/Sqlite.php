<?php

namespace Jawn\Database;

/**
 * Sqlite: An in-process library that implements a self-contained, serverless,
 * zero-configuration, transactional SQL database engine.
 */
class Sqlite
{
    use \Traits\SqlImportTrait;
    use \Traits\SqlParamsTrait;

    private $_conn;

    /**
     * Constructor creates connection
     *
     * @access  public
     * @param   object|string  $connection
     */
    public function __construct(object $connection)
    {
        if (is_string($connection)) {
            $path = $connection;
        } else {
            $path = $connection->path ?? ':memory:';
        }

        if (file_exists($path) || $path === ':memory:') {
            $db = new \SQLite3($path);
            $db->createFunction('contains', function ($needle, $haystack) {
                if (strpos($haystack, $needle) !== false) {
                    return true;
                }
                return false;
            }, 2);
            $db->createFunction('bin2hex', function ($str) {
                return bin2hex($str);
            }, 1);
            $db->createFunction('explode', function ($del, $str) {
                return json_encode(explode($del, $str));
            }, 2);
            $db->createFunction('hex2bin', function ($str) {
                return hex2bin($str);
            }, 1);
            $db->createFunction('implode', function ($del, $str) {
                return implode($del, json_decode($str));
            }, 2);
            $db->createFunction('join', function ($del, $str) {
                return implode($del, json_decode($str));
            }, 2);
            $db->createFunction('lcfirst', function ($str) {
                return lcfirst($str);
            }, 1);
            $db->createFunction('md5', function ($str) {
                return md5($str);
            }, 1);
            $db->createFunction('sha1', function ($str) {
                return sha1($str);
            }, 1);
            $db->createFunction('str_repeat', function ($str, $rep) {
                return str_repeat($str, $rep);
            }, 2);
            $db->createFunction('str_replace', function ($ser, $rep, $sub) {
                return str_replace($ser, $rep, $sub);
            }, 3);
            $db->createFunction('str_shuffle', function ($str) {
                return str_shuffle($str);
            }, 1);
            $db->createFunction('str_split', function ($str, $len) {
                return str_split($str, $len);
            }, 2);
            $db->createFunction('split', function ($del, $str) {
                return json_encode(explode($del, $str));
            }, 2);
            $db->createFunction('str_word_count ', function ($str) {
                return str_word_count($str);
            }, 1);
            $db->createFunction('strlen ', function ($str) {
                return strlen($str);
            }, 1);
            $db->createFunction('ucfirst', function ($str) {
                return ucfirst($str);
            }, 1);
            $db->createFunction('ucwords', function ($str) {
                return ucwords($str);
            }, 1);
            $db->createFunction('regxp', function ($pattern, $str) {
                return preg_match("/$pattern/", $str);
            }, 2);
            $db->createFunction('preg_replace', function (
                $pattern,
                $replacement,
                $string
            ) {
                return preg_replace("/$pattern/", $replacement, $string);
            }, 3);
            $db->createFunction('excel_date', function ($column) {
                $dateString = 'Thursday, 1 January 1970 + '
                    . ($column - 25569)
                    . ' days';
                return date('Y-m-d', strtotime($dateString));
            }, 1);

            $this->_conn = $db;
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
        $results = $this->_conn->query($sql);
        $ofTheKing = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
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
        $this->_conn->exec($sql);
    }
}
