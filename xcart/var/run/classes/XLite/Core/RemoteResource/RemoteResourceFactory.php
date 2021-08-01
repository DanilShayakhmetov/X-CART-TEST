<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\RemoteResource;

class RemoteResourceFactory
{
    /**
     * @param string $url
     *
     * @return IURL
     * @throws RemoteResourceException
     */
    public static function getRemoteResourceByURL($url)
    {
        /** @var IURL[] $resources */
        $resources = array_merge(static::getResources(), ['XLite\Core\RemoteResource\PlainURL']);

        if (!\XLite\Core\Converter::isURL($url)) {
            $url = \Includes\Utils\Operator::purifyLink($url);
        }

        foreach ($resources as $resource) {
            if ($resource::isMatch($url)) {
                return new $resource($url);
            }
        }

        throw new RemoteResourceException('Wrong resource identifier: ' . $url);
    }

    /**
     * @return IURL[]
     */
    protected static function getResources()
    {
        return [
            'XLite\Core\RemoteResource\DropBox',
            'XLite\Core\RemoteResource\OneDrive',
            'XLite\Core\RemoteResource\GoogleDrive',
            'XLite\Core\RemoteResource\Local',
        ];
    }
}
