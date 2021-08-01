<?php

namespace XLite\Module\Kliken\GoogleAds\View\Page\Admin;

use XLite\Module\Kliken\GoogleAds\Logic\Helper;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class KgaSettings extends \XLite\View\AView
{
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [Helper::PAGE_SLUG]
        );
    }

    protected function getDefaultTemplate()
    {
		if (Helper::hasAccountInfo()) {
            return 'modules/Kliken/GoogleAds/page/kga_settings/dashboard.twig';
        } else {
            return 'modules/Kliken/GoogleAds/page/kga_settings/getstarted.twig';
        }
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Kliken/GoogleAds/page/kga_settings/settings.css';

        return $list;
    }
}
