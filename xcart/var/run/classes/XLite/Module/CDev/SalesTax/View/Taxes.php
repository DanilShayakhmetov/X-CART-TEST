<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\View;

/**
 * Taxes widget (admin)
 */
class Taxes extends \XLite\View\Taxes\Settings
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/SalesTax/style.css';

        return $list;
    }

    /**
     * @return string
     */
    protected function getItemsTemplate()
    {
        return 'modules/CDev/SalesTax/rates.twig';
    }

    /**
     * @return string
     */
    protected function getOptionFieldsTemplate()
    {
        return 'modules/CDev/SalesTax/options.twig';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/SalesTax/admin.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getFormTarget()
    {
        return 'sales_tax';
    }

    /**
     * Get tax
     *
     * @return object
     */
    public function getTax()
    {
        return \XLite::getController()->getTax();
    }

    /**
     * Get CSS classes for dialog block
     *
     * @return string
     */
    protected function getDialogCSSClasses()
    {
        $result = parent::getDialogCSSClasses() . ' edit-sales-tax';

        if (\XLite\Core\Config::getInstance()->CDev->SalesTax->ignore_memberships) {
            $result .= ' no-memberships';
        }

        if ('P' != \XLite\Core\Config::getInstance()->CDev->SalesTax->taxableBase) {
            $result .= ' no-taxbase';
        }

        return $result;
    }
}
