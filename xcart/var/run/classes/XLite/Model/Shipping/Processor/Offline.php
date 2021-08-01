<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping\Processor;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Shipping processor model
 */
class Offline extends \XLite\Model\Shipping\Processor\AProcessor
{
    use ExecuteCachedTrait;

    /**
     * Default base rate
     */
    const PROCESSOR_DEFAULT_BASE_RATE = 0;

    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'offline';
    }

    /**
     * Returns processor name
     *
     * @return string
     */
    public function getProcessorName()
    {
        return 'Custom offline shipping';
    }

    /**
     * Enable admin to remove offline shipping methods
     *
     * @return boolean
     */
    public function isMethodDeleteEnabled()
    {
        return true;
    }

    /**
     * Returns activity status
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Returns offline shipping rates
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier    Shipping order modifier
     * @param boolean                              $ignoreCache Flag: if true then do not get rates from cache
     *                                                          (not used in offline processor) OPTIONAL
     *
     * @return array
     */
    public function getRates($modifier, $ignoreCache = false)
    {
        $rates = [];

        if ($modifier instanceof \XLite\Logic\Order\Modifier\Shipping) {
            // Find markups for all enabled offline shipping methods
            $processorId = $this->getProcessorId();
            $markups = $this->executeCachedRuntime(static function() use ($processorId, $modifier) {
                return \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')
                    ->findMarkupsByProcessor($processorId, $modifier);
            }, [$processorId, $modifier]);

            if (!empty($markups)) {
                // Create shipping rates list
                foreach ($markups as $markup) {
                    $rate = new \XLite\Model\Shipping\Rate();
                    $rate->setMethod($markup->getShippingMethod());
                    $rate->setBaseRate(static::PROCESSOR_DEFAULT_BASE_RATE);
                    $rate->setMarkup($markup);
                    $rate->setMarkupRate($markup->getMarkupValue());
                    $rates[] = $rate;
                }
            }
        }

        // Return shipping rates list
        return $rates;
    }

    /**
     * Returns true if shipping methods named may be modified by admin
     *
     * @return boolean
     */
    public function isMethodNamesAdjustable()
    {
        return true;
    }

    /**
     * Returns prepared delivery time
     *
     * @param \XLite\Model\Shipping\Rate $rate
     *
     * @return string|null
     */
    public function prepareDeliveryTime(\XLite\Model\Shipping\Rate $rate)
    {
        return $rate->getMethod() ? $rate->getMethod()->getDeliveryTime() : '';
    }
}
