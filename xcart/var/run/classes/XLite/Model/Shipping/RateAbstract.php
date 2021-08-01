<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping;

/**
 * Shipping rate model
 */
abstract class RateAbstract extends \XLite\Base\SuperClass
{
    const DELIVERY_TIME = 'delivery_time';

    /**
     * Shipping method object
     *
     * @var \XLite\Model\Shipping\Method
     */
    protected $method;

    /**
     * Shipping markup object
     *
     * @var \XLite\Model\Shipping\Markup
     */
    protected $markup;

    /**
     * Base rate value
     *
     * @var float
     */
    protected $baseRate = 0;

    /**
     * Markup rate value
     *
     * @var float
     */
    protected $markupRate = 0;

    /**
     * Rate's extra data (real-time rate calculation's details)
     *
     * @var \XLite\Core\CommonCell
     */
    protected $extraData;

    /**
     * Public class constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * getMethod
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * setMethod
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method object
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * getMarkup
     *
     * @return \XLite\Model\Shipping\Markup
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * setMarkup
     *
     * @param \XLite\Model\Shipping\Markup $markup Shipping markup object
     *
     * @return void
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;
    }

    /**
     * getBaseRate
     *
     * @return float
     */
    public function getBaseRate()
    {
        return (float) $this->baseRate;
    }

    /**
     * setBaseRate
     *
     * @param float $baseRate Base rate value
     *
     * @return void
     */
    public function setBaseRate($baseRate)
    {
        $this->baseRate = (float) $baseRate;
    }

    /**
     * getMarkupRate
     *
     * @return float
     */
    public function getMarkupRate()
    {
        if (!\XLite::isFreeLicense()) {
            $handlingFee = $this->getMethod()->getHandlingFeeValue();
            if ($this->getMethod()->getHandlingFeeType() == \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_PERCENT) {
                $handlingFee = ($this->getBaseRate() + $this->markupRate) * $handlingFee / 100;
            }
        } else {
            $handlingFee = 0;
        }

        return (float) $this->markupRate + $handlingFee;
    }

    /**
     * setMarkupRate
     *
     * @param float $markupRate Markup rate value
     *
     * @return void
     */
    public function setMarkupRate($markupRate)
    {
        $this->markupRate = (float) $markupRate;
    }

    /**
     * getExtraData
     *
     * @return \XLite\Core\CommonCell
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * setExtraData
     *
     * @param \XLite\Core\CommonCell $extraData Rate's extra data
     *
     * @return void
     */
    public function setExtraData(\XLite\Core\CommonCell $extraData)
    {
        $this->extraData = $extraData;
    }

    /**
     * Set delivery time
     *
     * @param string $value
     *
     * @return $this
     */
    public function setDeliveryTime($value)
    {
        $extraData = $this->getExtraData();

        if (!$extraData) {
            $extraData = new \XLite\Core\CommonCell;
        }

        $extraData->{static::DELIVERY_TIME} = $value;

        $this->setExtraData($extraData);

        return $this;
    }

    /**
     * Returns delivery time
     *
     * @return string|null
     */
    public function getDeliveryTime()
    {
        $extraData = $this->getExtraData();

        return $extraData ? $extraData->{static::DELIVERY_TIME} : null;
    }

    /**
     * Return prepared delivery time
     *
     * @return string|null
     */
    public function getPreparedDeliveryTime()
    {
        if ($this->getMethod() && $this->getMethod()->getProcessorObject()) {
            return $this->getMethod()->getProcessorObject()->prepareDeliveryTime($this);
        }

        return null;
    }

    /**
     * getTotalRate
     *
     * @return float
     */
    public function getTotalRate()
    {
        return $this->getBaseRate() + $this->getMarkupRate();
    }

    /**
     * Get taxable basis
     *
     * @return float
     */
    public function getTaxableBasis()
    {
        return $this->getBaseRate() + $this->getMarkupRate();
    }

    /**
     * getMethodId
     *
     * @return integer
     */
    public function getMethodId()
    {
        return $this->getMethod() ? $this->getMethod()->getMethodId() : null;
    }

    /**
     * getMethodName
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->getMethod() ? $this->getMethod()->getName() : null;
    }
}
