<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Controller\Admin;

use XLite\Controller\Features\TaxControllerTrait;

/**
 * Taxes controller
 */
class SalesTax extends \XLite\Controller\Admin\AAdmin
{
    use TaxControllerTrait;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Taxes');
    }

    // {{{ Widget-specific getters

    /**
     * Get tax
     *
     * @return object
     */
    public function getTax()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\SalesTax\Model\Tax')->getTax();
    }

    // }}}

    // {{{ Actions

    /**
     * Update tax rate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $tax = $this->getTax();

        $this->updateTaxSettings($tax);

        $optionNames = [
            'ignore_memberships',
            'addressType',
            'taxableBase',
        ];

        $optionsData = [];
        $request = \XLite\Core\Request::getInstance();
        foreach ($optionNames as $optionName) {
            $optionsData[] = [
                'name'     => $optionName,
                'category' => 'CDev\\SalesTax',
                'value'    => $request->$optionName ?: 'N',
            ];

        }
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOptions($optionsData);

        $list = new \XLite\Module\CDev\SalesTax\View\ItemsList\Model\Rate;
        $list->processQuick();

        $list2 = new \XLite\Module\CDev\SalesTax\View\ItemsList\Model\ShippingRate;
        $list2->processQuick();

        $rates = \XLite\Core\Database::getRepo('XLite\Module\CDev\SalesTax\Model\Tax\Rate')
            ->findBy(['tax' => null]);

        foreach ($rates as $rate) {
            $tax->addRates($rate);
            $rate->setTax($tax);
        }

        \XLite\Core\TopMessage::addInfo('Tax rates have been updated successfully');
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Remove tax rate
     *
     * @return void
     */
    protected function doActionRemoveRate()
    {
        $rate = null;
        $rateId = \XLite\Core\Request::getInstance()->id;

        foreach ($this->getTax()->getRates() as $r) {
            if ($r->getId() == $rateId) {
                $rate = $r;
                break;
            }
        }

        if ($rate) {
            $this->getTax()->getRates()->removeElement($rate);
            \XLite\Core\Database::getEM()->remove($rate);
            \XLite\Core\TopMessage::addInfo('Tax rate has been deleted successfully');
            $this->setPureAction(true);

        } else {
            $this->valid = false;
            \XLite\Core\TopMessage::addError('Tax rate has not been deleted successfully');
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Switch tax state
     *
     * @return void
     */
    protected function doActionSwitch()
    {
        $tax = $this->getTax();
        $tax->setEnabled(!$tax->getEnabled());
        \XLite\Core\Database::getEM()->flush();
        $this->setPureAction(true);

        if ($tax->getEnabled()) {
            \XLite\Core\TopMessage::addInfo('Tax has been enabled successfully');

        } else {
            \XLite\Core\TopMessage::addInfo('Tax has been disabled successfully');
        }
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('switch', 'expand', 'collapse'));
    }

    // }}}
}
