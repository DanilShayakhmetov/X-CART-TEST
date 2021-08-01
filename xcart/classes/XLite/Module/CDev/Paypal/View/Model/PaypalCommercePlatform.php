<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatformAPI;

class PaypalCommercePlatform extends \XLite\Module\CDev\Paypal\View\Model\ASettings
{
    /**
     * Schema of the "Your account settings" section
     *
     * @var array
     */
    protected $schemaAccount = [
        'merchant_id'   => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Merchant Id',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'client_id'     => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Client Id',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'client_secret' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Client Secret',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
    ];

    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        unset($this->schemaAdditional['prefix']);

        $this->schemaAdditional['3d_secure'] = [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => '3D Secure',
            self::SCHEMA_REQUIRED => false,
        ];
        $this->schemaAdditional['3d_secure_soft_exception'] = [
            self::SCHEMA_CLASS                                          => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL                                          => 'Authentication bypassed / unavailable liability shift',
            \XLite\View\FormField\Input\Checkbox\OnOff::PARAM_ON_LABEL  => 'Accept',
            \XLite\View\FormField\Input\Checkbox\OnOff::PARAM_OFF_LABEL => 'Decline',
            self::SCHEMA_REQUIRED                                       => false,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    '3d_secure' => array('1'),
                ),
            ),
        ];
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\CDev\Paypal\View\Form\PaypalCommercePlatformSettings';
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
        $method = $this->getModelObject();

        if (isset($data['mode']) && $data['mode'] !== $method->getSetting('mode')) {
            PaypalCommercePlatformAPI::dropPayPalTokenCash();

            $data['merchant_id'] = '';
            $data['client_id'] = '';
            $data['client_secret'] = '';
        }

        parent::setModelProperties($data);
    }
}
