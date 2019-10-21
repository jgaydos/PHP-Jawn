<?php

namespace Jawn\Traits;

trait SqlImportTrait
{
    /**
     * Imports array into table through updates or inserts
     *
     * @access  public
     * @param   string  $table    Table name
     * @param   array   $data    A single array or an array of arrays
     */
    public function import(string $table, array $data, array $keys = []): void
    {
        $sql = [];

        if (!is_array($data[key($data)] ?? null)) {
            $data = [$data];
        }

        $formatValue = function ($v) {
            /*return (is_string($v) ? "'" . str_replace("'", "''", $v) . "'"
                : (($v instanceof DateTime) ? "'{$v->format('Y-m-d H:i:s')}'"
                : ((is_null($v)) ? "NULL"
                : $v)));*/
            
            if (substr($v, 0, 1) == '0' && substr($v, 1, 1) != '.') {
                $v = "'".str_replace("'","''",$v)."'";
            } elseif ($v instanceof \DateTime) {
                $v = "'".$v->format('Y-m-d H:i:s')."'";
            } elseif (is_null($v)) {
                $v = 'null';
            } elseif (strlen($v) === 0) {
                $v = "''";
            } elseif (is_numeric($v)) {
                $v = $v;
            } elseif (is_string($v)) {
                $v = "'".str_replace("'","''",$v)."'";
            } else {
                $v = "'".str_replace("'","''",$v)."'";
            }
            return $v;
        };

        $formatColumn = function ($c) {
            foreach ([0 => 31, 123 => 255] as $start => $end) {
                for ($i = $start; $i <= $end; ++$i) {
                    $c = str_replace(chr($i), '', $c);
                }
            }
            return $c;
        };

        $format = function ($item) use ($formatColumn, $formatValue) {
            $ofTheKing = [];
            foreach ($item as $key => $value) {
                $ofTheKing[$formatColumn($key)] = $formatValue($value);
            }
            return $ofTheKing;
        };

        foreach ($data as $item) {
            $item = $format($item);
            if (count($keys) > 0) {
                // SELECT
                $where = '';
                foreach ($keys as $key) {
                    $where .= " AND [$key] = $item[$key]";
                }
                $where = substr($where, 5);
                if (count($this->query("SELECT 1 AS a FROM [{$table}] WHERE {$where};", [])) > 0) {
                    // UPDATE
                    $set = '';
                    foreach ($item as $key => $value) {
                        if (!in_array($key, $keys)) {
                            $set .= "[$key] = $value, ";
                        }
                    }
                    $set = substr($set, 0, -2);
                    $sql[] = "UPDATE [{$table}] SET $set WHERE {$where};";
                    continue; // move to next row and skip insert
                }
            }
            // INSERT
            $columns = '[' . implode('], [', array_keys($item)) . ']';
            $values = implode(', ', $item);
            $sql[] = "INSERT INTO [{$table}] ({$columns}) VALUES ({$values});";
        }

        $buffer = '';
        for($i = 0; $i < $c = count($sql); ++$i) {
            $buffer .= $sql[$i];
            if ($i % 20 === 0) {
                $this->execute($buffer, []);
                $buffer = '';
            }
        }
        if (strlen($buffer) !== 0) {
            $this->execute($buffer, []);
        }
    }
}
