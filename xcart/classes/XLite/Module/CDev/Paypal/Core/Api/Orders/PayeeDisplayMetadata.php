<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-payee_display_metadata
 *
 * @property string                                                 email
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\DisplayPhone display_phone
 * @property string                                                 brand_name
 */
class PayeeDisplayMetadata extends PayPalModel
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
     * @return PayeeDisplayMetadata
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\DisplayPhone
     */
    public function getDisplayPhone()
    {
        return $this->display_phone;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\DisplayPhone $display_phone
     *
     * @return PayeeDisplayMetadata
     */
    public function setDisplayPhone($display_phone)
    {
        $this->display_phone = $display_phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getBrandName()
    {
        return $this->brand_name;
    }

    /**
     * @param string $brand_name
     *
     * @return PayeeDisplayMetadata
     */
    public function setBrandName($brand_name)
    {
        $this->brand_name = $brand_name;

        return $this;
    }
}
