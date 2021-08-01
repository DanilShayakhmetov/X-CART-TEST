<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-person_details
 *
 * @property string                                                                         email_address
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Name                       name
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails[]             phone_contacts
 * @property \PayPal\Api\Address                                                            home_address
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent                date_of_birth
 * @property string                                                                         nationality_country_code
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IdentityDocument[]         identity_documents
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountOwnerRelationship[] account_owner_relationships
 */
class PersonDetails extends PayPalModel
{
    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->email_address;
    }

    /**
     * @param string $email_address
     *
     * @return PersonDetails
     */
    public function setEmailAddress($email_address)
    {
        $this->email_address = $email_address;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Name $name
     *
     * @return PersonDetails
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails[]
     */
    public function getPhoneContacts()
    {
        return $this->phone_contacts;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails[] $phone_contacts
     *
     * @return PersonDetails
     */
    public function setPhoneContacts($phone_contacts)
    {
        $this->phone_contacts = $phone_contacts;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails $phone_contact
     *
     * @return PersonDetails
     */
    public function addPhoneContact($phone_contact)
    {
        if (!$this->getPhoneContacts()) {

            return $this->setPhoneContacts([$phone_contact]);
        }

        return $this->setPhoneContacts(
            array_merge($this->getPhoneContacts(), [$phone_contact])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PhoneDetails $phone_contact
     *
     * @return PersonDetails
     */
    public function removePhoneContact($phone_contact)
    {
        return $this->setPhoneContacts(
            array_diff($this->getPhoneContacts(), [$phone_contact])
        );
    }

    /**
     * @return \PayPal\Api\Address
     */
    public function getHomeAddress()
    {
        return $this->home_address;
    }

    /**
     * @param \PayPal\Api\Address $home_address
     *
     * @return PersonDetails
     */
    public function setHomeAddress($home_address)
    {
        $this->home_address = $home_address;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\DateOfEvent $date_of_birth
     *
     * @return PersonDetails
     */
    public function setDateOfBirth($date_of_birth)
    {
        $this->date_of_birth = $date_of_birth;

        return $this;
    }

    /**
     * @return string
     */
    public function getNationalityCountryCode()
    {
        return $this->nationality_country_code;
    }

    /**
     * @param string $nationality_country_code
     *
     * @return PersonDetails
     */
    public function setNationalityCountryCode($nationality_country_code)
    {
        $this->nationality_country_code = $nationality_country_code;

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
     * @return PersonDetails
     */
    public function setIdentityDocuments($identity_documents)
    {
        $this->identity_documents = $identity_documents;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IdentityDocument $identity_document
     *
     * @return PersonDetails
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
     * @return PersonDetails
     */
    public function removeIdentityDocument($identity_document)
    {
        return $this->setIdentityDocuments(
            array_diff($this->getIdentityDocuments(), [$identity_document])
        );
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountOwnerRelationship[]
     */
    public function getAccountOwnerRelationships()
    {
        return $this->account_owner_relationships;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountOwnerRelationship[] $account_owner_relationships
     *
     * @return PersonDetails
     */
    public function setAccountOwnerRelationships($account_owner_relationships)
    {
        $this->account_owner_relationships = $account_owner_relationships;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountOwnerRelationship $account_owner_relationship
     *
     * @return PersonDetails
     */
    public function addName($account_owner_relationship)
    {
        if (!$this->getAccountOwnerRelationships()) {

            return $this->setAccountOwnerRelationships([$account_owner_relationship]);
        }

        return $this->setAccountOwnerRelationships(
            array_merge($this->getAccountOwnerRelationships(), [$account_owner_relationship])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\AccountOwnerRelationship $account_owner_relationship
     *
     * @return PersonDetails
     */
    public function removeName($account_owner_relationship)
    {
        return $this->setAccountOwnerRelationships(
            array_diff($this->getAccountOwnerRelationships(), [$account_owner_relationship])
        );
    }
}
