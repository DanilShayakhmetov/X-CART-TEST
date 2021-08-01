<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates\Plugin\ViewLists;

use Includes\Annotations\Parser\AnnotationParserFactory;
use Includes\ClassPathResolver;
use Includes\Decorator\ClassBuilder\ClassBuilderFactory;
use Includes\Decorator\Utils\Operator;
use Includes\Reflection\StaticReflectorFactory;
use Includes\Utils\Module\Manager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use XLite\Core\Layout;

/**
 * Main
 *
 */
class Main extends \Includes\Decorator\Plugin\Templates\Plugin\APlugin
{
    /**
     * Parameters for the tags
     */
    const PARAM_TAG_LIST_CHILD_CLASS      = 'class';
    const PARAM_TAG_LIST_CHILD_LIST       = 'list';
    const PARAM_TAG_LIST_CHILD_WEIGHT     = 'weight';
    const PARAM_TAG_LIST_CHILD_ZONE       = 'zone';
    const PARAM_TAG_LIST_CHILD_PRESET     = 'preset';
    const PARAM_TAG_LIST_CHILD_FIRST      = 'first';
    const PARAM_TAG_LIST_CHILD_LAST       = 'last';
    const PARAM_TAG_LIST_CHILD_CONTROLLER = 'controller';

    /**
     * Temporary index to use in templates hash
     */
    const PREPARED_SKIN_NAME = '____PREPARED____';

    /**
     * List of PHP classes with the "ListChild" tags
     *
     * @var array
     */
    protected $annotatedPHPCLasses;

    /**
     * Runtime cached path for view classes dir
     *
     * @var string
     */
    protected $viewClassesDir;

    /**
     * Path for temporary build classes
     *
     * @var
     */
    protected $tempClassesDir;

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // Create new
        $this->createLists();
    }

    /**
     * Remove existing lists from database
     *
     * @return void
     */
    protected function clearAll()
    {
        \XLite\Core\Database::getRepo('XLite\Model\ViewList')->clearAll();
    }

    /**
     * Create lists
     *
     * @return void
     */
    protected function createLists()
    {
        $nodes = $this->getAllListChildTags();
        $inserted = [];
        $repo = \XLite\Core\Database::getRepo('\XLite\Model\ViewList');

        foreach ($nodes as $key => $node) {
            list($node, $omitted) = $this->omitKeys($node, ['parent', 'name']);
            $parentKey = isset($omitted['parent']) ? $omitted['parent'] : null;

            if ($parentKey && !isset($inserted[$parentKey])) {
                $identifier = $node['child']
                    ? 'Class: ' . $node['child']
                    : 'Template: ' . $node['tpl'];
                throw new \Exception('ListChild preset (' . $identifier . ') cannot be inserted without existing parent record');
            }

            /** @var \XLite\Model\ViewList $entity */
            $entity = $repo->insert($node, false);

            if ($parentKey) {
                $entity->setParent($inserted[$parentKey]);
            }

            $inserted[$key] = $entity;
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Return all defined "ListChild" tags
     *
     * @return array
     */
    protected function getAllListChildTags()
    {
        return array_merge($this->getListChildTagsFromPHP(), $this->getListChildTagsFromTemplates());
    }

    /**
     * Return list of PHP classes with the "ListChild" tag
     *
     * @return array
     */
    protected function getAnnotatedPHPCLasses()
    {
        if ($this->annotatedPHPCLasses === null) {
            $this->annotatedPHPCLasses = [];

            $classPathResolver = new ClassPathResolver($this->getViewClassesDir());
            $reflectorFactory  = new StaticReflectorFactory($classPathResolver);

            foreach ($this->getViewClasses() as $pathname) {
                $reflector   = $reflectorFactory->reflectSource($pathname);
                $annotations = $reflector->getClassAnnotationsOfType('Includes\Annotations\ListChild');

                foreach ($annotations as $annotation) {
                    $newListChild = [
                        'child'  => $classPathResolver->getClass($pathname),
                        'list'   => $annotation->list,
                        'weight' => $annotation->weight,
                        'name'   => $annotation->name
                    ];

                    if ($annotation->zone) {
                        $newListChild['zone'] = $annotation->zone;
                    }

                    if ($annotation->preset) {
                        $newListChild['preset'] = $annotation->preset;
                    }

                    /** @var \Includes\Annotations\ListChild $annotation */
                    $this->annotatedPHPCLasses[] = $newListChild;
                }
            }
        }

        return $this->annotatedPHPCLasses;
    }

    /**
     * Returns an iterator for all core's and modules' View files
     *
     * @return array
     */
    protected function getViewClasses()
    {
        $classes = $this->getViewClassesDir();

        $viewDirs = array_merge(
            [$classes . 'XLite/View'],
            glob($classes . 'XLite/Module/*/*/View') ?: []
        );

        $viewFiles = [];

        foreach ($viewDirs as $dir) {
            $iterator   = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            $filesFiles = new RegexIterator($iterator, '/^.+\.php$/', RegexIterator::GET_MATCH);

            foreach ($filesFiles as $files) {
                foreach ($files as $file) {
                    $viewFiles[] = $file;
                }
            }
        }

        return $viewFiles;
    }

    /**
     * Create temporary cache for view classes with dependencies
     * Only for not rebuild mode
     */
    protected function buildTempClasses()
    {
        if ($this->tempClassesDir !== null || self::getStep() !== null) {
            return;
        }

        $this->tempClassesDir = $this->getViewClassesDir();
        \Includes\Autoloader::switchToOriginalClassDir();
        $modules = Manager::getRegistry()->getEnabledModuleIds();

        $classBuilder = (new ClassBuilderFactory())->create(
            LC_DIR_CLASSES,
            $this->tempClassesDir,
            $modules
        );

        $fileIterator = new \Includes\Utils\FileFilter(
            LC_DIR_CLASSES,
            Manager::getPathPatternForPHP(LC_DIR_CLASSES)
        );

        foreach ($fileIterator->getIterator() as $file) {
            $classBuilder->buildPathname((string) $file);
        }
    }

    /**
     * Remove temporary view classes cache if exist
     */
    protected function removeTempClasses()
    {
        if ($this->tempClassesDir === null || self::getStep() !== null) {
            return;
        }

        $deleteDir = substr($this->tempClassesDir, 0, strlen('classes' . LC_DS) * -1);
        \Includes\Autoloader::switchToCachedClassDir();
        \Includes\Utils\FileManager::unlinkRecursive($deleteDir);
        $this->tempClassesDir = null;
    }

    /**
     * Get path for view classes dir
     * @return string
     */
    protected function getViewClassesDir()
    {
        if (self::getStep() !== null) {
            return Operator::getClassesDir();
        }

        if ($this->viewClassesDir === null) {
            $dir = rtrim(LC_DIR_COMPILE, LC_DS);
            $this->viewClassesDir = $dir . '.' . md5(time()) . LC_DS . 'classes' . LC_DS;
        }

        return $this->viewClassesDir;
    }

    /**
     * Return all "ListChild" tags defined in PHP classes
     *
     * @return array
     */
    protected function getListChildTagsFromPHP()
    {
        try {
            $this->buildTempClasses();
            $classes = $this->getAnnotatedPHPCLasses();
            $listTags = $this->getAllListChildTagAttributes($classes);
            $this->removeTempClasses();
        } catch (\Exception $exception) {
            $this->removeTempClasses();
            throw $exception;
        }

        return $listTags;
    }

    /**
     * Return all "ListChild" tags defined in templates
     *
     * @return array
     */
    protected function getListChildTagsFromTemplates()
    {
        return $this->getAllListChildTagAttributes($this->prepareListChildTemplates($this->getAnnotatedTemplates()));
    }

    /**
     * Prepare list childs templates-based
     *
     * @param array $list List
     *
     * @return array
     */
    protected function prepareListChildTemplates(array $list)
    {
        $result = [];

        \XLite::getInstance()->initModules();

        $skins         = [];
        $hasSubstSkins = false;

        $layout = Layout::getInstance();

        foreach ($layout->getSkinsAll() as $interface => $path) {
            if (\XLite::MAIL_INTERFACE === $interface || \XLite::PDF_INTERFACE === $interface) {
                $skins[$interface] = [];
                foreach ($layout->getSkins($interface) as $skin) {
                    foreach ([\XLite::ADMIN_INTERFACE, \XLite::COMMON_INTERFACE, \XLite::CUSTOMER_INTERFACE] as $innerInterface) {
                        $skins[$interface][] = $skin . LC_DS . $innerInterface;
                    }
                }
            } else {
                $skins[$interface] = $layout->getSkins($interface);
            }

            if (!$hasSubstSkins) {
                $hasSubstSkins = 1 < count($skins[$interface]);
            }
        }

        foreach ($list as $i => $cell) {
            foreach ($skins as $interface => $paths) {
                foreach ($paths as $path) {
                    if (0 === strpos($cell['tpl'], $path . LC_DS)) {
                        $length           = strlen($path) + 1;
                        $list[$i]['tpl']  = substr($cell['tpl'], $length);
                        $list[$i]['zone'] = $interface;
                    }
                }
            }

            if (!isset($list[$i]['zone'])) {
                unset($list[$i]);
            }
        }

        if ($hasSubstSkins) {
            $patterns = $hash = [];

            foreach ($skins as $interface => $data) {
                $patterns[$interface] = [];

                foreach ($data as $skin) {
                    $patterns[$interface][] = preg_quote($skin, '/');
                }

                $patterns[$interface] = '/^(' . implode('|', $patterns[$interface]) . ')' . preg_quote(LC_DS, '/') . '(.+)$/US';
            }

            foreach ($list as $index => $item) {
                $path = \Includes\Utils\FileManager::getRelativePath($item['path'], LC_DIR_SKINS);

                if (preg_match($patterns[$item['zone']], $path, $matches)) {
                    $hash[$item['zone']][$item['tpl']][$matches[1]] = $index;
                    $list[$index]['tpl']                            = $matches[2];
                }
            }

            foreach ($hash as $interface => $tpls) {
                foreach ($tpls as $path => $indexes) {
                    $idx  = null;
                    $tags = [];
                    foreach (array_reverse($skins[$interface]) as $skin) {
                        if (isset($indexes[$skin])) {
                            $idx    = $indexes[$skin];
                            $tags[] = $list[$indexes[$skin]]['tags'];
                        }
                    }

                    foreach ($this->processTagsQuery($tags) as $tag) {
                        $tmp = $list[$idx];
                        unset($tmp['tags'], $tmp['path']);
                        $result[] = $tmp + $tag;
                    }
                }
            }

            // Convert template short path to UNIX-style
            if (DIRECTORY_SEPARATOR !== '/') {
                foreach ($result as $i => $v) {
                    $result[$i]['tpl'] = str_replace(DIRECTORY_SEPARATOR, '/', $v['tpl']);
                }
            }

        } else {

            foreach ($list as $cell) {
                foreach ($this->processTagsQuery([$cell['tags']]) as $tag) {
                    unset($cell['tags'], $cell['path']);
                    $result[] = $cell + $tag;
                }
            }

        }

        return $result;
    }

    /**
     * Process tags query
     *
     * @param array $tags Tags query
     *
     * @return array
     */
    protected function processTagsQuery(array $tags)
    {
        $result = [];
        $add = [];

        foreach ($tags as $step) {
            if (isset($step[static::TAG_CLEAR_LIST_CHILDREN])) {
                $result = [];
            }

            if (isset($step[static::TAG_LIST_CHILD])) {
                $result = $step[static::TAG_LIST_CHILD];
            }

            if (isset($step[static::TAG_ADD_LIST_CHILD])) {
                $add[] = $step[static::TAG_ADD_LIST_CHILD];
            }
        }

        return array_merge($result, ...$add);
    }

    /**
     * Return all defined "ListChild" tag attributes
     *
     * @param array $nodes List of nodes
     *
     * @return array
     */
    protected function getAllListChildTagAttributes(array $nodes)
    {
        $presetNodes = [];
        $result = array_reduce($nodes, function($acc, $item) use (&$presetNodes) {
            $data = $this->prepareListChildTagData($item);

            if (isset($data['preset'])) {
                $presetNodes[] = $data;
            } else {
                $key = $this->getUniqueHashForNode($data);
                $acc[$key] = $data;
            }

            return $acc;
        }, []);

        if (count($presetNodes) > 0) {
            foreach ($presetNodes as $node) {
                $key = $this->getUniqueHashForNode($node);

                if (array_key_exists($key, $result)) {
                    $node['parent'] = $key;
                }

                $newKey = $key . $node['preset'];
                $result[$newKey] = $node;
            }
        }

        return $result;
    }

    /**
     * @param $data
     * @return string
     */
    protected function getUniqueHashForNode($data)
    {
        $zone = isset($data['zone']) ? $data['zone'] : '';
        $tpl = isset($data['tpl']) ? $data['tpl'] : '';
        $child = isset($data['child']) ? $data['child'] : '';
        $nameOrList = isset($data['name']) && !empty($data['name']) ? $data['name'] : $data['list'];

        return md5($zone . $tpl . $child . $nameOrList);
    }

    /**
     * Prepare attributes of the "ListChild" tag
     *
     * @param array $data Tag attributes
     *
     * @return array
     */
    protected function prepareListChildTagData(array $data)
    {
        // Check the weight-related attributes
        $this->prepareWeightAttrs($data);

        // Check for preprocessors
        $this->preparePreprocessors($data);

        return $data;
    }

    /**
     * Check the weight-related attributes
     *
     * @param array &$data Data to prepare
     *
     * @return void
     */
    protected function prepareWeightAttrs(array &$data)
    {
        // The "weight" attribute has a high priority
        if (!isset($data[static::PARAM_TAG_LIST_CHILD_WEIGHT])) {

            // "First" and "last" - the reserved keywords for the "weight" attribute values
            foreach ($this->getReservedWeightValues() as $origKey => $modelKey) {

                if (isset($data[$origKey])) {
                    $data[static::PARAM_TAG_LIST_CHILD_WEIGHT] = $modelKey;
                }
            }
        } else {

            $data[static::PARAM_TAG_LIST_CHILD_WEIGHT] = intval($data[static::PARAM_TAG_LIST_CHILD_WEIGHT]);
        }

        // Set default value
        if (!isset($data[static::PARAM_TAG_LIST_CHILD_WEIGHT])) {
            $data[static::PARAM_TAG_LIST_CHILD_WEIGHT] = \XLite\Model\ViewList::POSITION_LAST;
        }
    }

    /**
     * Check for so called list "preprocessors"
     *
     * @param array &$data Data to use
     *
     * @return void
     */
    protected function preparePreprocessors(array &$data)
    {
        if (isset($data[static::PARAM_TAG_LIST_CHILD_CONTROLLER])) {
            // ...
        }
    }

    /**
     * There are some reserved words for the "weight" param of the "ListChild" tag
     *
     * @return void
     */
    protected function getReservedWeightValues()
    {
        return [
            static::PARAM_TAG_LIST_CHILD_FIRST => \XLite\Model\ViewList::POSITION_FIRST,
            static::PARAM_TAG_LIST_CHILD_LAST  => \XLite\Model\ViewList::POSITION_LAST,
        ];
    }
}
