<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function moveRestorePoints540()
{
    if (\Includes\Utils\FileManager::isFileReadable(LC_DIR_SERVICE . '.modules.migrations.php')) {
        ob_start();
        $log = @include(LC_DIR_SERVICE . '.modules.migrations.php');
        ob_get_clean();
        if (!empty($log) && is_array($log)) {
            \Includes\Utils\Operator::saveServiceYAML(LC_DIR_SERVICE . '.restore.points.php', $log);
        }
    }
}

return function()
{
    moveRestorePoints540();
};

