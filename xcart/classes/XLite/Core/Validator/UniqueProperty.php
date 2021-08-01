<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Property unique
 */
class UniqueProperty extends \XLite\Core\Validator\UniqueField
{
    /**
     * Class for property existing check
     *
     * @var \XLite\Model\AEntity
     */
    protected $propertyClass;

    /**
     * Constructor
     *
     * @param mixed $fieldClass     Field class OPTIONAL
     * @param mixed $fieldName      Field identifier OPTIONAL
     * @param mixed $fieldValue     Field value OPTIONAL
     * @param mixed $propertyClass  Property class OPTIONAL
     *
     * @return void
     */
    public function __construct($fieldClass = null, $fieldName = null, $fieldValue = null, $propertyClass = null)
    {
        parent::__construct($fieldClass, $fieldName, $fieldValue);

        if (isset($propertyClass)) {
            $this->propertyClass = $propertyClass;
        }
    }

    /**
     * @param mixed $data
     *
     * @return void
     */
    public function validate($data)
    {
        parent::validate($data);

        if (!\XLite\Core\Converter::isEmptyString($data) && $this->propertyClass) {
            /** @var \XLite\Model\AEntity $classObj */
            $classObj = new $this->propertyClass;
            $reflect = new \ReflectionClass($classObj);
            if ($reflect->hasProperty($data)) {
                $this->throwSKUError();
            }
        }
    }
}
