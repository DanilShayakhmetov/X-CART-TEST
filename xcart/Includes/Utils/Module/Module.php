<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

use Doctrine\ORM\Tools\SchemaTool;
use Includes\Autoloader;
use Includes\ClassPathResolver;
use Includes\Decorator\Utils\Tokenizer;
use Includes\Reflection\StaticReflectorFactory;
use Includes\Utils\Converter;
use Includes\Utils\FileFilter;
use Includes\Utils\FileManager;
use Includes\Utils\Operator;
use Includes\Utils\PropertyBag;
use XLite\Core\Database;
use XLite\Core\Layout;

/**
 * Internal module metadata representation
 *
 * @property string  $id
 * @property string  $version          Module version
 * @property string  $type             Module type
 * @property string  $author           Module author key
 * @property string  $name             Module key
 * @property string  $authorName       Module display author
 * @property string  $moduleName       Module display name
 * @property string  $description      Module description
 * @property string  $minorRequiredCoreVersion
 * @property array   $dependsOn        Ids of other modules, required for this module to enable
 * @property array   $incompatibleWith Ids of other modules, incompatible with this module
 * @property array   $skins
 * @property bool    $showSettingsForm
 * @property bool    $isSystem
 * @property bool    $canDisable
 * @property boolean $enabled          Module state
 * @property boolean $activeSkin       If this skin module is an active skin
 * @property boolean $yamlLoaded       Are module yaml files loaded?
 * @property array   $layoutColors     Layout colors of skin module
 * @property array   $directories      Module directories cache
 * @property array   $service
 */
class Module extends PropertyBag
{
    const ID_SEPARATOR = '-';

    public function __construct(array $data = null)
    {
        if (!isset($data['enabled'])) {
            $data['enabled'] = false;
        }

        if (!isset($data['yamlLoaded'])) {
            $data['yamlLoaded'] = false;
        }

        parent::__construct($data);
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return string
     */
    public static function buildId($author, $name)
    {
        list($author, $name) = static::explodeModuleId($author, $name);

        return $author . self::ID_SEPARATOR . $name;
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return array
     */
    public static function explodeModuleId($author, $name = null)
    {
        if ($name === null) {
            $result = preg_split('/\\\\|-/', $author);
            if (count($result) === 2) {

                return $result;
            }
        }

        return [$author, $name];
    }

    /**
     * @param array|string $xcartId
     *
     * @return array|string
     */
    public static function convertId($xcartId)
    {
        if (is_array($xcartId)) {
            return array_map(function ($item) {
                return static::convertId($item);
            }, $xcartId);
        }

        return str_replace('\\', self::ID_SEPARATOR, $xcartId);
    }

    /**
     * @param string $path
     *
     * @return null|string
     */
    public static function getModuleIdByFilePath($path)
    {
        return preg_match(static::getModuleIdByFilePathPattern(), $path, $matches)
            ? static::buildId($matches[1], $matches[2])
            : null;
    }

    /**
     * @param string $className
     *
     * @return null|string
     */
    public static function getModuleIdByClassName($className)
    {
        return preg_match(static::getModuleIdByClassNamePattern(), $className, $matches)
            ? static::buildId($matches[1], $matches[2])
            : null;
    }

    /**
     * @param string $moduleId
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function callMainClassMethod($moduleId, $methodName, array $arguments = [])
    {
        $className = static::getMainClassName($moduleId);

        return method_exists($className, $methodName)
            ? call_user_func_array([$className, $methodName], $arguments)
            : static::tryLoadingOriginalMainAndCallMethod($moduleId, $methodName, $arguments);
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getMainClassName($author, $name = null)
    {
        list($author, $name) = static::explodeModuleId($author, $name);

        return 'XLite\Module\\' . $author . '\\' . $name . '\Main';
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getMainClassFilePath($author, $name = null)
    {
        $sourcePath = static::getSourcePath($author, $name);

        return $sourcePath . 'Main.php';
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getMainDataFilePath($author, $name = null)
    {
        $sourcePath = static::getSourcePath($author, $name);

        return $sourcePath . 'main.yaml';
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getSourcePath($author, $name = null)
    {
        list($author, $name) = static::explodeModuleId($author, $name);

        return \LC_DIR_MODULES . $author . \LC_DS . $name . \LC_DS;
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getRelativeSourcePath($author, $name = null)
    {
        list($author, $name) = static::explodeModuleId($author, $name);

        return \LC_NAMESPACE . \LC_DS . 'Module' . \LC_DS . $author . \LC_DS . $name;
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getIconURL($author, $name = null)
    {
        list($author, $name) = static::explodeModuleId($author, $name);

        if ($author === 'CDev' && $name === 'Core') {
            return 'skins/admin/images/core_image.png';
        }

        $icon = 'classes' . \LC_DS . static::getRelativeSourcePath($author, $name) . \LC_DS . 'icon.png';

        if (!FileManager::isFileReadable(\LC_DIR_ROOT . \LC_DS . $icon)) {
            $icon = 'skins/admin/images/addon_default.png';
        }

        return $icon;
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getSkinPreviewURL($author, $name = null)
    {
        list($author, $name) = static::explodeModuleId($author, $name);

        $icon = 'admin' . \LC_DS . 'modules' . \LC_DS . $author . \LC_DS . $name . \LC_DS . 'preview_list.jpg';

        if (!FileManager::isFileReadable(\LC_DIR_SKINS . \LC_DS . $icon)) {
            return '';
        }

        return 'skins' . \LC_DS . $icon;
    }

    /**
     * Get structures to save when module is disabled
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return array
     */
    public static function getModuleProtectedStructures($author, $name = null)
    {
        list($author, $name) = static::explodeModuleId($author, $name);

        $tables       = [];
        $columns      = [];
        $dependencies = [];

        $moduleDir = static::getSourcePath($author, $name);

        if (FileManager::isExists($moduleDir)) {
            $filter = new FileFilter($moduleDir, '/Model' . preg_quote(LC_DS, '/') . '.*\.php$/Si');

            $sourceClassPathResolver = new ClassPathResolver($moduleDir);
            $reflectorFactory        = new StaticReflectorFactory($sourceClassPathResolver);

            foreach ($filter->getIterator() as $path => $data) {
                // DO NOT call "getInterfaces()" after the "getFullClassName()"
                // DO NOT use reflection to get interfaces
                $interfaces = Tokenizer::getInterfaces($path);
                $class      = Tokenizer::getFullClassName($path);

                // Do 'autoload' checking first since the class_exists tries to use autoloader
                // but fails into "cannot include file" warning when model class is not set to use (LC_Dependencies issue)
                if (Autoloader::checkAutoload($class) && class_exists($class)) {
                    // $reflectionClass = new \ReflectionClass($class);
                    if ($class
                        && is_subclass_of($class, '\XLite\Model\AEntity')
                        && !is_subclass_of($class, '\XLite\Model\Base\Dump')
                    ) {
                        $class = ltrim($class, '\\');
                        $len   = strlen(\Includes\Utils\Database::getTablesPrefix());

                        // DO NOT remove leading backslash in interface name
                        if (in_array('\XLite\Base\IDecorator', $interfaces, true)) {
                            $parent   = Tokenizer::getParentClassName($path);
                            $parent   = ltrim($parent, '\\');
                            $metadata = Database::getEM()->getClassMetadata($parent);
                            $table    = substr($metadata->getTableName(), $len);

                            $reflector = $reflectorFactory->reflectSource($path);
                            $deps      = $reflector->getPositiveDependencies();

                            $tool   = new SchemaTool(Database::getEM());
                            $schema = $tool->getCreateSchemaSql([$metadata]);

                            foreach ((array) $metadata->reflFields as $field => $reflection) {
                                $pattern = '/(?:, |\()(' . $field . ' .+)(?:, [A-Za-z]|\) ENGINE)/USsi';

                                if ($reflection->class === $class
                                    && !empty($metadata->fieldMappings[$field])
                                    && preg_match($pattern, $schema[0], $matches)
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

                                            if (preg_match($pattern, $schema[0], $matches)) {
                                                $columns[$table][$col['name']] = $matches[1];
                                                if (!empty($deps)) {
                                                    foreach ($deps as $dep) {
                                                        $dependencies[$dep][$table][$col['name']] = $matches[1];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                        } elseif (
                            Database::getRepo($class)
                            && Database::getRepo($class)->canDisableTable()
                        ) {
                            $tableName = substr(
                                Database::getEM()->getClassMetadata($class)->getTableName(),
                                $len
                            );
                            if ($tableName) {
                                // For base models table does not exist
                                $tables[] = $tableName;
                            }

                            $metadata = Database::getEM()->getClassMetadata($class);
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
     * @param string $moduleId
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    protected static function tryLoadingOriginalMainAndCallMethod($moduleId, $methodName, array $arguments = [])
    {
        $classPath = static::getMainClassFilePath($moduleId);
        $className = static::getMainClassName($moduleId);

        if (file_exists($classPath) && !class_exists($className, false)) {
            require_once $classPath;
        }

        return method_exists($className, $methodName)
            ? call_user_func_array([$className, $methodName], $arguments)
            : null;
    }

    /**
     * @return string
     */
    protected static function getModuleIdByFilePathPattern()
    {
        return implode(
            preg_quote(\LC_DS, '/'),
            ['/classes', 'XLite', 'Module', '(\w+)', '(\w+)', '/S']
        );
    }

    /**
     * @return string
     */
    protected static function getModuleIdByClassNamePattern()
    {
        return '/(?:\\\\)?XLite\\\\Module\\\\(\w+)\\\\(\w+)(\\\\|$)/S';
    }

    /**
     * Static functions
     */

    public function __set($name, $value)
    {
        $previous = $this->__get($name);
        parent::__set($name, $value);

        if ($name === 'enabled' && $previous !== $value) {
            $this->onModuleEnabledChange($previous, $value);
        }
    }

    /**
     * @return string
     */
    public function getDisplayAuthor()
    {
        return $this->authorName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->moduleName;
    }

    /**
     * @return array
     */
    public function getLayoutColors()
    {
        return $this->callClassMethod('getLayoutColors');
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    public function callClassMethod($methodName, array $arguments = [])
    {
        return static::callMainClassMethod($this->id, $methodName, $arguments);
    }

    /**
     * @param bool $skipMainClassCheck
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isActiveSkin()
    {
        return $this->isEnabled()
            && $this->isSkin()
            && $this->activeSkin;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function isSkin()
    {
        return $this->type === 'skin';
    }

    /**
     * @return bool
     */
    public function isPayment()
    {
        return $this->type === 'payment';
    }

    /**
     * @return bool
     */
    public function isShipping()
    {
        return $this->type === 'shipping';
    }

    /**
     * Return list of module directories
     *
     * @param bool $refetch
     *
     * @return array
     */
    public function getDirectories($refetch = false)
    {
        if (!$this->directories || $refetch) {
            $this->directories = array_merge($this->getClassDirs(), $this->getSkinDirs(), $this->getCustomSkinDirs());
        }

        return $this->directories;
    }

    public function hasSettingsForm()
    {
        return $this->callClassMethod('showSettingsForm');
    }

    /**
     * Is called when module changes its enabled state
     *
     * @param bool $oldValue
     * @param bool $newValue
     */
    protected function onModuleEnabledChange($oldValue, $newValue)
    {
        if ($oldValue === false && $newValue) {
            StructureRegistry::moveModuleToEnabledRegistry(
                $this->id
            );
        } elseif ($oldValue && $newValue === false) {
            StructureRegistry::moveModuleToDisabledRegistry(
                $this->id
            );
        }
    }

    /**
     * Return list of module directories which contain class files
     *
     * @return array
     */
    protected function getClassDirs()
    {
        return [
            static::getSourcePath($this->author, $this->name),
        ];
    }

    /**
     * Return list of module directories which contain templates
     *
     * @return array
     */
    protected function getSkinDirs()
    {
        $interfacePaths = [];

        foreach (Layout::getInstance()->getSkinsAll() as $interface => $path) {
            $interfacePaths = array_merge(
                $interfacePaths,
                Layout::getInstance()->getExistingCoreSkinPaths($interface)
            );
        }

        $modulePath = $this->author . LC_DS . $this->name;

        $result = array_reduce($interfacePaths, function ($acc, $item) use ($modulePath) {
            $path = $item . LC_DS . 'modules' . LC_DS . $modulePath;
            if (\Includes\Utils\FileManager::isDirReadable($path)) {
                $acc[] = $path;
            }

            return $acc;
        }, []);

        return array_values(array_unique($result));
    }

    /**
     * Return list of module directories which contain templates. Custom skins
     *
     * @return array
     */
    protected function getCustomSkinDirs()
    {
        $result = [];
        /** @var array $skins */
        $skins = (array) $this->callClassMethod('getSkins');

        // Collect the custom skins registered via the module
        foreach ($skins as $folders) {
            $folders = array_map(function ($path) {
                return LC_DIR_SKINS . $path;
            }, $folders);

            $result = array_merge($result, $folders);
        }

        return array_values(array_unique($result));
    }
}
