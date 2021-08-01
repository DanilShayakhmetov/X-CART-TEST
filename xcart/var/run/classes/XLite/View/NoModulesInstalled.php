<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use Includes\Utils\Module\Manager;

/**
 * No modules installed
 */
class NoModulesInstalled extends \XLite\View\Dialog
{
    /**
     * Limit
     *
     * @var integer
     */
    protected static $limit = null;

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'No discount modules installed';
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return static::t(
            'To boost your sales try to use Discounts coupons, Sale, Product Advisor, Volume discounts addons. Also you may be interested in all Marketing extensions from our Marketplace',
            $this->getDescriptionData()
        );
    }

    /**
     * Description specific data
     *
     * @return array
     */
    public function getDescriptionData()
    {
        return [
            'discountCoupons' => $this->getModuleURL('Coupons', 'CDev'),
            'sale'            => $this->getModuleURL('Sale', 'CDev'),
            'productAdvisor'  => $this->getModuleURL('ProductAdvisor', 'CDev'),
            'volumeDiscounts' => $this->getModuleURL('VolumeDiscounts', 'CDev'),
            'marketingTag'    => $this->getTagURL('Marketing'),
        ];
    }

    /**
     * Module URL for marketplace
     *
     * @param string $name   Name
     * @param string $author Author
     *
     * @return string
     */
    protected function getModuleURL($name, $author)
    {
        return Manager::getRegistry()->getModuleServiceURL($author, $name);
    }

    /**
     * Tag URL for marketplace
     *
     * @param string $tagName Tag name
     *
     * @return string
     */
    protected function getTagURL($tagName)
    {
        return Manager::getRegistry()->getServiceURL('available-addons', ['tag' => $tagName]);
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'no_modules_installed';
    }

}
