<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Image
 */
class Image extends \XLite\View\AView
{
    /**
     * Widget arguments names
     */
    const PARAM_IMAGE             = 'image';
    const PARAM_ALT               = 'alt';
    const PARAM_MAX_WIDTH         = 'maxWidth';
    const PARAM_MAX_HEIGHT        = 'maxHeight';
    const PARAM_SIZE_ID           = 'sizeId';
    const PARAM_CENTER_IMAGE      = 'centerImage';
    const PARAM_VERTICAL_ALIGN    = 'verticalAlign';
    const PARAM_USE_CACHE         = 'useCache';
    const PARAM_USE_DEFAULT_IMAGE = 'useDefaultImage';
    const PARAM_IMAGE_SIZE_TYPE   = 'imageSizeType';
    const PARAM_LAZY_LOAD         = 'lazyLoad';
    const PARAM_USE_TIMESTAMP     = 'useTimestamp';
    const PARAM_RESIZE_IMAGE      = 'resizeImage';


    /**
     * Vertical align types
     */
    const VERTICAL_ALIGN_TOP    = 'top';
    const VERTICAL_ALIGN_MIDDLE = 'middle';
    const VERTICAL_ALIGN_BOTTOM = 'bottom';

    /**
     * Default image (no image) dimensions
     */
    const DEFAULT_IMAGE_WIDTH  = 120;
    const DEFAULT_IMAGE_HEIGHT = 120;

    /**
     * Allowed properties names
     *
     * @var array
     */
    protected $allowedProperties = [
        'className'   => 'class',
        'id'          => 'id',
        'onclick'     => 'onclick',
        'style'       => 'style',
        'onmousemove' => 'onmousemove',
        'onmouseup'   => 'onmouseup',
        'onmousedown' => 'onmousedown',
        'onmouseover' => 'onmouseover',
        'onmouseout'  => 'onmouseout',
    ];

    /**
     * Additioanl properties
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Resized thumbnail URL
     *
     * @var string
     */
    protected $resizedURL = null;

    /**
     * Retina resized thumbnail URL
     *
     * @var string
     */
    protected $retinaResizedURL = null;

    /**
     * Use default image
     *
     * @var boolean
     */
    protected $useDefaultImage = false;

    /**
     * Set widget parameters
     *
     * @param array $params Widget parameters
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        // Save additional parameters
        foreach ($params as $name => $value) {
            if (isset($this->allowedProperties[$name])) {
                $this->properties[$this->allowedProperties[$name]] = $value;
            }
        }

        if ($this->getParam(self::PARAM_MAX_WIDTH) == 0
            && $this->getParam(self::PARAM_MAX_HEIGHT) == 0
            && $this->getParam(self::PARAM_IMAGE_SIZE_TYPE)
        ) {
            list($width, $height) = \XLite\Logic\ImageResize\Generator::getImageSizes(
                \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT,
                $this->getParam(self::PARAM_IMAGE_SIZE_TYPE)
            );

            if ($width && $height) {
                $this->getWidgetParams(self::PARAM_MAX_WIDTH)->setValue($width);
                $this->getWidgetParams(self::PARAM_MAX_HEIGHT)->setValue($height);
            }
        }
    }

    /**
     * Get image URL
     *
     * @return string
     */
    public function getURL()
    {
        $url = null;

        $image = $this->getParam(self::PARAM_IMAGE);
        if ($image && $image->isExists()) {
            // Specified image
            $url = $this->getParam(self::PARAM_USE_CACHE)
                ? $this->resizedURL
                : $image->getFrontURL();

            if ($this->getParam(self::PARAM_USE_TIMESTAMP)) {
                $url .= '?' . time();
            }
        }

        if (!$url && $this->getParam(self::PARAM_USE_DEFAULT_IMAGE)) {
            // Default image
            $url = \XLite::getInstance()->getOptions(['images', 'default_image']);

            if (!\XLite\Core\Converter::isURL($url)) {
                $url = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    $url,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL
                );
                $this->useDefaultImage = true;
            }
        }

        return $this->prepareURL($url);
    }

    /**
     * Get image alternative text
     *
     * @return void
     */
    public function getAlt()
    {
        return $this->getParam(self::PARAM_ALT);
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties()
    {
        $this->properties['src'] = $this->getURL();
        $this->properties['alt'] = $this->getAlt();
        if ($this->getParam(self::PARAM_MAX_WIDTH)) {
            $this->properties['data-max-width'] = max(0, $this->getParam(self::PARAM_MAX_WIDTH));
        }
        if ($this->getParam(self::PARAM_MAX_HEIGHT)) {
            $this->properties['data-max-height'] = max(0, $this->getParam(self::PARAM_MAX_HEIGHT));
        }
        if ($this->useDefaultImage) {
            $this->properties['data-is-default-image'] = $this->useDefaultImage;
        }

        if (
            $this->getParam(self::PARAM_IMAGE)
            && $this->getParam(self::PARAM_IMAGE)->isExists()
            && $this->getParam(self::PARAM_USE_CACHE)
            && ($this->resizedURL !== $this->retinaResizedURL &&
                !$this->isOriginalImageUrl($this->retinaResizedURL)
            )
        ) {
            $this->properties['srcset'] = $this->retinaResizedURL
                . ' ' . \XLite\Model\Base\Image::RETINA_RATIO . 'x';
        }

        if (
            $this->getParam(static::PARAM_LAZY_LOAD)
            && \XLite\Core\Layout::getInstance()->isLazyLoadEnabled()
        ) {
            $this->properties['data-src'] = $this->properties['src'];
            unset($this->properties['src']);
            $this->properties['class'] = ($this->properties['class'] ?? '') . ' lazyload ';

            if (!empty($this->properties['srcset'])) {
                $this->properties['data-srcset'] = $this->properties['srcset'];
                unset($this->properties['srcset']);
            }
        }

        return $this->properties;
    }

    /**
     * Checks if image url is not in images cache folder
     *
     * @param string $url Image URL
     *
     * @return bool
     */
    protected function isOriginalImageUrl($url)
    {
        $shopUrl = \XLite::getInstance()->getShopURL();

        if (strpos($url, $shopUrl) === 0) {
            $localPath = str_replace($shopUrl, '', $url);

            return \Includes\Utils\FileManager::getRelativePath($localPath, LC_DIR_IMAGES) !== null;
        }

        return false;
    }

    /**
     * Remove the protocol from the url definition
     *
     * @param string $url
     *
     * @return string
     */
    protected function prepareURL($url)
    {
        return str_replace(['http://', 'https://'], '//', $url);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/image.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_IMAGE             => new \XLite\Model\WidgetParam\TypeObject('Image', null, false, '\XLite\Model\Base\Image'),
            self::PARAM_ALT               => new \XLite\Model\WidgetParam\TypeString('Alt. text', '', false),
            self::PARAM_MAX_WIDTH         => new \XLite\Model\WidgetParam\TypeInt('Max. width', 0),
            self::PARAM_MAX_HEIGHT        => new \XLite\Model\WidgetParam\TypeInt('Max. height', 0),
            self::PARAM_CENTER_IMAGE      => new \XLite\Model\WidgetParam\TypeCheckbox('Center the image after resizing', true),
            self::PARAM_VERTICAL_ALIGN    => new \XLite\Model\WidgetParam\TypeString('Vertical align', self::VERTICAL_ALIGN_MIDDLE),
            self::PARAM_USE_CACHE         => new \XLite\Model\WidgetParam\TypeBool('Use cache', 1),
            self::PARAM_USE_DEFAULT_IMAGE => new \XLite\Model\WidgetParam\TypeBool('Use default image', 1),
            self::PARAM_IMAGE_SIZE_TYPE   => new \XLite\Model\WidgetParam\TypeString('Imeage size type', ''),
            self::PARAM_LAZY_LOAD         => new \XLite\Model\WidgetParam\TypeBool('Lazy load', 0),
            self::PARAM_USE_TIMESTAMP     => new \XLite\Model\WidgetParam\TypeBool('Use timestamp', 0),
            self::PARAM_RESIZE_IMAGE      => new \XLite\Model\WidgetParam\TypeBool('Resize Image', 1),
        ];
    }

    /**
     * checkImage
     *
     * @return boolean
     */
    protected function checkImage()
    {
        return $this->getParam(self::PARAM_IMAGE)
               && $this->getParam(self::PARAM_IMAGE)->isExists();
    }

    /**
     * checkDefaultImage
     *
     * @return boolean
     */
    protected function checkDefaultImage()
    {
        return $this->getParam(self::PARAM_USE_DEFAULT_IMAGE)
               && \XLite::getInstance()->getOptions(['images', 'default_image']);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible();

        if ($result) {

            if ($this->checkImage()) {
                $this->processImage();

            } elseif ($this->checkDefaultImage()) {
                $this->processDefaultImage();

            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Return a CSS style centering the image vertically and horizontally
     *
     * @return string
     */
    protected function setImageMargin()
    {
        $vertical = ($this->getParam(self::PARAM_MAX_HEIGHT) - (isset($this->properties['height']) ? $this->properties['height'] : 0)) / 2;

        switch ($this->getParam(self::PARAM_VERTICAL_ALIGN)) {
            case self::VERTICAL_ALIGN_TOP:
                $top = 0;
                $bottom = 0;
                break;

            case self::VERTICAL_ALIGN_BOTTOM:
                $top = $this->getParam(self::PARAM_MAX_HEIGHT) - $this->properties['height'];
                $bottom = 0;
                break;

            default:
                $top = max(0, ceil($vertical));
                $bottom = max(0, floor($vertical));
        }

        if (0 < $top || 0 < $bottom) {
            $this->addInlineStyle('margin: 0 auto;margin-bottom:' . $bottom . 'px;' . 'margin-top:' . $top . 'px;');
        }
    }

    /**
     * Add CSS styles to the value of "style" attribute of the image tag
     *
     * @param string $style CSS styles to be added to the end of "style" attribute
     *
     * @return void
     */
    protected function addInlineStyle($style)
    {
        if (!isset($this->properties['style'])) {
            $this->properties['style'] = $style;

        } else {
            $this->properties['style'] .= ' ' . $style;
        }
    }

    /**
     * Fit image to first available size if available
     *
     * @return array
     */
    protected function fitImage()
    {
        $width = max(0, $this->getParam(self::PARAM_MAX_WIDTH));
        $height = max(0, $this->getParam(self::PARAM_MAX_HEIGHT));
        $image = $this->getParam(self::PARAM_IMAGE);
        $sizes = $image ? \XLite\Logic\ImageResize\Generator::getModelImageSizes(get_class($image)) : [];

        if ($sizes && $width && $height) {
            $newmaxw = 0;
            $newmaxh = 0;
            foreach ($sizes as $name => $size) {
                if ($width <= $size[0] && $height <= $size[1]) {
                    if (($newmaxw >= $size[0] && $newmaxh >= $size[1]) || (!$newmaxw && !$newmaxh)) {
                        $newmaxw = $size[0];
                        $newmaxh = $size[1];
                    }
                }
            }
            if ($newmaxw && $newmaxh) {
                $width  = $newmaxw;
                $height = $newmaxh;
            }
        }

        return [$width, $height];
    }

    /**
     * Preprocess image
     *
     * @return void
     */
    protected function processImage()
    {
        if ($this->getParam(self::PARAM_RESIZE_IMAGE)) {

            list(
                $maxw,
                $maxh
                ) = $this->fitImage();

            list(
                $this->properties['width'],
                $this->properties['height'],
                $this->resizedURL,
                $this->retinaResizedURL
                ) = $this->getParam(self::PARAM_IMAGE)->getResizedURL($maxw, $maxh, $this->getParam(self::PARAM_MAX_WIDTH), $this->getParam(self::PARAM_MAX_HEIGHT));
        }

        // Center the image vertically and horizontally
        if ($this->getParam(self::PARAM_CENTER_IMAGE)) {
            $this->setImageMargin();
        }
    }

    /**
     * Preprocess default image
     *
     * @return void
     */
    protected function processDefaultImage()
    {

        list(
            $maxw,
            $maxh
            ) = $this->fitImage();

        list($this->properties['width'], $this->properties['height']) = \XLite\Core\ImageOperator::getCroppedDimensions(
            static::DEFAULT_IMAGE_WIDTH,
            static::DEFAULT_IMAGE_HEIGHT,
            $maxw,
            $maxh
        );

        // Center the image vertically and horizontally
        if ($this->getParam(self::PARAM_CENTER_IMAGE)) {
            $this->setImageMargin();
        }
    }
}
