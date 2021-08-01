<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Storefront status
 *
 * @ListChild (list="admin.main.page.header", weight="300", zone="admin")
 */
class StorefrontStatus extends \XLite\View\AView implements ProviderInterface
{
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'main_center/page_container_parts/header_parts/storefront_status.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'main_center/page_container_parts/header_parts/storefront_status.twig';
    }

    /**
     * Check - storefront switcher is visible or not
     *
     * @return boolean
     */
    protected function isTogglerVisible()
    {
        return \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return [
            'class' => [
                'storefront-status',
                (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'closed' : 'opened'),
            ],
        ];
    }

    /**
     * Get toggler tag attributes
     *
     * @return array
     */
    protected function getTogglerTagAttributes()
    {
        return [
            'class' => [
                'toggler',
                (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'off' : 'on'),
            ],
        ];
    }

    /**
     * Get switch link
     *
     * @return string
     */
    protected function getLink()
    {
        return $this->buildURL(
            'storefront',
            '',
            [
                'action'    => (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'open' : 'close'),
                'returnURL' => $this->getURL(),
            ]
        );
    }

    /**
     * Get accessible shop URL
     *
     * @return string
     */
    protected function getOpenedShopURL()
    {
        return \XLite::getController()->getAccessibleShopURL(true);
    }

    /**
     * Get accessible shop URL
     *
     * @return string
     */
    protected function getClosedShopURL()
    {
        return \XLite::getController()->getAccessibleShopURL(false);
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Do you really want to close storefront?' => static::t('Do you really want to close storefront?'),
        ];
    }

    /**
     * @return string
     */
    protected function getStorefrontLinkLabel()
    {
        return $this->isTogglerVisible()
            ? static::t('Your store is')
            : static::t('Your store');
    }
}
