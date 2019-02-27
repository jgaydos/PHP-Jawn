<?php

namespace Jawn;

/**
 * Database class
 */
class DB
{
    public static function __callStatic(string $name, array $argv)
    {
        $obj = new class
        {
            private $_connection = '';
            private $_table;

            private $_select = '*';
            private $_where = [];

            private $_inserts = [];

            public function connection($connection = ''): object
            {
                return Basket::database($connection);
            }

            public function query($name = '', $query, $params = [])
            {
                $ofTheKing = [];
                Console::info("Q -> Querying $name (" . substr(str_replace("  ", '', str_replace("\n", ' ', $query)), 0, 30) . '...)', '');
                if (preg_match('%^(select)%is', $query) > 0) {
                    $ofTheKing = Basket::database($name)->query($query, $params);
                } else {
                    $ofTheKing = Basket::database($name)->execute($query, $params);
                }
                Console::success('...Wubbalubbadubdub!');
                return $ofTheKing;
            }

            public function delete()
            {
                DB::query($this->_connection, 'DELETE FROM [' . $this->_table . ']');
            }

            public function get()
            {
                $query = 'SELECT ' . $this->_select . ' FROM [' . $this->_table . ']';

                if (count($this->_where) > 0) {
                    $query .= ' WHERE';
                    foreach ($this->_where as $condition) {
                        $query .= ' ' . $condition;
                    }
                }

                return DB::query($this->_connection, $query);
            }

            public function select($columns)
            {
                $this->_select = '';
                foreach ($columns as $column) {
                    $this->_select .= $column . ', ';
                }
                $this->_select = substr($this->_select, 0, -2);
                return $this;
            }

            public function selectRaw($columns)
            {
                $this->_select = $columns;
            }

            public function where($one, $two, $three = null)
            {
                if (is_null($three)) {
                    $this->_where[] = $one . ' = ' . $two;
                } else {
                    $this->_where[] = $one . ' ' . $two . ' ' . $three;
                }
                return $this;
            }

            public function whereRaw($condition)
            {
                $this->_where[] = $condition;
                return $this;
            }

            public function table($connection, $table)
            {
                $this->_connection = $connection;
                $this->_table = $table;
                return $this;
            }

            public function import($data = [], $keys = [])
            {
                if (empty($data)) {
                    throw new Exception('Import: $data is empty');
                }
                if (empty($this->_table)) {
                    throw new Exception('Import: Table must be set before import');
                }

                foreach ($data as $item) {
                    $columns = '[' . implode('], [', array_keys($item)) . ']';
                    $values = implode(', ', array_map(function ($v) {
                        return (is_string($v) ? "'" . str_replace("'", "''", $v) . "'" : (($v instanceof DateTime)
                            ? "'{$v->format('Y-m-d H:i:s')}'" : ((is_null($v)) ? "NULL" : $v)));
                    }, $item));
                    $this->_inserts[] = "INSERT INTO [$this->_table] ($columns) VALUES ($values);";
                }
                return $this;
            }

            public function run($chunk = 1)
            {
                for ($i = 0; $i < $count = count($this->_inserts); $i += $chunk) {
                    DB::query($this->_connection, implode(' ', array_slice($this->_inserts, $i, $chunk)));
                }
                $this->_inserts = []; // clear out after processing
            }
        };
        if (count($argv) === 0) {
            return $obj->{$name}();
        } elseif (count($argv) === 1) {
            return $obj->{$name}($argv[0]);
        } elseif (count($argv) === 2) {
            return $obj->{$name}($argv[0], $argv[1]);
        } elseif (count($argv) === 3) {
            return $obj->{$name}($argv[0], $argv[1], $argv[2]);
        } else {
            throw new Exception('Param number not handled');
        }
    }

}
