<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console;

use Symfony\Component\Console;
use XLite\Console\Command;

/**
 * Class Application
 * @package XLite\Console
 */
class Application extends Console\Application
{
    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new Command\Other\CheckYamlCommand();
        $defaultCommands[] = new Command\Other\CheckRepoCommand();
        $defaultCommands[] = new Command\Other\StoreDataToYamlCommand();
        $defaultCommands[] = new Command\Other\ReloadCommonLabels();

        $defaultCommands[] = new Command\Utils\RebuildViewLists();
        $defaultCommands[] = new Command\Utils\LoadYaml();
        $defaultCommands[] = new Command\Utils\ReloadModuleInstall();
        $defaultCommands[] = new Command\Utils\RunHook();
        $defaultCommands[] = new Command\Utils\YamlFormat();
        $defaultCommands[] = new Command\Utils\GenerateMainYaml();

        $defaultCommands[] = new Command\GenerateData\GenerateDataCommand();
        $defaultCommands[] = new Command\GenerateData\GenerateProductCommand();
        $defaultCommands[] = new Command\GenerateData\GenerateCategoryCommand();
        $defaultCommands[] = new Command\GenerateData\GenerateProfileCommand();

        $defaultCommands[] = new Command\Scaffolding\ItemsList();
        $defaultCommands[] = new Command\Scaffolding\FormModel();
        $defaultCommands[] = new Command\Scaffolding\Page();
        $defaultCommands[] = new Command\Scaffolding\Module();

        $defaultCommands[] = new Command\InAppMarketplace\Rebuild();
        $defaultCommands[] = new Command\InAppMarketplace\ModulesList();
        $defaultCommands[] = new Command\InAppMarketplace\SetState();
        $defaultCommands[] = new Command\InAppMarketplace\ApplyState();
        $defaultCommands[] = new Command\InAppMarketplace\RegisterLicense();
        $defaultCommands[] = new Command\InAppMarketplace\CheckForUpgrade();
        $defaultCommands[] = new Command\InAppMarketplace\Upgrade();

        return $defaultCommands;
    }
}
