<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use Firebase\JWT\JWT;

/**
 * Close storefront action controller
 */
class Storefront extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check if bus_token cookie is set
     *
     * @return bool
     */
    protected function checkBusToken()
    {
        $cookies = \XLite\Core\Request::getInstance()->getCookieData();

        if (isset($cookies['bus_token'])) {
            $privateKey = \Includes\Utils\ConfigParser::getOptions(['installer_details', 'auth_code']);
            $tokenData = (array) JWT::decode(
                $cookies['bus_token'],
                $privateKey,
                ['HS256']
            );
            
            return $tokenData
                && ($tokenData['read_only_access'] ?? false) !== true;
        }

        return false;
    }

    /**
     * Get access level
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return $this->checkBusToken()
            ? 0
            : parent::getAccessLevel();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return $this->checkBusToken() || parent::checkAccess();
    }

    /**
     * Close storefront
     *
     * @return void
     */
    protected function doActionClose()
    {
        \XLite\Core\Auth::getInstance()->closeStorefront();
        $this->fireEvent();
    }

    /**
     * Close storefront (secure token is not needed for this action)
     *
     * @return void
     */
    protected function doActionCloseWithoutFormIdCheck()
    {
        $this->doActionClose();
    }

    /**
     * Open storefront
     *
     * @return void
     */
    protected function doActionOpen()
    {
        \XLite\Core\Auth::getInstance()->openStorefront();
        $this->fireEvent();
    }

    /**
     * Open storefront (secure token is not needed for this action)
     *
     * @return void
     */
    protected function doActionOpenWithoutFormIdCheck()
    {
        $this->doActionOpen();
    }

    /**
     * Save storefront status in a marketplace storage (secure token is not needed for this action)
     *
     * @return void
     */
    protected function doActionSaveStatusInMarketplaceStorage()
    {
        $auth = \XLite\Core\Auth::getInstance();

        \XLite\Core\Marketplace::getInstance()->setStorefrontActivity(
            !$auth->isClosedStorefront(),
            $auth->getShopKey()
        );
    }

    /**
     * Fire event 
     * 
     * @return void
     */
    protected function fireEvent()
    {
        \XLite\Core\Event::switchStorefront(
            array(
                'opened' => !\XLite\Core\Auth::getInstance()->isClosedStorefront(),
                'link'   => $this->buildURL(
                    'storefront',
                    '',
                    array(
                        'action'    => (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'open' : 'close'),
                    )
                ),
                'privatelink' => $this->getAccessibleShopURL(false),
            )
        );

        if ($this->isAJAX()) {
            $this->silent = true;
            $this->setSuppressOutput(true);
        }
    }
}
