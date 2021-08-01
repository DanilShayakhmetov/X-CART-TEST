<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Model;

use Includes\Utils\Module\Module;

/**
 * @Decorator\Depend("!XC\MultiVendor")
 */
abstract class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Check if current page is page with sale module settings
     *
     * @return boolean
     */
    protected function isSaleModuleSettings()
    {
        return 'module' === $this->getTarget()
            && $this->getModule()
            && Module::buildId('CDev', 'Sale') === $this->getModule();
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        $list = parent::getSchemaFieldsForSection($section);

        if ($this->isSaleModuleSettings()) {
            unset($list['allow_vendors_edit_discounts']);
        }

        return $list;
    }

}
