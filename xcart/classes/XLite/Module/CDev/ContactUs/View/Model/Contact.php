<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\View\Model;


/**
 * Contact
 */
class Contact extends \XLite\View\Model\AModel
{
    /**
     * @inheritdoc
     */
    public function __construct($params = [], $sections = [])
    {
        $this->schemaDefault = [
            'name'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Your name',
                self::SCHEMA_PLACEHOLDER => static::t('Full Name'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'email'     => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text\Email',
                self::SCHEMA_LABEL       => 'Your e-mail',
                self::SCHEMA_PLACEHOLDER => static::t('Email Address'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'subject'   => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Subject',
                self::SCHEMA_PLACEHOLDER => static::t('Subject'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'message'   => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Textarea\Simple',
                self::SCHEMA_LABEL       => 'Message',
                self::SCHEMA_PLACEHOLDER => static::t('Your Message'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'recaptcha' => [
                self::SCHEMA_CLASS => '\XLite\Module\CDev\ContactUs\View\FormField\Captcha',
            ],
        ];

        parent::__construct($params, $sections);
    }

    /**
     * @inheritdoc
     *
     * @return \XLite\Module\CDev\ContactUs\Model\Contact
     */
    protected function getDefaultModelObject()
    {
        return new \XLite\Module\CDev\ContactUs\Model\Contact;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        if (!$value && in_array($name, ['name', 'email'])) {
            $auth = \XLite\Core\Auth::getInstance();
            if ($auth->isLogged()) {
                if ('email' == $name) {
                    $value = $auth->getProfile()->getLogin();

                } elseif (0 < $auth->getProfile()->getAddresses()->count()) {
                    $value = $auth->getProfile()->getAddresses()->first()->getName();
                }
            }
        }

        return $value;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('Message has been sent');
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        $this->validateCaptcha();

        if ($this->isValid()) {
            $this->sendEmail();
        }

        return $this->isValid();
    }

    /**
     * Validate captcha
     */
    protected function validateCaptcha()
    {
        $reCaptcha = \XLite\Module\CDev\ContactUs\Core\ReCaptcha::getInstance();

        if ($reCaptcha->isConfigured()) {
            $data = \XLite\Core\Request::getInstance()->getData();
            $response = $reCaptcha->verify(isset($data['g-recaptcha-response']) ? $data['g-recaptcha-response'] : '');

            if (!$response || !$response->isSuccess()) {
                $this->addErrorMessage('captcha', static::t('Please enter the correct captcha'));
            }
        }
    }

    /**
     * Send email
     */
    protected function sendEmail()
    {
        \XLite\Core\Mailer::sendContactUsMessage(
            $this->getModelObject(),
            \XLite\Core\Config::getInstance()->CDev->ContactUs->email
                ?: \XLite\Core\Mailer::getSupportDepartmentMails()
        );
    }

    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return '\XLite\Module\CDev\ContactUs\View\Form\ContactUs';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Send',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'btn  regular-main-button  submit',
            ]
        );

        return $result;
    }
}