<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Controller\Admin;


class Autocomplete extends \XLite\Controller\Admin\Autocomplete implements \XLite\Base\IDecorator
{
    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerProductVariant($term)
    {
        $data = array_map(
            function (\XLite\Module\XC\ProductVariants\Model\ProductVariant $variant) {
                return $variant->getVariantId();
            },
            \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                ->findProductVariantsByTerm($term, 5)
        );

        return array_combine($data, $data);
    }
}