<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\System;

use LogicException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use XCart\Bus\Exception\UploadException;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Uploader
{
    const FILEPART_EXT = '.filepart';

    /**
     * @var string
     */
    private $tmpDir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Application $app
     * @param Filesystem  $filesystem
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(Application $app, Filesystem $filesystem)
    {
        $filesystem->mkdir($app['config']['module_packs_dir']);

        return new self(
            $app['config']['tmp_dir'],
            $filesystem
        );
    }

    /**
     * @param string     $tmpDir
     * @param Filesystem $filesystem
     */
    public function __construct($tmpDir, Filesystem $filesystem)
    {
        $this->tmpDir     = $tmpDir;
        $this->filesystem = $filesystem;
    }

    /**
     * Process chunk upload
     *
     * @param Request $request
     *
     * @throws UploadException
     * @throws LogicException
     */
    public function processChunked(Request $request): void
    {
        $basename = $request->headers->get('X-Filename');
        $path     = $this->getTmpFilepath($basename);

        if ($this->isRestrictedType($basename)) {
            throw UploadException::fromRestrictedType($basename);
        }

        if ($request->headers->get('X-Offset') === 0) {
            $this->filesystem->remove($path);
        }

        if (($content = $request->getContent()) === false) {
            throw UploadException::fromNoInput();
        }

        if ($handle = fopen($path, 'ab')) {
            fwrite($handle, $content);
            fclose($handle);

        } else {
            throw UploadException::fromNoOutput();
        }
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function finalizeUpload(Request $request): string
    {
        $basename = $request->headers->get('X-Filename');

        if ($basename) {
            $this->filesystem->rename($this->getTmpFilepath($basename), $this->tmpDir . $basename, true);

            return $this->tmpDir . $basename;
        }

        return null;
    }

    /**
     * @param Request $request
     */
    public function cancelUpload(Request $request): void
    {
        $basename = $request->headers->get('X-Filename');

        if ($basename) {
            $this->filesystem->remove($this->getTmpFilepath($basename));
            $this->filesystem->remove($this->tmpDir . $basename);
        }
    }

    /**
     * @param string $basename
     *
     * @return string
     */
    public function getTmpFilepath($basename): string
    {
        return $this->tmpDir . $basename . static::FILEPART_EXT;
    }

    /**
     * @param string $mime
     *
     * @return bool
     */
    private function isRestrictedType($name): bool
    {
        return !preg_match('/(tar|tar\.gz|tgz)$/i', $name);
    }
}
