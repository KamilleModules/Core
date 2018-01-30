<?php


namespace Module\Core\Pdo;

use Core\Services\A;
use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
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
            QuickPdo::setOnQueryReadyCallback(function ($method, $query, $markers = null, $table = null, array $whereConds = null) use ($methods) {
                if (null === $markers) {
                    $markers = [];
                }

                $params = [
                    'method' => $method,
                    'query' => $query,
                    'markers' => $markers,
                    'table' => $table,
                    'whereConds' => $whereConds,
                    'methods' => $methods,
                ];
                Hooks::call("Core_onQuickPdoInteractionAfter", $params);


            });
            $this->initialized = true;
        }
    }

}