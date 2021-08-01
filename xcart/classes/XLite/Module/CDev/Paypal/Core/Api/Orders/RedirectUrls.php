<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-redirect_urls
 *
 * @property string return_url
 * @property string cancel_url
 */
class RedirectUrls extends PayPalModel
{
    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->return_url;
    }

    /**
     * @param string $return_url
     *
     * @return RedirectUrls
     */
    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancel_url;
    }

    /**
     * @param string $cancel_url
     *
     * @return RedirectUrls
     */
    public function setCancelUrl($cancel_url)
    {
        $this->cancel_url = $cancel_url;

        return $this;
    }
}
