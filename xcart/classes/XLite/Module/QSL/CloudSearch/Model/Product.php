<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model;


/**
 * Product model
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Returns meta description in the specified language
     *
     * @param $translation
     *
     * @return string
     */
    public function getTranslatedMetaDesc($translation)
    {
        return static::META_DESC_TYPE_AUTO === $this->getMetaDescType()
            ? static::generateMetaDescription($translation['briefDescription'] ?: $translation['description'])
            : $translation['metaDesc'];
    }
}
