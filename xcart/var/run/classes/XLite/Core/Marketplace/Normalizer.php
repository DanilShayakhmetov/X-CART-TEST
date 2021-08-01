<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace;

abstract class Normalizer
{
    abstract public function normalize($response);

    /**
     * @param array $entity
     * @param array $map
     *
     * @return array
     */
    protected function mapFields(array $entity, array $map) {
        $result = [];

        foreach ($map as $field => $replace) {
            $result[$replace] = isset($entity[$field])
                ? $entity[$field]
                : null;
        }

        return $result;
    }
}