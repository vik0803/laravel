<?php

namespace App\Helpers;

function autover($dir, $file)
{
    $time = filemtime(public_path() . $dir . '/' . $file);
    $dot = strrpos($file, '.');
    return asset($dir . '/' . substr($file, 0, $dot) . '.' . $time . substr($file, $dot));
}

function array_search_key_recursive($key, $array, $parents = false)
{
    if (isset($array[$key])) {
        return ($parents ? [$key] : $array[$key]);
    } else {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $return = array_search_key_recursive($key, $v, $parents);
                if ($return) {
                    if ($parents) {
                        $return[] = $k;
                    }
                    return $return;
                }
            }
        }
    }
    return false;
}

function array_search_value_recursive($value, $array, $parents = false)
{
    if ($key = array_search($value, $array)) {
        return ($parents ? [$key] : $array[$key]);
    } else {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $return = array_search_value_recursive($value, $v, $parents);
                if ($return) {
                    if ($parents) {
                        $return[] = $k;
                    }
                    return $return;
                }
            }
        }
    }
    return false;
}
