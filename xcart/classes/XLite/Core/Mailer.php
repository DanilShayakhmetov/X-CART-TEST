<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\Core\Mail\Common\AccessLinkCustomer;
use XLite\Core\Mail\Common\DeletedAdmin;
use XLite\Core\Mail\Common\FailedLoginAdmin;
use XLite\Core\Mail\Common\FailedTransactionAdmin;
use XLite\Core\Mail\Common\LowLimitAdmin;
use XLite\Core\Mail\Common\SafeMode;
use XLite\Core\Mail\Common\TestEmail;
use XLite\Core\Mail\Common\UpgradeSafeMode;
use XLite\Core\Mail\Order\CanceledAdmin;
use XLite\Core\Mail\Order\CanceledCustomer;
use XLite\Core\Mail\Order\ChangedAdmin;
use XLite\Core\Mail\Order\ChangedCustomer;
use XLite\Core\Mail\Order\CreatedAdmin as OrderCreatedAdmin;
use XLite\Core\Mail\Order\CreatedCustomer as OrderCreatedCustomer;
use XLite\Core\Mail\Order\FailedAdmin;
use XLite\Core\Mail\Order\FailedCustomer;
use XLite\Core\Mail\Order\ProcessedAdmin;
use XLite\Core\Mail\Order\ProcessedCustomer;
use XLite\Core\Mail\Order\ShippedCustomer;
use XLite\Core\Mail\Order\TrackingCustomer;
use XLite\Core\Mail\Order\WfaCustomer;
use XLite\Core\Mail\Order\BackorderCreatedAdmin;
use XLite\Core\Mail\Profile\CreatedAdmin;
use XLite\Core\Mail\Profile\CreatedCustomer;
use XLite\Core\Mail\Profile\RecoverPasswordAdmin;
use XLite\Core\Mail\Profile\RecoverPasswordCustomer;
use XLite\Core\Mail\Profile\RegisterAnonymous;
use XLite\Model\Order;
use XLite\Model\Payment\Transaction;
use XLite\Model\Profile;

/**
 * Mailer core class
 */
class Mailer extends \XLite\Base\Singleton
{
    /**
     * @param Profile $profile    Profile object
     * @param string  $password   Profile password OPTIONAL
     * @param boolean $byCheckout By checkout flag OPTIONAL
     */
    public static function sendProfileCreated(Profile $profile, $password = null, $byCheckout = false)
    {
        static::sendProfileCreatedAdmin($profile);

        static::sendProfileCreatedCustomer($profile, $password, $byCheckout);
    }

    /**
     * @param Profile $profile Profile object
     */
    public static function sendProfileCreatedAdmin(Profile $profile)
    {
        (new CreatedAdmin($profile))->schedule();
    }

    /**
     * @param Profile $profile    Profile object
     * @param string  $password   Profile password OPTIONAL
     * @param boolean $byCheckout By checkout flag OPTIONAL
     */
    public static function sendProfileCreatedCustomer(
        Profile $profile,
        $password = null,
        $byCheckout = false
    )
    {
        (new CreatedCustomer($profile, $password, $byCheckout))->schedule();
    }

    /**
     * @param Profile $profile  Profile object
     * @param string  $password Profile password
     */
    public static function sendRegisterAnonymousCustomer(Profile $profile, $password)
    {
        (new RegisterAnonymous($profile, $password))->schedule();
    }

    /**
     * @param string $deletedLogin Login of deleted profile
     */
    public static function sendProfileDeleted($deletedLogin)
    {
        static::sendProfileDeletedAdmin($deletedLogin);
    }

    /**
     * @param string $deletedLogin Login of deleted profile
     */
    public static function sendProfileDeletedAdmin($deletedLogin)
    {
        (new DeletedAdmin($deletedLogin))->schedule();
    }

    /**
     * @param string $postedLogin Login that was used in failed login attempt
     */
    public static function sendFailedAdminLoginAdmin($postedLogin)
    {
        (new FailedLoginAdmin(
            $postedLogin,
            Request::getInstance()->getClientIp()
        ))->schedule();
    }

    /**
     * @param Profile $profile              Profile
     * @param string  $userPasswordResetKey User password
     */
    public static function sendRecoverPasswordRequest($profile, $userPasswordResetKey)
    {
        if ($profile->isAdmin()) {
            (new RecoverPasswordAdmin($profile, $userPasswordResetKey))->schedule();
        } else {
            (new RecoverPasswordCustomer($profile, $userPasswordResetKey))->schedule();
        }
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderTrackingInformationCustomer(Order $order)
    {
        (new TrackingCustomer($order))->schedule();
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCreated(Order $order)
    {
        static::sendOrderCreatedAdmin($order);

        static::sendOrderCreatedCustomer($order);
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCreatedAdmin(Order $order)
    {
        (new OrderCreatedAdmin($order))->schedule();
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCreatedCustomer(Order $order)
    {
        (new OrderCreatedCustomer($order))->schedule();
    }

    /**
     * @param Order   $order                      Order model
     * @param boolean $ignoreCustomerNotification Flag: do not send notification to customer
     *                                            OPTIONAL
     */
    public static function sendOrderProcessed(Order $order, $ignoreCustomerNotification = false)
    {
        static::sendOrderProcessedAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderProcessedCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderProcessedAdmin(Order $order)
    {
        $mail = new ProcessedAdmin($order);

        if ($mail::isEnabled()) {
            $mail->schedule();

        } elseif ($order->isJustClosed()) {
            // OrderProcessed notification is disabled - send OrderCreated if order just created by customer
            static::sendOrderCreatedAdmin($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderProcessedCustomer(Order $order)
    {
        $mail = new ProcessedCustomer($order);

        if ($mail::isEnabled()) {
            $mail->schedule();

        } elseif ($order->isJustClosed()) {
            // OrderProcessed notification is disabled - send OrderCreated if order just created by customer
            static::sendOrderCreatedCustomer($order);
        }
    }

    /**
     * @param Order   $order                      Order model
     * @param boolean $ignoreCustomerNotification Flag: do not send notification to customer
     *                                            OPTIONAL
     */
    public static function sendOrderChanged(Order $order, $ignoreCustomerNotification = false)
    {
        static::sendOrderChangedAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderChangedCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderChangedAdmin(Order $order)
    {
        (new ChangedAdmin($order))->schedule();
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderChangedCustomer(Order $order)
    {
        (new ChangedCustomer($order))->schedule();
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderShipped(Order $order)
    {
        static::sendOrderShippedCustomer($order);
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderShippedCustomer(Order $order)
    {
        (new ShippedCustomer($order))->schedule();
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderWaitingForApprove(Order $order)
    {
        static::sendOrderWaitingForApproveCustomer($order);
    }

    /**
     * @param Order $order Order object
     */
    public static function sendOrderWaitingForApproveCustomer(Order $order)
    {
        (new WfaCustomer($order))->schedule();
    }

    /**
     * @param Order   $order                      Order model
     * @param boolean $ignoreCustomerNotification Flag: do not send notification to customer
     *                                            OPTIONAL
     */
    public static function sendOrderFailed(Order $order, $ignoreCustomerNotification = false)
    {
        static::sendOrderFailedAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderFailedCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderFailedAdmin(Order $order)
    {
        (new FailedAdmin($order))->schedule();
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderFailedCustomer(Order $order)
    {
        (new FailedCustomer($order))->schedule();
    }

    /**
     * @param Order   $order                      Order model
     * @param boolean $ignoreCustomerNotification Flag: do not send notification to customer
     *                                            OPTIONAL
     */
    public static function sendOrderCanceled(Order $order, $ignoreCustomerNotification = false)
    {
        static::sendOrderCanceledAdmin($order);

        if (!$ignoreCustomerNotification) {
            static::sendOrderCanceledCustomer($order);
        }
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCanceledAdmin(Order $order)
    {
        (new CanceledAdmin($order))->schedule();
    }

    /**
     * @param Order $order Order model
     */
    public static function sendOrderCanceledCustomer(Order $order)
    {
        (new CanceledCustomer($order))->schedule();
    }

    /**
     * @param Order $order Order model
     */
    public static function sendBackorderCreatedAdmin(Order $order)
    {
        (new BackorderCreatedAdmin($order))->schedule();
    }

    /**
     * @param Profile                        $profile Order model
     * @param \XLite\Model\AccessControlCell $acc     Order model
     */
    public static function sendAccessLinkCustomer(Profile $profile, \XLite\Model\AccessControlCell $acc)
    {
        (new AccessLinkCustomer($profile, $acc))->schedule();
    }

    /**
     * @param string  $key        Access key
     * @param boolean $keyChanged is key new
     */
    public static function sendSafeModeAccessKeyNotification($key, $keyChanged = false)
    {
        (new SafeMode($key, $keyChanged))->schedule();
    }

    /**
     */
    public static function sendUpgradeSafeModeAccessKeyNotification()
    {
        (new UpgradeSafeMode())->schedule();
    }

    /**
     * @param string $from Email address to send test email from
     * @param string $to   Email address to send test email to
     * @param string $body Body test email text OPTIONAL
     *
     * @return string
     */
    public static function sendTestEmail($from, $to, $body = '')
    {
        $mail = new TestEmail($from, $to, $body);
        $mail->send();

        return $mail->getError();
    }

    /**
     * @param array $data Product data
     */
    public static function sendLowLimitWarningAdmin($data)
    {
        (new LowLimitAdmin($data))->schedule();
    }

    /**
     * @param Transaction $transaction
     */
    public static function sendFailedTransactionAdmin(Transaction $transaction)
    {
        (new FailedTransactionAdmin($transaction))->schedule();
    }

    /**
     * Sales department e-mail:
     *
     * @return string
     */
    public static function getOrdersDepartmentMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->orders_department);

        return (is_array($emails) && !empty($emails))
            ? array_shift($emails)
            : static::getSiteAdministratorMail();
    }

    /**
     * Sales department e-mail:
     *
     * @return string[]
     */
    public static function getOrdersDepartmentMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->orders_department);

        return (is_array($emails) && !empty($emails))
            ? $emails
            : static::getSiteAdministratorMails();
    }

    /**
     * Customer relations e-mail
     *
     * @return string
     */
    public static function getUsersDepartmentMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->users_department);

        return (is_array($emails) && !empty($emails)) ? array_shift($emails) : '';
    }

    /**
     * Customer relations e-mail
     *
     * @return string[]
     */
    public static function getUsersDepartmentMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->users_department);

        return (is_array($emails) && !empty($emails)) ? $emails : [];
    }

    /**
     * Customer relations e-mail
     *
     * @return string
     */
    public static function getSupportDepartmentMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->support_department);

        return (is_array($emails) && !empty($emails)) ? array_shift($emails) : '';
    }

    /**
     * Support e-mails
     *
     * @return string[]
     */
    public static function getSupportDepartmentMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->support_department);

        return (is_array($emails) && !empty($emails)) ? $emails : [];
    }

    /**
     * Site administrator e-mail
     *
     * @return string
     */
    public static function getSiteAdministratorMail()
    {
        $emails = @unserialize(Config::getInstance()->Company->site_administrator);

        return (is_array($emails) && !empty($emails)) ? array_shift($emails) : '';
    }

    /**
     * Site administrator e-mail
     *
     * @return string[]
     */
    public static function getSiteAdministratorMails()
    {
        $emails = @unserialize(Config::getInstance()->Company->site_administrator);

        return (is_array($emails) && !empty($emails)) ? $emails : [];
    }
}
