<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Taxes;

/**
 * Taxes widget (admin)
 */
class TaxClasses extends Settings
{
    /**
     * @return string
     */
    protected function getItemsTemplate()
    {
        return 'tax_classes/classes.twig';
    }

    /**
     * @return string
     */
    protected function getFormTarget()
    {
        return 'tax_classes';
    }
}
