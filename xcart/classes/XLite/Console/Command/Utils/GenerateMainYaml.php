<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Utils;

use Includes\Utils\Module\Module;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateMainYaml extends Command
{
    protected function configure()
    {
        $this
            ->setName('utils:generateMainYaml')
            ->setDescription('Generate main.yaml for modules')
            ->setHelp('optional argument - module name')
            ->addArgument('module', InputArgument::OPTIONAL, 'Module name')
            ->addOption('filter', 'f', InputOption::VALUE_NONE, 'Filter fields with optional values');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io     = new SymfonyStyle($input, $output);
        $module = $input->getArgument('module');

        $helper = new \Includes\Decorator\Plugin\Doctrine\Plugin\UpdateModules\Main;

        $modules = $module ? [$module] : $helper->getModulesList();

        $progress = $io->createProgressBar(count($modules));
        $progress->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
        $progress->setMessage('Initiating...');
        $progress->start();

        foreach ($modules as $module) {
            $progress->advance();
            $progress->setMessage($module);

            $data = $helper->getModuleData($module);
            if ($data) {
                if ($input->getOption('filter')) {
                    $data = $helper->filterOptionalValues($data);
                }

                $helper->saveModuleData($module, $data);
            }
        }

        $progress->setMessage('Done');
        $progress->finish();

        $io->newLine(2);

        $io->success('Finished');
    }
}
