<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * @param array|null $status
 *
 * @return array|null
 */
return function ($status = null) {

    if (class_exists('\XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan')) {

        $repo = \XLite\Core\Database::getRepo('XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan');

        if (null === $status) {
            // $currentPosition - current iteration (in this case, 0 needs to be returned)
            // $maxPosition - total number of iterations
            return [0, $repo->countForExport()];
        }

        if (is_array($status)) {

            $currentPosition = $status[0];
            $maxPosition = $status[1];

            // Max rows to process in the iteration
            $chunkSize = 1000;

            // Iterate through entities
            $iterator = $repo->getExportIterator($currentPosition, $chunkSize);
            $iterator->rewind();

            // Initial value for internal counter
            $i = 0;

            while ($iterator->valid()) {
                /** @var \XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan $legacyPlan */
                $legacyPlan = $iterator->current();

                // Get current subscription plan entity
                $legacyPlan = $legacyPlan[0];

                /* actions */
                $product = $legacyPlan->getProduct();
                $xpaymentsCloudPlanRepo = \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan');
                $xpaymentsCloudPlan = $xpaymentsCloudPlanRepo->findOneBy(['product' => $product]);
                if (!$xpaymentsCloudPlan) {
                    $xpaymentsCloudPlan = new \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan();
                    \XLite\Core\Database::getEM()->persist($xpaymentsCloudPlan);
                }
                $xpaymentsCloudPlan->setIsSubscription($legacyPlan->getSubscription())
                    ->setSetupFee($legacyPlan->getSetupFee())
                    ->setCalculateShipping($legacyPlan->getCalculateShipping())
                    ->setType($legacyPlan->getType())
                    ->setNumber($legacyPlan->getNumber())
                    ->setPeriod($legacyPlan->getPeriod())
                    ->setReverse($legacyPlan->getReverse())
                    ->setPeriods($legacyPlan->getPeriods())
                    ->setFee($legacyPlan->getFee())
                    ->setProduct($product);
                /* /actions */

                // Increase counter and position values
                $i++;
                $currentPosition++;

                if ($chunkSize <= $i) {
                    // Counter has reached the maximum value - prepare returning value
                    break;
                }

                // Go to next product entity
                $iterator->next();
            }
            // Flush database changes
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Database::getEM()->clear();

            if ($currentPosition >= $maxPosition) {
                return null;
            } else {
                return [$currentPosition, $maxPosition];
            }
        }
    }
};
