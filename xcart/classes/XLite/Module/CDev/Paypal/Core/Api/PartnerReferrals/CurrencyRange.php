<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-currency_range
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Currency minimum_amount
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Currency maximum_amount
 */
class CurrencyRange extends PayPalModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Currency
     */
    public function getMinimumAmount()
    {
        return $this->minimum_amount;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Currency $minimum_amount
     *
     * @return CurrencyRange
     */
    public function setMinimumAmount($minimum_amount)
    {
        $this->minimum_amount = $minimum_amount;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Currency
     */
    public function getMaximumAmount()
    {
        return $this->maximum_amount;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Currency $maximum_amount
     *
     * @return CurrencyRange
     */
    public function setMaximumAmount($maximum_amount)
    {
        $this->maximum_amount = $maximum_amount;

        return $this;
    }
}
