<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Popup;

/**
 * Available domain dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class DomainIsAvailable extends \XLite\View\SimpleDialog
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getDomainName();
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'domain_is_available';

        return $list;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return $this->getDir() . '/body.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    protected function getDir()
    {
        return 'domain_name_page/popup';
    }

    /**
     * @return string[]
     */
    protected function getRegistrars()
    {
        return [
            'godaddy.com'          => 'https://godaddy.com/domainsearch/find?checkAvail=1&tmskey=&domainToCheck=%s',
            'networksolutions.com' => 'https://www.networksolutions.com/domain-name-registration/index.jsp ',
            'enom.com'             => 'https://www.enom.com/domains/search-results?query=%s',
            'name.com'             => 'https://www.name.com/domain/search/%s',
            'register.com'         => 'https://www.register.com/domain-name-registration/domain-name-search-results.jsp ',
        ];
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function getLink($url)
    {
        return sprintf($url, $this->getDomainName());
    }

    /**
     * @return mixed|null
     */
    protected function getDomainName()
    {
        return \XLite\Core\Request::getInstance()->domain_name;
    }
}
