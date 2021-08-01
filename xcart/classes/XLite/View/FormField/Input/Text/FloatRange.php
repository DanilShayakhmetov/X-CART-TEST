<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

use XLite\Model\WidgetParam\TypeInt;

/**
 * Float range
 */
class FloatRange extends ARange
{
    /**
     * Returns input widget class name
     *
     * @return string
     */
    protected function getInputWidgetClass()
    {
        return FloatInput::class;
    }

    /**
     * Returns end widget class
     *
     * @return string
     */
    protected function getEndWidgetClass()
    {
        return FloatWithInfinity::class;
    }

    /**
     * Returns default begin value
     *
     * @return mixed
     */
    protected function getDefaultBeginValue()
    {
        return 0;
    }

    /**
     * @return int
     */
    protected function getDefaultE(): int
    {
        return 2;
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [FloatInput::PARAM_E => new TypeInt(
            'Number of digits after the decimal separator',
            $this->getDefaultE()
        )];
    }

    /**
     * @return array
     */
    protected function getBeginWidgetParams()
    {
        $params = parent::getBeginWidgetParams();

        $params[FloatInput::PARAM_E] = $this->getParam(FloatInput::PARAM_E);

        return $params;
    }

    /**
     * @return array
     */
    protected function getEndWidgetParams()
    {
        $params = parent::getEndWidgetParams();

        $params[FloatInput::PARAM_E] = $this->getParam(FloatInput::PARAM_E);

        return $params;
    }
}
