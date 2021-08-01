<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Test e-mail controller
 */
class TestEmail extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Email Notifications');
    }

    /**
     * Flag to has email error
     *
     * @return string
     */
    public function hasTestEmailError()
    {
        return '' !== (string)\XLite\Core\Session::getInstance()->test_email_error;
    }

    /**
     * Return error test email sending
     *
     * @return string
     */
    public function getTestEmailError()
    {
        $error = (string)\XLite\Core\Session::getInstance()->test_email_error;

        \XLite\Core\Session::getInstance()->test_email_error = '';

        return $error;
    }

    /**
     * Action to send test email notification
     *
     * @return void
     */
    protected function doActionTestEmail()
    {
        $request = \XLite\Core\Request::getInstance();

        $error = \XLite\Core\Mailer::sendTestEmail(
            $request->test_from_email_address,
            $request->test_to_email_address,
            $request->test_email_body
        );

        if ($error) {
            \XLite\Core\Session::getInstance()->test_email_error = $error;
            \XLite\Core\TopMessage::getInstance()->addError('Error of test e-mail sending: ' . $error);

        } else {
            \XLite\Core\TopMessage::getInstance()->add('Test e-mail have been successfully sent');
        }
    }
}