<?php

use Mix\Framework\Container\Config;

if (!function_exists('env')) {
    /**
     * @param string $key
     * @param null $default
     * @return array|bool|string|null
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (!function_exists('dd')) {
    function dd(...$var)
    {
        var_dump($var);
        exit();
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        return Config::instance()->get($key, $default);
    }
}

if (!function_exists('logger')) {
    function logger(): \Monolog\Logger
    {
        return \Mix\Framework\Container\Logger::instance();
    }
}
