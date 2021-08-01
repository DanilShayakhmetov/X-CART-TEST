<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Tax classes controller
 */
class TaxClasses extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Check - is current place enabled or not
     *
     * @return boolean
     */
    static public function isEnabled()
    {
        return true;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Taxes');
    }

    /**
     * Update tax rate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\View\ItemsList\Model\TaxClass();
        $list->processQuick();
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionToggleTaxJar()
    {
        $this->silent = true;

        $value = !\XLite\Core\Config::getInstance()->XC->TaxJar->taxcalculation;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'XC\\TaxJar',
                'name'     => 'taxcalculation',
                'value'    => $value,
            )
        );

        \XLite\Core\TopMessage::addInfo(
            $value
                ? 'TaxJar tax calculation is enabled'
                : 'TaxJar tax calculation is disabled'
        );

        $this->translateTopMessagesToHTTPHeaders();
    }
}
