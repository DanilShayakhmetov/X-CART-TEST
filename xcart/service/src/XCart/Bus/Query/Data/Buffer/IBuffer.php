<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Buffer;

interface IBuffer
{
    /**
     * @param mixed $e
     *
     * @return static
     */
    public function add($e);

    /**
     * @return static
     */
    public function clear();

    /**
     * @return array
     */
    public function getAll();

    /**
     * @param string $criteria
     *
     * @return mixed
     */
    public function getByCriteria($criteria);
}