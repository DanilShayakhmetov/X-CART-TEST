<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\Model;

use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Helper;
use XLite\Module\CDev\USPS\View\FormField\Select\SpecialService;

/**
 * USPS configuration form model
 */
class CreateShipment extends \XLite\View\Model\AModel
{
    const SECTION_SHIPMENT_OPTIONS = 'shipmentOptions';

    protected $schemaShipmentOptions;

    public function __construct(array $params = [], array $sections = [])
    {
        $definedSections = [
            self::SECTION_DEFAULT          => '',
            self::SECTION_SHIPMENT_OPTIONS => static::t('Shipment options'),
        ];

        $this->sections = $definedSections + $this->sections;

        $this->schemaDefault         = $this->defineSchemaDefault();
        $this->schemaShipmentOptions = $this->defineSchemaShipmentOptions();

        parent::__construct($params, $sections);
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/CDev/USPS/shipments/create_shipment.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/USPS/shipments/create_shipment.css';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'select2/dist/js/select2.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    public function saveCurrentFormData()
    {
        $requestData = $this->prepareDataForMapping();
        $this->saveFormData($requestData);
    }

    /**
     * Save form fields in session
     *
     * @param mixed $data Data to save
     */
    protected function saveFormData($data)
    {
        $driver = \XLite\Core\Cache::getInstance()->getDriver();
        $driver->save($this->getFormName(), $data === null ? [] : $data);
    }

    /**
     * Return saved data for current form (all or certain field(s))
     *
     * @param string $field Data field to return OPTIONAL
     *
     * @return array
     */
    protected function getSavedForm($field = null)
    {
        $driver = \XLite\Core\Cache::getInstance()->getDriver();

        return $driver->contains($this->getFormName())
            ? $driver->fetch($this->getFormName())
            : [];
    }

    /**
     * Clear form fields in session
     */
    protected function clearFormData()
    {
    }

    protected function defineSchemaDefault()
    {
        $units = \XLite\Core\Config::getInstance()->Units;

        return [
            'methodName' => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Label',
                self::SCHEMA_LABEL => static::t('Method name'),
            ],
            'parcelType' => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Label',
                self::SCHEMA_LABEL => static::t('Parcel type'),
            ],
            'weight'           => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\Weight',
                self::SCHEMA_LABEL => static::t('Weight') . ' (' . $units->weight_symbol . ')',
            ],
            'dimensions'      => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\Dimensions',
                self::SCHEMA_LABEL => static::t('Dimensions') . ' (' . $units->dim_symbol . ')',
            ],
            'girth'           => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\FloatInput',
                self::SCHEMA_LABEL => static::t('Irregular parcel girth'),
            ],
            'specialServices' => [
                self::SCHEMA_CLASS => 'XLite\Module\CDev\USPS\View\FormField\Select\SpecialService',
                self::SCHEMA_LABEL => static::t('Special services'),
            ],
            'CODValue'        => [
                self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Price',
                self::SCHEMA_LABEL      => static::t('COD value'),
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                    ],
                ],
            ],
            'insuranceValue'  => [
                self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Price',
                self::SCHEMA_LABEL      => static::t('Insurance value'),
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                    ],
                ],
            ],
            'labelFormat'       => [
                self::SCHEMA_CLASS => 'XLite\Module\CDev\USPS\View\FormField\Select\LabelFormat',
                self::SCHEMA_LABEL => static::t('Label format'),
            ],
            'labelSize'       => [
                self::SCHEMA_CLASS => 'XLite\Module\CDev\USPS\View\FormField\Select\LabelSize',
                self::SCHEMA_LABEL => static::t('Label size'),
            ],
        ];
    }

    protected function defineSchemaShipmentOptions()
    {
        return [
            'hideTotalCarrierCharge'       => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\OnOff',
                self::SCHEMA_LABEL => static::t('Hide total carrier charge'),
                self::SCHEMA_HELP  => static::t('Hide the carrier shipping charge on the label'),
            ],
            'nonDeliveryOption'            => [
                self::SCHEMA_CLASS => 'XLite\Module\CDev\USPS\View\FormField\Select\NonDeliveryOption',
                self::SCHEMA_LABEL => static::t('Non delivery option'),
                self::SCHEMA_HELP  => static::t('Use this field for instructions in case the package is not delivered.'),
            ],
            'printCustomMessage1'          => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL => static::t('Print custom message 1'),
                self::SCHEMA_HELP  => static::t('This is a user specified message that gets printed on the face of the label. A string of up to 50 characters can be printed on the label.'),

                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 50,
            ],
            'printCustomMessage2'          => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL => static::t('Print custom message 2'),
                self::SCHEMA_HELP  => static::t('This is a user specified message that gets printed on the bottom of the label. A string of up to 50 characters can be printed on the label.'),

                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 50,
            ],
            'shippingLabelSenderSignature' => [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL => static::t('Shipping label sender signature'),
                self::SCHEMA_HELP  => static::t('Adds the sender’s signature and the date on CN22 and CP72 shipping labels. Enter the signature as a string. The Sender’s signature date is automatically populated.'),
            ],
        ];
    }

    /**
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        if ('specialServices' === $name) {
            $method = $this->getShippingMethod();
            if ($method) {
                $data[SpecialService::PARAM_SERVICE_ID]
                    = $method->getCode();
            }
        }

        return parent::getFieldBySchema($name, $data);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $value = null;

        $config = \XLite\Core\Config::getInstance()->CDev->USPS;
        switch ($name) {
            case 'methodName':
                $order   = $this->getOrder();
                /** @var \XLite\Model\Shipping\Method $method */
                $method = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($order->getShippingId());

                $value = $method->getName();

                break;
            case 'parcelType':
                $order   = $this->getOrder();
                $profile = $order->getProfile();

                $config     = \XLite\Core\Config::getInstance();
                $uspsConfig = $config->CDev->USPS;

                $toAddress   = Helper::convertArrayAddressToPBAddress($profile->getShippingAddress()->toArray());

                $value = $toAddress['countryCode'] === 'US'
                    ? $uspsConfig->pb_domestic_parcel_type
                    : $uspsConfig->pb_international_parcel_type;

                $parcelTypes = Helper::getParcelTypes();
                $value = $parcelTypes[$value];

                break;
            case 'weight':
                $value = 0;
                foreach ($this->getOrder()->getItems() as $item) {
                    $value += $item->getWeight();
                }
                break;
            case 'dimensions':
                $value = $config->dimensions;
                break;
            case 'girth':
                $value = $config->girth;
                break;
            case 'specialServices':
                $value = [];
                break;
            case 'CODValue':
            case 'insuranceValue':
                $value = $this->getOrder()->getTotal();
                break;
            case 'labelSize':
                $value = '';
                break;
            case 'hideTotalCarrierCharge':
                $value = false;
                break;
        }

        return $value;
    }

    /**
     * Return name of the current form
     *
     * @return string
     */
    protected function getFormName()
    {
        return parent::getFormName() . '#' . $this->getOrder()->getOrderId();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\CDev\USPS\View\Form\CreateShipment';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['createShipment'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => static::t('Create shipment'),
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button always-enabled',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        return $result;
    }

    protected function getHead()
    {
        return static::t('Create shipment');
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return \XLite::getController()->getOrder();
    }

    /**
     * @return \XLite\Model\Shipping\Method|null
     */
    protected function getShippingMethod()
    {
        $order    = $this->getOrder();
        $modifier = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        return $modifier ? $modifier->getMethod() : null;
    }
}
