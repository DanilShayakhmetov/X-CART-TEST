<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        $enabledRole = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneBy(['enabled' => true]);
        if (!$enabledRole) {
            $permanent = \XLite\Core\Database::getRepo('XLite\Model\Role')->getPermanentRole();
            if (!$permanent) {
                $permanent = \XLite\Core\Database::getRepo('XLite\Model\Role')->findFrame(0, 1);
                $permanent = 0 < count($permanent) ? array_shift($permanent) : null;
            }

            if ($permanent) {
                $permanent->setEnabled(true);
            }
        }
    }
);
