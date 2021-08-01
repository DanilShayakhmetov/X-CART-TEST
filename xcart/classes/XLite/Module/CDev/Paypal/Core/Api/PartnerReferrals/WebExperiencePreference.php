<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-web_experience_preference
 *
 * @property string partner_logo_url
 * @property string return_url
 * @property string return_url_description
 * @property string action_renewal_url
 * @property bool   show_add_credit_card
 * @property bool   show_mobile_confirm
 * @property bool   use_mini_browser
 * @property bool   use_hua_email_confirmation
 */
class WebExperiencePreference extends PayPalModel
{
    /**
     * @return string
     */
    public function getPartnerLogoUrl()
    {
        return $this->partner_logo_url;
    }

    /**
     * @param string $partner_logo_url
     *
     * @return WebExperiencePreference
     */
    public function setPartnerLogoUrl($partner_logo_url)
    {
        $this->partner_logo_url = $partner_logo_url;

        return $this;
    }

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
     * @return WebExperiencePreference
     */
    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnUrlDescription()
    {
        return $this->return_url_description;
    }

    /**
     * @param string $return_url_description
     *
     * @return WebExperiencePreference
     */
    public function setReturnUrlDescription($return_url_description)
    {
        $this->return_url_description = $return_url_description;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionRenewalUrl()
    {
        return $this->action_renewal_url;
    }

    /**
     * @param string $action_renewal_url
     *
     * @return WebExperiencePreference
     */
    public function setActionRenewalUrl($action_renewal_url)
    {
        $this->action_renewal_url = $action_renewal_url;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowAddCreditCard()
    {
        return $this->show_add_credit_card;
    }

    /**
     * @param bool $show_add_credit_card
     *
     * @return WebExperiencePreference
     */
    public function setShowAddCreditCard($show_add_credit_card)
    {
        $this->show_add_credit_card = $show_add_credit_card;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowMobileConfirm()
    {
        return $this->show_mobile_confirm;
    }

    /**
     * @param bool $show_mobile_confirm
     *
     * @return WebExperiencePreference
     */
    public function setShowMobileConfirm($show_mobile_confirm)
    {
        $this->show_mobile_confirm = $show_mobile_confirm;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseMiniBrowser()
    {
        return $this->use_mini_browser;
    }

    /**
     * @param bool $use_mini_browser
     *
     * @return WebExperiencePreference
     */
    public function setUseMiniBrowser($use_mini_browser)
    {
        $this->use_mini_browser = $use_mini_browser;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseHuaEmailConfirmation()
    {
        return $this->use_hua_email_confirmation;
    }

    /**
     * @param bool $use_hua_email_confirmation
     *
     * @return WebExperiencePreference
     */
    public function setUseHuaEmailConfirmation($use_hua_email_confirmation)
    {
        $this->use_hua_email_confirmation = $use_hua_email_confirmation;

        return $this;
    }
}
