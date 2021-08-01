<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model;

/**
 * Product
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    use \XLite\Module\CDev\GoSocial\Core\OpenGraphTrait;

    /**
     * User Open graph meta tags generator flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useCustomOG = false;

    /**
     * @inheritdoc
     */
    protected function isUseOpenGraphImage()
    {
        return (boolean)$this->getImage();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageWidth()
    {
        return $this->getImage()
            ? $this->getImage()->getWidth()
            : 0;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageHeight()
    {
        return $this->getImage()
            ? $this->getImage()->getHeight()
            : 0;
    }

    /**
     * Get Open Graph meta tags
     *
     * @param boolean $preprocessed Preprocessed OPTIONAL
     *
     * @return string
     */
    public function getOpenGraphMetaTags($preprocessed = true)
    {
        $tags = $this->getUseCustomOG()
            ? $this->getOgMeta()
            : $this->generateOpenGraphMetaTags();

        return $preprocessed ? $this->preprocessOpenGraphMetaTags($tags) : $tags;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphTitle()
    {
        return $this->getName();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphType()
    {
        return 'product';
    }

    /**
     * @return array
     */
    protected function defineAdditionalMetaTags()
    {
        return [
                'product:availability'     => $this->availableInDate()
                    ? (
                    $this->isOutOfStock()
                        ? 'oos'
                        : 'instock'
                    )
                    : 'pending',
                'product:condition'        => 'new',
                'product:retailer_item_id' => $this->getSku(),
            ]
            + $this->getOgPrice()
            + $this->getOgWeight();
    }

    /**
     * @return array
     */
    protected function getOgWeight()
    {
        return $this->getWeight()
            ? [
                'product:weight:value' => $this->getWeight(),
                'product:weight:units' => $this->getWeightUnit(),
            ]
            : [];
    }

    /**
     * @return string
     */
    protected function getWeightUnit()
    {
        return \XLite\Core\Config::getInstance()->Units->weight_unit === 'lbs'
            ? 'lb'
            : \XLite\Core\Config::getInstance()->Units->weight_unit;
    }

    /**
     * @return array
     */
    protected function getOgPrice()
    {
        $result = [
            'product:price:amount' => $this->getClearPrice(),
            'product:price:currency' => \XLite::getInstance()->getCurrency()->getCode(),
        ];

        if ($this->getClearPrice() !== $this->getDisplayPrice()) {
            $result['product:sale_price:amount'] = $this->getDisplayPrice();
            $result['product:sale_price:currency'] = \XLite::getInstance()->getCurrency()->getCode();
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphDescription()
    {
        return strip_tags($this->getCommonDescription());
    }

    /**
     * @inheritdoc
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        return str_replace(
            [
                '[PAGE_URL]',
                '[IMAGE_URL]',
            ],
            [
                $this->getFrontURL(),
                $this->getImage() ? $this->getImage()->getFrontURL() : '',
            ],
            $tags
        );
    }

    /**
     * Set useCustomOG
     *
     * @param boolean $useCustomOG
     * @return Product
     */
    public function setUseCustomOG($useCustomOG)
    {
        $this->useCustomOG = $useCustomOG;
        return $this;
    }

    /**
     * Get useCustomOG
     *
     * @return boolean 
     */
    public function getUseCustomOG()
    {
        return $this->useCustomOG;
    }
}
