<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Model\Shipping;

/**
 * Offline shipping method view model
 */
class Offline extends \XLite\View\Model\Shipping\Offline implements \XLite\Base\IDecorator
{
    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        $entity = $this->getModelObject();

        if ($entity->getFree() || $entity->isFixedFee()) {
            unset($schema['tableType'], $schema['shippingZone']);
        }

        return parent::getFieldsBySchema($schema);
    }
}
