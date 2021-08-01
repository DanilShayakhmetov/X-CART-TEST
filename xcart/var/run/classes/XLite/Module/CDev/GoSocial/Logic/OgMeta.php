<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Logic;


/**
 * OgMeta
 */
class OgMeta
{
    /**
     * Strip unallowed tags
     *
     * @param $data
     *
     * @return string
     */
    public static function prepareOgMeta($data)
    {
        $data = strip_tags(
            (string)$data,
            implode('', static::prepareAllowedOgMetaTags(
                static::getAllowedOgMetaTags()
            ))
        );

        return \XLite\Core\Converter::filterCurlyBrackets($data);
    }

    /**
     * @return array
     */
    protected static function getAllowedOgMetaTags()
    {
        return [
            'meta',
        ];
    }

    /**
     * Prepare tags list for strip_tags function
     *
     * @param array $tags
     *
     * @return array
     */
    protected static function prepareAllowedOgMetaTags(array $tags)
    {
        return array_map(
            function ($tag) {
                return "<{$tag}>";
            },
            $tags
        );
    }
}