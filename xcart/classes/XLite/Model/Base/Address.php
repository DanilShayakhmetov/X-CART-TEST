<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use XLite\Core\Database;
use XLite\Model\AddressField;

/**
 * Abstract address model
 *
 * @MappedSuperclass
 */
abstract class Address extends \XLite\Model\AEntity
{
    /**
     * Address fields (cache)
     *
     * @var array
     */
    static protected $addressFieldsCache = array();

    /**
     * Default field values (cache)
     *
     * @var array
     */
    static protected $defaultFieldValuesCache = array();

    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer")
     */
    protected $address_id;

    /**
     * Address type: residential/commercial
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $address_type = 'R';

    /**
     * State
     *
     * @var \XLite\Model\State
     *
     * @ManyToOne  (targetEntity="XLite\Model\State", cascade={"merge","detach"})
     * @JoinColumn (name="state_id", referencedColumnName="state_id", onDelete="SET NULL")
     */
    protected $state;

    /**
     * Country
     *
     * @var \XLite\Model\Country
     *
     * @ManyToOne  (targetEntity="XLite\Model\Country", cascade={"merge","detach"})
     * @JoinColumn (name="country_code", referencedColumnName="code", onDelete="SET NULL")
     */
    protected $country;

    public function map(array $data)
    {
        return parent::map($this->prepareDataForMap($data));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function prepareDataForMap(array $data)
    {
        $priorFields = [
            'country',
            'country_code',
        ];

        foreach ($priorFields as $priorField) {
            if (isset($data[$priorField])) {
                $data = [$priorField => $data[$priorField]] + $data;
            }
        }

        return $data;
    }

    /**
     * Get address fields list
     *
     * @return array(string)
     */
    public function getAvailableAddressFields()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findEnabledFields();
    }

    /**
     * Get state
     *
     * @return \XLite\Model\State
     */
    public function getState()
    {
        $state = null;

        if ($this->state) {

            // Real state object
            $state = $this->state;

        } else {
            $stateField = $this->getFieldValue('state_id');

            // Real state object from address fields
            if (
                $stateField
                && $stateField->getValue()
                && (!$this->getCountry() || !$this->getCountry()->isForcedCustomState())
            ) {
                $this->setStateId($stateField->getValue());
                $state = $this->state;
            }

            if (!$state) {
                // Custom state
                $state = new \XLite\Model\State;
                $state->setState($this->getCustomState());
            }
        }

        return $state;
    }

    /**
     * Set country
     *
     * @param integer $countryCode Country code
     *
     * @return void
     */
    public function setCountryCode($countryCode)
    {
        $this->setCountry(
            \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(array('code' => $countryCode))
        );
    }

    /**
     * Set country
     *
     * @param \XLite\Model\Country $country
     *
     * @return Address
     */
    public function setCountry(\XLite\Model\Country $country = null)
    {
        $this->country = $country;

        if ($this->country) {
            $this->setterProperty('country_code', $this->country->getCode());
        }

        return $this;
    }

    /**
     * Set state
     *
     * @param integer $stateId State id
     *
     * @return void
     */
    public function setStateId($stateId)
    {
        $this->setState(\XLite\Core\Database::getRepo('XLite\Model\State')->find($stateId));
    }

    /**
     * Set state
     *
     * @param mixed $state State object or state id or custom state name
     *
     * @return void
     * @todo Refactor?
     */
    public function setState($state)
    {
        if ($state instanceof \XLite\Model\State) {
            if ($this->getCountry()
                && $this->getCountry()->hasStates()
            ) {
                // Set by state object
                if ($state->getStateId()) {
                    if (!$this->state || $this->state->getStateId() != $state->getStateId()) {
                        $this->state = $state;
                        $this->setterProperty('state_id', $state->getStateId());
                    }
                    $this->setCustomState($this->state->getState());

                } else {
                    $this->state = null;
                    $this->setCustomState($state->getState());
                }

            } else {
                $this->state = null;
            }

        } elseif (is_string($state)) {
            $statesRepo = Database::getRepo('XLite\Model\State');

            if (
                $this->getCountry()
                && $this->getCountry()->hasStates()
                && ($state = $statesRepo->findOneBy([
                    'code' => $state,
                    'country' => $this->getCountry()
                ]))
            ) {
                $this->state = $state;
            } else {
                $this->state = null;
                $this->setCustomState($state);
            }
        }
    }

    /**
     * Get state Id
     *
     * @param boolean $strict Flag: true - do not use default value if current value is not set
     *
     * @return integer
     */
    public function getStateId($strict = false)
    {
        $state = $this->getState();

        if ($state) {
            $result = $state->getStateId();
        }

        return isset($result)
            ? $result
            : (!$strict ? static::getDefaultFieldPlainValue('state_id') : null);
    }

    /**
     * Get country code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getCountry()
            ? ($this->getCountry()->getCode() ?: static::getDefaultFieldPlainValue('country_code'))
            : static::getDefaultFieldPlainValue('country_code');
    }

    /**
     * Get country name
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->getCountry() ? $this->getCountry()->getCountry() : null;
    }

    /**
     * Get state name
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->getState()->getState();
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->getType() == \XLite\View\FormField\Select\AddressType::TYPE_COMMERCIAL
            ? static::t('Commercial')
            : static::t('Residential');
    }

    /**
     * Return default field value
     *
     * @param string $fieldName Field name
     *
     * @return string
     */
    public static function getDefaultFieldPlainValue($fieldName)
    {
        if (!isset(static::$defaultFieldValuesCache[$fieldName])) {
            $field = \XLite\Core\Database::getRepo('\XLite\Model\Config')
                ->findOneBy(array(
                    'category' => \XLite\Model\Config::SHIPPING_CATEGORY,
                    'name'     => static::getDefaultFieldName($fieldName)
                ));
            static::$defaultFieldValuesCache[$fieldName] = $field ? $field->getValue() : '';
        }

        return static::$defaultFieldValuesCache[$fieldName];
    }

    /**
     * Return name of the address field in the default shipping category of the settings
     *
     * @param string $fieldName
     *
     * @return string
     */
    public static function getDefaultFieldName($fieldName)
    {
        $result = \XLite\Model\Config::SHIPPING_VALUES_PREFIX;

        switch ($fieldName) {
            case 'country_code':
                $result .= 'country';
                break;

            case 'state_id':
                $result .= 'state';
                break;

            case 'street':
                $result .= 'address';
                break;

            default:
                $result .= $fieldName;
                break;
        }

        return $result;
    }

    /**
     * Return address field by service name
     *
     * @param string $fieldName Field service name
     *
     * @return \XLite\Model\AddressField
     */
    static protected function getAddressFieldByServiceName($fieldName)
    {
        if (empty(static::$addressFieldsCache[$fieldName])) {
            array_map(function (AddressField $field) {
                static::$addressFieldsCache[$field->getServiceName()] = $field;
            }, Database::getRepo('XLite\Model\AddressField')->findAll());
        }

        if (!isset(static::$addressFieldsCache[$fieldName])) {
            static::$addressFieldsCache[$fieldName] = Database::getRepo('XLite\Model\AddressField')
                ->findOneByServiceName($fieldName);
        }

        return static::$addressFieldsCache[$fieldName];
    }

    /**
     * Get required fields by address type
     *
     * @param string $atype Address type code
     *
     * @return array
     */
    public function getRequiredFieldsByType($atype)
    {
        return array();
    }

    /**
     * Returns list of alternative getters
     *
     * @return array
     */
    protected function getFieldsAlternativeNames()
    {
        return [
            'state_id' => ['custom_state']
        ];
    }

    /**
     * Get alternative getters for field by name
     *
     * @param $name
     *
     * @return array
     */
    protected function getFieldAlternativeNames($name)
    {
        return isset($this->getFieldsAlternativeNames()[$name])
            ? $this->getFieldsAlternativeNames()[$name]
            : [];
    }

    /**
     * Get required and empty fields
     *
     * @param string $atype Address type code
     *
     * @return array
     */
    public function getRequiredEmptyFields($atype)
    {
        $result = array();

        foreach ($this->getRequiredFieldsByType($atype) as $name) {
            $countryHasStates = $this->getCountry() && $this->getCountry()->hasStates();

            if ($name === 'state_id' && !$countryHasStates) {
                continue;
            }

            $method = 'get' . \XLite\Core\Converter::getInstance()->convertToCamelCase($name);

            if (!strlen($this->$method())) {
                foreach ($this->getFieldAlternativeNames($name) as $getter) {
                    $method = 'get' . \XLite\Core\Converter::getInstance()->convertToCamelCase($getter);
                    if (strlen($this->$method())) {
                        $valid = true;
                        break;
                    }
                }

                if (!isset($valid)) {
                    $result[] = $name;
                }
            }
        }

        return $result;
    }

    /**
     * Check - address is completed or not
     *
     * @param string $atype Address type code
     *
     * @return boolean
     */
    public function isCompleted($atype)
    {
        return 0 == count($this->getRequiredEmptyFields($atype));
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $entity = parent::cloneEntity();

        if ($this->getCountry()) {
            $entity->setCountry($this->getCountry());
        }

        if ($this->getState()) {
            $entity->setState($this->getState());
        }

        return $entity;
    }

    /**
     * Update record in database
     *
     * @return boolean
     */
    public function update()
    {
        return $this->checkAddressDuplicates() && parent::update();
    }

    /**
     * Create record in database
     *
     * @return boolean
     */
    public function create()
    {
        return $this->checkAddressDuplicates() && parent::create();
    }


    /**
     * Check if address has duplicates
     *
     * @return boolean
     */
    protected function checkAddressDuplicates()
    {
        return true;
    }


    /**
     * Check if address has duplicates
     *
     * @return boolean
     */
    public function checkAddress()
    {
        return true;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return bool
     */
    protected function checkAddressField($name, $value) {
        switch ($name) {
            case 'country':
                if ($value instanceof \XLite\Model\Country) {
                    return $value->getEnabled();
                }
                break;
            case 'country_code':
                $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($value);

                return $value && $country && $country->getEnabled();
                break;
        }

        return true;
    }

    // {{{ Address comparison

    /**
     * Check - this and specified addresses is equal or not
     *
     * @param \XLite\Model\Base\Address $address Address
     *
     * @return boolean
     */
    public function isEqualAddress(\XLite\Model\Base\Address $address)
    {
        $my = $this->getFieldsHash();
        $strange = $address->getFieldsHash();

        $intersect = array_intersect_assoc($my, $strange);

        return count($intersect) == count($my) && count($intersect) == count($strange);
    }

    /**
     * Get fields hash
     *
     * @return array
     */
    public function getFieldsHash()
    {
        $result = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
            $name = $field->getServiceName();

            if (
                (
                    $name === 'custom_state'
                    && $this->hasStates()
                    && ($this->getCountry() && !$this->getCountry()->isForcedCustomState())
                )
                || (
                    $name === 'state_id'
                    && (
                        !$this->hasStates()
                        || ($this->getCountry() && $this->getCountry()->isForcedCustomState())
                    )
                )
            ) {
                continue;
            }

            $methodName = 'get' . \XLite\Core\Converter::getInstance()->convertToCamelCase($name);
            $result[$name] = $this->$methodName();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function hasStates()
    {
        return $this->getCountry() && $this->getCountry()->hasStates();
    }

    // }}}
}
