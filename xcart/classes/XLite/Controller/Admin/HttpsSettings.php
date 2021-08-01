<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * HTTPS settings page controller
 */
class HttpsSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('HTTPS settings');
    }

    /**
     * Return url for read more link on invalid SSL
     *
     * @return string
     */
    public function getReadMoreUrl()
    {
        return 'https://www.sslshopper.com/ssl-checker.html';
    }

    /**
     * Enable HTTPS
     *
     * @return void
     */
    public function doActionEnableHTTPS()
    {
        $this->switchHTTPS(true);

        \XLite\Core\TopMessage::addInfo('HTTPS option has been successfully enabled');

        $this->setReturnURL($this->buildURL($this->get('target')));
    }

    /**
     * Disable HTTPS
     *
     * @return void
     */
    public function doActionDisableHTTPS()
    {
        $this->switchHTTPS(false);

        \XLite\Core\TopMessage::addInfo('HTTPS option has been disabled');

        $this->setReturnURL($this->buildURL($this->get('target')));
    }

    /**
     * Switch HTTPS options
     *
     * @param boolean $enableHTTPS Value
     *
     * @return void
     */
    protected function switchHTTPS($enableHTTPS)
    {
        $options = [
            'customer_security',
            'admin_security',
        ];

        foreach ($options as $option) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'name'     => $option,
                'category' => 'Security',
                'value'    => $enableHTTPS,
            ]);
        }
    }
}
