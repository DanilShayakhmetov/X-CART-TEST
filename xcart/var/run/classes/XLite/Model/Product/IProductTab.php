<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Product;

/**
 * Product tab interface
 */
interface IProductTab
{
    /**
     * Return Name
     *
     * @return string|null
     */
    public function getServiceName();

    /**
     * Return Position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Check if tab available
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Returns tab name
     *
     * @return string
     */
    public function getName();
}