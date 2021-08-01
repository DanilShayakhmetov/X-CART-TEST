<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Archive;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ArchiveFactory
{
    /**
     * @var ExecTar
     */
    private $execTar;

    /**
     * @var ArchiveTar
     */
    private $archiveTar;

    /**
     * @var PharTar
     */
    private $pharTar;

    /**
     * @var AArchive
     */
    private $packer;

    /**
     * @var AArchive
     */
    private $unpacker;

    /**
     * @param ExecTar    $execTar
     * @param ArchiveTar $archiveTar
     * @param PharTar    $pharTar
     */
    public function __construct(
        ExecTar $execTar,
        ArchiveTar $archiveTar,
        PharTar $pharTar
    ) {
        $this->execTar    = $execTar;
        $this->archiveTar = $archiveTar;
        $this->pharTar    = $pharTar;
    }

    /**
     * @return AArchive
     */
    public function getPacker()
    {
        if ($this->packer === null) {
            $this->packer = $this->detectPacker();
        }

        return $this->packer;
    }

    /**
     * @return AArchive
     */
    public function getUnpacker()
    {
        if ($this->unpacker === null) {
            $this->unpacker = $this->detectUnpacker();
        }

        return $this->unpacker;
    }

    /**
     * @return AArchive
     */
    private function detectPacker()
    {
        if ($this->execTar->isApplicable()) {
            return $this->execTar;
        }

        if ($this->pharTar->isApplicable()) {
            return $this->pharTar;
        }

        if ($this->archiveTar->isApplicable()) {
            return $this->archiveTar;
        }

        return new DummyTar();
    }

    /**
     * @return AArchive
     */
    private function detectUnpacker()
    {
        if ($this->execTar->isApplicable()) {
            return $this->execTar;
        }

        if ($this->archiveTar->isApplicable()) {
            return $this->archiveTar;
        }

        if ($this->pharTar->isApplicable()) {
            return $this->pharTar;
        }

        return new DummyTar();
    }
}
