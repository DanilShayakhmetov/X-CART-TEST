<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;

class Customer
{
    /**
     * @var array
     */
    protected static $orderTotalsByProfile = [];

    /**
     * @param string               $mc_eid
     * @param \XLite\Model\Profile $profile
     *
     * @return array
     */
    public static function getData($mc_eid, \XLite\Model\Profile $profile): array
    {
        $data = [
            'id'            => (string) ($profile->getProfileId() ?: $mc_eid),
            'email_address' => $profile->getLogin(),
            'opt_in_status' => false,
            'orders_count'  => $profile->getOrdersCount(),
            'total_spent'   => static::getTotalSpent($profile),
        ];

        $profileAddress = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        if ($profileAddress) {
            [$data['first_name'], $data['last_name'], $data['address']] = Address::getDataWithNames($profileAddress);
        }

        return $data;
    }

    /**
     * @param string               $mc_eid
     * @param \XLite\Model\Profile $profile
     * @param bool                 $customerExists
     *
     * @return array
     */
    public static function getDataForOrder($mc_eid, $profile, $customerExists): array
    {
        if ($profile) {
            return !$customerExists
                ? (['email_address' => $profile->getEmail()] + static::getData($mc_eid, $profile))
                : ['id' => (string) $profile->getProfileId()];
        }

        return ['id' => (string) $mc_eid];
    }

    /**
     * @param \XLite\Model\Profile $profile
     */
    protected static function getTotalSpent(\XLite\Model\Profile $profile)
    {
        if ($profile->getOrder() && $profile->getOrder()->getOrigProfile()) {
            $profile = $profile->getOrder()->getOrigProfile();
        }

        $profileId = $profile->getProfileId();

        if (!isset(static::$orderTotalsByProfile[$profileId])) {
            $cnd          = new \XLite\Core\CommonCell();
            $cnd->profile = $profile;
            $orders       = \XLite\Core\Database::getRepo('XLite\Model\Order')->search($cnd);

            if ($orders) {
                static::$orderTotalsByProfile[$profileId] = array_reduce(
                    $orders,
                    function ($carry, $order) {
                        /**
                         * @var \XLite\Model\Order $order
                         */
                        return $order->isProcessed()
                            ? $carry + $order->getTotal()
                            : $carry;
                    },
                    0
                );
            } else {
                static::$orderTotalsByProfile[$profileId] = 0;
            }
        }

        return static::$orderTotalsByProfile[$profileId];
    }
}
