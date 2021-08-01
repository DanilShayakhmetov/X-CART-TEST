<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-payee
 *
 * @property string                                                         email
 * @property string                                                         merchant_id
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\PayeeDisplayMetadata payee_display_metadata
 */
class Payee extends PayPalModel
{
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Payee
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @param string $merchant_id
     *
     * @return Payee
     */
    public function setMerchantId($merchant_id)
    {
        $this->merchant_id = $merchant_id;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\PayeeDisplayMetadata
     */
    public function getPayeeDisplayMetadata()
    {
        return $this->payee_display_metadata;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PayeeDisplayMetadata $payee_display_metadata
     *
     * @return Payee
     */
    public function setPayeeDisplayMetadata($payee_display_metadata)
    {
        $this->payee_display_metadata = $payee_display_metadata;

        return $this;
    }
}
