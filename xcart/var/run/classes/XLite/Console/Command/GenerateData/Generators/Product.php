<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\GenerateData\Generators;

/**
 * Class Product
 * @package XLite\Console\Command\GenerateData\Generators
 */
class Product
{
    private $attributes;
    private $options;
    private $optionsValues;
    private $productImages;
    private $wholesalePrices;
    /**
     * @var Image
     */
    private $imagesGenerator;

    public function __construct(
        $attributes,
        $options,
        $optionsValues,
        $productImages,
        $wholesalePrices,
        $imagesGenerator
    ) {
        $this->attributes = $attributes;
        $this->options = $options;
        $this->optionsValues = $optionsValues;
        $this->productImages = $productImages;
        $this->wholesalePrices = $wholesalePrices;
        $this->imagesGenerator = $imagesGenerator;
    }

    /**
     * @param $category
     * @param $suffix
     *
     * @return \XLite\Model\Product
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function generate($category, $suffix)
    {
        $product = $this->generateProductItself($category, $suffix);

        for ($i = 0; $i < $this->productImages; $i++) {
            $this->generateImages($product);
        }

        if (\XLite\Core\Operator::isClassExists('XLite\Module\CDev\Wholesale\Model\WholesalePrice')) {
            $this->generateWholesalePrices($product, $this->wholesalePrices);
        }

        for ($i = 0; $i < $this->attributes; $i++) {
            $this->generateAttributes($product, $i);
        }

        for ($i = 0; $i < $this->options; $i++) {
            $this->generateOptions($product, $i, $this->optionsValues);
        }

        return $product;
    }


    protected function generateProductItself(\XLite\Model\Category $category, $suffix)
    {
        /** @var \XLite\Model\Product $product */
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->insert(
            array(
                'sku'    => 'SKU' . $category->getCategoryId() . '_' . $suffix,
                'name'   => 'Test product #' . $category->getCategoryId() . ' - ' . $suffix,
                'price'  => mt_rand(1, 100),
                'weight' => mt_rand(1, 100),
            ),
            false
        );
        $link = new \XLite\Model\CategoryProducts;
        $link->setProduct($product);
        $link->setCategory($category);
        $product->addCategoryProducts($link);
        \XLite\Core\Database::getEM()->persist($link);

        return $product;
    }

    protected function generateImages(\XLite\Model\Product $product)
    {
        $image = new \XLite\Model\Image\Product\Image;
        $image->setProduct($product);

        if ($image->loadFromLocalFile($this->imagesGenerator->generateImage())) {
            $product->addImages($image);
        }
    }

    /**
     * @param \XLite\Model\Product $product
     * @param                      $count
     */
    protected function generateWholesalePrices(\XLite\Model\Product $product, $count)
    {
        $q1 = 1;
        $q2 = 10;
        for ($i = 0; $i < $count; $i++) {
            $last = $i === $count - 1;
            \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->insert(
                array(
                    'quantityRangeBegin' => $q1,
                    'quantityRangeEnd'   => $last ? 0 : $q2,
                    'product'            => $product,
                    'price'              => $product->getPrice() * round(mt_rand(0, 100) / 100, 2),
                ),
                false
            );
            $q1 = $q2 + 1;
            $q2 += 10;
        }

        \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\MinQuantity')->insert(
            array(
                'product'  => $product,
                'quantity' => 1,
            ),
            false
        );
    }

    protected function generateAttributes(\XLite\Model\Product $product, $suffix)
    {
        $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->insert(
            array(
                'product' => $product,
                'name'    => 'Test attribute ' . $product->getProductId() . '-' . $suffix,
            ),
            false
        );
        \XLite\Core\Database::getEM()->persist($attribute);
        $option = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->insert(
            array(
                'attribute' => $attribute,
                'name'      => 'value ' . $suffix,
            ),
            false
        );
        \XLite\Core\Database::getEM()->persist($option);
        $value = \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect')->insert(
            array(
                'attribute'        => $attribute,
                'product'          => $product,
                'attribute_option' => $option,
            ),
            false
        );
        \XLite\Core\Database::getEM()->persist($value);
    }

    /**
     * @param \XLite\Model\Product $product
     * @param                      $suffix
     * @param                      $optionsValuesCount
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    protected function generateOptions(\XLite\Model\Product $product, $suffix, $optionsValuesCount)
    {
        $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->insert(
            array(
                'product' => $product,
                'name'    => 'Test option ' . $product->getProductId() . '-' . $suffix,
            ),
            false
        );
        \XLite\Core\Database::getEM()->persist($attribute);
        for ($n = 0; $n < $optionsValuesCount; $n++) {
            $option = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->insert(
                array(
                    'attribute' => $attribute,
                    'name'      => 'value ' . $suffix . '-' . $n,
                ),
                false
            );
            \XLite\Core\Database::getEM()->persist($option);
            $value = \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect')->insert(
                array(
                    'attribute'        => $attribute,
                    'product'          => $product,
                    'attribute_option' => $option,
                    'priceModifier'    => $n > 0 ? round(mt_rand(0, 50) / 10, 1) : 0,
                ),
                false
            );
            \XLite\Core\Database::getEM()->persist($value);
        }
    }
}
