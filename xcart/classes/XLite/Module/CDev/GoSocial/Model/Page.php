<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model;

/**
 * Page
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
class Page extends \XLite\Module\CDev\SimpleCMS\Model\Page implements \XLite\Base\IDecorator
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
     * Show Social share buttons or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $showSocialButtons = false;

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
     * Set showSocialButtons
     *
     * @param boolean $showSocialButtons Show social buttons
     *
     * @return \XLite\Module\CDev\GoSocial\Model\Page
     */
    public function setShowSocialButtons($showSocialButtons)
    {
        $this->showSocialButtons = $showSocialButtons ? 1 : 0;

        return $this;
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
        return strip_tags($this->getTeaser());
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
        return str_replace(
            [
                '[PAGE_URL]',
                '[IMAGE_URL]',
            ],
            [
                htmlentities($this->getFrontURL(), ENT_COMPAT, 'UTF-8'),
                htmlentities($this->getImage() ? $this->getImage()->getFrontURL() : '', ENT_COMPAT, 'UTF-8'),
            ],
            $tags
        );
    }

    /**
     * Get showSocialButtons
     *
     * @return boolean 
     */
    public function getShowSocialButtons()
    {
        return $this->showSocialButtons;
    }
}

