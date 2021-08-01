<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\Recommendations;

use XLite\Core\Marketplace;
use XLite\Core\Marketplace\Constant;
use XLite\Core\Request;

/**
 * Class Modules
 * @ListChild (list="admin.main.page.content.center", zone="admin", weight="30")
 */
class Modules extends \XLite\View\AView
{

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'dashboard/recommendations/modules.twig';
    }

    public function getCSSFiles()
    {
        $css = parent::getCSSFiles();
        $css[] = 'dashboard/recommendations/modules.less';

        return $css;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'dashboard/recommendations/controller.js';

        return $list;
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->hasRootAccess()
            && $this->hasRecommendedAddons();
    }

    /**
     * @return string
     */
    protected function getHeaderTitle()
    {
        return self::t('Recommended add-ons');
    }

    /**
     * @return string
     */
    protected function getIframeSrc()
    {
        $request = Request::getInstance();

        return \XLite::getInstance()->getServiceURL(
            '#/iframe/recommended-modules',
            null,
            [
                'pageParams' => urlencode(
                    json_encode($request->getGetData())
                )
            ]
        );
    }

    /**
     * Return true if current page has recommended addons
     *
     * @return boolean
     */
    protected function hasRecommendedAddons(): bool
    {
        return (bool) array_filter(
            Marketplace::getInstance()->getXC5Notifications(),
            function ($xc5Notification) {
                $request = Request::getInstance();

                if ($xc5Notification[Constant::FIELD_NOTIFICATION_TYPE] === 'module') {
                    foreach ($xc5Notification[Constant::FIELD_NOTIFICATION_PAGE_PARAMS] as $pageParam) {
                        $paramKey   = $pageParam[Constant::FIELD_NOTIFICATION_PARAM_KEY];
                        $paramValue = $pageParam[Constant::FIELD_NOTIFICATION_PARAM_VALUE];

                        if ($paramValue !== $request->{$paramKey}) {
                            return false;
                        }
                    }

                } else {
                    return false;
                }

                return true;
            }
        );
    }
}