<?php


namespace Module\Core\Pdo;

use Core\Services\Hooks;
use Kamille\Services\XConfig;
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

            $c = XConfig::get("Core.quickPdoConfig");
            QuickPdo::setConnection($c['dsn'], $c['user'], $c['pass'], $c['options']);
            QuickPdo::setOnQueryReadyCallback(function ($method, $query, $markers = null, $table = null, array $whereConds = null) {

                if (null === $markers) {
                    $markers = [];
                }

                $params = [
                    'method' => $method,
                    'query' => $query,
                    'markers' => $markers,
                    'table' => $table,
                    'whereConds' => $whereConds,
                ];
                Hooks::call("Core_onQuickPdoQueryReady", $params);


            });


            QuickPdo::setOnDataAlterAfterCallback(function ($method, $query, $markers = null, $table = null, array $whereConds = null) {
                if (null === $markers) {
                    $markers = [];
                }

                $params = [
                    'method' => $method,
                    'query' => $query,
                    'markers' => $markers,
                    'table' => $table,
                    'whereConds' => $whereConds,
                ];
                Hooks::call("Core_onQuickPdoDataAlterAfter", $params);


            });



            $this->initialized = true;
        }
    }

}