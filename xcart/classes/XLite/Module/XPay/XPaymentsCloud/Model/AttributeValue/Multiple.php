<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\AttributeValue;

/**
 * Abstract multiple attribute value
 * @MappedSuperClass
 */
abstract class Multiple extends \XLite\Model\AttributeValue\Multiple implements \XLite\Base\IDecorator
{
    /**
     * Subscription fee modifier
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4, options={"default": 0.0000})
     */
    protected $xpaymentsSubscriptionFeeModifier = 0.0000;

    /**
     * Subscription fee modifier type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true, "default": "p" }, length=1)
     */
    protected $xpaymentsSubscriptionFeeModifierType = self::TYPE_PERCENT;

    /**
     * Find product by product_id request param
     *
     * @return \XLite\Model\Product|null
     */
    public static function findProduct()
    {
        $productId = \XLite\Core\Request::getInstance()->product_id;
        $itemId = \XLite\Core\Request::getInstance()->item_id;
        $result = null;

        if ($productId) {
            /** @var \XLite\Model\Product $result */
            $result = \XLite\Core\Database::getRepo('\XLite\Model\Product')->find($productId);
        } elseif ($itemId && is_numeric($itemId)) {
            /** @var \XLite\Model\OrderItem $orderItem */
            $orderItem = \XLite\Core\Database::getRepo('\XLite\Model\OrderItem')->find($itemId);
            if ($orderItem) {
                $result = $orderItem->getProduct();
            }
        }

        return $result;
    }

    /**
     * Return modifiers
     *
     * @return array
     */
    public static function getModifiers()
    {
        $modifiers = parent::getModifiers();
        $product = static::findProduct();

        if ($product && $product->hasSubscriptionPlan()) {

            $xpaymentsSubscriptionFeeModifier = [
                'xpaymentsSubscriptionFee' => [
                    'title' => 'Subscr. fee',
                    'symbol' => '$',
                ],
            ];

            $modifiers = array_merge($modifiers, $xpaymentsSubscriptionFeeModifier);
        }

        return $modifiers;
    }

    /**
     * Format modifier Subscription fee
     *
     * @param float $value Value
     *
     * @return string
     */
    public static function formatModifierSubscriptionFee($value)
    {
        return \XLite\View\Price::getInstance()->formatPrice($value, null, true);
    }

    /**
     * Format modifier
     *
     * @param float  $value Value
     * @param string $field Field
     *
     * @return string
     */
    public static function formatModifier($value, $field)
    {
        $product = static::findProduct();
        $result = parent::formatModifier($value, $field);

        if ($product && $product->hasSubscriptionPlan()) {

            if ('xpaymentsSubscriptionFee' == $field) {
                $result = parent::formatModifierPrice($value);
                $result .= ' to subscr.fee';
            } elseif ('price' == $field) {
                $result .= ' to setup fee';
            }
        }

        return $result;
    }

    /**
     * Get modifier base value
     *
     * @param string $field Field
     *
     * @return float
     */
    protected function getModifierBase($field)
    {
        if ('xpaymentsSubscriptionFee' == $field) {
            $result = $this->getModifierBaseXpaymentsSubscriptionFee();
        } else {
            $result = parent::getModifierBase($field);
        }

        return $result;
    }

    /**
     * Set subscriptionFee modifier
     *
     * @param float $xpaymentsSubscriptionFeeModifier
     */
    public function setXpaymentsSubscriptionFeeModifier($xpaymentsSubscriptionFeeModifier)
    {
        $this->xpaymentsSubscriptionFeeModifier = $xpaymentsSubscriptionFeeModifier;
    }

    /**
     * Get subscriptionFee modifier
     *
     * @return float
     */
    public function getXpaymentsSubscriptionFeeModifier()
    {
        return $this->xpaymentsSubscriptionFeeModifier;
    }

    /**
     * Set xpaymentsSubscriptionFeeModifierType
     *
     * @param string $xpaymentsSubscriptionFeeModifierType
     */
    public function setXpaymentsSubscriptionFeeModifierType($xpaymentsSubscriptionFeeModifierType)
    {
        $this->xpaymentsSubscriptionFeeModifierType = $xpaymentsSubscriptionFeeModifierType;
    }

    /**
     * Get xpaymentsSubscriptionFeeModifierType
     *
     * @return string
     */
    public function getXpaymentsSubscriptionFeeModifierType()
    {
        return $this->xpaymentsSubscriptionFeeModifierType;
    }

    /**
     * Get modifierBaseSubscriptionFee
     *
     * @return float
     */
    protected function getModifierBaseXpaymentsSubscriptionFee()
    {
        return $this->getProduct()->getXpaymentsClearFeePrice();
    }

}
