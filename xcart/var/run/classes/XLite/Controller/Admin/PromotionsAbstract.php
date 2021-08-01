<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Common promotions controller
 */
abstract class PromotionsAbstract extends \XLite\Controller\Admin\AAdmin
{
    /**
     * FIXME- backward compatibility
     *
     * @var array
     */
    protected $params = ['target', 'page'];

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $page = $this->getPage();
        $pages = $this->getPages();

        return $pages[$page] ?? static::t('Discounts');
    }

    /**
     * Returns purchase license URL
     *
     * @return string
     */
    public function getPurchaseLicenseURL()
    {
        return \XLite\Core\Marketplace::getBusinessPurchaseURL();
    }

    // {{{ Pages

    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = [];

        if (\XLite::isFreeLicense()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
        ) {
            $list['volume_discounts'] = [
                'name' => static::t('Volume discounts'),
                'tpl' => 'promotions/volume_discounts.twig',
            ];

            $list['coupons'] = [
                'name' => static::t('Coupons'),
                'tpl' => 'promotions/coupons.twig',
            ];
        }

        return $list;
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = [];

        foreach (static::getPagesStatic() as $key => $page) {
            if ($this->checkPageACL($page)) {
                $list[$key] = $page['name'];
            }
        }

        return $list;
    }

    /**
     * Check page permissions and return true if page is allowed
     *
     * @param array $page Page data
     *
     * @return boolean
     */
    protected function checkPageACL($page)
    {
        $result = true;

        if (empty($page['public_access'])
            && !\XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
        ) {
            $result = !empty($page['permission'])
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed($page['permission']);
        }

        return $result;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = [];

        foreach (static::getPagesStatic() as $key => $page) {
            $list[$key] = $page['tpl'];
        }

        return $list;
    }

    // }}}
}
