<?php


namespace Module\Core\Helper;


use Bat\FileSystemTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Services\XConfig;

class CoreHelper
{

    public static function isBackoffice(HttpRequestInterface $request)
    {
        $backUri = XConfig::get("Core.uriPrefixBackoffice");
        $uri = $request->uri(false);
        return (
            $backUri === $uri ||
            0 === strpos($uri, $backUri . "/")
        );
    }


    //--------------------------------------------
    // MAINTENANCE SYSTEM
    //--------------------------------------------
    /**
     * As far as maintenance is concerned, this simple rule is used:
     *
     * - App is in maintenance if the maintenance file exists
     * - App is NOT in maintenance if the maintenance file DOES NOT exist
     */
    public static function setMaintenanceMode($isOn = true)
    {
        $file = self::getMaintenanceFile();
        if (true === $isOn) {
            FileSystemTool::mkfile($file, "1");
        } else {
            FileSystemTool::remove($file);
        }
    }

    public static function isCurrentlyInMaintenance()
    {
        return file_exists(self::getMaintenanceFile());
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getMaintenanceFile()
    {
        $appDir = ApplicationParameters::get("app_dir", null, true);
        return $appDir . "/app_is_currently_in_maintenance.txt";
    }

}