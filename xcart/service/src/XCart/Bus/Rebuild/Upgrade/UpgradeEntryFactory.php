<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Upgrade;

use XCart\Bus\Domain\Module;
use XCart\Bus\Helper\HookFilter;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"token"="x_cart.bus.user_token"})
 */
class UpgradeEntryFactory
{
    /**
     * @var HookFilter
     */
    private $hookFilter;

    /**
     * @param HookFilter $hookFilter
     */
    public function __construct(
        HookFilter $hookFilter
    ) {
        $this->hookFilter = $hookFilter;
    }

    /**
     * @param string $id
     * @param Module  $module
     *
     * @return UpgradeEntry
     */
    public function buildEntry($id, Module $module): UpgradeEntry
    {
        return new UpgradeEntry($id, $module, $this->hookFilter);
    }
}
