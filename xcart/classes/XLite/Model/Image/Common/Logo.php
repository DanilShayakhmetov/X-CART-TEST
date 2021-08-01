<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image\Common;

/**
 * Content images file storage
 *
 * @Entity
 * @Table  (name="logo_images")
 */
class Logo extends \XLite\Model\Base\Image
{
    /**
     * Alternative image text
     *
     * @var string
     */
    protected $alt = '';

    /**
     * Set alt
     *
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Check - image is exists in DB or not
     *
     * @return boolean
     */
    public function isExists()
    {
        return true;
    }

    /**
     * Resize on view
     *
     * @return boolean
     */
    protected function isUseDynamicImageResizing()
    {
        return true;
    }
}
