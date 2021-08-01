<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Controller\Admin;

use XLite\Core\Database;
use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;
use XLite\Module\XC\MultiCurrency\Model\Repo\ActiveCurrency;

/**
 * Currencies management page controller
 */
class Currencies extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Localization');
    }

    /**
     * Do action 'add_currency'
     *
     * @return void
     */
    public function doActionAddCurrency()
    {
        $data = \XLite\Core\Request::getInstance()->getData();

        if (
            isset($data['currency_id'])
            && !empty($data['currency_id'])
        ) {
            $repo = Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency');
            list(, $errors) = $repo->addCurrency($data['currency_id']);

            if (!empty($errors)) {
                if (in_array(ActiveCurrency::ERRORS_RATE_REQUEST, $errors)) {
                    \XLite\Core\TopMessage::addWarning('Currency rate request was failed, please, set it manually');
                }

                if (in_array(ActiveCurrency::ERRORS_CURRENCY_NOT_FOUND, $errors)) {
                    \XLite\Core\TopMessage::addError('Currency with id X {{id}} not found');
                }
            }
        }
    }

    /**
     * Do action 'update'
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $data = \XLite\Core\Request::getInstance()->getData();

        $configTable = Database::getRepo('XLite\Model\Config')->getTableName();

        $list = new \XLite\Module\XC\MultiCurrency\View\ItemsList\Model\Currency\ActiveCurrencies;
        $list->processQuick();

        if (isset($data['rate_provider'])) {
            Database::getEM()->getConnection()->exec(
                "UPDATE $configTable SET value='"
                . $data['rate_provider']
                . '\' WHERE category=\'XC\\\\MultiCurrency\' AND name=\'rateProvider\''
            );
        }

        if (isset($data['currency_converter_api_key'])) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'name'     => 'currency_converter_api_key',
                'category' => 'XC\MultiCurrency',
                'value'    => $data['currency_converter_api_key'],
            ]);
        }

        if (isset($data['update_interval'])) {
            Database::getEM()->getConnection()->exec(
                "UPDATE $configTable SET value='"
                . $data['update_interval']
                . '\' WHERE category=\'XC\\\\MultiCurrency\' AND name=\'updateInterval\''
            );
        }

        if (isset($data['trailing_zeroes'])) {
            Database::getEM()->getConnection()->exec(
                "UPDATE $configTable SET value='"
                . ($data['trailing_zeroes'] == 1 ? 1 : 0)
                . '\' WHERE category=\'General\' AND name=\'trailing_zeroes\''
            );
        }

        \XLite\Core\Config::updateInstance();

        MultiCurrency::getInstance()->updateRates();
    }

    /**
     * Do action 'update_rates'
     *
     * @return void
     */
    public function doActionUpdateRates()
    {
        MultiCurrency::getInstance()->updateRates();
    }

    /**
     * Check if the form ID validation is needed
     *
     * @return boolean
     */
    protected function isActionNeedFormId()
    {
        return parent::isActionNeedFormId() && ('update_rates' != $this->getAction());
    }
}