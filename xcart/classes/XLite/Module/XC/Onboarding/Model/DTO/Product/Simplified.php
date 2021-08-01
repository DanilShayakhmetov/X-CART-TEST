<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Model\DTO\Product;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use XLite\Core\Translation;
use XLite\Model\DTO\Base\CommonCell;

class Simplified extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        $images       = [
            'delete'   => false,
            'position' => '',
            'alt'      => '',
            'temp_id'  => '',
        ];

        $default       = [
            'identity'             => $object->getProductId(),
            'name'                 => $object->getName(),
            'price'                => $object->getPrice(),
            'images'               => $images,
        ];

        $this->default = new CommonCell($default);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return void
     */
    public function populateTo($object, $rawData = null)
    {
        $default = $this->default;

        $object->setName((string) $default->name);

        if ($default->images) {
            $object->processFiles('images', [$default->images]);
        }

        $object->setEnabled(true);

        $object->setPrice((float) $default->price);

        $object->setCleanURL(\XLite\Core\Database::getRepo('XLite\Model\CleanURL')->generateCleanURL($object));
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     */
    public function afterPopulate($object, $rawData = null)
    {
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     */
    public function afterCreate($object, $rawData = null)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Attribute')->generateAttributeValues($object);

        if (!$object->getSku()) {
            $sku = \XLite\Core\Database::getRepo('XLite\Model\Product')->generateSKU($object);
            $object->setSku((string) $sku);
        }

        $fp = new \XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct();
        $fp->setProduct($object);
        $fp->setCategory(\XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory());

        \XLite\Core\Database::getEM()->persist($fp);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     */
    public function afterUpdate($object, $rawData = null)
    {
    }
}
