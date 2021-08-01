<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core;

/**
 * Package operations
 */
class Package extends \XLite\Core\Package implements \XLite\Base\IDecorator
{
    /**
     * Check box for limits and return true if box is satisfied limits
     *
     * @param array  $box    Box properties
     * @param array  $limits Array of limits
     * @param string &$error Error message
     *
     * @return boolean
     */
    protected function checkLimits($box, $limits, &$error)
    {
        $result = true;

        $error = null;

        if (!empty($limits) && is_array($limits)) {

            foreach ($limits as $key => $limit) {
                $boxValue = isset($box['items'][0]['separate_box'])
                    ? $box['box'][$key] ?? null
                    : $box[$key] ?? null;

                if (
                    $boxValue !== null
                    && $boxValue > $limit
                ) {
                    $result = false;
                    $error = sprintf('Limit failure: %s = %s (limit is %s)', $key, $boxValue, $limit);
                    break;
                }
            }
        }

        return $result;
    }
}
