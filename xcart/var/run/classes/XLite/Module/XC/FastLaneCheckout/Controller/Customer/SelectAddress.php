<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\Controller\Customer;

use XLite\Module\XC\FastLaneCheckout\Main;

/**
 * Select address from address book
 */
abstract class SelectAddress extends \XLite\Controller\Customer\SelectAddressAbstract implements \XLite\Base\IDecorator
{
    /**
     * Select address
     *
     * @return void
     */
    protected function doActionSelect()
    {
        $atype = \XLite\Core\Request::getInstance()->atype;
        $addressId = \XLite\Core\Request::getInstance()->addressId;
        $hasEmptyFields = \XLite\Core\Request::getInstance()->hasEmptyFields === 'true'
            ? true
            : false;

        if (Main::isFastlaneEnabled()) {
            $sameAddressState = \XLite\Core\Session::getInstance()->same_address !== null
                ? \XLite\Core\Session::getInstance()->same_address
                : $this->getCart()->getProfile()->isEqualAddress();

            $preserveSameAddress = ($sameAddressState && $atype == 's');

            $this->selectCartAddress($atype, $addressId, $hasEmptyFields, $preserveSameAddress);
            $this->silent = true;
            $this->setSuppressOutput(true);

        } else {
            parent::doActionSelect();
        }
    }
}
