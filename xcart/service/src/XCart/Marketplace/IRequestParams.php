<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace;

interface IRequestParams
{
    /**
     * @param array $params
     * @param array $config
     */
    public function __construct(array $params = [], array $config = []);

    /**
     * @return array
     */
    public function getParams();

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $data
     */
    public function setData(array $data);
}
