<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View;

use XLite;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Main;

/**
 * Controller widget extension
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AdminSearchPopup extends \XLite\View\AView
{
    const PRODUCT_LIMIT = 8;

    /**
     * Get commented data
     *
     * @return array
     */
    protected function getCommentedData()
    {
        $lng = [
            'lbl_showing_results_for'  => static::t('cs_showing_results_for'),
            'lbl_see_details'          => static::t('cs_see_details'),
            'lbl_see_more_results_for' => static::t('cs_see_more_results_for'),
            'lbl_suggestions'          => static::t('cs_suggestions'),
            'lbl_products'             => static::t('cs_products'),
            'lbl_categories'           => static::t('cs_categories'),
            'lbl_pages'                => static::t('cs_pages'),
            'lbl_manufacturers'        => static::t('cs_manufacturers'),
            'lbl_did_you_mean'         => static::t('cs_did_you_mean'),
        ];

        $client = new ServiceApiClient();

        $data = [
            'cloudSearch' => [
                'apiUrl'        => $client->getSearchApiUrl(),
                'apiKey'        => $client->getApiKey(),
                'priceTemplate' => static::formatPrice(0),
                'selector'      => '.searchpanel-product-admin-main input[name="substring"]',
                'lng'           => $lng,
                'requestData'   => [
                    'limits'     => [
                        'products'      => static::PRODUCT_LIMIT,
                        'categories'    => 0,
                        'pages'         => 0,
                        'manufacturers' => 0,
                    ],
                ],
            ],
        ];

        return $data;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/CloudSearch/init.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CloudSearch/admin_search_popup.less';

        return $list;
    }

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'modules/QSL/CloudSearch/loader.js';
        $list[static::RESOURCE_JS][] = 'modules/QSL/CloudSearch/lib/handlebars.min.js';
        $list[static::RESOURCE_JS][] = 'modules/QSL/CloudSearch/lib/jquery.hoverIntent.min.js';

        $list[static::RESOURCE_CSS][] = 'modules/QSL/CloudSearch/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CloudSearch/admin_search_popup.twig';
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'product_list';

        return $result;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return Main::isAdminSearchEnabled();
    }
}
