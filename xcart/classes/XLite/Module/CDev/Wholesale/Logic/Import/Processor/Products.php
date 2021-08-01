<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Logic\Import\Processor;

use XLite\Core\Database;
use XLite\Module\CDev\Wholesale\Logic\Export\Step\Products as ProductsExport;
use XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['wholesalePrices'] = [
            static::COLUMN_IS_MULTIPLE => true,
        ];

        $columns['minimumPurchaseQuantity'] = [
            static::COLUMN_IS_MULTIPLE => true,
        ];

        $columns['applySaleToWholesale'] = [];

        return $columns;
    }

    // }}}

    /**
     * @inheritdoc
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'WHOLESALE-DUPLICATE-ERR' => 'Tier with same quantity range and membership already defined.',
                'MIN-PURCHASE-QTY-FMT'    => 'Wrong minimum purchase quantity format',
                'APPLY-SALE-TO-WHOLESALE-FMT' => 'Wrong "Apply sale to wholesale" format',
            ];
    }

    protected function prepareMinPurchaseQuantity($value)
    {
        $result = array_map('strrev', explode('=', strrev($value), 2));

        if (count($result) === 2) {
            if ($result[1] === ProductsExport::ALL_CUSTOMERS_TIER) {
                return [
                    'membership' => null,
                    'quantity'   => (integer)$result[0],
                ];
            } else {
                $membership = Database::getRepo('XLite\Model\Membership')->findOneByName($result[1], false);
                return $membership
                    ? [
                        'membership' => $membership,
                        'quantity'   => (integer)$result[0],
                    ]
                    : null;
            }
        }

        return null;
    }

    // {{{ Verification

    /**
     * Verify 'wholesalePrices' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyWholesalePrices($value, array $column)
    {
        if ($value) {
            $values = [];
            foreach ($value as $price) {
                if (preg_match('/^(\d+)(-(\d+))?(\((.+)\))?=(\d+\.?\d*)(%?)$/iSs', $price, $m)) {
                    $data = [
                        'membership'         => $this->normalizeValueAsMembership($m[5]),
                        'price'              => $m[6],
                        'quantityRangeBegin' => $m[1],
                        'quantityRangeEnd'   => intval($m[3]),
                    ];
                    if (isset($m[7]) && '%' == $m[7]) {
                        $data['type'] = AWholesalePrice::WHOLESALE_TYPE_PERCENT;
                    } else {
                        $data['type'] = AWholesalePrice::WHOLESALE_TYPE_PRICE;
                    }

                    $callback = function ($tier) use ($data) {
                        return $data['quantityRangeBegin'] === $tier['quantityRangeBegin']
                            && $data['membership'] === $tier['membership'];
                    };

                    if (array_filter($values, $callback)) {
                        $this->addError('WHOLESALE-DUPLICATE-ERR', ['column' => $column,
                                                                    'value'  => $data]);
                    } else {
                        $values[] = $data;
                    }
                }
            }
        }
    }

    /**
     * Verify 'wholesalePrices' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMinimumPurchaseQuantity($value, array $column)
    {
        if ($value) {
            foreach ($value as $tier) {
                $data = $this->prepareMinPurchaseQuantity($tier);

                if ($data && $data['quantity'] <= 0) {
                    $this->addError('MIN-PURCHASE-QTY-FMT', ['column' => $column, 'value' => $tier]);
                }
            }
        }
    }

    /**
     * Verify 'applySaleToWholesale' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyApplySaleToWholesale($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('APPLY-SALE-TO-WHOLESALE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    // }}}

    // {{{ Import

    /**
     * Import 'wholesalePrices' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importWholesalePricesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        foreach (\XLite\Core\Database::getRepo('\XLite\Module\CDev\Wholesale\Model\WholesalePrice')->findByProduct($model) as $price) {
            \XLite\Core\Database::getRepo('\XLite\Module\CDev\Wholesale\Model\WholesalePrice')->delete($price);
        }

        if ($value) {
            $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice');
            foreach ($value as $price) {
                if (preg_match('/^(\d+)(-(\d+))?(\((.+)\))?=(\d+\.?\d*)(%?)$/iSs', $price, $m)) {
                    $data = [
                        'membership'         => $this->normalizeValueAsMembership($m[5]),
                        'product'            => $model,
                        'price'              => $m[6],
                        'quantityRangeBegin' => $m[1],
                        'quantityRangeEnd'   => intval($m[3]),
                    ];

                    if (isset($m[7]) && '%' == $m[7]) {
                        $data['type'] = AWholesalePrice::WHOLESALE_TYPE_PERCENT;
                    } else {
                        $data['type'] = AWholesalePrice::WHOLESALE_TYPE_PRICE;
                    }

                    $repo->insert($data, false);
                }
            }
        }
    }

    /**
     * Import 'wholesalePrices' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importMinimumPurchaseQuantityColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        $tiers = [];
        /* @var \XLite\Module\CDev\Wholesale\Model\Product $model */
        foreach ($value as $tier) {
            $data = $this->prepareMinPurchaseQuantity($tier);

            if ($data) {
                $tier = !empty($tiers[$data['membership']? $data['membership']->getMembershipId() : null])
                    ? $tiers[$data['membership'] ? $data['membership']->getMembershipId() : null]
                    : Database::getRepo('XLite\Module\CDev\Wholesale\Model\MinQuantity')->findOneBy([
                        'product'    => $model,
                        'membership' => $data['membership'],
                    ]);

                if (!$tier) {
                    $tier = new \XLite\Module\CDev\Wholesale\Model\MinQuantity;
                    $tier->setProduct($model);
                    $tier->setMembership($data['membership']);

                    Database::getEM()->persist($tier);
                }

                $tier->setQuantity($data['quantity']);

                $tiers[$data['membership'] ? $data['membership']->getMembershipId() : null] = $tier;
            }
        }
    }

    /**
     * Import 'applySaleToWholesale' attribute
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importApplySaleToWholesaleColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setApplySaleToWholesale($this->normalizeValueAsBoolean($value));
    }
    // }}}
}
