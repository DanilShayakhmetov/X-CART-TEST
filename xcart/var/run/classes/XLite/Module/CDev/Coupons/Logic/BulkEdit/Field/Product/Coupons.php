<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Logic\BulkEdit\Field\Product;

class Coupons extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $position = isset($options['position']) ? $options['position'] : 0;

        $coupons = [];
        /** @var \XLite\Module\CDev\Coupons\Model\Coupon $coupon */
        foreach (\XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->findAllProductSpecific() as $coupon) {
            $coupons[$coupon->getId()] = $coupon->getCode();
        }

        return [
            $name                => [
                'label'             => static::t('Coupons'),
                'type'              => 'XLite\View\FormModel\Type\Select2Type',
                'multiple'          => true,
                'choices'           => array_flip($coupons),
                'position'          => $position,
            ],
            $name . '_edit_mode' => [
                'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                'choices'           => [
                    static::t('Add')       => 'add',
                    static::t('Remove')    => 'remove',
                    static::t('Replace with') => 'replace_with',
                ],
                'placeholder'       => false,
                'multiple'          => false,
                'expanded'          => true,
                'is_data_field'     => false,
                'position'          => $position + 1,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name . '_edit_mode' => 'add',
            $name                => [],
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $couponIds = $data->{$name};

        $editMode = $data->{$name . '_edit_mode'};
        if ($editMode === 'remove') {
            $object->removeSpecificProductCoupons($couponIds);

        } elseif ($editMode === 'replace_with') {
            $object->replaceSpecificProductCoupons($couponIds);

        } else {
            $object->addSpecificProductCoupons($couponIds);
        }
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        return [
            $name => [
                'name'    => static::t('Coupons'),
                'orderBy' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return array
     */
    public static function getViewValue($name, $object)
    {
        $result = [];
        /** @var \XLite\Module\CDev\Coupons\Model\CouponProduct $couponProduct */
        foreach ($object->getCouponProducts() as $couponProduct) {
            $result[] = $couponProduct->getCoupon()->getCode();
        }

        return implode(', ', $result);
    }
}
