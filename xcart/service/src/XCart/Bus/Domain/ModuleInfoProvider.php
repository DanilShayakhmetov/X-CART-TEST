<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain;

use Silex\Application;
use Symfony\Component\Yaml\Parser;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ModuleInfoProvider
{
    /**
     * @var array
     */
    private static $skinModel = [
        'admin'    => ['admin' => []],
        'customer' => ['customer' => []],
        'console'  => ['console' => []],
        'mail'     => ['mail' => ['customer', 'common', 'admin']],
        'common'   => ['common' => []],
        'pdf'      => ['pdf' => ['customer', 'common', 'admin']],
    ];

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var ServiceDataProvider
     */
    private $serviceDataProvider;

    /**
     * @var ModuleFilesFactory
     */
    private $moduleFilesFactory;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param Application         $app
     * @param ServiceDataProvider $serviceDataProvider
     * @param ModuleFilesFactory  $moduleFilesFactory
     * @param FilesystemInterface $filesystem
     *
     * @return static
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        ServiceDataProvider $serviceDataProvider,
        ModuleFilesFactory $moduleFilesFactory,
        FilesystemInterface $filesystem
    ) {
        return new self(
            $app['config']['root_dir'],
            $serviceDataProvider,
            $moduleFilesFactory,
            $filesystem
        );
    }

    /**
     * @param string              $rootDir
     * @param ServiceDataProvider $serviceDataProvider
     * @param ModuleFilesFactory  $moduleFilesFactory
     * @param FilesystemInterface $filesystem
     */
    public function __construct(
        $rootDir,
        ServiceDataProvider $serviceDataProvider,
        ModuleFilesFactory $moduleFilesFactory,
        FilesystemInterface $filesystem
    ) {
        $this->rootDir             = $rootDir;
        $this->serviceDataProvider = $serviceDataProvider;
        $this->moduleFilesFactory  = $moduleFilesFactory;
        $this->filesystem          = $filesystem;

        $this->parser = new Parser();
    }

    /**
     * @return array
     */
    public static function getSkinModel(): array
    {
        return static::$skinModel;
    }

    /**
     * @return array
     */
    public function getAllPossibleModules(): array
    {
        chdir($this->getModulesDir());

        return array_map(function ($module) {
            return str_replace('/', '-', $module);
        }, glob('*/*'));
    }

    /**
     * Fields:
     * id
     * version
     * type
     * author
     * name
     * authorName
     * moduleName
     * description
     * minorRequiredCoreVersion
     * dependsOn
     * incompatibleWith
     * skins
     * showSettingsForm
     * isSystem
     * canDisable
     *
     * directories
     *
     * @param string      $moduleId
     * @param string|null $root
     *
     * @return array
     */
    public function getModuleInfo($moduleId, $root = null): array
    {
        if (empty($this->cache[$moduleId])) {
            $file = $this->getDataFile($moduleId, $root);

            if ($this->filesystem->exists($file)) {
                $this->cache[$moduleId] = $this->retrieveModuleInfo($moduleId, $file);
            }
        }

        return $this->cache[$moduleId] ?? [];
    }

    /**
     * @param string $moduleId
     * @param string $file
     *
     * @return array
     */
    public function retrieveModuleInfo($moduleId, $file): array
    {
        $moduleData = $this->parser->parseFile($file);
        [$author, $name] = Module::explodeModuleId($moduleId);

        $moduleInfo = [
            'id'                       => $moduleId,
            'version'                  => implode('.', Module::explodeVersion($moduleData['version'])),
            'type'                     => $moduleData['type'] ?? 'common',
            'author'                   => $author,
            'name'                     => $name,
            'authorName'               => $moduleData['authorName'] ?? $author,
            'moduleName'               => $moduleData['moduleName'] ?? $name,
            'description'              => $moduleData['description'] ?? '',
            'minorRequiredCoreVersion' => $moduleData['minorRequiredCoreVersion'] ?? 0,
            'dependsOn'                => $moduleData['dependsOn'] ?? [],
            'incompatibleWith'         => $moduleData['incompatibleWith'] ?? [],
            'skins'                    => $moduleData['skins'] ?? [],
            'showSettingsForm'         => $moduleData['showSettingsForm'] ?? false,
            'isSystem'                 => $moduleData['isSystem'] ?? false,
            'canDisable'               => $moduleData['canDisable'] ?? true,
        ];

        $moduleInfo['directories'] = $this->moduleFilesFactory->getDirectories($moduleId, $moduleInfo['skins']);

        $icon = 'classes/XLite/Module/' . $author . '/' . $name . '/icon.png';
        if (!is_readable($this->rootDir . '/' . $icon)) {
            $icon = 'skins/admin/images/addon_default.png';
        }
        $moduleInfo['icon'] = $icon;

        if ($moduleInfo['type'] === 'skin') {
            $preview                   = 'skins/admin/modules/' . $author . '/' . $name . '/preview_list.jpg';
            $moduleInfo['skinPreview'] = is_readable($this->rootDir . '/' . $preview)
                ? $preview
                : '';
        }

        $moduleInfo['service'] = $this->serviceDataProvider->getModuleServiceData($author, $name);

        return $moduleInfo;
    }

    /**
     * @param string      $moduleId
     * @param string|null $root
     *
     * @return string
     */
    private function getDataFile($moduleId, $root = null): string
    {
        [$author, $name] = Module::explodeModuleId($moduleId);

        return $this->getModulesDir($root) . $author . '/' . $name . '/main.yaml';
    }

    /**
     * @param string|null $root
     *
     * @return string
     */
    private function getModulesDir($root = null): string
    {
        return ($root ?: $this->rootDir) . 'classes/XLite/Module/';
    }
}
