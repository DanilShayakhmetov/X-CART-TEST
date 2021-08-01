<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\DataSource;
use XLite\View\AView;

class DataSources extends AView
{
    use ExecuteCachedTrait;

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getDataSource()
            && $this->getDataWidgets();
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/MultiVendor/notification_editor/sidebar/data_source/request_for_payment_status/script.js'
        ]);
    }

    /**
     * @return Data|null
     */
    protected function getDataSource()
    {
        return \XLite::getController()->getDataSource();
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/data_source/body.twig';
    }

    protected function getDataWidgets()
    {
        return $this->executeCachedRuntime(function () {
            return array_map(
                function ($class) {
                    /* @var DataSource $class */
                    return $class::buildNew($this->getDataSource());
                },
                array_filter(
                    $this->defineDataWidgets(),
                    function ($class) {
                        /* @var DataSource $class */
                        return $class::isApplicable($this->getDataSource());
                    }
                )
            );
        });
    }

    /**
     * @return DataSource[]
     */
    protected function defineDataWidgets()
    {
        return [
            '\XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\Order',
            '\XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\Profile',
            '\XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\Product',
            '\XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\Products',
        ];
    }

    /**
     * @param AView $widget
     */
    protected function displayDataWidget(AView $widget)
    {
        /* @var DataSource|AView $widget */
        $widget->setRenderingContext($this->getRenderingContext());
        $widget->init();
        $widget->display();
    }
}