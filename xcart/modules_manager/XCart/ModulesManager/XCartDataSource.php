<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ModulesManager;

class XCartDataSource implements IDataSource
{
    /**
     * @var array
     */
    private $cachedModulesList;

    /**
     * @var \XCart\ModulesManager\XCartDataSource\FileSource
     */
    private $fileSource;

    /**
     * @param XCartDataSource\FileSource $fileSource
     */
    public function __construct(XCartDataSource\FileSource $fileSource)
    {
        $this->fileSource = $fileSource;
    }

    /**
     * Compose module actual name
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return string
     */
    public static function getActualName($author, $name)
    {
        return $author . '\\' . $name;
    }

    /**
     * @param string $key
     */
    public function enableModule($key)
    {
        if (!$this->isModuleEnabled($key)
            && ($data = $this->getModule($key))
            && !$this->getModuleProperty($key, 'system')
        ) {
            $this->fileSource->enableModule($key);

            \Includes\Utils\Module\StructureRegistry::moveModuleToEnabledRegistry($data['author'] . '\\' . $data['name']);

            $this->setModuleProperty($key, 'enabled', true);
        }
    }

    public function disableModule($key)
    {
        if ($this->isModuleEnabled($key)
            && (
                LC_DEVELOPER_MODE
                || !$this->getModuleProperty($key, 'system')
                || \Includes\Utils\ConfigParser::getOptions(['performance', 'ignore_system_modules'])
            )
            && !defined('XC_UPGRADE_IN_PROGRESS')
        ) {
            // Short names
            $data = $this->getModule($key);

            $this->fileSource->disableModule($key);

            \Includes\Utils\Module\StructureRegistry::moveModuleToDisabledRegistry($data['author'] . '\\' . $data['name']);

            $this->setModuleProperty($key, 'enabled', false);
        }
    }

    public function installModule($key, array $data)
    {
        $this->updateModule($key, $data);
    }

    public function updateModule($key, array $data)
    {
        $this->fileSource->updateModule($key, $data);
    }

    public function renewModule($key)
    {
        $majorVersion = $this->fileSource->callModuleMethod($key, 'getMajorVersion');
        $minorVersion = $this->fileSource->callModuleMethod($key, 'getMinorVersion');
        $build        = $this->fileSource->callModuleMethod($key, 'getBuildVersion') ?: 0;

        if ($moduleRows = $this->getModule($key)) {
            $yamlLoaded = (int) $moduleRows['yamlLoaded'];
            $moduleName = $this->fileSource->callModuleMethod($key, 'getModuleName');
            $moduleDesc = $this->fileSource->callModuleMethod($key, 'getDescription');

            $data = [
                    'enabled'      => (int) $this->isModuleEnabled($key),
                    'installed'    => 1,
                    'moduleName'   => $moduleName,
                    'description'  => $moduleDesc,
                    'majorVersion' => $majorVersion,
                    'minorVersion' => $minorVersion,
                    'build'        => $build,
                ] + $moduleRows;

            if ((!$yamlLoaded) && $this->isModuleEnabled($key)) {
                $data['yamlLoaded']     = 1;
                $data['needToLoadYaml'] = 1;
            }
        } else {
            $data = $this->fileSource->getModuleData($key);
        }

        $this->updateModule($key, $data);
    }

    public function removeModule($key)
    {
        $this->fileSource->removeModule($key);

        unset($this->cachedModulesList[$key]);
    }

    public function getModulesList()
    {
        if (null === $this->cachedModulesList) {
            $modulesList = $this->fileSource->isModulesFileAvailable()
                ? $this->fileSource->getModulesList()
                : [];

            $this->cachedModulesList = $this->fileSource->fillModulesList($modulesList);
        }

        return $this->cachedModulesList;
    }

    public function getModule($key)
    {
        return isset($this->getModulesList()[$key])
            ? $this->getModulesList()[$key]
            : null;
    }

    protected function getModuleProperty($key, $property)
    {
        if ($module = $this->getModule($key)) {
            return isset($module[$property])
                ? $module[$property]
                : null;
        }

        return null;
    }

    protected function setModuleProperty($key, $property, $val)
    {
        if ($this->getModule($key)) {
            $this->cachedModulesList[$key][$property] = $val;
        }

        return null;
    }

    protected function isModuleEnabled($key)
    {
        return $this->getModuleProperty($key, 'enabled');
    }
}