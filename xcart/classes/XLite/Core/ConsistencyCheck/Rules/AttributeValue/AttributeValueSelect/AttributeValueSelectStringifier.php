<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\AttributeValue\AttributeValueSelect;

use XLite\Model\AttributeValue\AttributeValueSelect;

/**
 * Trait OrderModelStringifier
 * @package XLite\Core\ConsistencyCheck
 */
trait AttributeValueSelectStringifier
{
    /**
     * @param AttributeValueSelect $item
     *
     * @return string
     */
    public function stringifyModel(AttributeValueSelect $value)
    {
        return \XLite\Core\Translation::getInstance()->translate(
            'Attribute value ID',
            [
                'valueId' => $value->getUniqueIdentifier()
            ]
        );
    }
}
