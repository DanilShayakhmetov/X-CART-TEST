<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator\Engine;

/**
 * Simple engine
 */
class Simple extends \XLite\Core\ImageOperator\AEngine
{
    /**
     * Resize
     *
     * @param integer $width  Width
     * @param integer $height Height
     *
     * @return boolean
     */
    public function resize($width, $height)
    {
        return false;
    }

    /**
     * Resize bulk
     *
     * @param array $sizes
     *
     * @return array
     */
    public function resizeBulk($sizes)
    {
        return [];
    }

    /**
     * Rotate
     *
     * @param integer $degree Degree
     *
     * @return boolean
     */
    public function rotate($degree)
    {
        return false;
    }

    /**
     * Mirror
     *
     * @param boolean $horizontal type of mirroring
     *
     * @return boolean
     */
    public function mirror($horizontal = true)
    {
        return false;
    }
}
