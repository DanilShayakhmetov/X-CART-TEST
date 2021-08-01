<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AModifier;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @DataSourceFilter(name="readOnlyActions")
 * @Service\Service()
 */
class ReadOnlyActions extends AModifier
{
    /**
     * @return mixed
     */
    public function current()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        $actions                 = [];
        $actions['settings']     = $item['enabled'] && $item['showSettingsForm'];
        $actions['installDemo']  = $item->actions['install'] ?? false;
        $actions['purchaseDemo'] = $item->actions['purchase'] ?? false;

        $item->actions = $actions;

        return $item;
    }
}
