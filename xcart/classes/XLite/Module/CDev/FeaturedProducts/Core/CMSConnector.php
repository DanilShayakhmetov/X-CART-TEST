<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\Core;

/**
 * CMSConnector
 */
abstract class CMSConnector extends \XLite\Core\CMSConnector implements \XLite\Base\IDecorator
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        $this->widgetsList['\XLite\Module\CDev\FeaturedProducts\View\Customer\FeaturedProducts'] = 'Featured products';
        $this->widgetsList['\XLite\Module\CDev\FeaturedProducts\View\Customer\FeaturedProducts404'] = 'Featured products 404';
    }
}
