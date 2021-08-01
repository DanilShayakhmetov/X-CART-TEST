<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Timestamp of the application start
define('LC_START_TIME', time());
define('MAX_TIMESTAMP', PHP_INT_SIZE === 4 ? PHP_INT_MAX : PHP_INT_MAX >> 32);

// Namespaces
define('LC_NAMESPACE',          'XLite');
define('LC_NAMESPACE_INCLUDES', 'Includes');
define('LC_MODEL_NS',           LC_NAMESPACE . '\Model');
define('LC_MODEL_PROXY_NS',     LC_MODEL_NS . '\Proxy');

// Paths
define('LC_DIR',                 realpath(__DIR__));
define('LC_DIR_ROOT',            rtrim(LC_DIR, LC_DS) . LC_DS);
define('LC_DIR_CLASSES',         LC_DIR_ROOT . 'classes' . LC_DS);
define('LC_DIR_VAR',             LC_DIR_ROOT . 'var' . LC_DS);
define('LC_DIR_LIB',             LC_DIR_ROOT . 'lib' . LC_DS);
define('LC_DIR_SKINS',           LC_DIR_ROOT . 'skins' . LC_DS);
define('LC_DIR_IMAGES',          LC_DIR_ROOT . 'images' . LC_DS);
define('LC_DIR_FILES',           LC_DIR_ROOT . 'files' . LC_DS);
define('LC_DIR_CONFIG',          LC_DIR_ROOT . 'etc' . LC_DS);
define('LC_DIR_INCLUDES',        LC_DIR_ROOT . LC_NAMESPACE_INCLUDES . LC_DS);
define('LC_DIR_MODULES',         LC_DIR_CLASSES . LC_NAMESPACE . LC_DS . 'Module' . LC_DS);
define('LC_DIR_COMPILE',         LC_DIR_VAR . 'run' . LC_DS);
define('LC_DIR_CACHE_CLASSES',   LC_DIR_COMPILE . 'classes' . LC_DS);
define('LC_DIR_CACHE_SKINS',     LC_DIR_COMPILE . 'skins' . LC_DS);
define('LC_DIR_CACHE_MODULES',   LC_DIR_CACHE_CLASSES . LC_NAMESPACE . LC_DS . 'Module' . LC_DS);
define('LC_DIR_CACHE_MODEL',     LC_DIR_CACHE_CLASSES . LC_NAMESPACE . LC_DS . 'Model' . LC_DS);
define('LC_DIR_CACHE_PROXY',     LC_DIR_CACHE_MODEL . 'Proxy' . LC_DS);
define('LC_DIR_CACHE_RESOURCES', LC_DIR_VAR . 'resources' . LC_DS);
define('LC_DIR_BACKUP',          LC_DIR_VAR . 'backup' . LC_DS);
define('LC_DIR_DATA',            LC_DIR_VAR . 'data' . LC_DS);
define('LC_DIR_TMP',             LC_DIR_VAR . 'tmp' . LC_DS);
define('LC_DIR_LOCALE',          LC_DIR_VAR . 'locale');
define('LC_DIR_DATACACHE',       LC_DIR_VAR . 'datacache');
define('LC_DIR_LOG',             LC_DIR_VAR . 'log' . LC_DS);
define('LC_DIR_CACHE_IMAGES',    LC_DIR_VAR . 'images' . LC_DS);
define('LC_DIR_SERVICE',         LC_DIR_FILES . 'service' . LC_DS);

define('LC_OS_WINDOWS', 'WIN' === strtoupper(substr(PHP_OS, 0, 3)));
define('LC_IS_PHP_7', version_compare(PHP_VERSION, '7.0.0', '>='));

// Current X-Cart version
define('LC_VERSION', '5.4.1.23');
