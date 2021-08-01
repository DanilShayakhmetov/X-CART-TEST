<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model;

/**
 * Sale discount
 *
 * @Decorator\Depend ("CDev\Sale")
 */
 class SaleDiscount extends \XLite\Module\CDev\Sale\Model\SaleDiscountAbstract implements \XLite\Base\IDecorator
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
     * Custom Open graph meta tags
     *
     * @var string
     *
     * @Column (type="text", nullable=true)
     */
    protected $ogMeta = '';

    /**
     * @inheritdoc
     */
    protected function isUseOpenGraphImage()
    {
        return false;
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
        return 'product.group';
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
    protected function getOpenGraphDescription()
    {
        return strip_tags($this->getMetaDesc());
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

    /**
     * @inheritdoc
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        $saleDiscountUrl = $this->getId()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'sale_discount',
                        '',
                        ['id' => $this->getId()],
                        \XLite::getCustomerScript(),
                        true
                    )
                )
            ) : \XLite::getInstance()->getShopURL();

        return str_replace(
            [
                '[PAGE_URL]',
            ],
            [
                htmlentities($saleDiscountUrl, ENT_COMPAT, 'UTF-8'),
            ],
            $tags
        );
    }
}

