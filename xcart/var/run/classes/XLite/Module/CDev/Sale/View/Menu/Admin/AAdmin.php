<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Module\CDev\Sale\View\Menu\Admin;

abstract class AAdmin extends \XLite\Module\CDev\SalesTax\View\Menu\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        parent::__construct();

        $this->addRelatedTarget('sale_discount', 'promotions', [], ['page' => 'sale_discounts']);
    }
}
