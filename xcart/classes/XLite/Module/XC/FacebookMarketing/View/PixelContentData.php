<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View;

/**
 * Class PixelContentData
 *
 * @ListChild (list="center.top", weight="10")
 */
class PixelContentData extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FacebookMarketing/pixel_content_data/body.twig';
    }

    protected function getPixelData()
    {
        $pixelData = [];
        $target = \XLite::getController()->getTarget();
        switch (true) {
            case ('main' === $target):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Home page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ('category' === $target):
                $pixelData = [
                    'type' => 'category',
                    'data' => [
                        'content_name' => $this->getCategory()->getName(),
                        'content_category' => implode(
                            ' > ',
                            array_map(
                                function($category) {
                                    return $category->getName();
                                },
                                $this->getCategory()->getPath())
                        ),
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ('sale_products' === $target):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Sale page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ('coming_soon' === $target):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Coming soon page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ('new_arrivals' === $target):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'New arrivals page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ('bestsellers' === $target):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Bestsellers page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
        }

        return $pixelData;
    }
}