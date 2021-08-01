<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        $permissions = ['manage reviews'];
        $repo        = \XLite\Core\Database::getRepo('XLite\Model\Role\Permission');
        foreach ($permissions as $code) {
            $permission = $repo->findOneByCode($code);
            if ($permission) {
                $repo->delete($permission, false);
            }
        }
    }
);
