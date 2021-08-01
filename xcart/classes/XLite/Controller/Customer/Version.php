<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Version
 */
class Version extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Handles the request.
     *
     * @return void
     */
    public function handleRequest()
    {
        $scode = \XLite\Core\Request::getInstance()->scode;

        if (!$scode) {
            $this->display404();
        } else {
            $url = \XLite::getInstance()->getShopURL(
                'service.php#/version?scode=' . $scode
            );
            $this->setReturnURL($url);
        }

        $this->doRedirect();
    }

    /**
     * Stub for the CMS connectors
     *
     * @return boolean
     */
    protected function checkStorefrontAccessibility()
    {
        return true;
    }

}
