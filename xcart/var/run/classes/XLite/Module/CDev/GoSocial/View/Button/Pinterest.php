<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Button;

/**
 * Pinterest button
 *
 * @ListChild (list="buttons.share", weight="400")
 */
class Pinterest extends \XLite\Module\CDev\GoSocial\View\Button\ASocialButton
{
    /**
     * Define button attributes
     *
     * @return array
     */
    protected function defineButtonParams()
    {
        $list = array();

        $product = $this->getModelObject();
        $image = $product->getImage();

        $list['data-pin-do'] = 'buttonPin';
        $list['data-pin-custom'] = 'true';
        $list['data-pin-url'] = $product->getFrontURL();
        $list['data-pin-media'] = isset($image)
            ? $image->getFrontURL()
            : null;
        $list['data-pin-description'] = $product->getName();


        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $image = $this->getModelObject()->getImage();

        return parent::isVisible()
            && isset($image)
            && $image->isExists()
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->pinterest_use;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonType()
    {
        return self::BUTTON_CLASS_PINTEREST;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonLabel()
    {
        return static::t('Pin');
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = [
            'url' => '//assets.pinterest.com/js/pinit.js',
            'async'=> true,
            'defer'=> true,
        ];

        return $list;
    }

    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [ 'modules/CDev/GoSocial/button/social_button_pinterest.css' ]
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/button/social_button_pinterest.twig';
    }
}
