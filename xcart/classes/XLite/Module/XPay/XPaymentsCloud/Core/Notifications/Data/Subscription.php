<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core\Notifications\Data;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Core\Mailer;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Provider;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription as SubscriptionModel;

/**
 * Subscription provider
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class Subscription extends Provider
{
    use ExecuteCachedTrait;

    /**
     * @param string $templateDir
     *
     * @return SubscriptionModel|null
     */
    public function getData($templateDir): ?SubscriptionModel
    {
        return $this->getSubscription($templateDir);
    }

    /**
     * @param string $templateDir
     *
     * @return string
     */
    public function getName($templateDir): string
    {
        return 'subscription';
    }

    /**
     * @param string $templateDir
     * @param mixed  $value
     *
     * @return array|array[]
     */
    public function validate($templateDir, $value): array
    {
        $result = [];

        if (!$this->findSubscriptionById($value)) {
            $result = [
                [
                    'code'  => 'subscription_nf',
                    'value' => $value,
                ],
            ];
        }

        return $result;
    }

    /**
     * @param string $templateDir
     *
     * @return bool
     */
    public function isAvailable($templateDir): bool
    {
        return (bool)$this->getSubscription($templateDir);
    }

    /**
     * @return string[]
     */
    protected function getTemplateDirectories(): array
    {
        return [
            Mailer::XPAYMENTS_SUBSCRIPTION_ORDER_CREATED,
            Mailer::XPAYMENTS_SUBSCRIPTION_SUBSCRIPTION_FAILED,
            Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED,
            Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_SUCCESSFUL,
            Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_ACTIVE,
            Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_STOPPED,
        ];
    }

    /**
     * @param $templateDir
     *
     * @return null|SubscriptionModel
     */
    protected function getSubscription($templateDir): ?SubscriptionModel
    {
        return $this->executeCachedRuntime(function () use ($templateDir) {
            return $this->findSubscriptionById($this->getValue($templateDir))
                ?: Database::getRepo(SubscriptionModel::class)
                    ->findDumpSubscription();
        });
    }

    /**
     * @param string $variantId
     *
     * @return null|SubscriptionModel
     */
    protected function findSubscriptionById($subscriptionId): ?SubscriptionModel
    {
        return Database::getRepo(SubscriptionModel::class)->findOneBy([
            'id' => $subscriptionId,
        ]);
    }

}
