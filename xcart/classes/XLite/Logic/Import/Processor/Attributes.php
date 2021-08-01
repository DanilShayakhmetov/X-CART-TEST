<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Processor;

/**
 * Attributes import processor
 */
class Attributes extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Current attribute name
     *
     * @var string
     */
    protected $currentAttrName = '';

    /**
     * Get title
     *
     * @return string
     */
    static public function getTitle()
    {
        return static::t('Attributes imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Attribute');
    }

    /**
     * Assemble maodel conditions
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function assembleModelConditions(array $data)
    {
        $conditions = parent::assembleModelConditions($data);

        if (
            !empty($conditions['productClass'])
            && is_object($conditions['productClass'])
            && !$conditions['productClass']->isPersistent()
        ) {
            $conditions['productClass'] = $conditions['productClass']->getName();
        }

        return $conditions;
    }

    /**
     * Detect model via non-default language
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        if (
            !empty($data['name'])
            && is_array($data['name'])
            && !isset($data['name'][$this->importer->getLanguageCode()])
        ) {
            //DetectModel relies on default admin language which is empty. Change Default Lng to work DetectModel properly
            $oldLang = $this->importer->getLanguageCode();
            $this->importer->setLanguageCode(array_key_first($data['name']));
        }

        $res = parent::detectModel($data);

        if (!empty($oldLang)) {
            $this->importer->setLanguageCode($oldLang);
        }
        return $res;
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'position'     => [],
            'name'         => [
                static::COLUMN_IS_KEY          => true,
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'product'      => [
                static::COLUMN_IS_KEY => true,
            ],
            'class'        => [
                static::COLUMN_IS_KEY          => true,
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_PROPERTY        => 'productClass',
                static::COLUMN_LENGTH          => 255,
            ],
            'group'        => [
                static::COLUMN_IS_KEY          => true,
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_PROPERTY        => 'attributeGroup',
                static::COLUMN_LENGTH          => 255,
            ],
            'options'      => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_MULTIPLE     => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'type'         => [],
            'displayMode'  => [],
            'displayAbove' => [],
        ];
    }

    // }}}

    // {{{ Column metadata
    /**
     * Get value for default language, allow to get non-default value right from file
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function getDefLangValue($value)
    {
        $parent_value = parent::getDefLangValue($value);
        if (is_null($parent_value) && is_array($value) && !empty($value)) {
            $parent_value = array_values($value)[0];
        }

        return $parent_value;
    }
    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'ATTR-PRODUCT-FMT'        => 'The product with "{{value}}" SKU does not exist',
                'ATTR-POSITION-FMT'       => 'Wrong position format',
                'ATTR-GROUP-FMT'          => 'The "{{value}}" group is not created',
                'ATTR-TYPE-FMT'           => 'Wrong type format',
                'ATTR-MODE-S-FMT'         => 'Wrong display mode format for selector',
                'ATTR-MODE-NOT-S-FMT'     => 'Wrong display mode format for not selector',
                'ATTR-NAME-FMT'           => 'The name is empty',
                'ATTR-DISPLAY-ABOVE-FMT'  => 'Wrong display above price format',
            ];
    }

    /**
     * Get error texts
     *
     * @return array
     */
    public static function getErrorTexts()
    {
        return parent::getErrorTexts()
            + [
                'ATTR-GROUP-FMT'    => 'New attribute group will be created',
            ];
    }

    /**
     * Returns csv format manual URL
     *
     * @return string
     */
    public static function getCSVFormatManualURL()
    {
        return static::t('https://kb.x-cart.com/import-export/csv_format_by_x-cart_data_type/csv_import_classes_&_attributes.html');
    }

    /**
     * Verify 'position' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPosition($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('ATTR-POSITION-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'type' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyType($value, array $column)
    {
        if ($this->verifyValueAsEmpty($value) || !\XLite\Model\Attribute::getTypes($value)) {
            $this->addError('ATTR-TYPE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'displayMode' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyDisplayMode($value, array $column)
    {
        if (
            in_array($this->currentRowData['type'], [\XLite\Model\Attribute::TYPE_CHECKBOX, \XLite\Model\Attribute::TYPE_HIDDEN, \XLite\Model\Attribute::TYPE_TEXT], true)
            && $value === \XLite\Model\Attribute::SELECT_BOX_MODE
        ) {
            return;
        }

        if ($this->currentRowData['type'] === \XLite\Model\Attribute::TYPE_SELECT) {
            $availableDisplayModes = \XLite\Model\Attribute::getDisplayModes();
            if (!isset($availableDisplayModes[$value])) {
                $this->addError('ATTR-MODE-S-FMT', ['column' => $column, 'value' => $value]);
            }
        } elseif (!$this->verifyValueAsEmpty($value)) {
            $this->addError('ATTR-MODE-NOT-S-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'displayAbove' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyDisplayAbove($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addError('ATTR-DISPLAY-ABOVE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'group' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyGroup($value, array $column)
    {
        $value = $this->getDefLangValue($value);
        if (
            !$this->verifyValueAsEmpty($value)
            && 0 == \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findOneByName($value, true)
        ) {
            $this->addWarning('ATTR-GROUP-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'group' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyName($value, array $column)
    {
        $value = $this->getDefLangValue($value);
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('ATTR-NAME-FMT', ['column' => $column, 'value' => $value]);
        }
        $this->currentAttrName = $value;
    }

    /**
     * Verify 'product class' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyClass($value, array $column)
    {
        $value = $this->getDefLangValue($value);
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsProductClass($value)) {
            $this->addWarning('GLOBAL-PRODUCT-CLASS-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'product' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyProduct($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsProduct($value)) {
            $this->addWarning('ATTR-PRODUCT-FMT', ['column' => $column, 'value' => $value, 'name' => $this->currentAttrName]);
        }
    }

    /**
     * Verify 'options' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyOptions($value, array $column)
    {
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'position' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizePositionValue($value)
    {
        return abs(intval($value));
    }

    /**
     * Normalize 'class' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\ProductClass
     */
    protected function normalizeClassValue($value)
    {
        $productClass = $this->normalizeValueAsProductClass($value);

        return $productClass;
    }

    /**
     * Normalize 'group' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\AttributeGroup
     */
    protected function normalizeGroupValue($value)
    {
        if (!\XLite\Core\Converter::isEmptyString($this->currentRowData['class'])) {
            $className = $this->getDefLangValue($this->currentRowData['class']);
            $productClass = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')->findOneByName($className);

        } else {
            $productClass = null;
        }

        return $this->normalizeValueAsAttributeGroup($value, $productClass);
    }

    /**
     * Normalize 'product' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\ProductClass
     */
    protected function normalizeProductValue($value)
    {
        return $this->normalizeValueAsProduct($value);
    }

    // }}}

    // {{{ Import

    /**
     * Import 'options' value
     *
     * @param \XLite\Model\Attribute $model  Attribute
     * @param array                  $value  Value
     * @param array                  $column Column info
     *
     * @return void
     */
    protected function importOptionsColumn(\XLite\Model\Attribute $model, array $value, array $column)
    {
        if ($value && !$this->verifyValueAsNull($value)) {
            foreach ($value as $index => $val) {
                $option = $model->getAttributeOptions()->get($index);
                if (!$option) {
                    $option = new \XLite\Model\AttributeOption();
                    $option->setAttribute($model);
                    $model->getAttributeOptions()->add($option);

                    \XLite\Core\Database::getEM()->persist($option);
                }
                $this->updateModelTranslations($option, $val);
            }

            while (count($model->getAttributeOptions()) > count($value)) {
                $option = $model->getAttributeOptions()->last();
                \XLite\Core\Database::getRepo('\XLite\Model\AttributeOption')->delete($option, false);
                $model->getAttributeOptions()->removeElement($option);
            }
        } elseif ($this->verifyValueAsNull($value)) {
            foreach ($model->getAttributeOptions() as $option) {
                \XLite\Core\Database::getEM()->remove($option);
            }
            $model->getAttributeOptions()->clear();
        }
    }

    /**
     * Import 'group' value
     *
     * @param \XLite\Model\Attribute $model  Attribute
     * @param string                 $value  Value
     * @param array                  $column Column info
     *
     * @return void
     */
    protected function importGroupColumn(\XLite\Model\Attribute $model, $value, array $column)
    {
        if ($value) {
            $group = $this->normalizeGroupValue($value);
            $this->updateModelTranslations($group, $value);
            $group->setProductClass($model->getProductClass());
            $model->setAttributeGroup($group);
        }
    }

    /**
     * Import 'displayAbove' value
     *
     * @param \XLite\Model\Attribute $model  Attribute
     * @param mixed                  $value  Value
     * @param array                  $column Column info
     *
     * @return void
     */
    protected function importDisplayAboveColumn(\XLite\Model\Attribute $model, $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && $this->verifyValueAsBoolean($value)) {
            $model->setDisplayAbove($this->normalizeValueAsBoolean($value));
        }
    }

    // }}}
}
