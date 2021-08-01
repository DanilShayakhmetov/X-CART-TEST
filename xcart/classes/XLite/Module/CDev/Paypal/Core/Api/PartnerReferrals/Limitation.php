<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-limitation
 *
 * @property string   name
 * @property string[] restrictions
 */
class Limitation extends PayPalModel
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Limitation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * @param string[] $restrictions
     *
     * @return Limitation
     */
    public function setRestrictions($restrictions)
    {
        $this->restrictions = $restrictions;

        return $this;
    }

    /**
     * @param string $restriction
     *
     * @return Limitation
     */
    public function addRestriction($restriction)
    {
        if (!$this->getRestrictions()) {

            return $this->setRestrictions([$restriction]);
        }

        return $this->setRestrictions(
            array_merge($this->getRestrictions(), [$restriction])
        );
    }

    /**
     * @param string $restriction
     *
     * @return Limitation
     */
    public function removeRestriction($restriction)
    {
        return $this->setRestrictions(
            array_diff($this->getRestrictions(), [$restriction])
        );
    }
}
