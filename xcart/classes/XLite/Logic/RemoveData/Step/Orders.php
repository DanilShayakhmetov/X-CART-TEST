<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\RemoveData\Step;


class Orders extends \XLite\Logic\RemoveData\Step\AStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\Order
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order');
    }
    
    // }}}

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        $this->getRepository()->initializeNextOrderNumber();
    }
}
