<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use Includes\Utils\Module\Manager;

/**
 * Tax banner page
 *
 * @ListChild (list="taxes.help.section", zone="admin", weight=10)
 */
class TaxBanner extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'tax_classes';
        $result[] = 'sales_tax';
        $result[] = 'vat_tax';
        $result[] = 'canadian_taxes';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'tax_banner/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'tax_banner/body.twig';
    }

    /**
     * Define list of help links
     *
     * @return array
     */
    protected function defineHelpLinks()
    {
        $links   = [];
        $links[] = [
            'title' => static::t('Setting up tax'),
            'url'   => static::t('https://kb.x-cart.com/taxes/tax_guide.html'),
        ];
        $links[] = [
            'title' => static::t('Setting up tax classes'),
            'url'   => static::t('https://kb.x-cart.com/taxes/setting_up_tax_classes.html'),
        ];
        $links[] = [
            'title' => static::t('Setting up European / UK Taxes'),
            'url'   => static::t('https://kb.x-cart.com/taxes/UK_EU_taxes/'),
        ];
        $links[] = [
            'title' => static::t('Setting up US Taxes'),
            'url'   => static::t('https://kb.x-cart.com/taxes/us_taxes/'),
        ];
        $links[] = [
            'title' => static::t('Setting up Canadian taxes'),
            'url'   => static::t('https://kb.x-cart.com/taxes/canadian_taxes/'),
        ];

        return $links;
    }

    /**
     * Get list of help links
     *
     * @return array
     */
    protected function getHelpLinks()
    {
        return $this->defineHelpLinks();
    }

    /**
     * Return AvaTax Module link
     *
     * @return string
     */
    protected function getAvaTaxLink()
    {
        return Manager::getRegistry()->getModuleServiceURL('XC', 'AvaTax');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Controller\Admin\TaxClasses::isEnabled();
    }
}
