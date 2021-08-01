<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Import\Step;

/**
 * Import step
 */
 class Import extends \XLite\Logic\Import\Step\ImportAbstract implements \XLite\Base\IDecorator
{

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->createNonExistentAliases();
    }
}