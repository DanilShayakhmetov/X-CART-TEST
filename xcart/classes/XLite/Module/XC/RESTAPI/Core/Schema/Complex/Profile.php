<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema\Complex;

/**
 * Profile schema
 */
class Profile implements \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
{
    /**
     * Convert model
     *
     * @param \XLite\Model\AEntity $model            Entity
     * @param boolean              $withAssociations Convert with associations
     *
     * @return array
     */
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations)
    {
        $addresses = array();
        foreach ($model->getAddresses() as $address) {
            $addresses[] = $this->convertAddressModel($address);
        }

        $roles = array();
        foreach ($model->getRoles() as $role) {
            $roles[] = $role->getName();
        }

        return array(
            'profileId'  => $model->getProfileId(),
            'login'      => $model->getLogin(),
            'email'      => $model->getLogin(),
            'status'     => $model->getStatus(),
            'referer'    => $model->getReferer(),
            'firstLogin' => $model->getFirstLogin(),
            'lastLogin'  => $model->getLastLogin(),
            'addresses'  => $addresses,
            'addedDate'  => $model->getAdded() ? date('c', $model->getAdded()) : null,
            'membership' => $model->getMembership() ? $model->getMembership()->getName() : '',
            'roles'      => $roles,
            'firstname'  => $model->getBillingAddress() ? $model->getBillingAddress()->getFirstname() : '',
            'lastname'   => $model->getBillingAddress() ? $model->getBillingAddress()->getLastname() : '',
        );
    }

    /**
     * Convert address model
     * @param  \XLite\Model\Address $address Address
     * @return array
     */
    protected function convertAddressModel(\XLite\Model\Address $address)
    {
        $result = [];

        $backwardsCompatDict = [
            'street'        => 'address',
            'state_id'      => 'state',
            'custom_state'  => 'state',
            'country_code'  => 'country',
        ];

        foreach ($address->getAddressFields() as $field) {
            if (!$field->getAddressField()->getEnabled()) {
                continue;
            }

            $key = $field->getAddressField()->getServiceName();
            if (isset($backwardsCompatDict[$key])) {
                $key = $backwardsCompatDict[$key];
            }

            $result[$key] = $field->getValue();
        }

        if ($address->getCountry() && $address->getCountry()->hasStates()) {
            $result['state'] = $address->getState()->getCode()
                ?: $address->getState()->getState();
        }

        $result['isDefaultShippingAddress'] = $address->getIsShipping();
        $result['isDefaultBillingAddress'] = $address->getIsBilling();

        return $result;
    }

    /**
     * Prepare input
     *
     * @param array $data Data
     *
     * @return array
     */
    public function prepareInput(array $data)
    {
        return [ true, $data ];
    }

    /**
     * Preload data
     *
     * @param \XLite\Model\AEntity $entity Product
     * @param array                $data   Data
     *
     * @return void
     */
    public function preloadData(\XLite\Model\AEntity $entity, array $data)
    {
    }
}
