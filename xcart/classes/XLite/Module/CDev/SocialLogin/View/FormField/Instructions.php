<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View\FormField;


/**
 * Configuration instructions widget for SocialLogin
 */
class Instructions extends \XLite\View\FormField\Label\ALabel
{
    const TYPE_FACEBOOK = 'Facebook';
    const TYPE_GOOGLE   = 'Google';
    const TYPE_APPLE    = 'Apple';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/SocialLogin/common.css';
        $list[] = 'modules/CDev/SocialLogin/style.css';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            'kbLink' => new \XLite\Model\WidgetParam\TypeString('KB Link', ''),
            'kbLabel' => new \XLite\Model\WidgetParam\TypeString('KB Label', ''),
        );
    }

    /**
     * Process all occurencies of WEB_LC_ROOT
     *
     * @param mixed $str Input string
     *
     * @return string
     */
    public function processUrls($str)
    {
        return str_replace(
            \XLite\Model\Base\Catalog::WEB_LC_ROOT,
            htmlentities(\XLite::getInstance()->getShopURL(null)),
            $str
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/SocialLogin/form_field/instructions.twig';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SocialLogin/form_field/instructions.twig';
    }

    /**
     * @return string
     */
    public function getKBLink()
    {
        return static::t($this->getParam('kbLink'));
    }

    /**
     * @return string
     */
    public function getKBLabel()
    {
        return static::t($this->getParam('kbLabel'));
    }

    /**
     * @param string $param Connection name
     *
     * @return boolean
     */
    public function isConnected($param)
    {
        switch ($param) {
            case self::TYPE_FACEBOOK:
                if (\XLite\Core\Config::getInstance()->CDev->SocialLogin->fb_client_id
                    && \XLite\Core\Config::getInstance()->CDev->SocialLogin->fb_client_secret)
                    return true;
                break;
            case self::TYPE_GOOGLE:
                if (\XLite\Core\Config::getInstance()->CDev->SocialLogin->gg_client_id
                    && \XLite\Core\Config::getInstance()->CDev->SocialLogin->gg_client_secret)
                    return true;
                break;
            case self::TYPE_APPLE:
                if (\XLite\Core\Config::getInstance()->CDev->SocialLogin->apple_identifier)
                    return true;
                break;
        }

        return false;
    }

    /**
     * @param string $param Connection name
     *
     * @return string
     */
    public function getConnectedLabel($param)
    {
        switch ($param) {
            case self::TYPE_FACEBOOK:
                return static::t('Configured. First you need to create a Facebook application for your site.');
                break;
            case self::TYPE_GOOGLE:
                return static::t('Configured. To enable Google login, you first need to create OAuth2 client ID for your site.');
                break;
            case self::TYPE_APPLE:
                return static::t('Configured. To configure web authentication.');
                break;
        }

        return null;
    }

    /**
     * @param string $param Connection name
     *
     * @return string
     */
    public function getNotConnectedLabel($param)
    {
        switch ($param) {
            case self::TYPE_FACEBOOK:
                return static::t('Not configured. First you need to create a Facebook application for your site.');
                break;
            case self::TYPE_GOOGLE:
                return static::t('Not configured. To enable Google login, you first need to create OAuth2 client ID for your site.');
                break;
            case self::TYPE_APPLE:
                return static::t('Not configured. To configure web authentication.');
                break;
        }

        return null;
    }
}
