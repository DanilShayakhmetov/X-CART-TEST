<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Product Qty
 */
class ProductQty extends \XLite\Core\Validator\AValidator
{
    /**
     * Product Id (saved)
     *
     * @var integer
     */
    protected $productId;

    /**
     * qty value visible
     *
     * @var integer
     */
    protected $qty;

    /**
     * qty value after form loaded
     *
     * @var integer
     */
    protected $qty_origin;

    /**
     * Constructor
     *
     * @param integer $productId
     * @param integer $qty
     * @param integer $qty_origin
     *
     * @return void
     */
    public function __construct($productId = null, $qty = null, $qty_origin = null)
    {
        parent::__construct();

        if (isset($productId)) {
            $this->productId  = intval($productId);
            $this->qty        = intval($qty);
            $this->qty_origin = intval($qty_origin);
        }
    }

    /**
     * @param mixed $data
     *
     * @return void
     */
    public function validate($data)
    {
        if (!\XLite\Core\Converter::isEmptyString($data) && $this->qty !== $this->qty_origin) {
            $current = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->productId);
            if ($current && $this->qty_origin != $current->getAmount()) {
                $this->throwQtyError();
            }
        }
    }

    /**
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    protected function throwQtyError()
    {
        throw $this->throwError(\XLite\Core\Translation::lbl('Product quantity has changed'));
    }
}
