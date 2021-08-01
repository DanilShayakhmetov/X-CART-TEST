<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-business_details
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails       phone_contacts
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Address            business_address
 * @property string                                                                 business_type
 * @property string                                                                 category
 * @property string                                                                 sub_category
 * @property string                                                                 purpose_code
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessName[]     names
 * @property string                                                                 business_description
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent[]      event_dates
 * @property string[]                                                               website_urls
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\CurrencyRange      annual_sales_volume_range
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\CurrencyRange      average_monthly_volume_range
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IdentityDocument[] identity_documents
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\EmailContact[]     email_contacts
 */
class BusinessDetails extends PayPalModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails
     */
    public function getPhoneContacts()
    {
        return $this->phone_contacts;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails $phone_contacts
     *
     * @return BusinessDetails
     */
    public function setPhoneContacts($phone_contacts)
    {
        $this->phone_contacts = $phone_contacts;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Address
     */
    public function getBusinessAddress()
    {
        return $this->business_address;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Address $business_address
     *
     * @return BusinessDetails
     */
    public function setBusinessAddress($business_address)
    {
        $this->business_address = $business_address;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessType()
    {
        return $this->business_type;
    }

    /**
     * @param string $business_type
     *
     * @return BusinessDetails
     */
    public function setBusinessType($business_type)
    {
        $this->business_type = $business_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return BusinessDetails
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubCategory()
    {
        return $this->sub_category;
    }

    /**
     * @param string $sub_category
     *
     * @return BusinessDetails
     */
    public function setSubCategory($sub_category)
    {
        $this->sub_category = $sub_category;

        return $this;
    }

    /**
     * @return string
     */
    public function getPurposeCode()
    {
        return $this->purpose_code;
    }

    /**
     * @param string $purpose_code
     *
     * @return BusinessDetails
     */
    public function setPurposeCode($purpose_code)
    {
        $this->purpose_code = $purpose_code;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessName[]
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessName[] $names
     *
     * @return BusinessDetails
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessName $name
     *
     * @return BusinessDetails
     */
    public function addName($name)
    {
        if (!$this->getNames()) {

            return $this->setNames([$name]);
        }

        return $this->setNames(
            array_merge($this->getNames(), [$name])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BusinessName $name
     *
     * @return BusinessDetails
     */
    public function removeName($name)
    {
        return $this->setNames(
            array_diff($this->getNames(), [$name])
        );
    }

    /**
     * @return string
     */
    public function getBusinessDescription()
    {
        return $this->business_description;
    }

    /**
     * @param string $business_description
     *
     * @return BusinessDetails
     */
    public function setBusinessDescription($business_description)
    {
        $this->business_description = $business_description;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent[]
     */
    public function getEventDates()
    {
        return $this->event_dates;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent[] $event_dates
     *
     * @return BusinessDetails
     */
    public function setEventDates($event_dates)
    {
        $this->event_dates = $event_dates;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent $event_date
     *
     * @return BusinessDetails
     */
    public function addEventDate($event_date)
    {
        if (!$this->getEventDates()) {

            return $this->setEventDates([$event_date]);
        }

        return $this->setEventDates(
            array_merge($this->getEventDates(), [$event_date])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent $event_date
     *
     * @return BusinessDetails
     */
    public function removeEventDate($event_date)
    {
        return $this->setEventDates(
            array_diff($this->getEventDates(), [$event_date])
        );
    }

    /**
     * @return string[]
     */
    public function getWebsiteUrls()
    {
        return $this->website_urls;
    }

    /**
     * @param string[] $website_urls
     *
     * @return BusinessDetails
     */
    public function setWebsiteUrls($website_urls)
    {
        $this->website_urls = $website_urls;

        return $this;
    }

    /**
     * @param string $website_url
     *
     * @return BusinessDetails
     */
    public function addWebsiteUrl($website_url)
    {
        if (!$this->getEventDates()) {

            return $this->setEventDates([$website_url]);
        }

        return $this->setEventDates(
            array_merge($this->getEventDates(), [$website_url])
        );
    }

    /**
     * @param string $website_url
     *
     * @return BusinessDetails
     */
    public function removeWebsiteUrl($website_url)
    {
        return $this->setEventDates(
            array_diff($this->getEventDates(), [$website_url])
        );
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\CurrencyRange
     */
    public function getAnnualSalesVolumeRange()
    {
        return $this->annual_sales_volume_range;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\CurrencyRange $annual_sales_volume_range
     *
     * @return BusinessDetails
     */
    public function setAnnualSalesVolumeRange($annual_sales_volume_range)
    {
        $this->annual_sales_volume_range = $annual_sales_volume_range;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\CurrencyRange
     */
    public function getAverageMonthlyVolumeRange()
    {
        return $this->average_monthly_volume_range;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\CurrencyRange $average_monthly_volume_range
     *
     * @return BusinessDetails
     */
    public function setAverageMonthlyVolumeRange($average_monthly_volume_range)
    {
        $this->average_monthly_volume_range = $average_monthly_volume_range;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IdentityDocument[]
     */
    public function getIdentityDocuments()
    {
        return $this->identity_documents;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IdentityDocument[] $identity_documents
     *
     * @return BusinessDetails
     */
    public function setIdentityDocuments($identity_documents)
    {
        $this->identity_documents = $identity_documents;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IdentityDocument $identity_document
     *
     * @return BusinessDetails
     */
    public function addIdentityDocument($identity_document)
    {
        if (!$this->getIdentityDocuments()) {

            return $this->setIdentityDocuments([$identity_document]);
        }

        return $this->setIdentityDocuments(
            array_merge($this->getIdentityDocuments(), [$identity_document])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IdentityDocument $identity_document
     *
     * @return BusinessDetails
     */
    public function removeIdentityDocument($identity_document)
    {
        return $this->setIdentityDocuments(
            array_diff($this->getIdentityDocuments(), [$identity_document])
        );
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\EmailContact[]
     */
    public function getEmailContacts()
    {
        return $this->email_contacts;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\EmailContact[] $email_contacts
     *
     * @return BusinessDetails
     */
    public function setEmailContacts($email_contacts)
    {
        $this->email_contacts = $email_contacts;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\EmailContact $email_contact
     *
     * @return BusinessDetails
     */
    public function addEmailContact($email_contact)
    {
        if (!$this->getEmailContacts()) {

            return $this->setEmailContacts([$email_contact]);
        }

        return $this->setEmailContacts(
            array_merge($this->getEmailContacts(), [$email_contact])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\EmailContact $email_contact
     *
     * @return BusinessDetails
     */
    public function removeEmailContact($email_contact)
    {
        return $this->setEmailContacts(
            array_diff($this->getEmailContacts(), [$email_contact])
        );
    }
}
