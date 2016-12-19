<?php

namespace PhalconApi;

class Core
{
    public static function arrayFlatten($array)
    {
        $return = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, self::arrayFlatten($value));
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}
