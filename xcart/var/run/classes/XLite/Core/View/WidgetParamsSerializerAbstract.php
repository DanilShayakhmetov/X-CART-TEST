<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;

use Serializable;
use XLite\Core\Serialization\SerializableNative;


/**
 * WidgetParamsSerializer provides serialization support for widget params.
 * Widget param values are serialized when dynamic widget placeholder is generated and unserialized
 * when placeholder is reified into a rendered widget.
 *
 */
abstract class WidgetParamsSerializerAbstract
{
    /**
     * Serialize widget params into a string
     *
     * @param array $widgetParams
     *
     * @return string
     *
     * @throws WidgetParamsSerializationException
     */
    public function serialize(array $widgetParams)
    {
        $params = [];

        foreach ($widgetParams as $key => $param) {
            $value = $param->value;

            if ($value instanceof \XLite\Core\Serialization\SerializableEntity) {
                $value = \XLite\Core\Serialization\EntitySerializer::serialize($value);
            } elseif (!$this->isSerializable($value)) {
                throw new WidgetParamsSerializationException("Unable to serialize \"$key\" widget param");
            }

            $params[$key] = $value;
        }

        return serialize($params);
    }

    /**
     * Unserialize widget params from $params string.
     *
     * @param string $params
     *
     * @return array
     */
    public function unserialize($params)
    {
        return array_map([$this, 'unserializeParam'], unserialize($params));
    }

    /**
     * Unserialize widget parameter.
     *
     * @param mixed $param
     *
     * @return mixed
     */
    protected function unserializeParam($param)
    {
        return $param instanceof \XLite\Core\Serialization\ASerializedEntity
            ? $param->restore()
            : $param;
    }

    /**
     * Check if the given value is serializable. Serializable values are objects of classes
     * implementing Serializable, scalars, nulls, and arrays of serializable values.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isSerializable($value)
    {
        return is_scalar($value)
            || is_null($value)
            || (
                is_object($value) && (
                    $value instanceof Serializable
                    || $value instanceof SerializableNative
                )
            )
            || is_array($value)
            && array_reduce($value, function ($result, $arrayItem) {
                return $result && $this->isSerializable($arrayItem);
            }, true);
    }
}