<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ModulesManager\XCartDataSource;


class FileSource
{
    const MODULES_FILE_NAME = '.decorator.modules.ini.php';

    public function isModulesFileAvailable()
    {
        return \Includes\Utils\FileManager::isFileReadable($this->getModulesFilePath());
    }

    /**
     * Get modules list file path
     *
     * @return string
     */
    protected function getModulesFilePath()
    {
        return LC_DIR_VAR . static::MODULES_FILE_NAME;
    }

    public function enableModule($key)
    {
        if ($this->isModulesFileAvailable()) {
            list($author, $name) = explode('\\', $key);

            $pattern = '/(\[' . $author . '\][^\[]+\s*' . $name . '\s*=)\s*\S+/Ss';
            \Includes\Utils\FileManager::replace($this->getModulesFilePath(), '$1 1', $pattern);
        }
    }

    public function disableModule($key)
    {
        if ($this->isModulesFileAvailable()) {
            list($author, $name) = explode('\\', $key);

            $pattern = '/(\[' . $author . '\][^\[]+\s*' . $name . '\s*=)\s*\S+/Ss';
            \Includes\Utils\FileManager::replace($this->getModulesFilePath(), '$1 0', $pattern);
        }
    }

    public function updateModule($key, array $data)
    {
        if ($data['enabled'] && $data['needToLoadYaml'] || static::isModulesFileAvailable()) {
            list($author, $name) = explode('\\', $key);

            $this->addModuleYamlFile($author, $name);
        }
    }

    protected function addModuleYamlFile($author, $name)
    {
        $dir = 'classes' . LC_DS
            . LC_NAMESPACE . LC_DS
            . 'Module' . LC_DS
            . $author . LC_DS
            . $name;

        $file = $dir . LC_DS . 'install.yaml';

        if (\Includes\Utils\FileManager::isFileReadable($file)) {
            \Includes\Decorator\Plugin\Doctrine\Utils\FixturesManager::addFixtureToList($file);
        }

        foreach ((glob($dir . LC_DS . 'install_*.yaml') ?: []) as $translationFile) {
            if (\Includes\Utils\FileManager::isFileReadable($translationFile)) {
                \Includes\Decorator\Plugin\Doctrine\Utils\FixturesManager::addFixtureToList($translationFile);
            }
        }
    }

    public function removeModule($key)
    {
        // TODO: Implement removeModule() method.
    }

    public function getModulesList()
    {
        $list = [];

        if ($this->isModulesFileAvailable()) {
            foreach (parse_ini_file($this->getModulesFilePath(), true) as $author => $data) {
                foreach ($data as $name => $enabled) {
                    if ($enabled) {
                        $list[$author . '\\' . $name] = [
                            'actualName' => \XCart\ModulesManager\XCartDataSource::getActualName($author, $name),
                            'name'       => $name,
                            'author'     => $author,
                            'enabled'    => $enabled,
                            'moduleName' => $name,
                            'authorName' => $author,
                            'yamlLoaded' => 0,
                        ];
                    }
                }
            }
        }

        return $list;
    }

    public function fillModulesList(array $list)
    {
        foreach ($list as $name => $module) {
            if ($this->isModuleInstalled($name)) {
                $list[$name] = array_merge(
                    $this->getModuleData($name),
                    $list[$name]
                );
            }
        }

        return $list;
    }

    public function getModuleData($key)
    {
        list($author, $name) = explode('\\', $key);
        return [
            'name'                     => $name,
            'author'                   => $author,
            'enabled'                  => 0,
            'installed'                => static::isModuleInstalled($key),
            'yamlLoaded'               => 0,
            'date'                     => time(),
            'fromMarketplace'          => 0,
            'isSystem'                 => (int)static::callModuleMethod($key, 'isSystem'),
            'majorVersion'             => static::callModuleMethod($key, 'getMajorVersion'),
            'minorVersion'             => static::callModuleMethod($key, 'getMinorVersion'),
            'build'                    => static::callModuleMethod($key, 'getBuildVersion') ?: 0,
            'minorRequiredCoreVersion' => static::callModuleMethod($key, 'getMinorRequiredCoreVersion'),
            'moduleName'               => static::callModuleMethod($key, 'getModuleName'),
            'authorName'               => static::callModuleMethod($key, 'getAuthorName'),
            'authorEmail'              => '',
            'description'              => static::callModuleMethod($key, 'getDescription'),
            'iconURL'                  => static::callModuleMethod($key, 'getIconURL'),
            'pageURL'                  => static::callModuleMethod($key, 'getPageURL'),
            'authorPageURL'            => static::callModuleMethod($key, 'getAuthorPageURL'),
            'dependencies'             => serialize((array)static::callModuleMethod($key, 'getDependencies')),
            'isPayment'                => $this->callModuleMethod($key, 'isPaymentModule'),
            'isSkin'                   => (int) $this->callModuleMethod($key, 'isSkinModule'),
            'isShipping'               => (int) $this->callModuleMethod($key, 'isShippingModule'),
            'version'                  => $this->callModuleMethod($key, 'getVersion'),
            'isSeparateUninstall'      => (int) $this->callModuleMethod($key, 'isSeparateUninstall'),
            'canDisable'               => (int) $this->callModuleMethod($key, 'canDisable'),
            'rating'                   => 0,
            'votes'                    => 0,
            'downloads'                => 0,
            'price'                    => 0.00,
            'currency'                 => 'USD',
            'revisionDate'             => 0,
            'packSize'                 => 0,
            'editions'                 => serialize([]),
            'editionState'             => 0,
            'xcnPlan'                  => 0,
            'hasLicense'               => 0,
            'isLanding'                => 0,
            'landingPosition'          => 0,
            'xbProductId'              => 0,
            'private'                  => 0,
            'wave'                     => 0,
            'salesChannelPos'          => -1,
            'tags'                     => serialize([]),
        ];
    }

    /**
     * Get class name by module name
     *
     * @param string $moduleName Module actual name
     *
     * @return string
     */
    protected function getClassNameByModuleName($moduleName)
    {
        return 'XLite\Module\\' . $moduleName . '\Main';
    }

    /**
     * Method to access module main class methods
     *
     * @param string $module Module actual name
     * @param string $method Method to call
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function callModuleMethod($module, $method, array $args = [])
    {
        if (static::isModuleInstalled($module)) {
            return call_user_func_array([static::getClassNameByModuleName($module), $method], $args);
        }

        return null;
    }

    /**
     * Check if module is installed
     *
     * @param string $module Module actual name
     *
     * @return boolean
     */
    public function isModuleInstalled($module)
    {
        return \Includes\Utils\Operator::checkIfClassExists(static::getClassNameByModuleName($module));
    }
}