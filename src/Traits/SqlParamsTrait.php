<?php

namespace Jawn\Traits;

trait SqlParamsTrait
{
    /**
     * Replaces ? in SQL query with respective value from params array
     * as well as named replcements (:one). All replacements are litteral.
     * @access  private
     * @param   string  $query    SQL query
     * @param   array  $params    SQL query params
     */
    private function params(string $query, array $params): string
	{
		if (array_keys($params) !== range(0, count($params) - 1)) {
            // associative array for named replacement
            foreach ($params as $name => $param) {
                $name = ':'.ltrim($name, ':');
                $query = str_replace($name, $param, $query);
            }
        } else {
            // sequencial array for ? replacement
            foreach ($params as $param) {
                if (($pos = strpos($query, '?')) !== false) {
                    $query = substr_replace($query, $param, $pos, strlen('?'));
                }
            }
        }
		return $query;
    }
}
