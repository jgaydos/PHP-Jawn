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
            private $connection = '';
            private $table;

            private $select = '*';
            private $where = [];

            private $inserts = [];

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
                DB::query($this->connection, 'DELETE FROM [' . $this->table . ']');
            }

            public function get()
            {
                $query = 'SELECT ' . $this->select . ' FROM [' . $this->table . ']';

                if (count($this->where) > 0) {
                    $query .= ' WHERE';
                    foreach ($this->where as $condition) {
                        $query .= ' ' . $condition;
                    }
                }

                return DB::query($this->connection, $query);
            }

            public function select($columns)
            {
                $this->select = '';
                foreach ($columns as $column) {
                    $this->select .= $column . ', ';
                }
                $this->select = substr($this->select, 0, -2);
                return $this;
            }

            public function selectRaw($columns)
            {
                $this->select = $columns;
            }

            public function where($one, $two, $three = null)
            {
                if (is_null($three)) {
                    $this->where[] = $one . ' = ' . $two;
                } else {
                    $this->where[] = $one . ' ' . $two . ' ' . $three;
                }
                return $this;
            }

            public function whereRaw($condition)
            {
                $this->where[] = $condition;
                return $this;
            }

            public function table($connection, $table)
            {
                $this->connection = $connection;
                $this->table = $table;
                return $this;
            }

            public function import($data = [], $keys = [])
            {
                if (empty($data)) {
                    throw new Exception('Import: $data is empty');
                }
                if (empty($this->table)) {
                    throw new Exception('Import: Table must be set before import');
                }

                foreach ($data as $item) {
                    $columns = '[' . implode('], [', array_keys($item)) . ']';
                    $values = implode(', ', array_map(function ($v) {
                        return (is_string($v) ? "'" . str_replace("'", "''", $v) . "'" : (($v instanceof DateTime)
                            ? "'{$v->format('Y-m-d H:i:s')}'" : ((is_null($v)) ? "NULL" : $v)));
                    }, $item));
                    $this->inserts[] = "INSERT INTO [$this->table] ($columns) VALUES ($values);";
                }
                return $this;
            }

            public function run($chunk = 1)
            {
                for ($i = 0; $i < $count = count($this->inserts); $i += $chunk) {
                    DB::query($this->connection, implode(' ', array_slice($this->inserts, $i, $chunk)));
                }
                $this->inserts = []; // clear out after processing
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
