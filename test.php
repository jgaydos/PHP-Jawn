<?php

require 'vendor/autoload.php';

function test($args, $params) {
    $ofTheKing = true;
    if (count($args) !== count($params)) return false;
    foreach ($args as $key => $arg) {
        $func = "is_{$params[$key]}";
        if (is_callable($func)) {
            if (!$func($arg)) {
                $ofTheKing = false;
            }
        } else {
            if (!is_a($arg, $params[$key])) {
                $ofTheKing = false;
            }
        }
    }
    return $ofTheKing;
}

function test2() {
    $args = func_get_args();
    if (test($args, ['string','string'])) echo 1;
    elseif (test($args, ['string','int'])) echo 2;
    elseif (test($args, ['string', 'array'])) echo 3;
    elseif (test($args, ['string', 'test3'])) echo 4;
    else echo 0;
}

class test3 {}

test2('asd', 'fgh');
test2('asd', 'fgh', 'fgh');
test2('asd', 1);
test2('asd', []);
test2('asd', new test3);
