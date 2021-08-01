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
 * @Service\Service()
 */
class IntegrityViolationProcessor
{
    /**
     * @var FilesystemEntriesBuilder
     */
    private $filesystemEntriesBuilder;

    /**
     * @param FilesystemEntriesBuilder $filesystemEntriesBuilder
     */
    public function __construct(
        FilesystemEntriesBuilder $filesystemEntriesBuilder
    ) {
        $this->filesystemEntriesBuilder = $filesystemEntriesBuilder;
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private static function postProcessFiles(array $files): array
    {
        $pattern = static::getExcludedPattern();

        return array_filter($files, function ($file) use ($pattern) {
            return !preg_match($pattern, $file);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return string
     */
    private static function getExcludedPattern(): string
    {
        $list = array_merge(
            [
                'list' => [],
                'raw'  => [],
            ],
            static::getCommonExcludePatterns()
        );

        $toImplode = $list['raw'];

        foreach ($list['list'] as $pattern) {
            $toImplode[] = preg_quote($pattern, '/');
        }

        return '/^(?:' . implode('|', $toImplode) . ')/Ss';
    }

    /**
     * @return array
     */
    private static function getCommonExcludePatterns(): array
    {
        $patterns = [
            ".*\/.gitattributes",
            ".*\/.gitignore",
            ".*\/?.htaccess",
            ".*\/?.DS_Store",
            ".*\/?.Modules.php",
            ".*.log",
            ".*skins\/common\/images\/flags_svg\/.*.svg",
        ];

        return [
            'list' => [],
            'raw'  => $patterns,
        ];
    }

    /**
     * @param Module $module
     * @param int    $start
     * @param int    $length
     *
     * @return array
     */
    public function getViolationsStructure(Module $module, $start = null, $length = null): ?array
    {
        try {
            $filesystemEntries = $this->filesystemEntriesBuilder->getFilesystemEntries($module);

            $endIndex = $start + $length;
            $progress = $endIndex / count($filesystemEntries);
            $isFinal  = $endIndex >= count($filesystemEntries);

            if ($start !== null || $length !== null) {
                $filesystemEntries = array_slice($filesystemEntries, (int) $start, $length);
            }

            $entries = $this->getViolations($filesystemEntries);

            return [
                'entries'  => $entries,
                'isFinal'  => $isFinal,
                'progress' => min(max($progress, 0), 1),
                'error'    => '',
            ];
        } catch (KnownHashesException $e) {
            return [
                'entries'  => [],
                'isFinal'  => true,
                'progress' => 1,
                'error'    => $e->getErrorMessage(),
            ];
        }
    }

    /**
     * @param array $filesystemEntries
     *
     * @return array
     */
    public function getViolations($filesystemEntries): array
    {
        $result = [];

        $filesystemEntries = static::postProcessFiles($filesystemEntries);
        foreach ($filesystemEntries as $path => $filesystemEntry) {
            $actual      = $filesystemEntry['actual'];
            $known       = $filesystemEntry['known'];
            $resultEntry = [
                'filepath'    => $path,
                'hash_actual' => '',
                'hash_known'  => (string) $known,
                'type'        => 'unknown',
            ];

            if ($actual && !$known) {
                $resultEntry['type'] = 'added';
                $result[]            = $resultEntry;
                continue;
            }

            if (!$actual && $known) {
                $resultEntry['type'] = 'removed';
                $result[]            = $resultEntry;
                continue;
            }

            $actualHash = file_exists($actual)
                ? md5_file($actual)
                : '';
            if ($actualHash !== $known) {
                $resultEntry['hash_actual'] = $actualHash;
                $resultEntry['type']        = 'modified';
                $result[]                   = $resultEntry;
                continue;
            }
        }

        return $result;
    }
}
