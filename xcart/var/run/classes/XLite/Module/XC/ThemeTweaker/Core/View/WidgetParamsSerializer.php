<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\ThemeTweaker\Core\View;

use Serializable;
use XLite\Module\XC\ThemeTweaker\Core\Translation;


/**
 * WidgetParamsSerializer provides serialization support for widget params.
 * Widget param values are serialized when dynamic widget placeholder is generated and unserialized when placeholder is reified into a rendered widget.
 *
 */
 class WidgetParamsSerializer extends \XLite\Core\View\WidgetParamsSerializerAbstract implements \XLite\Base\IDecorator
{
    /**
     * Serialize widget params into a string
     *
     * @param array $widgetParams
     *
     * @return string
     * @throws \XLite\Core\View\WidgetParamsSerializationException
     */
    public function serialize(array $widgetParams)
    {
        if (Translation::getInstance()->isInlineEditingEnabled()) {
            foreach ($widgetParams as $key => $param) {
                if ($param->value instanceof \Twig_Markup) {
                    $param->setValue((string) $param->value);
                }
            }
        }

        return parent::serialize($widgetParams);
    }
}