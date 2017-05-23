<?php


namespace Module\Core\Planets\TabathaCache;


use Kamille\Services\XLog;
use TabathaCache\Cache\TabathaCache;

class DebugTabathaCache extends TabathaCache
{

    protected function onCacheCreate($cacheId, array $deleteIds) // override me
    {
        XLog::debug("[Core module] - DebugTabathaCache: cache create: $cacheId, deleteIds: " . implode(', ', $deleteIds));
    }

    protected function onCacheHit($cacheId, array $deleteIds) // override me
    {
        XLog::debug("[Core module] - DebugTabathaCache: cache hit: $cacheId, deleteIds: " . implode(', ', $deleteIds));
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