<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Model\DTO\Product;


 class Info extends \XLite\Module\XC\FreeShipping\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function init($object)
    {
        parent::init($object);

        $this->marketing->facebookMarketingEnabled = $object->getFacebookMarketingEnabled();
    }

    /**
     * @inheritdoc
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setFacebookMarketingEnabled((boolean) $this->marketing->facebookMarketingEnabled);
    }
}