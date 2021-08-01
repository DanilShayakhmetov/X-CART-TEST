<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// One minute to execute the script
@set_time_limit(300);

define('XCN_ADMIN_SCRIPT', true);

try {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'top.inc.rebuild.php';

    if (PHP_SAPI === 'cli') {
        $options = getopt('', ['request::']);
        $_REQUEST = json_decode($options['request'], true);
    }

    $rebuild = new \XLite\Rebuild\OldSystemAdapter();
    $rebuild->setRequest($_REQUEST);

    $rebuild->processRequest();
} catch (\Throwable $e) {
    \Includes\ErrorHandler::handleException($e);
}
