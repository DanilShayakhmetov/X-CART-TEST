<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Utils;

use Includes\Utils\ModulesManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XLite\Console\Command\Helpers;

class ReloadModuleInstall extends Command
{
    use Helpers\ModuleTrait;

    protected function configure()
    {
        $this
            ->setName('utils:reloadModuleInstall')
            ->setDescription('Load install.yaml file of the given module')
            ->setHelp('Similar to the utils:loadYaml, this command loads the install.yaml file of the given module. Use it when you need to update the module installation data without reinstalling the module.')

            ->addArgument('module', InputArgument::REQUIRED, 'Module name, in "Author\\\\ModuleName" format')
            ->addOption('reloadLabelsOnly', 'l', InputOption::VALUE_NONE, 'Carefully reload install fixtures, avoid duplicates')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Loading the module install.yaml');

        $moduleName = $input->getArgument('module');

        try {
            $state = $this->getModuleStateByName($moduleName);

        } catch(\Exception $e) {
            $io->error($e->getMessage());
            return;
        }

        if ($state !== Helpers\Module::ENABLED) {
            $io->warning($this->getMessageByState($state, $moduleName));
            return;
        }

        list($author, $name) = explode('\\', $moduleName);

        $files = ModulesManager::getModuleYAMLFiles($author, $name);

        if ($files) {
            $this->loadModuleInstallYamls($files, $output, $input->getOption('reloadLabelsOnly'));
        } else {
            $io->warning('No files found for "' . $moduleName . '" module');
        }

        \XLite\Core\Database::getCacheDriver()->deleteAll();
        \XLite::getInstance()->getContainer()->get('widget_cache')->deleteAll();

        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Database::getEM()->clear();
    }

    /**
     * @param array           $files
     * @param OutputInterface $output
     * @param boolean         $onlyLabels
     *
     * @return int|mixed
     */
    protected function loadModuleInstallYamls(array $files, OutputInterface $output, $onlyLabels)
    {
        $command = $this->getApplication()->find('utils:loadYaml');

        $arguments = [
            'command' => $command->getName(),
            'files'   => $files,
        ];

        if ($onlyLabels) {
            $arguments['--allowedModels'] = implode(',' , [
               'XLite\Model\LanguageLabel'
            ]);
        }
        $input = new ArrayInput($arguments);
        return $command->run($input, $output);
    }
}
