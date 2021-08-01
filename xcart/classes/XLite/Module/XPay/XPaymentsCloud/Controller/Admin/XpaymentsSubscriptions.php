<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Admin;

use XLite\Controller\Admin\AAdmin;
use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XPaymentsCloud\ApiException;
use XPaymentsCloud\Response;

/**
 * Subscriptions list controller
 */
class XpaymentsSubscriptions extends AAdmin
{
    /**
     * Is search visible
     *
     * @return boolean
     */
    public function isSearchVisible()
    {
        return true;
    }

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return isset($searchParams[$paramName])
            ? $searchParams[$paramName]
            : null;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $subscriptionId = isset(\XLite\Core\Request::getInstance()->subscription_id)
            ? \XLite\Core\Request::getInstance()->subscription_id
            : 0;

        $subscription = \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription')
            ->find($subscriptionId);

        return ($subscription)
            ? static::t('X-Payments subscription #{{id}}', ['id' => $subscriptionId])
            : static::t('X-Payments subscriptions');
    }

    /**
     * Define the session cell name for the subscriptions list
     *
     * @return string
     */
    protected function getSessionCellName()
    {
        return \XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model\Subscription::getSessionCellName();
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $searchParams = \XLite\Core\Session::getInstance()->{$this->getSessionCellName()};

        return is_array($searchParams) ? $searchParams : [];
    }

    /**
     * Update list
     *
     * @return void
     * @throws \XPaymentsCloud\ApiException
     */
    protected function doActionUpdate()
    {

        $data = \XLite\Core\Request::getInstance()->getData();

        foreach ($data['data'] as $id => $row) {

            /** @var Subscription $subscription */
            $subscription = \XLite\Core\Database::getRepo('XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription')
                ->find($id);

            if (!$subscription) {
                continue;
            }

            $updateParams = [];

            if (
                isset($row['status'])
                && $row['status'] != $subscription->getStatus()
            ) {
                $updateParams['status'] = $row['status'];
            }

            if (
                isset($row['card'])
                && (
                    !$subscription->getCardId()
                    || $row['card'] != $subscription->getCardId()
                )
            ) {
                $updateParams['cardId'] = $row['card'];
            }

            if (!empty($updateParams)) {
                try {
                    $api = XPaymentsCloud::getClient();
                    $response = $api->doUpdateSubscription($subscription->getXpaymentsSubscriptionPublicId(), $updateParams);
                    $xpaymentsSubscription = $response->getSubscription();

                    if ($xpaymentsSubscription) {

                        $subscription->createOrUpdateFromXpaymentsSubscription($xpaymentsSubscription)->update();
                        if (
                            \XPaymentsCloud\Model\Subscription::STATUS_ACTIVE === $xpaymentsSubscription->getStatus()
                            || \XPaymentsCloud\Model\Subscription::STATUS_STOPPED === $xpaymentsSubscription->getStatus()
                        ) {
                            $subscription->processStatusChangeNotify();
                        }

                    }
                } catch (ApiException $e) {
                    XPaymentsCloud::log($e->getMessage());
                }
            }

            if (
                isset($row['shipping_address'])
                && (
                    !$subscription->getShippingAddress()
                    || $row['shipping_address'] != $subscription->getShippingAddress()->getAddressId())
            ) {
                $shippingAddress = \XLite\Core\Database::getRepo('XLite\Model\Address')
                    ->find($row['shipping_address']);
                if ($shippingAddress) {
                    $oldAddress = $subscription->getShippingAddress();
                    $newAddress = $shippingAddress->cloneEntity();
                    $newAddress->setProfile(null);
                    $newAddress->create();
                    $subscription->setShippingAddress($newAddress);
                    if ($oldAddress->getProfile() === null) {
                        $oldAddress->delete();
                    }
                }
            }

            \XLite\Core\TopMessage::getInstance()->addInfo(static::t('Subscriptions have been successfully updated'));
        }
    }

}
