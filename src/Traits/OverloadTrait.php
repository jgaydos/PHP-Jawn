<?php

namespace Jawn\Traits;

trait OverloadTrait
{
    public function overload(array $argvs, array $pattern): bool
    {
        $ofTheKing = false;

        foreach ($argvs as $key => $argv) {
            $types = explode('|', $pattern[$key]);
            if (count($types) === 0) {
                $types = [$pattern[$key]];
            }
            foreach ($types as $type) {
                $func = "is_$type";
                if (function_exists($func)) {
                    if ($func($argv)) {
                        $ofTheKing = true;
                    }
                } else {
                    if (is_a($argv, $type)) {
                        $ofTheKing = true;
                    }
                }
            }
        }

        return $ofTheKing;
    }
}
