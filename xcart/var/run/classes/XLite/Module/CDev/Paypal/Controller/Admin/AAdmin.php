<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Admin;

/**
 * AAdmin
 *
 */
abstract class AAdmin extends \XLite\Module\XC\Concierge\Controller\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    public function isLocalHost(): bool
    {
        $host = \XLite::getInstance()->getOptions([
            'host_details',
            $this->isHTTPS() ? 'https_host' : 'http_host'
        ]);

        $isIPv4 = preg_match('/^([0-9]{1,3}[\.]){3}[0-9]{1,3}$/', $host);
        $isIPv6 = preg_match('/^((^|:)([0-9a-fA-F]{0,4})){1,8}$/', $host);

        if ($isIPv4 || $isIPv6) {
            return !filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );
        }

        $isTestDomain = preg_match('/\.test/i', $host);

        return ($host === 'localhost') || $isTestDomain;
    }
}
