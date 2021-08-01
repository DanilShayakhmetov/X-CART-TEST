<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI;

interface IMapper
{
    /**
     * @param mixed $data
     */
    public function setData($data);

    /**
     * @return mixed
     */
    public function getMapped();
}
