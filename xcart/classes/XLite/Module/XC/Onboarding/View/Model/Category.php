<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\Model;

/**
 * Category view model
 */
class Category extends \XLite\View\Model\Category implements \XLite\Base\IDecorator
{
    protected function performActionUpdate()
    {
        $this->getModelObject()->dropDemoFlagOnUpdate();

        return parent::performActionUpdate();
    }
}
