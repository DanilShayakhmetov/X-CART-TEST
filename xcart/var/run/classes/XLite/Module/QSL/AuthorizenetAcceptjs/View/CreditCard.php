<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\AuthorizenetAcceptjs\View;

/**
 * Credit card widget
 */
class CreditCard extends \XLite\View\CreditCard
{

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AuthorizenetAcceptjs/credit_card_form.twig';
    }

}

