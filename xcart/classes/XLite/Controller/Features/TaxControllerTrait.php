<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Features;

/**
 * TaxControllerTrait
 */
trait TaxControllerTrait
{
    abstract public function getTax();

    /**
     * @param $tax
     */
    protected function updateTaxSettings($tax)
    {
        $name = trim(\XLite\Core\Request::getInstance()->name);
        if (0 < strlen($name)) {
            $tax->setName($name);

        } else {
            \XLite\Core\TopMessage::addError('The name of the tax has not been preserved, because that is not filled');
        }

        $currentState = $tax->getEnabled();
        $newState = (bool) \XLite\Core\Request::getInstance()->enabled;

        if ($newState !== $currentState) {
            $tax->setEnabled($newState);

            if ($newState) {
                \XLite\Core\TopMessage::addInfo('Tax has been enabled successfully');
            } else {
                \XLite\Core\TopMessage::addInfo('Tax has been disabled successfully');
            }
        }
    }
}
