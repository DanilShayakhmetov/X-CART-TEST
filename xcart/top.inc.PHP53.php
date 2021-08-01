<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


use Includes\Autoloader;
use Includes\Decorator\Utils\CacheManager;

// It's the feature of PHP 5. We need to explicitly define current time zone.
// See also http://bugs.php.net/bug.php?id=48914
@date_default_timezone_set(@date_default_timezone_get());

require_once (__DIR__ . LC_DS . 'top.inc.const.php');

// Disabled xdebug coverage for Selenium-based tests [DEVELOPMENT PURPOSE]
if (isset($_COOKIE) && !empty($_COOKIE['no_xdebug_coverage']) && function_exists('xdebug_stop_code_coverage')) {
    @xdebug_stop_code_coverage();
}

// Autoloading routines
require_once (LC_DIR_INCLUDES . 'Autoloader.php');
Autoloader::registerEverythingExceptClassCache();

// Fire the error if LC is not installed
if (!defined('XLITE_INSTALL_MODE')) {
    \Includes\ErrorHandler::checkIsLCInstalled();
}

if (!defined('LC_DO_NOT_REBUILD_CACHE')) {
    CacheManager::checkRebuildBlock();
}

// So called "developer" mode. Set it to "false" for production mode!
define('LC_DEVELOPER_MODE', (bool) \Includes\Utils\ConfigParser::getOptions(array('performance', 'developer_mode')));

// Correct error handling mode
ini_set('display_errors', LC_DEVELOPER_MODE);

// Fatal error and exception handlers
register_shutdown_function(array('\Includes\ErrorHandler', 'shutdown'));
set_exception_handler(array('\Includes\ErrorHandler', 'handleException'));

require_once (LC_DIR_INCLUDES . 'prepend.php');

Autoloader::registerClassCacheProductionAutoloader();

// Check and (if needed) rebuild classes cache

//if (!defined('LC_DO_NOT_REBUILD_CACHE')) {
//    CacheManager::rebuildCache();
//}

// Do not register development class cache autoloader when:
// 1) Cache rebuild is in progress (other process is rebuilding the cache in separate var/run folder).
// 2) Script has opted out of using development class cache autoloader by defining LC_DO_NOT_REBUILD_CACHE (for example, ./restoredb)
if (LC_DEVELOPER_MODE && !CacheManager::isRebuildInProgress() && !defined('LC_DO_NOT_REBUILD_CACHE')) {
    Autoloader::registerClassCacheDevelopmentAutoloader();
} else {
    Autoloader::registerClassCacheProductionAutoloader();
}
