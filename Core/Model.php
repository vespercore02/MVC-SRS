<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB1()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST1 . ';dbname=' . Config::DB_NAME1 . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER1, Config::DB_PASSWORD1);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    protected static function getDB2()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST2 . ';dbname=' . Config::DB_NAME2 . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER2, Config::DB_PASSWORD2);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    protected static function getDB3()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST3 . ';dbname=' . Config::DB_NAME3 . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER3, Config::DB_PASSWORD2);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    protected static function getDB4()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST4 . ';dbname=' . Config::DB_NAME4 . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER4, Config::DB_PASSWORD4);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    protected static function getDB5()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST5 . ';dbname=' . Config::DB_NAME5 . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER5, Config::DB_PASSWORD4);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}
