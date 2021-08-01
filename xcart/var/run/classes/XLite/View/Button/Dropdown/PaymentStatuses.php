<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

use XLite\View\Button\Features\TooltippedTrait;

/**
 * Order print
 */
class PaymentStatuses extends \XLite\View\Button\Dropdown\ADropdown
{
    use TooltippedTrait;

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $statuses = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Payment')->findBy(
            [],[ 'position' => 'asc' ]
        );

        $list = [];
        $position = 100;
        /** @var \XLite\Model\Order\Status\Payment $status */
        foreach ($statuses as $status) {
            if ($this->isStatusExcluded($status)) {
                continue;
            }

            $additionalData = array(
                'statusToSet' => $status->getId(),
            );

            $list[$status->getId()] = [
                'class' => 'XLite\View\Button\Regular',
                'params'   => [
                    'label'      => $status->getName(),
                    'style'      => 'action link list-action',
                    'action'     => 'changePaymentStatus',
                    'formParams' => $additionalData
                ],
                'position' => $position,
            ];
            $position += 100;
        }

        return $list;
    }

    /**
     * @param $status
     *
     * @return bool
     */
    protected function isStatusExcluded($status)
    {
        $statuses = $this->getExcludedStatuses();
        return in_array($status->getCode(), $statuses, true);
    }

    /**
     * @return array
     */
    protected function getExcludedStatuses()
    {
        return \XLite\Model\Order\Status\Payment::getDisallowedToSetManuallyStatuses();
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('Change payment status for selected');
    }
}
