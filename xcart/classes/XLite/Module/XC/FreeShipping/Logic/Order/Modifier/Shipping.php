<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Logic\Order\Modifier;

/**
 * Decorate shipping modifier
 */
class Shipping extends \XLite\Logic\Order\Modifier\Shipping implements \XLite\Base\IDecorator
{
    /**
     * Get shipped items
     *
     * @return array
     */
    public function getItems()
    {
        $items = parent::getItems();
        $result = array();

        foreach ($items as $item) {
            if (!$this->isIgnoreShippingCalculation($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Get shipped items for check condition
     *
     * @return array
     */
    public function getItemsCondition()
    {
        $items = parent::getItemsCondition();
        $result = array();

        foreach ($items as $item) {
            if (!$this->isIgnoreShippingCalculation($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Get shipping rates
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    public function getRates()
    {
        $rates = parent::getRates();

        $rates = $this->prepareFreeShippingModuleRates($rates);

        return $rates;
    }

    /**
     * Prepare rates
     *
     * @param \XLite\Model\Shipping\Rate[] $rates
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function prepareFreeShippingModuleRates($rates)
    {
        $unsetFree = true;

        // Get total fixed fees value
        $fixedFee = $this->getItemsFreightFixedFee();

        // Get count of items
        $itemsCount = count($this->getItems());

        $isShipForFree = null;

        foreach ($this->getItems() as $item) {
            if (!$item->isShipForFree() && !$item->isFreeShipping()) {
                $isShipForFree = false;
                break;
            } elseif (is_null($isShipForFree) && $item->isShipForFree()) {
                $isShipForFree = true;
            }
        }

        $unsetFree = !$isShipForFree;

        if (0 == $itemsCount) {

            // There are no items

            if (0 < $fixedFee) {
                // There are items with fixed fee, remove all methods except 'Freight fixed fee'
                foreach ($rates as $k => $rate) {
                    if (!$rate->getMethod()->isFixedFee()) {
                        unset($rates[$k]);
                    }
                }

            } else {
                // There are no items with fixed fee, remove method 'Freight fixed fee'
                foreach ($rates as $k => $rate) {
                    if ($rate->getMethod()->isFixedFee()) {
                        unset($rates[$k]);
                    }
                }
                // Are all items marked as Free shipping?
                $unsetFree = false;

                if (!$isShipForFree) {
                    foreach ($rates as $rate) {
                        $rate->setBaseRate(0);
                        $rate->setMarkupRate(0);
                        if (!$rate->getMethod()->getFree()) {
                            // Non free shipping method found
                            $unsetFree = true;
                        }
                    }
                }
            }
        }

        // Correct shipping rates list
        foreach ($rates as $k => $rate) {

            $doUnset = false;

            if ($unsetFree && $rate->getMethod()->getFree()) {
                // Unset 'Free shipping' method
                $doUnset = true;

            } elseif (0 < $fixedFee) {

                if (0 < $itemsCount && $rate->getMethod()->isFixedFee()) {
                    // Unset 'Freight fixed fee' method if there are other methods
                    $doUnset = true;

                } else {
                    $rates[$k]->setFreightRate($fixedFee);
                }

            } elseif ($rate->getMethod()->isFixedFee()) {
                $doUnset = true;
            }

            if ($doUnset) {
                unset($rates[$k]);
            }
        }

        return $rates;
    }

    /**
     * Return true if order item must be excluded from shipping rates calculations
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isIgnoreShippingCalculation($item)
    {
        return $item->getObject()
            && (
                $item->getObject()->getFreeShip()
                || $item->isFreeShipping()
                || (
                    !$item->isShipForFree()
                    && $this->isIgnoreProductsWithFixedFee()
                    && 0 < $item->getObject()->getFreightFixedFee()
                )
            );
    }

    /**
     * Get sum of freight fixed fee of all order items
     *
     * @return float
     */
    protected function getItemsFreightFixedFee()
    {
        $result = 0;

        $items = parent::getItems();
        foreach ($items as $item) {
            if (
                $item->getObject()
                && !$item->getObject()->getFreeShip()
                && !$item->getObject()->isShipForFree()
                && 0 < $item->getObject()->getFreightFixedFee()
                && !$this->isAppliedFreeShippingCoupon($item)
            ) {
                $result += $item->getObject()->getFreightFixedFee() * $item->getAmount();
            }
        }

        return $result;
    }

    /**
     * Return true if products with defined shipping freight should be ignored in shipping rates calculations
     *
     * @return boolean
     */
    protected function isIgnoreProductsWithFixedFee()
    {
        $mode = \XLite\Core\Config::getInstance()->XC && \XLite\Core\Config::getInstance()->XC->FreeShipping
            ? \XLite\Core\Config::getInstance()->XC->FreeShipping->freight_shipping_calc_mode
            : null;

        return \XLite\Module\XC\FreeShipping\View\FormField\FreightMode::FREIGHT_ONLY == $mode;
    }

    /**
     * Returns true if any of order items are shipped
     *
     * @return boolean
     */
    protected function isShippable()
    {
        $result = parent::isShippable();

        foreach (parent::getItems() as $item) {
            if ($item->isShippable()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Returns false by default
     *
     * @param \XLite\Model\OrderItem $item Order item model
     *
     * @return boolean
     */
    protected function isAppliedFreeShippingCoupon($item) {
        return false;
    }
}
