<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\FormField\Select;

use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Helper;

class SpecialService extends \XLite\View\FormField\Select\Multiple
{
    const PARAM_SERVICE_ID = 'serviceId';

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $specialServices = Helper::getSpecialServices();
        $serviceId       = $this->getParam(self::PARAM_SERVICE_ID);

        $result = [];
        foreach ($specialServices as $key => $specialService) {
            if (empty($serviceId) || in_array($serviceId, $specialService['serviceId'], true)) {
                $result[$key] = $specialService['name'];
            }
        }

        return $result;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SERVICE_ID => new \XLite\Model\WidgetParam\TypeString('ServiceId', ''),
        ];
    }
}
