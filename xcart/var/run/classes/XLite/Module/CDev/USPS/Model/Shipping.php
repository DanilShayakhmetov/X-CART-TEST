<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model;

use XLite\Module\CDev\USPS\Model\Shipping\Processor\PB;
use XLite\Module\CDev\USPS\Model\Shipping\Processor\USPS;

/**
 * Common shipping method
 */
 class Shipping extends \XLite\Module\XC\NotFinishedOrders\Model\Shipping implements \XLite\Base\IDecorator
{
    /**
     * @param string $processorId
     *
     * @return null|\XLite\Model\Shipping\Processor\AProcessor
     */
    public static function getProcessorObjectByProcessorId($processorId)
    {
        if ($processorId === 'usps') {
            $result = null;

            $processors = \XLite\Model\Shipping::getInstance()->getProcessors();
            $config = \XLite\Core\Config::getInstance()->CDev->USPS;
            foreach ($processors as $obj) {
                if (($config->dataProvider === 'pitneyBowes' && $obj instanceof PB)
                    || ($config->dataProvider !== 'pitneyBowes' && $obj instanceof USPS)
                ) {
                    $result = $obj;
                    break;
                }
            }

            return $result;

        } else {

            return parent::getProcessorObjectByProcessorId($processorId);
        }
    }
}
