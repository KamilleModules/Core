<?php


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Environment\Web\Environment;

$appDir = ApplicationParameters::get('app_dir');


$env = Environment::getEnvironment();


$dbName = "kamille";


if ('dev' === $env) {

    $quickPdoConf = [
        "dsn" => "mysql:dbname=$dbName;host=127.0.0.1",
        "user" => "root",
        "pass" => "root",
        "options" => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        ],
    ];
} else {
    $quickPdoConf = [
        "dsn" => "mysql:dbname=$dbName;host=127.0.0.1",
        "user" => "root",
        "pass" => "root",
        "options" => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        ],
    ];
}


$conf = [
    /**
     * check documentation for more info
     */
    "exceptionController" => 'Controller\Core\ExceptionController:render',
    "useFileLoggerListener" => true,
    "logFile" => $appDir . "/logs/kamille.log.txt",
    "showExceptionTrace" => false,
    //--------------------------------------------
    // DATABASE
    //--------------------------------------------
    "database" => $dbName,
    "useDbLoggerListener" => true,
    "dbLogFile" => $appDir . "/logs/kamille.sql.log.txt",
    "useQuickPdo" => true,
    "quickPdoConfig" => $quickPdoConf,
    //--------------------------------------------
    // TABATHA
    //--------------------------------------------
    /**
     * Hook into QuickPdo instance of the app and clean the tabatha cache using tabathaDb strategy
     * (https://github.com/lingtalfi/TabathaCache#tabatha-db).
     *
     */
    "useTabathaDb" => true,
    "enableTabathaCache" => false,
    //--------------------------------------------
    // JS
    //--------------------------------------------
    "addJqueryEndWrapper" => true,
    //--------------------------------------------
    // ROUTSY
    //--------------------------------------------
    "useCssAutoload" => true,
    //--------------------------------------------
    // DUAL SITE
    //--------------------------------------------
    /**
     * A dual site is when you have a frontoffice AND a backoffice handled by the same application code.
     * If it's not dual, then you only have a front office, or a backoffice, but not both.
     */
    "dualSite" => true,
    "defaultProtocol" => 'http', // http|https
    "uriPrefixBackoffice" => "/admin",
    "themeBack" => "nullosAdmin",
    "themeFront" => ApplicationParameters::get("theme"),

];