<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Model\Product;

/**
 * tab multilingual data
 *
 * @Entity
 * @Table (name="product_tab_translations")
 */

class TabTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Tab name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name = '';

    /**
     * Tab brief info
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $brief_info = '';

    /**
     * Tab Content
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $content = '';

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Return BriefInfo
     *
     * @return string
     */
    public function getBriefInfo()
    {
        return $this->brief_info;
    }

    /**
     * Set BriefInfo
     *
     * @param string $brief_info
     *
     * @return $this
     */
    public function setBriefInfo($brief_info)
    {
        $this->brief_info = $brief_info;
        return $this;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * @return $this
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
}
