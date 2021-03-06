Core module
=================
2017-04-05



Core module for the [kamille framework](https://github.com/lingtalfi/Kamille).




Install
===========
using the [kamille installer tool](https://github.com/lingtalfi/kamille-installer-tool)
```bash
kamille install Core
```


What is it?
==============
This module is a fundamental module in a kamille app,
as it lays down some fundations on which other modules can rely.


Basically, this module owns the dispatch loop (the loop on which the Request is thrown, described in [kam](https://github.com/lingtalfi/kam)),
and so it handles decisions related to that dispatch loop, like choosing the fallback page controller (if no other route matched),
or choosing the exception controller (the controller used if an exception is thrown somewhere, but caught only at the dispatch loop level).

It also provides code level options, like for instance whether or not to display the exception trace in the logs.
 
 
 
 
Configuration keys
====================

- fallbackController      
    - description: The default controller used by the static router when no other page matches
    - default value: Controller\Core\PageNotFoundController:render

- exceptionController
    - description: The exception controller used when an exception was caught at the WebApplicationHandler 
            level (which is btw a bad thing as it should probably be caught earlier)
    - default value: Controller\Core\ExceptionController:render             
- useFileLoggerListener
    - description: Whether or not to use the default useFileLoggerListener provided by the Core module. It will write logs to the file specified with the logFile parameter             
    - default value: true       
- logFile
    - description: This is the log file for the core module (which brings up XLog functionality)
    - default value: $appDir . "/logs/kamille.log.txt" 
- showExceptionTrace
    - description: Whether or not to show the exception trace in the logs. You can use the H::exceptionToString($e) method.
    - default value: true
- useCssAutoload
    - description: Whether or not to autoload the css files based on their existence at the location defined in the 
    laws system (part two https://github.com/lingtalfi/laws).
    - default value: true
    
    




Hooks
=========

- Core_addLoggerListener: add listeners to the LoggerInterface object (from the [Logger](https://github.com/lingtalfi/logger) planet), which is then accessible via the XLog object
- Core_feedUri2Controller: add routes to the StaticObjectRouter router 




Services
===========

- Core_webApplicationHandler: return a WebApplicationHandler instance.
    The WebApplicationHandler object handles any request by passing it to the WebApplication object (of the 
    kamille framework), therefore you can and should use it right from the index.php file.
    
    Basically, the WebApplicationHandler wraps the WebApplication, so that it can make it available
    to the modules land (i.e. modules can hook into the dispatch loop). 
    It also adds its own exception handler (i.e. when an exception is uncaught at the dispatch loop level), 
    and a pageNotFound controller, which by default displays a 404 page. 
    




Controllers
===============

See documentation for more info



Widget Dependencies
=========
- [HttpError](https://github.com/KamilleWidgets/HttpError)
- [Exception](https://github.com/KamilleWidgets/Exception)











Others
==========
- it uses [lnc1](https://github.com/lingtalfi/layout-naming-conventions#lnc_1) as the layout naming convention




History Log
------------------
    
- 1.19.0 -- 2018-02-06

    - enhance WebApplicationHandler.handle method, now creates ApplicationRegistry: isBackoffice variable for sites of type dual 
    
- 1.18.0 -- 2018-01-30

    - enhance QuickPdoInitializer, now delegates via Core_onQuickPdoInteractionAfter hook 
    
- 1.17.0 -- 2017-11-20

    - add maintenance handling
    
- 1.16.0 -- 2017-07-01

    - add Core_Localyser service
    
- 1.15.0 -- 2017-06-08

    - add Core_OnTheFlyFormProvider service
    
- 1.14.0 -- 2017-06-07

    - add Core_LinkGenerator service
    
- 1.13.0 -- 2017-05-29

    - add Core_umail service
    
- 1.12.0 -- 2017-05-26

    - ServiceController's uri now starts with the module prefix
    
- 1.11.0 -- 2017-05-23

    - add Core_TabathaCache service
    
- 1.10.0 -- 2017-05-22

    - add Core_configureLawsUtil hook
    
- 1.9.0 -- 2017-05-06

    - add Core_ModalGscpResponseDefaultButtons hook
    
- 1.8.0 -- 2017-04-28

    - add Core_QuickPdoInitializer service, and A corresponding shortcut 
    - removed fallbackController configuration key
    
- 1.7.0 -- 2017-04-23

    - add Core_autoLawsConfig hook
    
- 1.6.0 -- 2017-04-22

    - moved laws files into Core namespace
    
- 1.5.0 -- 2017-04-18

    - add RouteRouter as a router for the WebApplicationHandler
    
- 1.4.0 -- 2017-04-08

    - changed configuration key pageNotFoundController to fallbackController

- 1.3.0 -- 2017-04-07

    - add EarlyRouter
    
- 1.2.0 -- 2017-04-07

    - add HttpError widget dependency

- 1.1.0 -- 2017-04-06

    - changed FallbackController to PageNotFoundController
    
- 1.0.0 -- 2017-04-05

    - initial commit