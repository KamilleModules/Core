<?php


namespace Module\Core\Planets\TabathaCache;


use Kamille\Services\XLog;
use TabathaCache\Cache\TabathaCache;

class DebugTabathaCache extends TabathaCache
{

    public function get($cacheId, callable $generateCallback, $deleteIds)
    {
        XLog::debug("[Core module] - DebugTabathaCache: get cache $cacheId");
        return parent::get($cacheId, $generateCallback, $deleteIds);
    }

    public function clean($deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }
        XLog::debug("[Core module] - DebugTabathaCache: clean cache " . implode(", ", $deleteIds));
        parent::clean($deleteIds); //
    }

}