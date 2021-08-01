<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\GenerateData\Generators;

use XLite\Core\Config;

/**
 * Class Profile
 * @package XLite\Console\Command\GenerateData\Generators
 */
class Profile
{
    protected $type;

    public function __construct(
        $type
    ) {
        $this->type = $type;
    }

    /**
     * @param $profileData
     *
     * @return \XLite\Model\Profile
     */
    public function generate($profileData)
    {
        $profile = $this->generateProfile($this->type, $profileData);
        \XLite\Core\Database::getEM()->persist($profile);

        return $profile;
    }

    /**
     * @param $type
     * @param $userData
     *
     * @return \XLite\Model\Profile
     */
    protected function generateProfile($type, $userData)
    {
        $profile = new \XLite\Model\Profile();

        $profile->setLogin($userData->email);

        $address = new \XLite\Model\Address;

        $address->setProfile($profile);
        $address->setIsShipping(true);
        $address->setIsBilling(true);
        $address->setIsWork(true);
        $address->map(array(
            'firstname'     => $userData->name->first,
            'lastname'      => $userData->name->last,
        ));

        $address->setCountry(\XLite\Core\Database::getRepo('XLite\Model\Country')->find($userData->nat));

        $location = $userData->location;

        if ($address->getCountry()->hasStates()) {
            $address->setCustomState(null);
            $address->setState($address->getCountry()->getStates()[0]);
        } else {
            $address->setState(null);
            $address->setCustomState($location->state);
        }

        $address->setZipcode($location->postcode);
        $address->setCity($location->city);
        $address->setStreet($location->street->name.' '.$location->street->number);

        $profile->addAddresses($address);

        //Set the same password "guest" for all profiles
        $profile->setPassword('084e0343a0486ff05530df6c705c8bb4');

        $profile->prepareBeforeUpdate();

        $this->setProfileType($profile, $this->type);

        return $profile;
    }

    /**
     * @param \XLite\Model\Profile $profile
     * @param $type
     */
    protected function setProfileType(\XLite\Model\Profile $profile, $type)
    {
        if ($type === 'admin') {
            $profile->setAccessLevel(100);

            $role = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneByName('Administrator');
            $role->addProfiles($profile);

            $profile->setRole($role);
        }

    }

    /**
     * @return array
     */
    public static function getAllowedTypes()
    {
        return ['customer', 'admin'];
    }
}
