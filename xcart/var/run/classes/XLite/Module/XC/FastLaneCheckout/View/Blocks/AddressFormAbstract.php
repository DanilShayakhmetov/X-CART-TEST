<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
abstract class AddressFormAbstract extends \XLite\View\Checkout\AAddressBlock
{
    /**
     * Returns block class name
     *
     * @return boolean
     */
    abstract public function getClassName();

    /**
     * Returns block short class name
     *
     * @return boolean
     */
    abstract public function getShortAddressType();

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = array();

        $list[] = array(
            'file'  => FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = array();

        $list[] = FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/address_form.js';

        return $list;
    }

    public function getListName($field = null)
    {
        $name = 'checkout_fastlane.blocks.address.' . $this->getClassName();

        if ($field) {
            $name .= '.' . $field;
        }

        return $name;
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/template.twig';
    }

    /**
     * Check - form is visible or not
     *
     * @return boolean
     */
    protected function isFormVisible()
    {
        return true;
    }

    /**
     * Add CSS classes to the list of attributes
     *
     * @param string $fieldName Field service name
     * @param array  $fieldData Array of field properties (see getAddressFields() for the details)
     *
     * @return array
     */
    public function getFieldAttributes($fieldName, array $fieldData)
    {
        $attrs = parent::getFieldAttributes($fieldName, $fieldData);

        // Vue.js attributes
        $attrs['v-model'] = 'fields.' . $fieldName;
        $attrs['debounce'] = '1000';
        $attrs['data-shortname'] = $fieldName;
        $attrs['class'] = $attrs['class'] . ' show-valid-state';

        return $attrs;
    }

    /**
     * @return string
     */
    public function defineFormSchema()
    {
        $schema = array();
        foreach ($this->getAddressSchemaFields() as $field => $data) {
            $schema[$field] = (string) $this->getFieldValue($field);
        }

        return $schema;
    }

    /**
     * @return string
     */
    public function serializeFormSchema()
    {
        return json_encode($this->defineFormSchema());
    }

    /**
     * Get field css classes
     *
     * @param string $fieldName Field service name
     *
     * @return array
     */
    protected function getFieldClasses($fieldName)
    {
        return array(
            'field-' . $fieldName,
        );
    }

    /**
     * Get field placeholder
     *
     * @param string $name File short name
     *
     * @return string
     */
    protected function getFieldPlaceholder($name)
    {
        switch ($name) {
            case 'firstname':
                $result = static::t('Firstname');
                break;

            case 'lastname':
                $result = static::t('Lastname');
                break;

            case 'street':
                $result = static::t('Street address');
                break;

            case 'city':
                $result = static::t('City');
                break;

            case 'custom_state':
                $result = static::t('State');
                break;

            case 'zipcode':
                $result = static::t('Zipcode');
                break;

            case 'phone':
                $result = static::t('Phone number');
                break;

            case 'email':
                $result = static::t('E-mail');
                break;

            default:
                $result = '';
        }

        return $result;
    }

    /**
     * Get an array of address fields
     *
     * @return array
     */
    public function getAddressFields()
    {
        $result = parent::getAddressFields();

        if (array_key_exists('email', $result)) {
            $result['email']['class'] = 'XLite\View\FormField\Input\Text';
        }

        return $result;
    }
}
