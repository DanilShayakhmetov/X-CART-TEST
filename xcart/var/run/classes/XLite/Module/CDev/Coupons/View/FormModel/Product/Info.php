<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormModel\Product;

 class Info extends \XLite\Module\CDev\GoSocial\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $coupons = [];
        foreach (\XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->findAllProductSpecific() as $coupon) {
            /** @var \XLite\Module\CDev\Coupons\Model\Coupon $coupon */
            $coupons[$coupon->getId()] = $coupon->getCode();
        }

        $schema['prices_and_inventory']['coupons'] = [
            'label'    => static::t('Coupons'),
            'type'     => 'XLite\View\FormModel\Type\Select2Type',
            'multiple' => true,
            'choices'  => array_flip($coupons),
            'position' => 150,
        ];

        return $schema;
    }
}
