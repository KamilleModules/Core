<?php


namespace Module\Core\Planets\TabathaCache;


use Kamille\Services\XLog;
use TabathaCache\Cache\TabathaCache;

class DebugTabathaCache extends TabathaCache
{

    private $logIdentifier;

    public function __construct()
    {
        parent::__construct();
        $this->logIdentifier = 'tabatha';
    }


    protected function onCacheCreate($cacheId, array $deleteIds) // override me
    {
        XLog::log("[Core module] - DebugTabathaCache: cache create: $cacheId, deleteIds: " . implode(', ', $deleteIds), $this->logIdentifier);
    }

    protected function onCacheHit($cacheId, array $deleteIds) // override me
    {
        XLog::log("[Core module] - DebugTabathaCache: cache hit: $cacheId, deleteIds: " . implode(', ', $deleteIds), $this->logIdentifier);
    }


    public function clean($deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }
        XLog::log("[Core module] - DebugTabathaCache: clean cache " . implode(", ", $deleteIds), $this->logIdentifier);
        parent::clean($deleteIds); //
    }

}