<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\ImageResize;

use XLite\Core\Skin;

/**
 * ImageResize
 */
class Generator extends \XLite\Logic\AGenerator
{
    const MODEL_PRODUCT  = 'XLite\Model\Image\Product\Image';
    const MODEL_CATEGORY = 'XLite\Model\Image\Category\Image';
    const MODEL_LOGO     = 'XLite\Model\Image\Common\Logo';

    /**
     * Image sizes
     *
     * @var array
     */
    protected static $imageSizes = [];

    /**
     * Image sizes cache
     *
     * @var array
     */
    protected static $imageSizesCache;

    /**
     * Returns available image sizes
     *
     * @return array
     */
    public static function defineImageSizes()
    {
        return [
            static::MODEL_PRODUCT  => [
                'XXSThumbnail'     => [60, 60],
                'XSThumbnail2'     => [60, 60], // Items list (admin area)
                'XSThumbnail'      => [60, 60], // Product thumbnail in the list of detailed images (details page)
                'SMThumbnail'      => [60, 60], // Product thumbnail on the cart items list
                'MDThumbnail'      => [120, 120],
                'CommonThumbnail'  => [120, 120], // Products list thumbnail (mainly for sidebar lists)
                'SBSmallThumbnail' => [160, 160], // Sidebar products list small thumbnail
                'SBBigThumbnail'   => [240, 240], // Sidebar products list big thumbnail
                'LGThumbnailList'  => [160, 160], // Center products list thumbnail
                'LGThumbnailGrid'  => [160, 160], // Center products grid thumbnail
                'Default'          => [300, 300], // Product thumbnail on the details page
            ],
            static::MODEL_CATEGORY => [
                'XXSThumbnail' => [60, 60],
                'XSThumbnail'  => [60, 60],
                'MDThumbnail'  => [120, 120],
                'LGThumbnail'  => [240, 240],
                'Default'      => [240, 240], // Category thumbnail
            ],
            static::MODEL_LOGO => [
                'Default' => [221, 40],
                'Invoice' => [330, 180]
            ],
        ];
    }

    /**
     * Get list of images sizes which administrator can edit via web interface
     *
     * @return array
     */
    public static function getEditableImageSizes()
    {
        return [
            static::MODEL_PRODUCT  => [
                'LGThumbnailList',
                'LGThumbnailGrid',
                'Default',
            ],
            static::MODEL_CATEGORY => [
                'Default',
            ],
            static::MODEL_LOGO     => [
                'Default',
            ],
        ];
    }

    /**
     * Add new sizes (or rewrite existing)
     *
     * @param array $sizes Image sizes
     */
    public static function addImageSizes(array $sizes)
    {
        static::$imageSizes = static::mergeImageSizes(static::$imageSizes, $sizes);
    }

    /**
     * Merge two sizes arrays
     *
     * @param array $baseSizes Base sizes
     * @param array $newSizes  New sizes
     *
     * @return array
     */
    public static function mergeImageSizes(array $baseSizes, array $newSizes)
    {
        foreach ($newSizes as $model => $modelSizes) {
            if (!is_array($modelSizes)) {
                continue;
            }

            foreach ($modelSizes as $name => $size) {
                if (!isset($baseSizes[$model])) {
                    $baseSizes[$model] = [];
                }

                if (is_numeric($name)) {
                    $baseSizes[$model][] = $size;

                } else {
                    $baseSizes[$model][$name] = $size;
                }
            }
        }

        return $baseSizes;
    }

    /**
     * Returns sizes for given class
     *
     * @param string $class Class
     *
     * @return array
     */
    public static function getModelImageSizes($class)
    {
        $sizes = static::getImageSizes();

        return isset($sizes[$class]) ? $sizes[$class] : [];
    }

    /**
     * Returns all sizes
     *
     * @param string $model Model OPTIONAL
     * @param string $code  Code OPTIONAL
     *
     * @return array
     */
    public static function getImageSizes($model = null, $code = null)
    {
        if (static::$imageSizesCache === null) {
            $baseSizes               = static::defineImageSizes();
            static::$imageSizesCache = static::mergeImageSizes($baseSizes, static::$imageSizes);

            $dbImageSizes = static::getDbImageSizes();
            if ($dbImageSizes) {
                static::$imageSizesCache = static::mergeImageSizes(static::$imageSizesCache, $dbImageSizes);
            }
        }

        if (null !== $model && null !== $code) {
            $result = isset(static::$imageSizesCache[$model][$code]) ? static::$imageSizesCache[$model][$code] : null;

        } else {
            $result = static::$imageSizesCache;
        }

        return $result;
    }

    /**
     * Get images sizes from database
     *
     * @return array
     */
    public static function getDbImageSizes()
    {
        $result = [];

        $sizes = Skin::getInstance()->getCurrentImagesSettings();

        if ($sizes) {
            foreach ($sizes as $size) {
                $result[$size->getModel()][$size->getCode()] = [
                    $size->getWidth(),
                    $size->getHeight(),
                ];
            }
        }

        return $result;
    }

    /**
     * @return void
     */
    public static function clearImageSizesCache()
    {
        static::$imageSizesCache = null;
    }

    // {{{ Steps

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            'XLite\Logic\ImageResize\Step\Categories',
            'XLite\Logic\ImageResize\Step\Products',
        ];
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'imageResize';
    }

    // }}}
}
