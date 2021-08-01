<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;

/**
 * Some useful constants
 */
define('LC_DS_QUOTED', preg_quote(LC_DS, '/'));
define('LC_DS_OPTIONAL', '(' . LC_DS_QUOTED . '|$)');

/**
 * ModulesManager
 */
abstract class ModulesManager extends \Includes\Utils\AUtils
{
    /**
     * Pattern to get module name by class name
     */
    const CLASS_NAME_PATTERN = '/(?:\\\)?XLite\\\Module\\\(\w+\\\\\w+)(\\\|$)/US';

    /**
     * Modules list file name
     */
    const MODULES_FILE_NAME   = '.decorator.modules.ini.php';
    const XC_FREE_LICENSE_KEY = 'XC5-FREE-LICENSE';

    /**
     * List of active modules
     *
     * @var array
     */
    protected static $activeModules;

    /**
     * Active modules hash
     */
    protected static $activeModulesHash;

    /**
     * Flag: true - active modules list is processed
     *
     * @var boolean
     */
    protected static $isActiveModulesProcessed = false;

    /**
     * Data for class tree walker
     *
     * @var array
     */
    protected static $quotedPaths;

    /**
     * @var \XCart\ModulesManager
     */
    private static $moduleManager;

    /**
     * @return \XCart\ModulesManager
     */
    public static function getModuleManager()
    {
        if (null === static::$moduleManager) {
            static::$moduleManager = new \XCart\ModulesManager(new \XCart\ModulesManager\XCartDataSource(
                new \XCart\ModulesManager\XCartDataSource\FileSource()
            ));
        }

        return static::$moduleManager;
    }


    // {{{ Name conversion routines

    /**
     * Retrieve module name from class name
     *
     * @param string $className Class name to parse
     *
     * @return string
     */
    public static function getModuleNameByClassName($className)
    {
        return preg_match(static::CLASS_NAME_PATTERN, $className, $matches) ? $matches[1] : null;
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
     * Return module relative dir
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return string
     */
    public static function getRelativeDir($author, $name)
    {
        return $author . LC_DS . $name . LC_DS;
    }

    /**
     * Return module absolute dir
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return string
     */
    public static function getAbsoluteDir($author, $name)
    {
        return LC_DIR_MODULES . static::getRelativeDir($author, $name);
    }

    /**
     * Return module icon file path
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return string
     */
    public static function getModuleIconFile($author, $name)
    {
        return static::getModuleImageFile($author, $name, 'icon.png');
    }

    /**
     * Return module icon file path
     *
     * @param string $author Module author
     * @param string $name   Module name
     * @param string $image  Image name
     *
     * @return string
     */
    public static function getModuleImageFile($author, $name, $image)
    {
        return static::getAbsoluteDir($author, $name) . $image;
    }

    /**
     * Return module YAML file path
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return array
     */
    public static function getModuleYAMLFiles($author, $name)
    {
        $result = [];
        $dir = static::getAbsoluteDir($author, $name);

        $result[] = $dir . 'install.yaml';

        foreach ((glob($dir . 'install_*.yaml') ?: []) as $translationFile) {
            $result[] = $translationFile;
        }

        return $result;
    }

    /**
     * Return module YAML file path
     *
     * @return array
     */
    public static function getCoreYAMLFiles()
    {
        $result = [];

        $files = [
            LC_DIR_ROOT . 'sql' . LC_DS . 'xlite_data.yaml',
            LC_DIR_ROOT . 'sql' . LC_DS . 'xlite_data_lng.yaml',
        ];

        foreach ($files as $file) {
            if (\Includes\Utils\FileManager::isExists($file)) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * Get module by file name
     *
     * @param string $file File name
     *
     * @return string
     * @deprecated
     */
    public static function getFileModule($file)
    {
        return Module::getModuleIdByFilePath($file);
    }

    // }}}

    // {{{ Methods to access installed module main class

    /**
     * Initialize active modules
     *
     * @return void
     */
    public static function initModules()
    {
        foreach (static::getActiveModules() as $module => $data) {
            static::callModuleMethod($module, 'init');
        }
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
    public static function callModuleMethod($module, $method, array $args = [])
    {
        return Module::callMainClassMethod($module, $method, $args);
    }

    /**
     * Get module info from it's main class
     *
     * @param string $author         Module author
     * @param string $name           Module name
     * @param array  $additionalData Data to add to result OPTIONAL
     *
     * @return array
     */
    protected static function getModuleDataFromClass($author, $name, array $additionalData = [])
    {
        $module = static::getActualName($author, $name);

        $result = [
            'name'                     => $name,
            'author'                   => $author,
            'enabled'                  => (int)static::isActiveModule($module),
            'installed'                => 1,
            'yamlLoaded'               => 0,
            'date'                     => time(),
            'fromMarketplace'          => 0,
            'isSystem'                 => (int)static::callModuleMethod($module, 'isSystem'),
            'majorVersion'             => static::callModuleMethod($module, 'getMajorVersion'),
            'minorVersion'             => static::callModuleMethod($module, 'getMinorVersion'),
            'build'                    => static::callModuleMethod($module, 'getBuildVersion') ?: 0,
            'minorRequiredCoreVersion' => static::callModuleMethod($module, 'getMinorRequiredCoreVersion'),
            'moduleName'               => static::callModuleMethod($module, 'getModuleName'),
            'authorName'               => static::callModuleMethod($module, 'getAuthorName'),
            'authorEmail'              => '',
            'description'              => static::callModuleMethod($module, 'getDescription'),
            'iconURL'                  => static::callModuleMethod($module, 'getIconURL'),
            'pageURL'                  => static::callModuleMethod($module, 'getPageURL'),
            'authorPageURL'            => static::callModuleMethod($module, 'getAuthorPageURL'),
            'dependencies'             => serialize((array)static::callModuleMethod($module, 'getDependencies')),
            'tags'                     => serialize([]),
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
        ];

        return array_replace_recursive($result, $additionalData);
    }

    // }}}

    // {{{ Active modules

    /**
     * Return list of active modules (or check a single module)
     *
     * @return array
     */
    public static function getActiveModulesHash()
    {
        if (!static::$activeModulesHash) {
            static::$activeModulesHash = md5(serialize(static::getActiveModules()));
        }

        return static::$activeModulesHash;
    }

    /**
     * Return list of active modules (or check a single module)
     *
     * @return array
     */
    public static function getActiveModules()
    {
        if (null === static::$activeModules) {
            // Fetch enabled modules from the common list
            //$enabledModules = \Includes\Utils\ArrayManager::searchAllInArraysArray(
            //    static::getModulesList(),
            //    'enabled',
            //    true
            //);
            $enabledModules = Manager::getRegistry()->getEnabledModuleIds();

            // Fetch system modules from the disabled modules list
            //$systemModules = static::getSystemModules();

            // Get full list of active modules
            static::$activeModules = $enabledModules; // + $systemModules;
        }

        return static::$activeModules;
    }

    /**
     * Return list of processed active modules
     * @deprecated
     */
    public static function processActiveModules()
    {
        static::getActiveModules();

        if (false === static::$isActiveModulesProcessed) {

            // Remove unsupported modules from list
            static::checkVersions();

            // Remove unsafe modules
            static::performSafeModeProtection();

            // Remove modules with corrupted dependencies
            static::correctDependencies();

            if (\Includes\Utils\ConfigParser::getOptions(['performance', 'ignore_system_modules'])) {
                array_map(['static', 'disableModule'], array_keys(static::getSystemModules(true)));
            }

            static::$isActiveModulesProcessed = true;
        }

        return static::$activeModules;
    }

    /**
     * Check if module is active
     *
     * @param string|null $moduleName Module name
     *
     * @return boolean
     */
    public static function isActiveModule($moduleName)
    {
        return Manager::getRegistry()->isModuleEnabled($moduleName);
    }

    /**
     * Check if module is inactive
     *
     * @param string|null $moduleName Module name
     *
     * @return boolean
     */
    public static function isInactiveModule($moduleName)
    {
        return !static::isActiveModule($moduleName);
    }

    /**
     * Check if all modules are active
     *
     * @param array $moduleNames Module names
     *
     * @return boolean
     */
    public static function areActiveModules(array $moduleNames)
    {
        return array_filter(array_map(['static', 'isActiveModule'], $moduleNames)) == $moduleNames;
    }

    /**
     * Check if all modules are inactive
     *
     * @param array $moduleNames Module names
     *
     * @return boolean
     */
    public static function areInactiveModules(array $moduleNames)
    {
        return array_filter(array_map(['static', 'isInactiveModule'], $moduleNames)) == $moduleNames;
    }

    /**
     * Get the list of disabled system modules
     *
     * @param bool $force
     *
     * @return array
     */
    protected static function getSystemModules($force = false)
    {
        $modules = [];

        if (!\Includes\Utils\ConfigParser::getOptions(['performance', 'ignore_system_modules']) || $force) {
            foreach (static::getModulesList() as $module => $data) {
                if (!empty($data['isSystem'])) {
                    $modules[$module] = $data;
                }
            }
        }

        return $modules;
    }

    /**
     * Disable modules with non-correct versions
     * @deprecated
     */
    protected static function checkVersions()
    {
        $checkLicense = static::isFreeLicense();
        foreach (static::$activeModules as $module => $data) {
            if (\XLite::getInstance()->checkVersion(static::callModuleMethod($module, 'getMajorVersion'), '!=')
                || (
                    \XLite::getInstance()->checkVersion(static::callModuleMethod($module, 'getMajorVersion'), '=')
                    && \XLite::getInstance()->checkMinorVersion(static::callModuleMethod($module, 'getMinorRequiredCoreVersion'), '<')
                )
                || static::isModuleLicenseInappropriate($data, $checkLicense)
            ) {
                static::disableModule($module);
            }
        }
    }

    /**
     * Check if the license is free
     *
     * @return array
     */
    public static function getSoftDisableList()
    {
        $modules = array_filter(
            static::getActiveModules(),
            function ($module) {
                return !in_array(
                    $module['author'],
                    [
                        'QSL',
                        'Qualiteam',
                        'CDev',
                        'XC',
                    ],
                    true
                );
            }
        );

        return array_keys($modules + \Includes\SafeMode::getUnsafeModulesList());
    }

    /**
     * Check if the license is free
     *
     * @return array
     */
    public static function getHardDisableList()
    {
        $modules = array_filter(
            static::getActiveModules(),
            function ($module) {
                return !in_array(
                    $module['author'],
                    [
                        'CDev',
                        'XC',
                    ],
                    true
                );
            }
        );

        return array_keys($modules + \Includes\SafeMode::getUnsafeModulesList());
    }

    /**
     * Check if the license is free
     *
     * @return array
     */
    public static function getCoreDisableList()
    {
        return array_keys(static::getActiveModules() + \Includes\SafeMode::getUnsafeModulesList());
    }

    /**
     * Check if the license is free
     *
     * @param array $restorePoint Restore point
     * @deprecated
     */
    protected static function restoreToPoint($restorePoint)
    {
        $modules = [];
        $active = static::getActiveModules();
        foreach ($active as $key => $module) {
            $toDisable = true;
            foreach ($restorePoint['current'] as $id => $moduleName) {
                if ($moduleName !== null && $key === $moduleName) {
                    $moduleName = null;
                    $toDisable = false;
                    break;
                }
            }
            if ($toDisable) {
                $modules[] = $key;
            }
        }

        //modules to enable
        $toEnable = [];
        $installed = static::getModulesList();
        foreach ($restorePoint['current'] as $id => $moduleName) {
            $isInstalled = array_key_exists($moduleName, $installed);
            $isActive = array_key_exists($moduleName, $active);
            if ($isInstalled && !$isActive) {
                $toEnable[] = $moduleName;
            }
        }

        // Enable modules
        array_walk($toEnable, ['static', 'enableModule']);

        // Disable modules
        array_walk($modules, ['static', 'disableModule']);

        $date = \DateTime::createFromFormat(\XCart\ModulesManager\RestorePointsRepository::RESTORE_DATE_FORMAT, $restorePoint['date']);
        \Includes\Decorator\Utils\PersistentInfo::set('restoredTo', $date->getTimestamp());

        $restorationRecord = static::getRestorationRecord($restorePoint['date']);
        \XCart\ModulesManager\RestorePointsRepository::saveRestorePoint($restorationRecord);
    }

    /**
     * Disable some (or all) modules in SafeMode
     * @deprecated
     */
    protected static function performSafeModeProtection()
    {
        if (\Includes\SafeMode::isSafeModeStarted()) {
            if (!\Includes\SafeMode::isRestoreDateSet()) {
                $modules = static::getSafeModeDisableList();

                // Disable modules
                array_walk($modules, ['static', 'disableModule']);
            } else {
                $restorePoint = \XCart\ModulesManager\RestorePointsRepository::getRestorePoint(\Includes\SafeMode::getRestoreDate());
                if (\XCart\ModulesManager\RestorePointsRepository::isRestorePointValid($restorePoint)) {
                    //modules to disable
                    static::restoreToPoint($restorePoint);
                }
            }

            \Includes\SafeMode::cleanupIndicator();
        }
    }

    protected static function getSafeModeDisableList()
    {
        switch (\Includes\SafeMode::getResetMode()) {
            case \Includes\SafeMode::MODE_CORE:
                return static::getCoreDisableList();
            case \Includes\SafeMode::MODE_SOFT:
                return static::getSoftDisableList();
            default:
                return static::getHardDisableList();
        }
    }

    /**
     * Disable modules with incorrect dependencies
     * @deprecated
     */
    protected static function correctDependencies()
    {
        $dependencies = [];

        foreach (static::$activeModules as $module => $data) {
            $dependencies = array_merge_recursive(
                $dependencies,
                array_fill_keys(static::callModuleMethod($module, 'getDependencies') ?: [], $module)
            );
        }

        $dependencies = array_diff_key($dependencies, static::$activeModules);
        array_walk_recursive($dependencies, ['static', 'disableModule']);

        // http://bugtracker.qtmsoft.com/view.php?id=41330
        static::excludeMutualModules();
    }

    /**
     * Disable so called "mutual exclusive" modules
     *
     * @deprecated
     */
    protected static function excludeMutualModules()
    {
        $list = [];

        foreach (static::$activeModules as $module => $data) {
            $list = array_merge_recursive($list, static::callModuleMethod($module, 'getMutualModulesList') ?: []);
        }

        array_walk_recursive($list, ['static', 'disableModule']);
    }

    /**
     * Check if the table is existed
     *
     * @param string $table Table name without DB prefix (short notation)
     *
     * @return boolean
     */
    protected static function checkTable($table)
    {
        $result = \Includes\Utils\Database::fetchAll('SHOW TABLES LIKE \'' . get_db_tables_prefix() . $table . '\'');

        return !empty($result);
    }

    /**
     * Check if the license is free
     *
     * @return boolean
     */
    protected static function isFreeLicense()
    {
        return 'Free' === static::getLicense();
    }

    /**
     * Check if the license is free
     *
     * @return boolean
     */
    protected static function getLicense()
    {
        $license = '';

        if (static::checkTable('module_keys')) {
            $key = \Includes\Utils\Database::fetchAll(
                'SELECT keyData FROM ' . get_db_tables_prefix() . 'module_keys WHERE name=\'Core\' AND author=\'CDev\''
            );

            if ($key && isset($key[0])) {
                $keyData = unserialize($key[0]['keyData']);
                $license = isset($keyData['editionName']) ? $keyData['editionName'] : '';
            }
        }

        return $license;
    }


    /**
     * Defines if the module must be disabled according license flag
     *
     * @param array   $module      Module
     * @param boolean $licenseFlag License flag
     *
     * @return boolean
     */
    protected static function isModuleLicenseInappropriate($module, $licenseFlag)
    {
        $result = false;

        if ($licenseFlag) {
            $marketplaceModule = static::getMarketplaceModule($module);
            if ($marketplaceModule) {
                $edition = unserialize($marketplaceModule['editions']);
                if (!empty($edition) && is_array($edition)) {
                    $result = !in_array(static::getLicense(), $edition, true);
                }
            }
        }

        return $result;
    }


    /**
     * Retrieve the marketplace module for the given one
     *
     * @param array $module Module array structure
     *
     * @return array
     */
    protected static function getMarketplaceModule($module)
    {
        $marketplaceModule = \Includes\Utils\Database::fetchAll(
            'SELECT * FROM ' . static::getTableName() . ' WHERE name= ? AND author= ? AND fromMarketplace= ?',
            [$module['name'], $module['author'], 1]
        );

        return empty($marketplaceModule) ? null : $marketplaceModule[0];
    }

    // }}}

    // {{{ Methods to manage module states (installed/enabled)

    /**
     * Set module enabled flag fo "false"
     *
     * @param string $key Module actual name (key)
     * @deprecated
     */
    public static function disableModule($key)
    {
        static::getModuleManager()->disableModule($key);
        unset(static::$activeModules[$key]);
    }

    /**
     * Set module enabled flag fo "false"
     *
     * @param string $key Module actual name (key)
     * @deprecated
     */
    public static function enableModule($key)
    {
        static::getModuleManager()->enableModule($key);
        static::$activeModules[$key] = static::getModuleManager()->getModule($key);
    }

    /**
     * Returns empty record about successful restoration
     *
     * @param string $restoredTo restore point date in RESTORE_DATE_FORMAT
     *
     * @return array
     */
    public static function getRestorationRecord($restoredTo)
    {
        return [
            'date'       => date(\XCart\ModulesManager\RestorePointsRepository::RESTORE_DATE_FORMAT),
            'restoredTo' => $restoredTo,
        ];
    }

    /**
     * Get structures to save when module is disabled
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return array
     */
    public static function getModuleProtectedStructures($author, $name)
    {
        $tables = [];
        $columns = [];
        $dependencies = [];

        $moduleDir = static::getAbsoluteDir($author, $name);

        if (\Includes\Utils\FileManager::isExists($moduleDir)) {
            $filter = new \Includes\Utils\FileFilter($moduleDir, '/Model' . preg_quote(LC_DS, '/') . '.*\.php$/Si');

            $sourceClassPathResolver = new \Includes\ClassPathResolver($moduleDir);
            $reflectorFactory = new \Includes\Reflection\StaticReflectorFactory($sourceClassPathResolver);

            foreach ($filter->getIterator() as $path => $data) {

                // DO NOT call "getInterfaces()" after the "getFullClassName()"
                // DO NOT use reflection to get interfaces
                $interfaces = \Includes\Decorator\Utils\Tokenizer::getInterfaces($path);
                $class = \Includes\Decorator\Utils\Tokenizer::getFullClassName($path);

                // Do 'autoload' checking first since the class_exists tries to use autoloader
                // but fails into "cannot include file" warning when model class is not set to use (LC_Dependencies issue)
                if (\Includes\Autoloader::checkAutoload($class) && class_exists($class)) {
                    // $reflectionClass = new \ReflectionClass($class);
                    if ($class
                        && is_subclass_of($class, '\XLite\Model\AEntity')
                        && !is_subclass_of($class, '\XLite\Model\Base\Dump')
                    ) {
                        $class = ltrim($class, '\\');
                        $len = strlen(\Includes\Utils\Database::getTablesPrefix());

                        // DO NOT remove leading backslash in interface name
                        if (in_array('\XLite\Base\IDecorator', $interfaces, true)) {
                            $parent = \Includes\Decorator\Utils\Tokenizer::getParentClassName($path);
                            $parent = ltrim($parent, '\\');
                            $metadata = \XLite\Core\Database::getEM()->getClassMetadata($parent);
                            $table = substr($metadata->getTableName(), $len);

                            $reflector = $reflectorFactory->reflectSource($path);
                            $deps = $reflector->getPositiveDependencies();

                            $tool = new \Doctrine\ORM\Tools\SchemaTool(\XLite\Core\Database::getEM());
                            $schema = $tool->getCreateSchemaSql([$metadata]);

                            foreach ((array)$metadata->reflFields as $field => $reflection) {
                                $pattern = '/(?:, |\()(' . $field . ' .+)(?:, [A-Za-z]|\) ENGINE)/USsi';

                                if ($reflection->class === $class
                                    && !empty($metadata->fieldMappings[$field])
                                    && preg_match($pattern, reset($schema), $matches)
                                ) {
                                    $columns[$table][$field] = $matches[1];
                                    if (!empty($deps)) {
                                        foreach ($deps as $dep) {
                                            $dependencies[$dep][$table][$field] = $matches[1];
                                        }
                                    }
                                }
                            }

                            foreach ($metadata->associationMappings as $mapping) {

                                if ($metadata->reflFields[$mapping['fieldName']]->class === $class) {

                                    if (isset($mapping['joinTable']) && $mapping['joinTable']) {
                                        $tables[] = substr($mapping['joinTable']['name'], $len);

                                    } elseif (isset($mapping['joinColumns']) && $mapping['joinColumns']) {
                                        foreach ($mapping['joinColumns'] as $col) {
                                            $pattern = '/(?:, |\()(' . $col['name'] . ' .+)(?:, [A-Za-z]|\) ENGINE)/USsi';

                                            if (preg_match($pattern, reset($schema), $matches)) {
                                                $columns[$table][$col['name']] = $matches[1];
                                            }
                                        }
                                    }
                                }
                            }

                        } elseif (
                            \XLite\Core\Database::getRepo($class)
                            && \XLite\Core\Database::getRepo($class)->canDisableTable()
                        ) {
                            $tableName = substr(
                                \XLite\Core\Database::getEM()->getClassMetadata($class)->getTableName(),
                                $len
                            );
                            if ($tableName) {
                                // For base models table does not exist
                                $tables[] = $tableName;
                            }

                            $metadata = \XLite\Core\Database::getEM()->getClassMetadata($class);
                            foreach ($metadata->associationMappings as $mapping) {
                                if (isset($mapping['joinTable']) && $mapping['joinTable']) {
                                    $tables[] = substr($mapping['joinTable']['name'], $len);
                                }
                            }
                        }
                    }
                }
            }
        }


        return [
            'tables'       => $tables,
            'columns'      => $columns,
            'dependencies' => $dependencies,
        ];
    }

    /**
     * Get modules list file path
     *
     * @return string
     */
    protected static function getModulesFilePath()
    {
        return LC_DIR_VAR . static::MODULES_FILE_NAME;
    }

    /**
     * Check if modules list file exists
     *
     * @return boolean
     */
    public static function isModulesFileExists()
    {
        return \Includes\Utils\FileManager::isFileReadable(static::getModulesFilePath());
    }

    // }}}

    // {{{ DB-related routines

    /**
     * Return name of the table where the module info is stored
     *
     * @return string
     */
    protected static function getTableName()
    {
        return get_db_tables_prefix() . 'modules';
    }

    /**
     * Part of SQL query to fetch composed module name
     *
     * @return string
     */
    protected static function getModuleNameField()
    {
        return 'CONCAT(author,\'\\\\\',name) AS actualName, ';
    }

    // {{{ List of all modules

    /**
     * Fetch list of modules
     *
     * @return array
     */
    public static function getModulesList()
    {
        return static::getModuleManager()->getModulesList();
    }

    // }}}

    // {{{ Modules info manipulations

    /**
     * Remove file with active modules list
     *
     * @return void
     */
    public static function removeFile()
    {
        \Includes\Utils\FileManager::deleteFile(static::getModulesFilePath());
    }

    /**
     * Write module info to DB
     *
     * @param string  $author              Module author
     * @param string  $name                Module name
     * @param boolean $isModulesFileExists Flag: true means that the installation process is going now OPTIONAL
     * @deprecated
     */
    public static function switchModule($author, $name, $isModulesFileExists = false)
    {
        static::getModuleManager()->renewModule(static::getActualName($author, $name));
    }

    // }}}

    // {{{ Module paths

    /**
     * Return pattern to check PHP file paths
     *
     * @return string
     */
    public static function getPathPatternForPHP()
    {
        $root = preg_quote(\Includes\Decorator\ADecorator::getClassesDir(), '/') . 'XLite';

        $registry = Manager::getRegistry();
        $enabledModules = array_map(function ($item) use ($registry) {
            list($author, $name) = Module::explodeModuleId($item);

            return $author . LC_DS_QUOTED . $name;
        }, $registry->getEnabledModuleIds());

        $modules = '(' . implode('|', $enabledModules) . ')';

        return '/^(?:'
            . $root . LC_DS_QUOTED . '((?!Module)[a-zA-Z0-9]+)' . LC_DS_QUOTED . '.+'
            . '|' . $root . LC_DS_QUOTED . 'Module' . LC_DS_QUOTED . $modules . LC_DS_QUOTED . '.+'
            . '|' . $root
            . '|' . $root . LC_DS_QUOTED . 'Module' . LC_DS_QUOTED . '[a-zA-Z0-9]+'
            . '|' . $root . LC_DS_QUOTED . '[a-zA-Z0-9]+'
            . ')\.php$/Ss';
    }

    /**
     * Return pattern to check .twig file paths
     *
     * @return string
     */
    public static function getPathPatternForTemplates()
    {
        return static::getPathPattern(
            preg_quote(LC_DIR_SKINS, '/') . '\w+',
            'modules',
            'twig'
        );
    }

    ///**
    // * Callback to collect module paths
    // *
    // * @param \Includes\Decorator\DataStructure\Graph\Modules $node Current module node
    // *
    // * @return void
    // */
    //public static function getModuleQuotedPathsCallback(\Includes\Decorator\DataStructure\Graph\Modules $node)
    //{
    //    static::$quotedPaths[$node->getActualName()] = str_replace('\\', LC_DS_QUOTED, $node->getActualName());
    //}

    ///**
    // * Return list of relative module paths
    // *
    // * @return array
    // */
    //protected static function getModuleQuotedPaths()
    //{
    //    if (null === static::$quotedPaths) {
    //        static::$quotedPaths = [];
    //        \Includes\Decorator\ADecorator::getModulesGraph()->walkThrough(
    //            [get_called_class(), 'getModuleQuotedPathsCallback']
    //        );
    //    }
    //
    //    return static::$quotedPaths;
    //}

    /**
     * Return pattern to file path against active modules list
     *
     * @param string $rootPath  Name of the root directory
     * @param string $dir       Name of the directory with modules
     * @param string $extension File extension
     *
     * @return string
     */
    protected static function getPathPattern($rootPath, $dir, $extension)
    {
        $modulePattern = $dir . LC_DS_QUOTED . ($placeholder = '@') . LC_DS_OPTIONAL;

        $registry = Manager::getRegistry();
        $enabledModules = array_map(function ($item) use ($registry) {
            list($author, $name) = Module::explodeModuleId($item);

            return $author . LC_DS_QUOTED . $name;
        }, $registry->getEnabledModuleIds());

        return '/^' . $rootPath . '(.((?!' . str_replace($placeholder, '\w+', $modulePattern) . ')|'
            . str_replace($placeholder, '(' . implode('|', $enabledModules) . ')', $modulePattern)
            . '))*\.' . $extension . '$/i';
    }

    // }}}
}
