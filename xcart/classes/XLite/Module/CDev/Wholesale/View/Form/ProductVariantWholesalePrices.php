<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\Form;

/**
 * WholesalePrices form
 */
class ProductVariantWholesalePrices extends \XLite\Module\CDev\Wholesale\View\Form\WholesalePrices
{
    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $list = [];

        $list['page'] = $this->page;
        $list['id'] = $this->getProductVariant()->getId();

        return $list;
    }
}
