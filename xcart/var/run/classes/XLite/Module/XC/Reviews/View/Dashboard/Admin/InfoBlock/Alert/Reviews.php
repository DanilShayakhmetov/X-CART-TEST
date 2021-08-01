<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Dashboard\Admin\InfoBlock\Alert;

use XLite\Core\Auth;

/**
 * @ListChild (list="dashboard.info_block.alerts", weight="200", zone="admin")
 */
class Reviews extends \XLite\View\Dashboard\Admin\InfoBlock\AAlert
{
    /**
     * Maximum reviews count
     */
    const MAX_REVIEWS_COUNT = 100;

    /**
     * @var int
     */
    protected $reviewsCount;

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $result   = parent::getCSSFiles();
        $result[] = 'modules/XC/Reviews/alert.less';

        return $result;
    }

    /**
     * @return int
     */
    protected function getCounter()
    {
        if (null === $this->reviewsCount) {
            $this->reviewsCount = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->search(
                $this->getReviewsSearchParams(),
                true
            );
        }

        return static::MAX_REVIEWS_COUNT <= $this->reviewsCount
            ? (static::MAX_REVIEWS_COUNT - 1) . '+'
            : $this->reviewsCount;
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' product-reviews-reviews';
    }

    /**
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('modules/XC/Reviews/images/icon-new-reviews.svg');
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('New product reviews');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL('reviews');
    }

    /**
     * Get parameters to search product reviews
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getReviewsSearchParams()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_NEW} = 1;
        $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::P_LIMIT}    = [0, static::MAX_REVIEWS_COUNT];

        return $cnd;
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && (Auth::getInstance()->hasRootAccess()
                || Auth::getInstance()->isPermissionAllowed('manage reviews'));
    }
}
