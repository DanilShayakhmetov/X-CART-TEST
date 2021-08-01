<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Controller\Admin;

use XLite\Module\CDev\USPS\Main;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\RequestException;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\TokenStorage\FactoryException;

/**
 * USPS module settings page controller
 */
class Usps extends \XLite\Controller\Admin\ShippingSettings
{
    /**
     * Returns shipping options
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions()
    {
        $list = [];

        $CODRelatedOptions = ['first_class_mail_type', 'use_cod_price', 'cod_price'];
        foreach (parent::getOptions() as $option) {
            if (!in_array($option->getName(), $CODRelatedOptions, true)
                || $this->isUSPSCODPaymentEnabled()
            ) {
                $list[] = $option;
            }

            if ('cacheOnDeliverySeparator' === $option->getName()) {
                $list[] = new \XLite\Model\Config([
                    'name'        => 'cod_status',
                    'type'        => 'XLite\View\FormField\Input\Checkbox\OnOff',
                    'value'       => $this->isUSPSCODPaymentEnabled() ? true : false,
                    'orderby'     => $option->getOrderby() + 1,
                    'option_name' => static::t('"Cash on delivery" status'),
                ]);
            }
        }

        return $list;
    }

    /**
     * @return string
     */
    public function getDataProvider()
    {
        return \XLite\Core\Config::getInstance()->CDev->USPS->dataProvider;
    }

    /**
     * @return string
     */
    public function getRegistrationURL()
    {
        return Main::getRequestFactory()->getRegistrationURL();
    }

    /**
     * getOptionsCategory
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'CDev\USPS';
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\USPS\View\Model\Settings';
    }

    /**
     * Get shipping processor
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->USPS;

        return 'pitneyBowes' === $config->dataProvider
            ? new \XLite\Module\CDev\USPS\Model\Shipping\Processor\PB()
            : new \XLite\Module\CDev\USPS\Model\Shipping\Processor\USPS();
    }

    /**
     * Returns current processor id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'usps';
    }

    /**
     * Returns request data
     *
     * @return array
     */
    protected function getRequestData()
    {
        $list               = parent::getRequestData();
        $list['dimensions'] = serialize($list['dimensions']);

        return $list;
    }

    /**
     * Check if 'Cash on delivery (FedEx)' payment method enabled
     *
     * @return boolean
     */
    protected function isUSPSCODPaymentEnabled()
    {
        return \XLite\Module\CDev\USPS\Model\Shipping\Processor\USPS::isCODPaymentEnabled();
    }

    /**
     * Get schema of an array for test rates routine
     *
     * @return array
     */
    protected function getTestRatesSchema()
    {
        $schema = parent::getTestRatesSchema();

        foreach (['srcAddress', 'dstAddress'] as $k) {
            unset($schema[$k]['city'], $schema[$k]['state']);
        }

        unset($schema['dstAddress']['type']);

        return $schema;
    }

    /**
     * Get input data to calculate test rates
     *
     * @param array $schema  Input data schema
     * @param array &$errors Array of fields which are not set
     *
     * @return array
     */
    protected function getTestRatesData(array $schema, &$errors)
    {
        $data = parent::getTestRatesData($schema, $errors);

        $config = \XLite\Core\Config::getInstance()->CDev->USPS;

        $package = [
            'weight'   => $data['weight'],
            'subtotal' => $data['subtotal'],
            'length'   => $config->dimensions[0],
            'width'    => $config->dimensions[1],
            'height'   => $config->dimensions[2],
        ];

        $data['packages'] = [$package];

        unset($data['weight'], $data['subtotal']);

        return $data;
    }

    /**
     * Do action 'Update'
     *
     * @return void
     * @throws \Exception
     */
    public function doActionUpdate()
    {
        $config    = \XLite\Core\Config::getInstance()->CDev->USPS;
        $pbEmailId = $config->pbEmailId;
        $pbSandbox = $config->pbSandbox;

        if (\XLite\Core\Request::getInstance()->dataProvider === 'pitneyBowes'
            && $pbSandbox !== \XLite\Core\Request::getInstance()->pbSandbox
        ) {
            $pbEmailId = '';
            Main::getRequestFactory()->dropToken();
        }

        parent::doActionUpdate();

        if (\XLite\Core\Request::getInstance()->dataProvider === 'pitneyBowes'
            && $pbEmailId !== \XLite\Core\Request::getInstance()->pbEmailId
        ) {
            if (\XLite\Core\Request::getInstance()->pbEmailId) {
                $requestFactory = Main::getRequestFactory();

                try {
                    $pbEmailId           = \XLite\Core\Request::getInstance()->pbEmailId;
                    $merchantInfoRequest = $requestFactory->createMerchantInfoRequest($pbEmailId);
                    $merchantInfo        = $merchantInfoRequest->performRequest();

                    $this->setPBShipperId($merchantInfo['postalReportingNumber']);

                } catch (FactoryException $e) {
                    Main::log($e->getMessage());
                    \XLite\Core\TopMessage::addWarning('Unable to get merchant info');
                    $this->setPBShipperId('');

                } catch (RequestException $e) {
                    Main::log($e->getMessage());
                    \XLite\Core\TopMessage::addWarning('Unable to get merchant info');
                    $this->setPBShipperId('');
                }

            } else {
                $this->setPBShipperId('');
            }
        }
    }

    protected function setPBShipperId($shipperId)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            [
                'category' => 'CDev\USPS',
                'name'     => 'pbShipperId',
                'value'    => $shipperId,
            ]
        );
    }

    /**
     * Refresh list of available USPS shipping methods
     *
     * @return void
     */
    protected function doActionRefresh()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->USPS;

        // Prepare default input data
        $data               = [];
        $data['packages']   = [];
        $data['packages'][] = [
            'weight'   => 5,
            'subtotal' => 50,
            'length'   => $config->length,
            'width'    => $config->width,
            'height'   => $config->height,
        ];
        $data['srcAddress'] = [
            'country' => 'US',
            'zipcode' => '10001',
        ];

        // Prepare several destination addresses
        $dstAddresses   = [];
        $dstAddresses[] = [
            'country' => 'US',
            'zipcode' => '90001',
        ];
        $dstAddresses[] = [
            'country' => 'CA',
            'zipcode' => 'V7P 1S0',
        ];
        $dstAddresses[] = [
            'country' => 'GB',
            'zipcode' => 'EC1A 1BB',
        ];
        $dstAddresses[] = [
            'country' => 'CN',
            'zipcode' => '100001',
        ];

        foreach ($dstAddresses as $addr) {

            $data['dstAddress'] = $addr;

            // Get rates for each destination address.
            // All non-existing methods will be created after this
            $rates = $this->getProcessor()->getRates($data, true);
        }

        $this->setReturnURL(
            $this->buildURL('shipping_methods', null, ['processor' => 'usps'])
        );
    }
}
