<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

class PaypalButton extends \XLite\View\Model\AModel
{
    /**
     * Schema of the default section
     *
     * @var array
     */
    protected $schemaDefault = [
        'model_header'  => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL => 'Funding methods (checkout page)',
            self::SCHEMA_HELP  => 'When multiple funding sources are available to the buyer, PayPal automatically determines which additional buttons are appropriate to display. However, you can choose to opt-in or out-of displaying specific funding sources.',
        ],
        'funding_card'  => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'Credit or debit card (Visa, MasterCard, American Express, Discover, and so on)',
            self::SCHEMA_REQUIRED => false,
        ],
        'funding_elv'   => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'ELV/SEPA',
            self::SCHEMA_REQUIRED => false,
        ],
        'funding_venmo' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'Venmo',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\CDev\Paypal\View\Form\PaypalButton';
    }

    /**
     * There is no object for settings
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
        return \XLite\Core\Config::getInstance()->CDev->Paypal->{$name};
    }

    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        if ($this->isPaypalForMarketplaces()) {
            unset($schema['funding_venmo']);
        }

        return parent::getFieldsBySchema($schema);
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return 'XLite\View\StickyPanel\Payment\Settings';
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        foreach ($data as $name => $value) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'CDev\Paypal',
                'name'     => $name,
                'value'    => $value,
            ]);
        }
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function isPaypalForMarketplaces()
    {
        return $this->getPaymentMethod()
               && $this->getPaymentMethod()->getServiceName() === \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM;
    }
}
