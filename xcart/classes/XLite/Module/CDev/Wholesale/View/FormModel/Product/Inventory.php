<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormModel\Product;

class Inventory extends \XLite\View\FormModel\Product\Inventory implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Wholesale/form_model/product/inventory/style.css';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineSections()
    {
        $sections                              = parent::defineSections();
        $sections['minimum_purchase_quantity'] = [
            'label'    => static::t('Minimum purchase quantity'),
            'position' => 100,
        ];

        return $sections;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $result = parent::defineFields();

        $position                = 100;
        $minimumPurchaseQuantity = [
            'membership_0' => [
                'label'             => static::t('All customers'),
                'type'              => 'XLite\View\FormModel\Type\PatternType',
                'inputmask_pattern' => [
                    'alias'      => 'integer',
                    'rightAlign' => false,
                ],
                'position'          => $position,
            ],
        ];

        $description = [];

        $minimumPurchaseQuantityData = $this->getDataObject()->minimum_purchase_quantity;
        $maximumOrderQuantity        = \XLite\Core\Config::getInstance()->General->default_purchase_limit;
        if (isset($minimumPurchaseQuantityData->membership_0)
            && $minimumPurchaseQuantityData->membership_0 > $maximumOrderQuantity) {

            $description[] = static::t(
                '"Maximum order quantity (per product)" is set to {{max_order_quantity}} in the settings. It will be ignored for this product since you have defined the minimum purchase quantity as {{min_purchase_quantity}}.',
                [
                    'max_order_quantity'    => $maximumOrderQuantity,
                    'min_purchase_quantity' => $minimumPurchaseQuantityData->membership_0,
                ]
            );
        }

        $maximumOrderAmount = \XLite\Core\Config::getInstance()->General->maximal_order_amount;
        $product            = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);

        $price = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->getPrice(
            $product,
            $minimumPurchaseQuantityData->membership_0
        );
        $price = $price ?: $product->getPrice();
        if ($maximumOrderAmount < $minimumPurchaseQuantityData->membership_0 * $price) {
            $description[] = static::t(
                '"Maximum allowed order subtotal" is set to {{max_order_subtotal}} in the settings. It will be ignored for this product since you have specified {{product_amount}} ({{product_quantity}} * {{product_price}}) as the minimum purchase subtotal.',
                [
                    'max_order_subtotal' => \XLite\View\AView::formatPrice($maximumOrderAmount),
                    'product_amount'     => \XLite\View\AView::formatPrice($minimumPurchaseQuantityData->membership_0 * $price),
                    'product_quantity'   => $minimumPurchaseQuantityData->membership_0,
                    'product_price'      => \XLite\View\AView::formatPrice($price),
                ]
            );

        }

        if ($description) {
            $minimumPurchaseQuantity['membership_0']['description'] = implode('<br/><br/>', $description) . '<br/><br/>';
            $minimumPurchaseQuantity['membership_0']['form_row_class'] = 'form-group warning-sign';
        }

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll() as $membership) {
            $position                      += 100;
            $key                           = 'membership_' . $membership->getMembershipId();
            $minimumPurchaseQuantity[$key] = [
                'label'             => $membership->getName(),
                'type'              => 'XLite\View\FormModel\Type\PatternType',
                'inputmask_pattern' => [
                    'alias'      => 'integer',
                    'rightAlign' => false,
                ],
                'position'          => $position,
            ];

            $description = [];

            if (isset($minimumPurchaseQuantityData->{$key})
                && $minimumPurchaseQuantityData->{$key} > $maximumOrderQuantity) {
                $description[] = static::t(
                    '"Maximum order quantity (per product)" is set to {{max_order_quantity}} in the settings. It will be ignored for this product since you have defined the minimum purchase quantity as {{min_purchase_quantity}}.',
                    [
                        'max_order_quantity'    => $maximumOrderQuantity,
                        'min_purchase_quantity' => $minimumPurchaseQuantityData->{$key},
                    ]
                );
            }

            $price = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->getPrice(
                $product,
                $minimumPurchaseQuantityData->membership_0,
                $membership
            );
            $price = $price ?: $product->getPrice();
            if ($maximumOrderAmount < $minimumPurchaseQuantityData->{$key} * $price) {
                $description[] = static::t(
                    '"Maximum allowed order subtotal" is set to {{max_order_subtotal}} in the settings. It will be ignored for this product since you have specified {{product_amount}} ({{product_quantity}} * {{product_price}}) as the minimum purchase subtotal.',
                    [
                        'max_order_subtotal' => \XLite\View\AView::formatPrice($maximumOrderAmount),
                        'product_amount'     => \XLite\View\AView::formatPrice($minimumPurchaseQuantityData->{$key} * $price),
                        'product_quantity'   => $minimumPurchaseQuantityData->{$key},
                        'product_price'      => \XLite\View\AView::formatPrice($price),
                    ]
                );
            }

            if ($description) {
                $minimumPurchaseQuantity[$key]['description'] = implode('<br/><br/>', $description) . '<br/><br/>';
                $minimumPurchaseQuantity[$key]['form_row_class'] = 'form-group warning-sign';
            }
        }

        $result['minimum_purchase_quantity'] = $minimumPurchaseQuantity;

        return $result;
    }
}
