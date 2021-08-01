<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model;

class PaypalButtonCell extends \XLite\Model\Base\Dump
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var string
     */
    protected $shape;

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size ?: 'responsive';
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color ?: ($this->id === 'credit' ? 'darkblue' : 'gold');
    }

    /**
     * @return string
     */
    public function getShape()
    {
        return $this->shape ?: 'rect';
    }
}
