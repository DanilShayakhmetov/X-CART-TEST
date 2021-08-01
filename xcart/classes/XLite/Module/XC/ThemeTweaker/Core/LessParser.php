<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

use XLite\Core\Layout;

/**
 * LESS parser wrapper
 */
class LessParser extends \XLite\Core\LessParser implements \XLite\Base\IDecorator
{
    /**
     * Define the new LESS variables for the specific resource
     *
     * @param array $data Resource data
     *
     * @return array
     */
    protected function getModifiedLESSVars($data)
    {
        $lessVars = parent::getModifiedLESSVars($data);

        $xlite = \XLite::getInstance();
        $layout = Layout::getInstance();

        if ($layout->getInterface() == \XLite::ADMIN_INTERFACE) {
            $lessVars['admin-skin'] = '\'' . $xlite->getShopURL(
                    $layout->getWebPath()
                ) . '\'';
        }

        if ($layout->getInterface() == \XLite::CUSTOMER_INTERFACE) {
            $lessVars['customer-skin'] = '\'' . $xlite->getShopURL(
                    $layout->getWebPath()
                ) . '\'';
        }

        return $lessVars;
    }
}
