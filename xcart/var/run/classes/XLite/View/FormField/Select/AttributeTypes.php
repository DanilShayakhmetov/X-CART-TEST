<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Attribute types selector
 */
class AttributeTypes extends \XLite\View\FormField\Select\Regular
{
    const PARAM_EXCLUDE_HIDDEN = 'excludeHidden';

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return \XLite\Model\Attribute::getTypes();
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        if ($this->getParam(static::PARAM_EXCLUDE_HIDDEN)) {
            unset($options[\XLite\Model\Attribute::TYPE_HIDDEN]);
        }

        return $options;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_EXCLUDE_HIDDEN => new \XLite\Model\WidgetParam\TypeBool('Exclude field hidden', true),
        );
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
        $attrs = parent::setCommonAttributes($attrs);

        $attrs['data-value'] = $this->getValue();

        return $attrs;
    }
}
