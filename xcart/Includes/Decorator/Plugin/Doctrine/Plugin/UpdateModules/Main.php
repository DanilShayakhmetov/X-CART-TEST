<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\UpdateModules;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;

/**
 * Main 
 * @todo: temporary logic for old modules
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        foreach ($this->getModulesList() as $module) {
            list($author, $name) = explode('/', $module);
            $file = \LC_DIR_MODULES . $author . \LC_DS . $name . \LC_DS . 'main.yaml';

            if (!file_exists($file)) {
                $data = $this->getModuleData($module);
                if ($data) {
                    $this->saveModuleData($module, $data);
                    Manager::getRegistry()->updateModule($author, $name, $data);
                }
            }
        }
        
        Manager::getRegistry()->save();
    }

    public function getModulesList()
    {
        chdir(\LC_DIR_MODULES);

        return glob('*/*');
    }

    public function getModuleData($module)
    {
        $data = [];

        list($author, $name) = explode('/', $module);
        $moduleId = Module::buildId($author, $name);

        $majorVersion = Module::callMainClassMethod($moduleId, 'getMajorVersion');
        if (empty($majorVersion)) {
            return $data;
        }

        $minor = (int) Module::callMainClassMethod($moduleId, 'getMinorVersion');
        $build = (int) Module::callMainClassMethod($moduleId, 'getBuildVersion') ?: 0;

        //$data['id'] = $moduleId;

        $data['version'] = $majorVersion . '.' . $minor . '.' . $build;

        $types = [
            0x1  => 'custom',
            0x2  => 'payment',
            0x4  => 'skin',
            0x8  => 'shipping',
            null => 'common',
        ];

        $type = Module::callMainClassMethod($moduleId, 'getModuleType');

        $data['type'] = $types[$type];

        $data['author']      = $author;
        $data['name']        = $name;
        $data['authorName']  = Module::callMainClassMethod($moduleId, 'getAuthorName') ?: $author;
        $data['moduleName']  = Module::callMainClassMethod($moduleId, 'getModuleName') ?: $name;
        $data['description'] = Module::callMainClassMethod($moduleId, 'getDescription') ?: '';

        $data['minorRequiredCoreVersion'] = (int) Module::callMainClassMethod($moduleId, 'getMinorRequiredCoreVersion');

        $data['dependsOn'] = Module::convertId(
            Module::callMainClassMethod($moduleId, 'getDependencies') ?: []
        );

        $data['incompatibleWith'] = Module::convertId(
            Module::callMainClassMethod($moduleId, 'getMutualModulesList') ?: []
        );

        $data['skins'] = Module::callMainClassMethod($moduleId, 'getSkins') ?: [];

        $data['showSettingsForm'] = Module::callMainClassMethod($moduleId, 'showSettingsForm') ?: false;
        $isSystem = Module::callMainClassMethod($moduleId, 'isSystem') ?: false;
        if ($isSystem) {
            $data['isSystem'] = true;
        }
        $data['canDisable']       = Module::callMainClassMethod($moduleId, 'canDisable') ?: true;

        return $data;
    }

    public function saveModuleData($module, $data)
    {
        list($author, $name) = explode('/', $module);

        $file = \LC_DIR_MODULES . $author . \LC_DS . $name . \LC_DS . 'main.yaml';

        $dumper = new \Symfony\Component\Yaml\Dumper();
        $dumper->setIndentation(2);

        unset($data['author'], $data['name']);
        $dump = preg_replace('#\-(\n)+[\s]*+#', '- ', $dumper->dump($data, 5));

        file_put_contents($file, $dump);
    }

    public function filterOptionalValues($data)
    {
        $result = [];
        foreach ($data as $name => $value) {
            if ($name === 'version'
                || ($name === 'type' && $value !== 'common')
                || ($name === 'authorName' && $value !== $data['author'])
                || ($name === 'moduleName' && $value !== $data['name'])
                || ($name === 'description' && !empty(trim($value)))
                || ($name === 'minorRequiredCoreVersion' && ((int) $value) > 0)
                || ($name === 'dependsOn' && !empty(array_filter($value)))
                || ($name === 'incompatibleWith' && !empty(array_filter($value)))
                || ($name === 'skins' && !empty(array_filter($value)))
                || ($name === 'showSettingsForm' && $value)
                || ($name === 'isSystem' && $value)
                || ($name === 'canDisable' && !$value)
            ) {
                $result[$name] = $value;
            }
        }

        return $result;
    }
}
