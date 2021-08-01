<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model;


class CategoryTranslation extends \XLite\Model\CategoryTranslation implements \XLite\Base\IDecorator
{
    /**
     * Custom Open graph meta tags
     *
     * @var string
     *
     * @Column (type="text", nullable=true)
     */
    protected $ogMeta = '';

    /**
     * Return OgMeta
     *
     * @return string
     */
    public function getOgMeta()
    {
        return $this->ogMeta;
    }

    /**
     * Set OgMeta
     *
     * @param string $ogMeta
     *
     * @return $this
     */
    public function setOgMeta($ogMeta)
    {
        $this->ogMeta = $ogMeta;
        return $this;
    }
}