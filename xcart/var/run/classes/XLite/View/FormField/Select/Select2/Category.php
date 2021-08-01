<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Select2;

use XLite\Core\Request;
use XLite\Core\Validator;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\View\FormField\Select\MultipleTrait;
use XLite\View\FormField\Select\Select2Trait;

/**
 * Category selector
 */
class Category extends \XLite\View\FormField\Select\Category
{
    use ExecuteCachedTrait, MultipleTrait, Select2Trait {
        MultipleTrait::getCommonAttributes as getCommonAttributesMultiple;
        MultipleTrait::setCommonAttributes as setCommonAttributesMultiple;
        MultipleTrait::isOptionSelected as isOptionSelectedMultiple;
        Select2Trait::getCommentedData as getSelect2CommentedData;
        Select2Trait::getValueContainerClass as getSelect2ContainerClass;
    }

    const PARAM_MULTIPLE          = 'multiple';
    const PARAM_OBJECT_CLASS_NAME = 'objectClassName';
    const PARAM_OBJECT_ID_NAME    = 'objectIdName';
    const PARAM_OBJECT_ID         = 'objectId';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_MULTIPLE => new \XLite\Model\WidgetParam\TypeBool('Select multiple', false),
            self::PARAM_OBJECT_CLASS_NAME => new \XLite\Model\WidgetParam\TypeString('Object class name'),
            self::PARAM_OBJECT_ID_NAME => new \XLite\Model\WidgetParam\TypeString('Object Id name', 'id'),
            self::PARAM_OBJECT_ID => new \XLite\Model\WidgetParam\TypeInt('Object Id'),
        ];
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = $this->getSelect2ContainerClass();

        $class .= ' input-category-select2';

        return $class;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        if ($this->getParam(static::PARAM_MULTIPLE)) {
            return $this->setCommonAttributesMultiple($attrs);
        }

        return parent::setCommonAttributes($attrs);
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        if ($this->getParam(static::PARAM_MULTIPLE)) {
            return $this->getCommonAttributesMultiple();
        }

        return parent::getCommonAttributes();
    }

    /**
     * Get option attributes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionAttributes($value, $text)
    {
        $attributes = parent::getOptionAttributes($value, $text);

        if ($value !== 0 && $value !== 'no_category') {
            $category = \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategory($value);

            if (!$category->isVisible()) {
                $attributes['data-disabled'] = true;
            }
        }

        return $attributes;
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = [];

        if ($this->getValue()) {
            foreach ($this->getValue() as $selectedCategoryId) {
                if ($selectedCategoryId == '0') {
                    $list[$selectedCategoryId] = static::t('Any category');
                } elseif ($selectedCategoryId == 'no_category') {
                    $list[$selectedCategoryId] = static::t('No category assigned');
                } else {
                    $selectedCategory = \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategory($selectedCategoryId);
                    if ($selectedCategory->isRootCategory()) {
                        $list[$selectedCategoryId] = $this->getTarget() == 'category'
                            ? static::t('Root category')
                            : static::t('Any category');
                    } else {
                        $list[$selectedCategoryId] = $selectedCategory->getStringPath();
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/select/select2/category.less';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/select/select2/category.js';

        return $list;
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array_merge($this->getSelect2CommentedData(), [
            'placeholder-lbl'     => static::t('Any category'),
            'disabled-lbl'        => static::t('Category is not accessible'),
            'short-lbl'           => static::t('Please enter 3 or more characters'),
            'more-lbl'            => static::t('Loading more results...'),
            'displayNoCategory'   => $this->getParam(static::PARAM_DISPLAY_NO_CATEGORY) ? 1 : 0,
            'displayRootCategory' => $this->getParam(static::PARAM_DISPLAY_ROOT_CATEGORY) ? 1 : 0,
            'displayAnyCategory'  => $this->getParam(static::PARAM_DISPLAY_ANY_CATEGORY) ? 1 : 0,
            'excludeCategory'     => $this->getParam(static::PARAM_EXCLUDE_CATEGORY) ?? 0,
        ]);
    }

    /**
     * Check field validity
     *
     * @return bool
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();
        $objectClass = $this->getParam(self::PARAM_OBJECT_CLASS_NAME);

        if ($result && $objectClass && $this->getValue()) {
            $validator = new Validator\LoopProtect(
                $this->getParam(self::PARAM_NAME),
                $objectClass,
                $this->getObjectId()
            );
            try {
                foreach ($this->getValue() as $value) {
                    $validator->validate($value);
                }
            } catch (Validator\Exception $exception) {
                $result = false;
                $this->errorMessage = static::t('The directory selected as a parent directory has already been specified as a child directory');
            }
        }

        return $result;
    }

    /**
     * Returns object id
     *
     * @return integer
     */
    protected function getObjectId()
    {
        return $this->getParam(static::PARAM_OBJECT_ID)
            ?: Request::getInstance()->{$this->getParam(static::PARAM_OBJECT_ID_NAME)};
    }
}
