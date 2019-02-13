<?php

namespace Traits;

trait SqlParamsTrait
{
    /**
     * Replaces ? in SQL query with respective value from params array
     * @access  private
     * @param   string  $query    SQL query
     * @param   array  $params    SQL query params
     */
    private function params(string $query, array $params): string
	{
		foreach ($params as $param) {
			if ($pos = strpos($query, '?') !== false) {
                $query = substr_replace($query, $param, $pos, strlen('?'));
            }
        }
		return $query;
    }

    /**
     * The postgres driver is a turd and does its params
     * different from everyone else.  I am not changing
     * so the params must.
     *
     * @access  private
     * @param   string  $query    SQL query
     * @return  array   $ofTheKing    Altered Query
     */
    private function pgsqlParams(string $query): string
	{
		$arr = explode('?', $query);
        $ofTheKing = '';
        $c = count($arr);
        for ($i = 0; $i < $c; ++$i) {
            $ofTheKing .= $arr[$i];
            if ($i + 1 != $c) {
                $ofTheKing .= '$'.($i+1);
            }
        }
        return $ofTheKing;
    }
}
