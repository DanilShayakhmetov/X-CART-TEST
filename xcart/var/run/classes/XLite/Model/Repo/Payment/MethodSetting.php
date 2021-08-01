<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Payment;

/**
 * Payment method setting repository
 *
 * @Api\Operation\Read(modelClass="XLite\Model\Payment\MethodSetting", summary="Retrieve payment method setting by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Payment\MethodSetting", summary="Retrieve all payment method settings")
 * @Api\Operation\Update(modelClass="XLite\Model\Payment\MethodSetting", summary="Update payment method setting by id")
 */
class MethodSetting extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Alternative record identifiers
     *
     * @
     * var array
     */
    protected $alternativeIdentifier = [
        ['name', 'payment_method'],
    ];
}
