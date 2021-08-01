<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

use XLite\View\HttpsCheckerTrait;

/**
 * IntegrityCheck sticky panel
 */
class HttpsSettings extends \XLite\View\Base\FormStickyPanel
{
    use HttpsCheckerTrait;

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' https-settings-panel');

        return $class;
    }

    /**
     * Buttons list (cache)
     *
     * @var array
     */
    protected $buttonsList;

    /**
     * Get buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        if (!isset($this->buttonsList)) {
            $this->buttonsList = $this->defineButtons();
        }

        return $this->buttonsList;
    }

    /**
     * Get URL of the page where SSL certificate can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite::getXCartURL('https://www.x-cart.com/ssl');
    }

    /**
     * @return string
     */
    protected function getEnableHttpsUrl()
    {
        return \XLite::getController()->buildURL('https_settings', 'enable_HTTPS');
    }

    /**
     * @return string
     */
    protected function getDisableHttpsUrl()
    {
        return \XLite::getController()->buildURL('https_settings', 'disable_HTTPS');
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = [];

        $noSsl = !$this->isAvailableHTTPS() || !$this->isValidSSL();

        if ($this->isEnabledHTTPS()) {
            $list['disable'] = $this->getDisableHttpsButton();
        } else {
            $list['enable'] = $this->getEnableHttpsButton($noSsl);
        }

        if ($noSsl) {
            $list['purchase'] = $this->getPurchaseButton();
        }

        return $list;
    }

    /**
     * @param bool $confirm
     *
     * @return \XLite\View\AView
     */
    protected function getEnableHttpsButton($confirm = false)
    {
        return $this->getWidget(
            [
                'confirmText' => $confirm
                    ? static::t('Are you sure you want to enable https anyway?')
                    : null,
                'style'       => 'action regular-main-button always-enabled',
                'label'       => static::t('Enable HTTPS'),
                'jsCode'      => 'self.location="' . $this->getEnableHttpsUrl() . '"',
            ],
            ($confirm ? 'XLite\View\Button\ConfirmRegular' : 'XLite\View\Button\Regular')
        );
    }

    /**
     * @return \XLite\View\AView
     */
    protected function getDisableHttpsButton()
    {
        return $this->getWidget(
            [
                'style'  => 'action regular-main-button always-enabled',
                'label'  => static::t('Disable HTTPS'),
                'jsCode' => 'self.location="' . $this->getDisableHttpsUrl() . '"',
            ],
            'XLite\View\Button\Regular'
        );
    }

    /**
     * Get "save" widget
     *
     * @return \XLite\View\AView
     */
    protected function getPurchaseButton()
    {
        return $this->getWidget(
            [
                'style'  => 'action regular-button always-enabled',
                'label'  => static::t('Purchase SSL certificate'),
                'jsCode' => 'window.open("' . $this->getPurchaseURL() . '", "_blank")',
            ],
            'XLite\View\Button\Regular'
        );
    }
}
