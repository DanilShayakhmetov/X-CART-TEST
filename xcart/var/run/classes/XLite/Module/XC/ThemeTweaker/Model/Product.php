<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model;

use XLite\Module\XC\ThemeTweaker\Model\Features\InlineEditableEntityTrait;

 class Product extends \XLite\Module\XC\Upselling\Model\Product implements \XLite\Base\IDecorator
{
    use InlineEditableEntityTrait;

    public function defineEditableProperties()
    {
        return ['description', 'briefDescription'];
    }

    /**
     * Provides metadata for the property
     *
     * @param  string  $property Checked entity property
     * @return array
     */
    public function getFieldMetadata($property)
    {
        return array_merge(
            parent::getFieldMetadata($property),
            $this->getInlineEditableMetadata()
        );
    }
}
