<?php

namespace App\Helpers;

function autover($resource)
{
    $time = filemtime(public_path() . $resource);
    $dot = strrpos($resource, '.');
    return asset(substr($resource, 0, $dot) . '.' . $time . substr($resource, $dot));
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

function multiKsort(&$array)
{
    ksort($array);
    foreach (array_keys($array) as $k) {
        if (is_array($array[$k])) {
            multiKsort($array[$k]);
        }
    }
}

function arrayToTree($array, $parent = null)
{
    $array = array_combine(array_column($array, 'id'), array_values($array));
    foreach ($array as $k => &$v) {
        if (isset($array[$v['parent']])) {
            $array[$v['parent']]['children'][$k] = &$v;
        }
        unset($v);
    }
    return array_filter($array, function($v) use ($parent) {
        return $v['parent'] == $parent;
    });
}
