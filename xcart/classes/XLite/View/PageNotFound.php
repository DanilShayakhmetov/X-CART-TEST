<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Page not found block
 *
 * @ListChild (list="center")
 * @ListChild (list="admin.center", zone="admin")
 */
class PageNotFound extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = \XLite::TARGET_404;

        return $result;
    }

    /**
     * Add NOINDEX in meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();
        $list[] = '<meta name="robots" content="noindex,nofollow"/>';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return '404.twig';
    }

    /**
     * @return string
     */
    protected function getEmail()
    {
        return \XLite\Core\Config::getInstance()->CDev->ContactUs->showEmail
            ? \XLite\Core\Mailer::getUsersDepartmentMail()
            : '';
    }

    /**
     * @return string
     */
    protected function getPhone()
    {
        return \XLite\Core\Config::getInstance()->Company->company_phone;
    }

    /**
     * Is show email address
     *
     * @return boolean
     */
    protected function isShowEmail()
    {
        return \XLite\Core\Config::getInstance()->CleanURL->show_email_404;
    }

    /**
     * @return string
     */
    protected function getPageType()
    {
        $pageType = 'default';

        if (\XLite\Core\Request::getInstance()->category_id) {
            if (\XLite\Core\Request::getInstance()->product_id) {
                $pageType = 'product';
            } else {
                $pageType = 'category';
            }
        }

        return $pageType;
    }

    /**
     * @return string
     */
    protected function getRegularText()
    {
        $regularText = static::t('default-404-text');
        $regularText = str_replace('{home}', static::buildURL(), $regularText);
        $regularText = str_replace('{contact us}', static::buildURL('contact_us'), $regularText);

        return $regularText;
    }
}

