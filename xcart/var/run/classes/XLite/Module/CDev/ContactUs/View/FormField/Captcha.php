<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\View\FormField;

/**
 * Captcha
 */
class Captcha extends \XLite\View\FormField\AFormField
{
    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Module\CDev\ContactUs\Core\ReCaptcha::getInstance()->isConfigured();
    }

    /**
     * @inheritdoc
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_COMPLEX;
    }

    /**
     * @inheritdoc
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/ContactUs/contact_us/fields/field.captcha.twig';
    }
}