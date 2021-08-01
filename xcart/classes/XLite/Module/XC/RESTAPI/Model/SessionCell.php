<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model;

/**
 * Session
 */
abstract class SessionCell extends \XLite\Model\SessionCell implements \XLite\Base\IDecorator
{
    /**
     * Plain property getter for REST API
     *
     * @param string $name  Field name
     * @param array  $field Field metadata
     *
     * @return string
     */
    protected function getterPropertyForREST($name, array $field)
    {
        $value = parent::getterPropertyForREST($name, $field);

        if ('value' == $name && $value) {
            $value = unserialize($value);
        }

        return $value;
    }
}
