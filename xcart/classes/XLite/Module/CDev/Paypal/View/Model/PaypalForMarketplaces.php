<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

use XLite\Module\CDev\Paypal\Core\PaypalForMarketplacesAPI;

/**
 * PaypalForMarketplaces
 */
class PaypalForMarketplaces extends \XLite\Module\CDev\Paypal\View\Model\ASettings
{
    /**
     * Schema of the "Your account settings" section
     *
     * @var array
     */
    protected $schemaAccount = [
        'email'                => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'PayPal For Marketplaces account email',
            self::SCHEMA_REQUIRED => true,
        ],
        'client_id'            => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'PayPal API Client ID',
            self::SCHEMA_REQUIRED => true,
        ],
        'secret'               => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'PayPal API Secret',
            self::SCHEMA_REQUIRED => true,
        ],
        'partner_id'           => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'PayPal Partner ID',
            self::SCHEMA_REQUIRED => true,
        ],
        'bn_code'              => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'PayPal BN Code',
            self::SCHEMA_REQUIRED => true,
        ],
        'additional_email_sep' => [
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Separator\AdditionalAccount',
            self::SCHEMA_LABEL    => 'Additional PayPal Business Account',
            self::SCHEMA_REQUIRED => true,
        ],
        'additional_email'     => [
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Input\AdditionalAccount',
            self::SCHEMA_LABEL    => '',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * Schema of the "Additional settings" section
     *
     * @var array
     */
    protected $schemaAdditional = [
        'payment_descriptor'           => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Payment descriptor',
            self::SCHEMA_REQUIRED => false,
        ],
        'mode'           => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\TestLiveMode',
            self::SCHEMA_LABEL    => 'Test/Live mode',
            self::SCHEMA_REQUIRED => false,
        ],
        'prefix'         => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Order id prefix',
            self::SCHEMA_HELP     => 'You can define an order id prefix, which would precede each order number in your shop, to make it unique',
            self::SCHEMA_REQUIRED => false,
        ],
        'disburse_funds' => [
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\DisburseFunds',
            self::SCHEMA_LABEL    => 'Disburse funds',
            self::SCHEMA_HELP     => 'Please note that this setting cannot be changed once your first vendor has been successfully onboarded',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/settings/PaypalForMarketplaces/style.css';

        return $list;
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
        if (isset($data['mode']) && $data['mode'] !== $this->getModelObject()->getSetting('mode')) {
            PaypalForMarketplacesAPI::dropPayPalTokenCash();
        }

        parent::setModelProperties($data);
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
        if (isset($schema['disburse_funds'])) {
            $method  = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
                \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
            );

            if ($method->getSetting('disburse_funds_option_locked')) {
                $schema['disburse_funds'][\XLite\View\FormField\AFormField::PARAM_ATTRIBUTES] = [
                    'readonly' => true,
                    'disabled' => true,
                ];
            }
        }

        return parent::getFieldsBySchema($schema);
    }
}
