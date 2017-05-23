<?php


namespace Module\Core\Pdo;

use Core\Services\A;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use QuickPdo\QuickPdo;

/**
 * This class just loads a QuickPdo instance.
 */
class QuickPdoInitializer
{

    private $initialized;


    public function __construct()
    {
        $this->initialized = false;
    }

    public function init()
    {
        if (false === $this->initialized) {
            $methods = [
                'update' => "update",
                'replace' => 'create',
                'insert' => 'create',
                'delete' => 'delete',
            ];
            $c = XConfig::get("Core.quickPdoConfig");
            QuickPdo::setConnection($c['dsn'], $c['user'], $c['pass'], $c['options']);
            QuickPdo::setOnQueryReadyCallback(function ($method, $query, $markers = null, $table = null) use ($methods) {
                if (null === $markers) {
                    $markers = [];
                }
                XLog::log(new QuickPdoQueryFormatter($query, $markers), 'sql.log');

                if (true === XConfig::get("Core.useTabathaDb") && array_key_exists($method, $methods)) {
                    // table: might inherit the db prefix from the QuickPdo call, let the user figure that out? or todo: change this
                    A::cache()->clean($table . "." . $methods[$method]);
                }

            });
            $this->initialized = true;
        }
    }

}