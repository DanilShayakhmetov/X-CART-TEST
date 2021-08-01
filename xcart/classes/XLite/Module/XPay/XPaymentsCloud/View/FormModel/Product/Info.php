<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\FormModel\Product;

use XLite\Core\Database;

/**
 * Product form model
 */
class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();
        $product = Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);

        if (
            $product
            && $product->hasSubscriptionPlan()
        ) {
            $schema['prices_and_inventory']['price']['label'] = static::t('Setup fee');
        }

        return $schema;
    }

}
