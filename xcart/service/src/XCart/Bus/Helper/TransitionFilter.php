<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Helper;

use XCart\Bus\Domain\Module;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class TransitionFilter
{
    /**
     * @param array $list
     *
     * @return array
     */
    public function sortAscending(array $list): array
    {
        uasort($list, static function ($a, $b) {
            if ($a['id'] === 'CDev-Core') {
                return -1;
            }

            if ($b['id'] === 'CDev-Core') {
                return 1;
            }

            return strcmp($a['id'], $b['id']);
        });

        return $list;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    public function sortDescending(array $list): array
    {
        return array_reverse($this->sortAscending($list));
    }
}
