<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Admin;

/**
 * Create box 
 */
class CreateBox extends \XLite\View\Product\Details\Admin\AAdmin
{
    const PARAM_IS_SPECIFIC = 'isSpecific';

    /**
     * @return mixed
     */
    public function getPersonalOnly()
    {
        return $this->getParam(static::PARAM_IS_SPECIFIC);
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_IS_SPECIFIC => new \XLite\Model\WidgetParam\TypeBool('Is specific', true),
        );
    }
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product/attributes';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/create_box.twig';
    }

    /**
     * Get default attributes widgets
     *
     * @return array
     */
    protected function getDefaultWidgets()
    {
        $list = array();

        foreach (\XLite\Model\Attribute::getTypes() as $type => $name) {
            $list[] = $this->getWidget(
                array(),
                \XLite\Model\Attribute::getWidgetClass($type)
            );
        }

        return $list;
    }
}
