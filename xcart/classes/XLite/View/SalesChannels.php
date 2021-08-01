<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * SalesChannels
 */
class SalesChannels extends \XLite\View\IFrame
{
    public function getIFrameAttributes()
    {
        return array_replace(
            parent::getIFrameAttributes(),
            [
                'width'  => '1050',
                'height' => '470',
                'id'     => 'sales-channels-iframe'
            ]
        );
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'left_menu/sales_channels/style.css';
        return $list;
    }

    /**
     * @return string
     */
    protected function getSrc()
    {
        return \XLite::getInstance()->getServiceURL('#/iframe/sales-channels', null, [
            'moduleInstallMode' => 'single',
            'max_items'         => $this->getMaxItems()
        ]);
    }

    protected function getMaxItems()
    {
        return 4;
    }
}
