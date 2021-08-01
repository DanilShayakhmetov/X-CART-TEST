<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Order\Modifier;

use XLite\Model\Shipping\Rate;

/**
 * Shipping modifier
 */
class Shipping extends \XLite\Logic\Order\Modifier\AShipping
{
    /**
     * Modifier code
     */
    const MODIFIER_CODE = 'SHIPPING';

    /**
     * Modifier unique code
     *
     * @var string
     */
    protected $code = self::MODIFIER_CODE;

    /**
     * Selected rate (cache)
     *
     * @var \XLite\Model\Shipping\Rate
     */
    protected $selectedRate;

    /**
     * Runtime cache
     * @var array
     */
    protected $rates = [];

    /**
     * @inheritdoc
     */
    public function initialize(\XLite\Model\Order $order, \XLite\DataSet\Collection\OrderModifier $list)
    {
        $this->rates = [];
        parent::initialize($order, $list);
    }

    /**
     * Check - can apply this modifier or not
     *
     * @return boolean
     */
    public function canApply()
    {
        return parent::canApply()
            && $this->isShippable();
    }

    /**
     * Calculate
     *
     * @return \XLite\Model\Order\Surcharge
     */
    public function calculate()
    {
        $surcharge = null;
        $cost = null;

        if ($this->isShippable()) {
            if (!$this->isCart() || !$this->order->isIgnoreLongCalculations()) {
                $rate = $this->getSelectedRate();

                if (null !== $rate) {
                    $cost = $this->getOrder()->getCurrency()->roundValue($rate->getTotalRate());
                } else {
                    $this->resetOrderSurcharges();
                }
            } else {
                $cost = $this->getOrder()->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);
            }

            if ($cost !== null) {
                $surcharge = $this->addOrderSurcharge($this->code, (float) $cost, false, true);
            }
        }

        if (!$surcharge) {
            $this->resetOrderSurcharges();
        }

        return $surcharge;
    }

    /**
     * Reset order surcharges
     *
     * @return void
     */
    protected function resetOrderSurcharges()
    {
        foreach ($this->order->getSurcharges() as $s) {
            if ($s->getType() === $this->type && $s->getCode() === $this->code) {
                $this->getOrder()->removeSurcharge($s);
                \XLite\Core\Database::getEM()->remove($s);
            }
        }
    }

    /**
     * Check - shipping rates exists or not
     *
     * @return boolean
     */
    public function isRatesExists()
    {
        return (bool)$this->getRates();
    }

    /**
     * Get shipping rates
     *
     * @return array(\XLite\Model\Shipping\Rate)
     */
    public function getRates()
    {
        $hash = \XLite\Model\Shipping::getInstance()->getHash($this);

        if (!isset($this->rates[$hash])) {
            if ($this->isCart()) {
                $this->rates[$hash] = $this->calculateRates();
            } else {
                $restored = $this->restoreRates();
                $rates = array_merge($restored, array_filter(
                    $this->calculateRates(),
                    function (Rate $rate) use ($restored) {
                        foreach ($restored as $restoredRate) {
                            if ($restoredRate->getMethod() === $rate->getMethod()) {
                                return false;
                            }
                        }

                        return true;
                    }
                ));

                $this->rates[$hash] = $rates;
            }
        }

        return $this->rates[$hash];
    }

    /**
     * Returns true if any of order items are shipped
     *
     * @return boolean
     */
    protected function isShippable()
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->isShippable()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    // {{{ Shipping rates

    /**
     * Calculate shipping rates
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function calculateRates()
    {
        $rates = [];

        if ($this->isShippable()) {
            $rates = $this->retrieveRates();
            uasort($rates, array($this, 'compareRates'));
        }

        return $rates;
    }

    /**
     * Retrieve available shipping rates
     *
     * @return array
     */
    protected function retrieveRates()
    {
        return \XLite\Model\Shipping::getInstance()->getRates($this);
    }

    /**
     * Restore rates
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function restoreRates()
    {
        $rates = array();

        if ($this->order->getShippingId()) {
            /** @var \XLite\Model\Shipping\Method $method */
            $method = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($this->order->getShippingId());

            if ($method) {
                $rate = new \XLite\Model\Shipping\Rate();
                $rate->setMethod($method);
                $rate->setBaseRate(0);
                $rate->setMarkupRate($this->order->getSurchargeSumByType($this->type));

                $rates[] = $rate;
            }
        }

        return $rates;
    }

    /**
     * Shipping rates sorting callback
     *
     * @param \XLite\Model\Shipping\Rate $a First shipping rate
     * @param \XLite\Model\Shipping\Rate $b Second shipping rate
     *
     * @return integer
     */
    protected function compareRates(\XLite\Model\Shipping\Rate $a, \XLite\Model\Shipping\Rate $b)
    {
        $aMethod = $a->getMethod();
        $bMethod = $b->getMethod();

        $aPosition = $aMethod->getPosition();
        $bPosition = $bMethod->getPosition();

        if ('offline' !== $aMethod->getProcessor() && 'offline' !== $bMethod->getProcessor()) {
            if ($aMethod->getProcessor() !== $bMethod->getProcessor()) {
                $aPosition = $aMethod->getParentMethod()->getPosition();
                $bPosition = $bMethod->getParentMethod()->getPosition();
            }
        } elseif ('offline' !== $aMethod->getProcessor()) {
            $aPosition = $aMethod->getParentMethod()->getPosition();

        } elseif ('offline' !== $bMethod->getProcessor()) {
            $bPosition = $bMethod->getParentMethod()->getPosition();
        }

        return $aPosition === $bPosition ? 0 : ($aPosition < $bPosition ? -1 : 1);
    }

    // }}}

    // {{{ Current shipping method and rate

    /**
     * Get selected shipping rate
     *
     * @return \XLite\Model\Shipping\Rate
     */
    public function getSelectedRate()
    {
        $selectedRate = null;
        $shippingId = (int)$this->order->getShippingId();

        // Get shipping rates
        $rates = $shippingId > 0 ? $this->getRates() : [];

        foreach ($rates as $rate) {
            if ($shippingId === (int)$rate->getMethodId()) {
                $selectedRate = $rate;
                break;
            }
        }

        $this->setSelectedRate($selectedRate);

        return $selectedRate;
    }

    /**
     * Set shipping rate and shipping method id
     *
     * @param \XLite\Model\Shipping\Rate $rate Shipping rate object OPTIONAL
     *
     * @return void
     */
    public function setSelectedRate(\XLite\Model\Shipping\Rate $rate = null)
    {
        $this->selectedRate = $rate;
        $newShippingId = $rate ? (int)$rate->getMethodId() : 0;

        if ((int)$this->order->getShippingId() !== $newShippingId) {
            $this->order->setShippingId($newShippingId);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Reset selected rate
     *
     * @return void
     */
    public function resetSelectedRate()
    {
        $this->selectedRate = null;
    }

    /**
     * Get shipping method
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function getMethod()
    {
        $result = null;

        if (!$this->isCart()
            && $this->order->getShippingId()
        ) {
            /** @var \XLite\Model\Shipping\Method $result */
            $result = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($this->order->getShippingId());
        }

        if (!$result) {
            $rate = $this->getSelectedRate();
            if (null !== $rate) {
                $result = $rate->getMethod();
            }
        }

        return $result;
    }

    /**
     * Get shipping method name
     *
     * @return string|void
     */
    public function getActualName()
    {
        $name = null;

        if ($this->getMethod()) {
            $name = $this->getMethod()->getName();
        } elseif ($this->order->getShippingMethodName()) {
            $name = $this->order->getShippingMethodName();
        }

        return $name;
    }

    // }}}

    // {{{ Shipping calculation data

    /**
     * Get shipped items
     *
     * @return \XLite\Model\OrderItem[]
     */
    public function getItems()
    {
        $result = array();

        foreach ($this->order->getItems() as $item) {
            if ($item->isShippable()) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Get order weight
     *
     * @return float
     */
    public function getWeight()
    {
        $weight = 0;

        foreach ($this->getItems() as $item) {
            $weight += $item->getWeight();
        }

        return $weight;
    }

    /**
     * Count shipped items quantity
     *
     * @return integer
     */
    public function countItems()
    {
        $result = 0;

        foreach ($this->getItems() as $item) {
            $result += $item->getAmount();
        }

        return $result;
    }

    /**
     * Get order subtotal only for shipped items
     *
     * @return float
     */
    public function getSubtotal()
    {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            $subtotal += $this->getItemSubtotal($item);
        }

        return $subtotal;
    }

    /**
     * Get item subtotal
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return float
     */
    protected function getItemSubtotal($item)
    {
        return $item->getTotal();
    }

    /**
     * Get order discounted subtotal only for shipped items
     *
     * @return float
     */
    public function getDiscountedSubtotal()
    {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            $subtotal += $this->getItemDiscountedSubtotal($item);
        }

        return $subtotal;
    }

    /**
     * Get item discounted subtotal
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return float
     */
    protected function getItemDiscountedSubtotal($item)
    {
        return $item->getDiscountedSubtotal();
    }

    /**
     * Get shipped items for check condition
     *
     * @return array
     */
    public function getItemsCondition()
    {
        $result = array();

        foreach ($this->order->getItems() as $item) {
            if ($item->isShippable()) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Get order weight for check condition
     *
     * @return float
     */
    public function getWeightCondition()
    {
        $weight = 0;

        foreach ($this->getItemsCondition() as $item) {
            $weight += $item->getWeight();
        }

        return $weight;
    }

    /**
     * Count shipped items quantity for check condition
     *
     * @return integer
     */
    public function countItemsCondition()
    {
        $result = 0;

        foreach ($this->getItemsCondition() as $item) {
            $result += $item->getAmount();
        }

        return $result;
    }

    /**
     * Get order subtotal only for shipped items for check condition
     *
     * @return float
     */
    public function getSubtotalCondition()
    {
        $subtotal = 0;

        foreach ($this->getItemsCondition() as $item) {
            /** @var \XLite\Model\OrderItem $item */
            $subtotal += $item->getTotal();
        }

        return max(0, $subtotal);
    }

    /**
     * Get order discounted subtotal only for shipped items for check condition
     *
     * @return float
     */
    public function getDiscountedSubtotalCondition()
    {
        $discountedSubtotal = 0;

        foreach ($this->getItemsCondition() as $item) {
            /** @var \XLite\Model\OrderItem $item */
            $discountedSubtotal += $item->getDiscountedSubtotal();
        }

        return $discountedSubtotal;
    }


    // }}}

    // {{{ Surcharge operations

    /**
     * Get surcharge name
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    public function getSurchargeInfo(\XLite\Model\Base\Surcharge $surcharge)
    {
        $info = new \XLite\DataSet\Transport\Order\Surcharge;

        $info->name = \XLite\Core\Translation::lbl('Shipping cost');
        $info->notAvailableReason = \XLite\Core\Translation::lbl('Shipping address is not defined');

        return $info;
    }

    // }}}
}
