<?php

namespace SoftInvest\Helpers;

use Illuminate\Support\Facades\DB;
use stdClass;

class DBWrap
{
    /**
     * @return \Illuminate\Support\Facades\DB
     */
    public static function getDB(): DB
    {
        return new DB();
    }

    /**
     * @param string $name
     * @param array<mixed> $params
     *
     * @return array<iterable>|string|false|int
     */
    public static function callSelect(string $name, array $params): array|string|false|int
    {
        return static::callProc($name, $params, '* FROM ');
    }

    /**
     * @param string $name
     * @param array<iterable> $params
     * @param string $from
     *
     * @return array<iterable|string|false|int>
     */
    public static function callProc(string $name, array $params, string $from = ''): array|string|false|int
    {
        $q = [];
        foreach ($params as $w) {
            $q[] = '?';
        }

        if ($params) {
            $query = 'SELECT ' . $from . $name . '(' . join(',', $q) . ')';
        } else {
            $query = 'SELECT ' . $from . $name . '()';
        }

        $result = DB::select($query, $params);
        if ($result && $from) {
            return (array)$result;
        }

        $a = explode('.', $name);
        if (count($a) > 1) {
            $name = $a[1];
        }
        if ($result && isset($result[0]->$name)) {
            $result = $result[0]->$name;

            return $result;
        }

        return false;
    }

    /**
     * @param string $name
     * @param array<mixed> $params
     *
     * @return array<iterable>|string|false|int
     */
    public static function callSelectInsecure(string $name, array $params): array|string|false|int
    {
        return static::callProcInsecure($name, $params, '* FROM ');
    }

    /**
     * @param string $name
     * @param array<iterable> $params
     * @param string $from
     *
     * @return array<iterable>|string|false|int
     */
    public static function callProcInsecure(string $name, array $params, string $from = ''): array|string|false|int
    {
        if ($params) {
            $query = 'SELECT ' . $from . $name . '(' . join(',', $params) . ')';
        } else {
            $query = 'SELECT ' . $from . $name . '()';
        }
        $result = DB::select($query);

        if ($result && $from) {
            return (array)$result;
        }
        if ($result && isset($result[0]->$name)) {
            $result = $result[0]->$name;

            return $result;
        }

        return false;
    }

    /**
     * @param string $name
     * @param array<string|int|float|bool|null> $params
     *
     * @return \stdClass|string|false
     */
    public static function callSelectOne(string $name, array $params): stdClass|string|false
    {
        $ret = static::callProc($name, $params, '* FROM ');
        /**
         * @var array<int, array> $ret
         */
        if (!$ret) {
            return false;
        }
        /**
         * @var stdClass|string|false $result
         */
        $result = array_shift($ret);

        return $result;
    }

    /**
     * @param string $name
     * @param array<string|int|float|bool|null> $params
     *
     * @return \stdClass|string|false
     */
    public static function callSelectOneInsecure(string $name, array $params): stdClass|string|false
    {
        $ret = static::callProcInsecure($name, $params, '* FROM ');
        /**
         * @var array<int, array> $ret
         */
        if (!$ret) {
            return false;
        }
        /**
         * @var stdClass|string|false $result
         */
        $result = array_shift($ret);

        return $result;
    }
}
