<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core\ConsistencyCheck;

use XLite\Core\ConsistencyCheck\Retriever;
use XLite\Module\XC\ProductVariants\Core\ConsistencyCheck\Rules\AttributesRule;

/**
 * Class Director
 * @package XLite\Module\XC\ProductVariants\Core\ConsistencyCheck
 */
class Director extends \XLite\Core\ConsistencyCheck\Director implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getRetrievers()
    {
        $retrievers = parent::getRetrievers();

        $retrievers['variants'] = [
            'name'      => 'Product Variants',
            'retriever' => new Retriever($this->getVariantsRules()),
        ];

        return $retrievers;
    }

    /**
     * @return array
     */
    protected function getVariantsRules()
    {
        return [
            'variants_has_attributes' => new AttributesRule(
                \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
            ),
        ];
    }

}
