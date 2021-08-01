<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model;

/**
 * Category 
 */
class Category extends \XLite\Model\Category implements \XLite\Base\IDecorator
{
    use \XLite\Module\CDev\GoSocial\Core\OpenGraphTrait;

    /**
     * Custom Open graph meta tags
     *
     * @var string
     *
     * @Column (type="text", nullable=true)
     */
    protected $ogMeta = '';

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
        return (boolean)$this->getImage() || $this->isRootCategory();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageWidth()
    {
        return $this->getImage()
            ? $this->getImage()->getWidth()
            : null;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageHeight()
    {
        return $this->getImage()
            ? $this->getImage()->getHeight()
            : null;
    }

    /**
     * @inheritdoc
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
        return 'website';
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphDescription()
    {
        return strip_tags($this->getDescription());
    }

    /**
     * @inheritdoc
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        $categoryURL = $this->getParent()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL('category', '', ['category_id' => $this->getCategoryId()], \XLite::getCustomerScript())
                )
            )
            : \XLite::getInstance()->getShopURL();

        $imageURL = '';

        if ($this->getImage()) {
            $imageURL = $this->getImage()->getFrontURL();
        } elseif ($this->isRootCategory()) {
            $imageURL = \XLite::getInstance()->getShopURL(\XLite\Core\Layout::getInstance()->getLogo());
        }

        return strtr(
            $tags,
            [
                '[PAGE_URL]' => $categoryURL,
                '[IMAGE_URL]' => $imageURL,
            ]
        );
    }

    /**
     * Set useCustomOG
     *
     * @param boolean $useCustomOG
     * @return static
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
