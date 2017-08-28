<?php


namespace Module\Core\Pdo;

use Core\Services\A;
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
                XLog::log(new QuickPdoQueryFormatter($query, $markers), 'sql.log');

                if (true === XConfig::get("Core.useTabathaDb") && array_key_exists($method, $methods)) {
                    // table: might inherit the db prefix from the QuickPdo call, let the user figure that out? or todo: change this
                    $deleteId = $table . "." . $methods[$method];

                    /**
                     * For delete and update statements, most of the time we use whereConds containing the primary keys.
                     * It turns out it might be very interesting for the application (from an efficiency point of view) to be able
                     * to clean the cache only for specific table records.
                     *
                     * By that I mean imagine the two following cases:
                     *
                     * - a cache is cleaned every time a record is deleted from the table my_table
                     * - a cache is cleaned every time the record with id#8 is deleted from the table my_table
                     *
                     * Now imagine that our cache needs only to be cleaned when id#8 is removed from that table.
                     * Then the second use case becomes by far much more efficient, because we don't loose the cache
                     * every time another record is removed from the database.
                     *
                     * It turns out this case where we need to detect specific records deleting/updating occurs
                     * more often than not.
                     *
                     * Therefore, whenever we can, we use those precise identifiers.
                     * They look like this:
                     *
                     * - my_table.delete.8
                     *
                     * Or, if the table has a primary key composed of multiple columns, we separate the column values
                     * with a dot (this allows us for even more fine-tuning), like this:
                     *
                     * - my_table.delete.8.6
                     *
                     *
                     * Note that this notation works well with the dot namespacing system provided by default by
                     * tabatha cache.
                     * This means if your deleteId is:
                     *
                     * - my_table.delete.*, the cache cleaning will be trigger every time delete is detected on that table.
                     *
                     * However, if your deleteId is:
                     *
                     * - my_table.delete.6, then the cache cleaning will only be trigger when the entry with id=6 is deleted (id is just an example)
                     *
                     *
                     */
                    if (is_array($whereConds)) {
                        $isPk = true;
                        $pks = [];
                        foreach ($whereConds as $whereCond) {
                            if (is_array($whereCond) && '=' === $whereCond[1]) {
                                $pks[] = $whereCond[2];
                            } else {
                                $isPk = false;
                                break;
                            }
                        }

                        if (true === $isPk) {
                            $deleteId .= '.' . implode('.', $pks);
                        }
                    }


                    if (true === ApplicationParameters::get("debug")) {
                        XLog::log("Tabatha-deleteId: $deleteId", "testId");
                    }
                    A::cache()->clean($deleteId);
                }
            });
            $this->initialized = true;
        }
    }

}