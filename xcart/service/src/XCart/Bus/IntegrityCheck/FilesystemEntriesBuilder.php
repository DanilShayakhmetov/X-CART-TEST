<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\IntegrityCheck;

use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\Rebuild\KnownHashesException;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * Class FilesystemEntriesBuilder
 * @Service\Service()
 */
class FilesystemEntriesBuilder
{
    /**
     * @var KnownHashesRetriever
     */
    private $knownHashesRetriever;

    /**
     * @var ActualFilesRetriever
     */
    private $actualFilesRetriever;

    /**
     * IntegrityViolationProcessor constructor.
     *
     * @param KnownHashesRetriever             $knownHashesRetriever
     * @param ActualFilesRetriever             $actualFilesRetriever
     */
    public function __construct(
        KnownHashesRetriever $knownHashesRetriever,
        ActualFilesRetriever $actualFilesRetriever
    ) {
        $this->knownHashesRetriever = $knownHashesRetriever;
        $this->actualFilesRetriever = $actualFilesRetriever;
    }

    /**
     * @param Module $module
     *
     * @return array
     * @throws KnownHashesException
     */
    public function getFilesystemEntries(Module $module): array
    {
        $filesystemEntries = $this->buildFilesystemEntries(
            $this->knownHashesRetriever->getHashes($module),
            $this->actualFilesRetriever->getActualFilesPaths($module)
        );

        return $filesystemEntries;
    }

    /**
     * @param array $knownHashes
     * @param array $actualFiles
     *
     * @return array
     */
    protected function buildFilesystemEntries($knownHashes, $actualFiles)
    {
        $result = [];

        foreach ($knownHashes as $path => $knownHash) {
            $result[$path] = [
                'known'  => $knownHash,
                'actual' => null
            ];
        }

        foreach ($actualFiles as $path => $entry) {
            if (!isset($result[$path])) {
                $result[$path] = [
                    'known'  => null,
                    'actual' => null
                ];
            }

            $result[$path]['actual'] = $entry;
        }

        return $result;
    }
}
