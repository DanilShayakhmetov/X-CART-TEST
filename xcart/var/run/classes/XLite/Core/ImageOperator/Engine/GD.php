<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator\Engine;

use XLite\Core\ImageOperator\ADTO;

/**
 * GD engine
 */
class GD extends \XLite\Core\ImageOperator\AEngine
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * mime type => gd type
     *
     * @var array
     */
    protected static $types = [
        'image/jpeg' => 'jpeg',
        'image/jpg'  => 'jpeg',
        'image/gif'  => 'gif',
        'image/xpm'  => 'xpm',
        'image/gd'   => 'gd',
        'image/gd2'  => 'gd2',
        'image/wbmp' => 'wbmp',
        'image/bmp'  => 'wbmp',
        'image/png'  => 'png',
    ];

    /**
     * Check - enabled engine or not
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return parent::isEnabled()
            && \XLite\Core\Converter::isGDEnabled();
    }

    /**
     * Get image GD-based type
     *
     * @param string $mimeType
     *
     * @return string|null
     */
    protected static function getGDImageType($mimeType)
    {
        return isset(static::$types[$mimeType]) ? static::$types[$mimeType] : null;
    }

    public function __construct()
    {
        parent::__construct();

        $this->options['unsharp_mask_filter_on_resize']
            = (bool) \XLite\Core\Config::getInstance()->Performance->unsharp_mask_filter_on_resize;
    }

    /**
     * Resize
     *
     * @param integer $width  Width
     * @param integer $height Height
     *
     * @return boolean
     */
    public function resize($width, $height)
    {
        $result = false;

        if ($this->resource) {
            $image = $this->getImage();
            $resource = $this->resource;

            $newResource = imagecreatetruecolor($width, $height);

            if (static::getGDImageType($image->getType()) == 'gif') {
                $transparency      = imagecolortransparent($image);
                $transparent_color = imagecolorsforindex($image, 127);
                $transparency      = imagecolorallocate($newResource, 255, 255, 255);
                imagefill($newResource, 0, 0, $transparency);
                imagecolortransparent($newResource, $transparency);
            } else {
                imagealphablending($newResource, false);
                imagesavealpha($newResource, true);
                $transparent = imagecolorallocatealpha($newResource, 255, 255, 255, 127);
                imagefilledrectangle($newResource, 0, 0, $width, $height, $transparent);
            }

            $result = imagecopyresampled(
                $newResource,
                $resource,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $image->getWidth(),
                $image->getHeight()
            );

            if ($result) {
                imagedestroy($resource);
                $this->resource = $this->postProcessResource($newResource);
                $this->updateImageFromResource();
            }
        }

        return $result;
    }

    /**
     * Resize bulk
     *
     * @param array $sizes
     *
     * @return array
     */
    public function resizeBulk($sizes)
    {
        $result = [];

        if ($this->resource) {
            $image = $this->getImage();
            $resource = $this->resource;

            foreach ($sizes as $key => $size) {
                $newResource = imagecreatetruecolor($size['width'], $size['height']);

                if (static::getGDImageType($image->getType()) == 'gif') {
                    $transparency      = imagecolortransparent($image);
                    $transparent_color = imagecolorsforindex($image, 127);
                    $transparency      = imagecolorallocate($newResource, 255, 255, 255);
                    imagefill($newResource, 0, 0, $transparency);
                    imagecolortransparent($newResource, $transparency);
                } else {
                    imagealphablending($newResource, false);
                    imagesavealpha($newResource, true);
                    $transparent = imagecolorallocatealpha($newResource, 255, 255, 255, 127);
                    imagefilledrectangle($newResource, 0, 0, $size['width'], $size['height'], $transparent);
                }

                $result = imagecopyresampled(
                    $newResource,
                    $resource,
                    0,
                    0,
                    0,
                    0,
                    $size['width'],
                    $size['height'],
                    $image->getWidth(),
                    $image->getHeight()
                );

                if ($result) {
                    $tmpResource = $this->postProcessResource($newResource);
                    $tmpImage = clone $this->getImage();
                    $this->updateImageFromResource(null, $tmpResource, $tmpImage);

                    $sizes[$key]['tmp'] = $tmpImage;
                    imagedestroy($tmpResource);
                }
            }

            $result = $sizes;

            imagedestroy($resource);
        }

        return $result;
    }

    public function rotate($degree)
    {
        $result = false;

        if ($this->resource) {
            $resource = imagerotate($this->resource, $degree, 0);
            imagedestroy($this->resource);
            $this->resource = $resource;
            $this->updateImageFromResource(100);
            return true;
        }

        return $result;
    }

    public function mirror($horizontal = true)
    {
        $result = false;

        if ($this->resource) {
            $image = $this->getImage();
            $resource = $this->resource;
            $width = $image->getWidth();
            $height = $image->getHeight();

            $newResource = imagecreatetruecolor($width, $height);

            $transparentIndex = imagecolortransparent($resource);

            if ($transparentIndex >= 0) {
                imagepalettecopy($resource, $newResource);
                imagefill($newResource, 0, 0, $transparentIndex);
                imagecolortransparent($newResource, $transparentIndex);
                imagetruecolortopalette($newResource, true, 256);

            } else {
                imagealphablending($newResource, false);
                imagesavealpha($newResource, true);

                $transparent = imagecolorallocatealpha($newResource, 255, 255, 255, 127);
                imagefilledrectangle($newResource, 0, 0, $width, $height, $transparent);
            }

            $result = imagecopyresampled(
                $newResource,
                $resource,
                0,
                0,
                $horizontal ? ($width - 1) : 0,
                !$horizontal ? ($height - 1) : 0,
                $width,
                $height,
                $horizontal ? -$width : $width,
                !$horizontal ? -$height : $height
            );

            if ($result) {
                imagedestroy($resource);
                $this->resource = $newResource;
                $this->updateImageFromResource(100);
            }
        }

        return $result;
    }


    protected function postProcessResource($resource)
    {
        if ($this->options['progressive']) {
            imageinterlace($resource, true);
        }

        if ($this->options['unsharp_mask_filter_on_resize']) {
            include_once LC_DIR_LIB . 'phpunsharpmask.php';

            $unsharpedResource = UnsharpMask($resource);
            if ($unsharpedResource) {
                $resource = $unsharpedResource;
            }
        }

        return $resource;
    }

    protected function updateImageFromResource($quality = null, $resource = null, $image = null)
    {
        $quality  = $quality ?: $this->getQuality();
        $resource = $resource ?: $this->resource;
        $image    = $image ?: $this->getImage();

        $func = 'image' . static::getGDImageType($image->getType());

        if ($resource && function_exists($func)) {
            $quality = $this->processResultQuality($quality);

            ob_start();
            if ($quality !== null) {
                $func($resource, null, $quality);

            } else {
                $func($resource);
            }

            $image->setBody(ob_get_contents());
            $image->setWidth(imagesx($resource));
            $image->setHeight(imagesy($resource));

            ob_end_clean();
        }
    }

    /**
     * Returns image quality
     *
     * @param integer $quality
     *
     * @return integer
     */
    protected function processResultQuality($quality)
    {
        $image = $this->getImage();

        switch (static::getGDImageType($image->getType())) {
            case 'jpeg':
                $result = $quality;
                break;

            case 'png':
                $result = ((100 - $quality) / 100) * 9;
                break;

            default:
                $result = null;
        }

        return $result;
    }

    /**
     * Get quality from options
     *
     * @return mixed
     */
    protected function getQuality()
    {
        return $this->options['resize_quality'] ?: 100;
    }

    /**
     * @param ADTO $image
     */
    public function setImage($image)
    {
        parent::setImage($image);

        $body = $image->getBody();
        $this->resource = $image && $body && function_exists('imagecreatefromstring')
            ? imagecreatefromstring($body)
            : null;
    }
}
