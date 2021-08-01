<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\GenerateData\Generators;


use Symfony\Component\Filesystem\Filesystem;

class Image
{
    const TYPE_SAME     = 'same';
    const TYPE_UNIQUE   = 'unique';

    private $type;
    private $filesystem;

    public function __construct($type)
    {
        if (
            $type
            && !in_array($type, [
                static::TYPE_SAME,
                static::TYPE_UNIQUE,
            ], true)
        ) {
            throw new \InvalidArgumentException("Unknown type '{$type}'");
        }

        $this->type = $type ?: static::TYPE_SAME;
        $this->filesystem = new Filesystem();
    }

    /**
     * Returns new image path
     *
     * @return string
     * @throws \Exception
     */
    public function generateImage()
    {
        switch ($this->type) {
            case static::TYPE_UNIQUE:
                return $this->generateUniqueImage();
            default:
                return $this->getDefaultImagePath();
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateUniqueImage()
    {
        $image = imagecreate(rand(300, 400), rand(200, 300));
        imagecolorallocate(
            $image,
            rand(0, 255),
            rand(0, 255),
            rand(0, 255)
        );
        ob_start();
        imagepng($image);
        $content = ob_get_contents();
        ob_end_clean();

        $path = $this->generateNewImagePath();

        $this->filesystem->dumpFile($path, $content);

        return $path;
    }

    private static function getTmpDir()
    {
        return LC_DIR_VAR . 'tmp/dump_images/';
    }

    public static function clearTmpDir()
    {
        (new Filesystem())->remove(static::getTmpDir());
    }

    /**
     * @return string
     */
    private function generateNewImagePath()
    {
        $path = static::getTmpDir() . md5(microtime(true) + mt_rand(0, 1000000)) . '.png';

        return file_exists($path)
            ? $this->generateNewImagePath()
            : $path;
    }

    /**
     * @return string
     */
    private function getDefaultImagePath()
    {
        return LC_DIR_ROOT . 'public/error_image.png';
    }
}