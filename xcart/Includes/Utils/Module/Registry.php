<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

use Includes\Utils\Converter;
use Includes\Utils\FileManager;
use Includes\Utils\Operator;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use Symfony\Component\Yaml\Parser;

class Registry
{
    /**
     * @var array
     */
    private $runtimeCache;

    /**
     * @var IStorage
     */
    private $storage;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var array
     */
    private $data;

    /**
     * @param IStorage $storage
     */
    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
        $this->parser  = new Parser();

        $this->refresh();
    }

    /**
     * Refetches data from internal module storage
     */
    public function refresh()
    {
        $this->data = $this->storage->fetch();
    }

    /**
     * Updates internal module storage
     */
    public function save()
    {
        $this->storage->save($this->data);
    }

    /**
     * Clears module storage
     */
    public function clear()
    {
        $this->data         = [];
        $this->runtimeCache = [];
        $this->save();
        $this->refresh();
    }

    /**
     * Updates internal modules representation
     *
     * @param array $list
     * @param array $integratedList
     */
    public function updateModules(array $list, $integratedList)
    {
        foreach ($list as $author => $modules) {
            foreach ($modules as $name => $enabled) {
                if ($enabled === 'remove') {
                    $existing = $this->getModule($author, $name);
                    if ($existing) {
                        StructureRegistry::removeModuleFromDisabledStructure(
                            $existing->id
                        );
                    }

                    unset($list[$author][$name]);
                }
            }
        }

        $this->data = $this->mergeWith($list, $integratedList);
        $this->calculateActiveSkin();
        $this->save();
    }

    /**
     * Sets last
     */
    public function calculateActiveSkin()
    {
        $enabledSkinIds = array_filter(
            array_map(function ($module) {
                return $module->enabled ? $module->id : null;
            }, $this->getSkinModules())
        );

        try {
            $sorted = Manager::sortModulesByDependency($enabledSkinIds);

            $activeSkinId = array_pop($sorted);

            $this->updateModule($activeSkinId, null, ['activeSkin' => true]);
        } catch (CircularDependencyException $e) {
        } catch (ElementNotFoundException $e) {
        }
    }

    /**
     * Init Module Manually
     *
     * @param $moduleId
     */
    public function initModuleManually($moduleId)
    {
        [$author, $name] = Module::explodeModuleId($moduleId);

        $module = $this->getModule($author, $name);

        $this->registerSkinsManually($module);
    }

    /**
     * Skins manual registration method.
     *
     * @param $module
     */
    protected function registerSkinsManually($module)
    {
        foreach ($module->skins as $interface => $skinsToRegister) {
            foreach ($skinsToRegister as $skin) {
                static::registerSkin($skin, $interface);
            }
        }
    }

    /**
     * Make one skin entry registration to provide a flexible skin registration
     *
     * @param $skin
     * @param $interface
     */
    static protected function registerSkin($skin, $interface)
    {
        \XLite\Core\Layout::getInstance()->addSkin($skin, $interface);
    }

    /**
     * Fetches modules metadata from Main.php
     *
     * @param $moduleId
     *
     * @return array
     */
    public function fetchModuleMetadata($moduleId)
    {
        $moduleData = $this->parser->parse(file_get_contents(Module::getSourcePath($moduleId) . 'main.yaml'));
        [$author, $name] = Module::explodeModuleId($moduleId);

        $moduleInfo = [
            'id'                       => $moduleId,
            'version'                  => $moduleData['version'],
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

        $moduleInfo['dependsOn']        = Module::convertId((array) $moduleInfo['dependsOn']);
        $moduleInfo['incompatibleWith'] = Module::convertId((array) $moduleInfo['incompatibleWith']);

        $serviceFile           = Module::getSourcePath($moduleId) . '/service.yaml';
        $moduleInfo['service'] = file_exists($serviceFile) ? $this->parser->parse(file_get_contents($serviceFile)) : [];

        return $moduleInfo;
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return $this->executeCachedRuntime(function () {
            $result = [];

            $data = $this->data ?: [];
            foreach ($data as $author => $modules) {
                foreach ($modules as $name => $module) {
                    $result[$module->id] = $module;
                }
            }

            ksort($result, SORT_NATURAL);

            return $result;
        });
    }

    /**
     * @return array
     */
    public function getSkinModules()
    {
        return $this->executeCachedRuntime(function () {
            return array_filter($this->getModules(), function ($module) {
                /** @var Module $module */
                return $module->isSkin();
            });
        });
    }

    /**
     * @return array
     */
    public function getEnabledPaymentModules()
    {
        return $this->executeCachedRuntime(function () {
            return array_filter($this->getModules(), static function ($module) {
                /** @var Module $module */
                return $module->isEnabled() && $module->isPayment();
            });
        });
    }

    /**
     * @return array
     */
    public function getEnabledShippingModules()
    {
        return $this->executeCachedRuntime(function () {
            return array_filter($this->getModules(), static function ($module) {
                /** @var Module $module */
                return $module->isEnabled() && $module->isShipping();
            });
        });
    }

    /**
     * @param bool $skipMainClassCheck
     *
     * @return array
     */
    public function getEnabledModuleIds($skipMainClassCheck = false)
    {
        return $this->executeCachedRuntime(function () use ($skipMainClassCheck) {
            $result = [];

            $data = $this->data ?: [];
            foreach ($data as $author => $modules) {
                foreach ($modules as $name => $module) {
                    if ($module->enabled
                        && ($skipMainClassCheck
                            || Operator::checkIfClassExists(Module::getMainClassName($author, $name))
                            || FileManager::isFileReadable(Module::getMainDataFilePath($author, $name))
                        )
                    ) {
                        $result[] = Module::buildId($author, $name);
                    }
                }
            }

            sort($result, SORT_NATURAL);

            return $result;
        }, ['skipMainClassCheck' => $skipMainClassCheck]);
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return Module
     */
    public function getModule($author, $name = null)
    {
        [$author, $name] = Module::explodeModuleId($author, $name);

        return $this->data[$author][$name] ?? null;
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return array
     */
    public function getDependencies($author, $name = null)
    {
        return $this->getModule($author, $name) ? $this->getModule($author, $name)->dependsOn : [];
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return array
     */
    public function getIncompatibleWith($author, $name = null)
    {
        return $this->getModule($author, $name)->incompatibleWith;
    }

    /**
     * @return array|mixed
     */
    public function getNonLoadedYamlFiles()
    {
        $files = [];

        foreach ($this->getEnabledModuleIds(true) as $moduleId) {
            $module = $this->getModule($moduleId);
            if ($module && !$module->yamlLoaded) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $files = array_merge($files, $this->getYamlFiles($module->author, $module->name));
            }
        }

        return array_unique($files);
    }

    /**
     * Mark all enabled modules as yamlLoaded = true
     */
    public function markEnabledModulesAsLoaded()
    {
        foreach ($this->getEnabledModuleIds() as $moduleId) {
            $module = $this->getModule($moduleId);
            if ($module) {
                $module->yamlLoaded = true;
                StructureRegistry::registerModuleToEnabledRegistry(
                    $module->id,
                    Module::getModuleProtectedStructures($module->id)
                );
            }
        }

        $this->save();
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return bool
     */
    public function isModuleEnabled($author, $name = null)
    {
        $module = $this->getModule($author, $name);

        return $module
            ? $module->isEnabled()
            : null;
    }

    /**
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    public function getServiceURL($path, array $params = [])
    {
        return \XLite::getInstance()->getServiceURL(
            '#/'
            . $path
            . ($params ? ('?' . http_build_query($params)) : '')
        );
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public function getModuleServiceURL($author, $name = null)
    {
        $module = $this->getModule($author, $name);

        if ($module) {
            return \XLite::getInstance()->getServiceURL('#/installed-addons', null, ['moduleId' => $module->id]);
        }

        return \XLite::getInstance()->getServiceURL('#/marketplace', null, ['moduleId' => Module::buildId($author, $name)]);
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public function getModuleSettingsUrl($author, $name = null)
    {
        $module = $this->getModule($author, $name);

        return $module
            ? Converter::buildURL('module', '', ['moduleId' => $module->id], \XLite::getAdminScript())
            : null;
    }

    /**
     * Calls init() method on enabled modules
     */
    public function initModules()
    {
        foreach ($this->getEnabledModuleIds() as $moduleId) {
            $module = $this->getModule($moduleId);
            if ($module) {
                $module->callClassMethod('init');
            }
        }
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string[]
     */
    public function getYamlFiles($author, $name = null)
    {
        $sourcePath = Module::getSourcePath($author, $name);

        $result = [
            $sourcePath . 'install.yaml',
        ];

        foreach (glob($sourcePath . 'install_*.yaml') ?: [] as $translationFile) {
            $result[] = $translationFile;
        }

        return $result;
    }

    /**
     * @param string $author
     * @param string $name
     * @param array  $props
     *
     * @return bool If operation is successful
     */
    public function updateModule($author, $name = null, array $props = [])
    {
        [$author, $name] = Module::explodeModuleId($author, $name);

        /** @var Module|null $module */
        $module = $this->data[$author][$name] ?? null;

        if ($module) {
            $module->merge($props);

            return true;
        }

        return false;
    }

    /**
     * @param array $list
     * @param array $integratedList
     *
     * @return array
     */
    protected function mergeWith(array $list, $integratedList)
    {
        $result = [];

        foreach ($list as $author => $modules) {
            $result[$author] = [];

            foreach ($modules as $name => $enabled) {
                $integrated = $integratedList[$author][$name] ?? false;

                $result[$author][$name] = $this->createModuleRecord($author, $name, $enabled, $integrated);
            }
        }

        return $result;
    }

    /**
     * @param      $author
     * @param      $name
     * @param bool $enabled
     *
     * @return Module
     */
    protected function createModuleRecord($author, $name = null, $enabled = false, $integrated = false)
    {
        list($author, $name) = Module::explodeModuleId($author, $name);

        $existing = $this->getModule($author, $name);

        $module = new Module([
            'enabled' => $enabled,
            'yamlLoaded' => $integrated
        ]);

        if ($existing && $existing instanceof Module) {
            $existing->enabled  = $enabled;
            $module->yamlLoaded = $existing->yamlLoaded;
        }

        $module->merge($this->fetchModuleMetadata(Module::buildId($author, $name)));

        return $module;
    }

    /** Port of runtime cache trait */

    /**
     * @param callable $callback
     * @param null     $cacheKeyParts
     * @param bool     $force
     *
     * @return mixed
     */
    protected function executeCachedRuntime(callable $callback, $cacheKeyParts = null, $force = false)
    {
        if (null === $cacheKeyParts) {
            $cacheKeyParts = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        }

        $cacheKey = $this->getRuntimeCacheKey([$cacheKeyParts]);

        if (!isset($this->runtimeCache[$cacheKey]) || $force) {
            $this->runtimeCache[$cacheKey] = $callback();
        }

        return $this->runtimeCache[$cacheKey];
    }

    /**
     * Calculate key for cache storage
     *
     * @param mixed $cacheKeyParts
     *
     * @return string
     */
    protected function getRuntimeCacheKey($cacheKeyParts)
    {
        return is_scalar($cacheKeyParts) ? (string) $cacheKeyParts : md5(serialize($cacheKeyParts));
    }
}
