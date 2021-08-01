<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Controller\Admin;

/**
 * GlobalTab
 */
abstract class GlobalTabAbstract extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        $this->addLocationNode(static::t('Product tabs'), $this->buildURL('global_tabs'));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $tabId = \XLite\Core\Request::getInstance()->tab_id;
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab');

        return ($tabId && $tab = $repo->find($tabId))
            ? $tab->getName()
            : static::t('New Tab');
    }

    /**
     * Action update
     */
    protected function doActionUpdateGlobalTab()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('global_tab', '', [
                'tab_id' => $this->getModelForm()->getModelObject()->getGlobalTab()->getId()
            ]));
        }
    }

    /**
     * Action update
     */
    protected function doActionUpdateGlobalTabAndClose()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('global_tabs'));
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\XC\CustomProductTabs\View\Model\GlobalTab';
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog');
    }
}