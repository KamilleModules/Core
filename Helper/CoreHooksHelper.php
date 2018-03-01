<?php


namespace Module\Core\Helper;


use Kamille\Services\XConfig;
use Logger\Formatter\TagFormatter;
use Logger\Listener\FileLoggerListener;

class CoreHooksHelper
{

    protected static function Core_addLoggerListener(\Logger\LoggerInterface $logger)
    {
        if (true === XConfig::get("Core.useFileLoggerListener")) {
            $f = XConfig::get("Core.logFile");
            $logger->addListener(FileLoggerListener::create()
                ->setFormatter(TagFormatter::create())
                ->setIdentifiers(null)
                ->removeIdentifier("sql.log")
                ->setPath($f));
        }


        if (true === XConfig::get("Core.useDbLoggerListener")) {

            $f = XConfig::get("Core.dbLogFile");
            $logger->addListener(FileLoggerListener::create()
                ->setFormatter(TagFormatter::create())
                ->setIdentifiers(null)
                ->setPath($f));
        }
    }

}