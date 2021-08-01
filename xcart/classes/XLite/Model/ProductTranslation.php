<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Product multilingual data
 *
 * @Entity
 *
 * @Table (name="product_translations",
 *         indexes={
 *              @Index (name="ci", columns={"code","id"}),
 *              @Index (name="id", columns={"id"}),
 *              @Index (name="name", columns={"name"})
 *         }
 * )
 */
class ProductTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Product name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Product description
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $description = '';

    /**
     * Product brief description
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $briefDescription = '';

    /**
     * Meta tags
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $metaTags = '';

    /**
     * Product meta description
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $metaDesc = '';

    /**
     * Meta title
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $metaTitle = '';

    /**
     * Set name
     *
     * @param string $name
     * @return ProductTranslation
     */
    public function setName($name)
    {
        $this->name = mb_substr($name, 0 , $this->getFieldLength('name'));
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ProductTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set briefDescription
     *
     * @param string $briefDescription
     * @return ProductTranslation
     */
    public function setBriefDescription($briefDescription)
    {
        $this->briefDescription = $briefDescription;
        return $this;
    }

    /**
     * Get briefDescription
     *
     * @return string
     */
    public function getBriefDescription()
    {
        return $this->briefDescription;
    }

    /**
     * Set metaTags
     *
     * @param string $metaTags
     * @return ProductTranslation
     */
    public function setMetaTags($metaTags)
    {
        $this->metaTags = mb_substr($metaTags, 0, $this->getFieldLength('metaTags'));
        return $this;
    }

    /**
     * Get metaTags
     *
     * @return string 
     */
    public function getMetaTags()
    {
        return $this->metaTags;
    }

    /**
     * Set metaDesc
     *
     * @param string $metaDesc
     * @return ProductTranslation
     */
    public function setMetaDesc($metaDesc)
    {
        $this->metaDesc = $metaDesc;
        return $this;
    }

    /**
     * Get metaDesc
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return $this->metaDesc;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return ProductTranslation
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = mb_substr($metaTitle, 0, $this->getFieldLength('metaTitle'));
        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string 
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Get label_id
     *
     * @return integer 
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return ProductTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    protected function getFieldLength($name)
    {
        return $this->getRepository()->getFieldInfo($name, 'length');
    }
}
