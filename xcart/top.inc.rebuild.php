<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Short name
define('LC_DS', DIRECTORY_SEPARATOR);

// Modes
define('LC_IS_CLI_MODE', 'cli' === PHP_SAPI);

// Common end-of-line
define('LC_EOL', LC_IS_CLI_MODE ? "\n" : '<br />');

define('LC_DEVELOPER_MODE', false);

require_once __DIR__ . LC_DS . 'vendor' . LC_DS . 'autoload.php';
require_once __DIR__ . LC_DS . 'service' . LC_DS . 'vendor' . LC_DS . 'autoload.php';
require_once __DIR__ . LC_DS . 'modules_manager' . LC_DS . 'autoload.php';

// Define error handling functions and check PHP version (if needed)
require_once __DIR__ . LC_DS . 'error_handler.rebuild.php';
require_once __DIR__ . LC_DS . 'top.inc.const.php';

require_once(LC_DIR_INCLUDES . 'prepend.php');

// Autoloading routines
require_once LC_DIR_INCLUDES . 'Autoloader.php';
\Includes\Autoloader::registerEverythingExceptClassCache();
\Includes\Autoloader::registerClassCacheProductionAutoloader();

register_shutdown_function(['\Includes\ErrorHandler', 'shutdown']);
set_exception_handler(['\Includes\ErrorHandler', 'handleException']);

require_once LC_DIR_CLASSES . 'XLite/Rebuild/OldSystemAdapter.php';
require_once LC_DIR_CLASSES . 'XLite/Rebuild/Executor/IRebuildExecutor.php';
require_once LC_DIR_CLASSES . 'XLite/Rebuild/Executor/StepExecutor.php';
require_once LC_DIR_CLASSES . 'XLite/Rebuild/Executor/ActionExecutor.php';
require_once LC_DIR_CLASSES . 'XLite/Rebuild/Executor/HookExecutor.php';
require_once LC_DIR_CLASSES . 'XLite/Rebuild/Executor/Entry/Hook.php';
require_once LC_DIR_CLASSES . 'XLite/Rebuild/Executor/StartRebuildExecutor.php';
