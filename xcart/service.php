<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

@set_time_limit(300);

require_once __DIR__ . '/service/vendor/autoload.php';
require_once __DIR__ . '/modules_manager/autoload.php';

$config = new XCart\ConfigParser\ConfigParser($_SERVER, __DIR__ . '/etc/');

$appFactory = require __DIR__ . '/service/src/App.php';

/** @var \Silex\Application $app */
$app = $appFactory($config->getData());

$app->run(\XCart\Bus\Core\Request::createFromGlobals());
