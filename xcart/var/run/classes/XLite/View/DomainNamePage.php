<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Module\XC\Onboarding\Main;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class DomainNamePage extends \XLite\View\AView
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return [
            'cloud_domain_name'
        ];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            $this->getDir() . '/style.less',
        ];
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return [
            $this->getDir() . '/controller.js',
        ];
    }

    /**
     * @return mixed
     */
    protected function getDomainName()
    {
        return \XLite::getInstance()->getOptions(['host_details', 'http_host_orig'])
            ?: \XLite::getInstance()->getOptions(['host_details', 'http_host']);
    }

    /**
     * @return mixed
     */
    protected function isSelfDomain()
    {
        return false;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'domain_name_page';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return \XLite::getInstance()->getOptions(['service', 'is_cloud'])
            && !\XLite::getInstance()->getOptions(['service', 'is_trial']);
    }
}