<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Core;

/**
 * CMSConnector
 */
abstract class CMSConnector extends \XLite\Core\CMSConnectorAbstract implements \XLite\Base\IDecorator
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        $this->widgetsList['\XLite\Module\XC\Upselling\View\Customer\UpsellingProducts'] = 'Related products';
        $this->widgetsList['\XLite\Module\XC\Upselling\View\Customer\UpsellingProducts404'] = 'Related products 404';
    }
}
