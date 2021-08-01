<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Restore;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module as XCartModule;
use Includes\Utils\Module\Registry;
use XCart\Bus\Domain\Module;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 *
 * This class actively uses X-Cart classes and is considered to be dangerous in day-to-day production use.
 * Mainly it is utilized during restoredb procedure and developer mode (for providing on-the-fly module data).
 */
class ModuleInfoProvider
{
    public const MODULE_TYPE_CUSTOM_MODULE = 0x1;
    public const MODULE_TYPE_PAYMENT       = 0x2;
    public const MODULE_TYPE_SKIN          = 0x4;
    public const MODULE_TYPE_SHIPPING      = 0x8;

    /**
     * @var $registry
     */
    protected $registry;

    /**
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor()
    {
        $registry = Manager::getRegistry();

        return new self(
            $registry
        );
    }

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Retrieves all installed modules from X-Cart in BUS-compatible format
     *
     * @return array
     *
     * @todo: test
     */
    public function getInstalledModules(): array
    {
        $modules = $this->registry->getModules();

        return array_reduce($modules, function ($acc, $xcartModule) {
            /** @var XCartModule $xcartModule */
            /** @var Module $module */
            $module = $this->buildModuleRecord($xcartModule);

            $acc[$module->id] = $module;

            return $acc;
        }, []);
    }

    /**
     * @param XCartModule $xcartModule
     *
     * @return Module
     */
    private function buildModuleRecord(XCartModule $xcartModule): Module
    {
        $module = new Module();

        $module->id                       = $xcartModule->author . '-' . $xcartModule->name;
        $module->version                  = $xcartModule->version;
        $module->type                     = $xcartModule->type;
        $module->author                   = $xcartModule->author;
        $module->name                     = $xcartModule->name;
        $module->authorName               = $xcartModule->authorName;
        $module->moduleName               = $xcartModule->moduleName;
        $module->description              = $xcartModule->description;
        $module->minorRequiredCoreVersion = $xcartModule->minorRequiredCoreVersion;
        $module->dependsOn                = $xcartModule->dependsOn;
        $module->incompatibleWith         = $xcartModule->incompatibleWith;
        $module->showSettingsForm         = $xcartModule->showSettingsForm;
        $module->isSystem                 = $xcartModule->isSystem;
        $module->canDisable               = $xcartModule->canDisable;
        $module->icon                     = XCartModule::getIconURL($xcartModule->author, $xcartModule->name);
        $module->installed                = true;
        $module->installedDate            = time();
        $module->integrated               = $xcartModule->enabled;
        $module->enabled                  = $xcartModule->enabled;
        $module->enabledDate              = floor(time() / 60) * 60;
        $module->skinPreview              = XCartModule::getSkinPreviewURL($xcartModule->author, $xcartModule->name);
        $module->service                  = $xcartModule->service;

        return $module;
    }
}
