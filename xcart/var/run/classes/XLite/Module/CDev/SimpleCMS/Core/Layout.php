<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Core;

use XLite\Core\URLManager;

/**
 * Layout manager
 */
 class Layout extends \XLite\Module\QSL\CloudSearch\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo);

        return $url ?: parent::getLogo();
    }

    /**
     * Get logo alt
     *
     * @return string
     */
    public function getLogoAlt()
    {
        return \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo_alt
            ?: parent::getLogoAlt();
    }

    /**
     * Get logo to invoice
     *
     * @return string
     */
    public function getInvoiceLogo()
    {
        $partUrl = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo);

        if (!$partUrl) {
            return parent::getInvoiceLogo();
        }

        $imageSizes = \XLite\Logic\ImageResize\Generator::defineImageSizes();
        $invoiceLogoSizes = $imageSizes['XLite\Model\Image\Common\Logo']['Invoice'];

        $url = "var/images/logo/" . implode('.', $invoiceLogoSizes) . '/' . $partUrl;
        $path = LC_DIR_ROOT . $url;

        if (!file_exists($path)) {
            return parent::getInvoiceLogo();
        }

        switch ($this->currentInterface) {
            case \XLite::PDF_INTERFACE:
            case \XLite::MAIL_INTERFACE:
                return $url;

            default:
                return URLManager::getShopURL(
                    $url,
                    null,
                    [],
                    URLManager::URL_OUTPUT_SHORT
                );
        }
    }

    /**
     * Return favicon resource path
     *
     * @return string
     */
    public function getFavicon()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->favicon);

        return $url ?: parent::getFavicon();
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->appleIcon);

        return $url ?: parent::getAppleIcon();
    }
}
