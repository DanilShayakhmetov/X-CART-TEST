<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\InAppMarketplace;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetState extends AMarketplace
{
    protected function configure(): void
    {
        $this
            ->setName('marketplace:set-state')
            ->setDescription('Set modules state <info>[!] BETA</info>')
            ->setHelp('')
            ->addOption('enable', '-e', InputOption::VALUE_REQUIRED, 'Modules to enable', '')
            ->addOption('disable', '-d', InputOption::VALUE_REQUIRED, 'Modules to disable', '')
            ->addOption('install', '-i', InputOption::VALUE_REQUIRED, 'Modules to install', '')
            ->addOption('remove', '-r', InputOption::VALUE_REQUIRED, 'Modules to remove', '');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Set modules state <info>[!] BETA</info>');

        $changeUnits = [];

        foreach ($this->parseModulesList($input->getOption('enable')) as $moduleId) {
            $changeUnits[] = $this->getEnableChangeUnit($moduleId);
        }

        foreach ($this->parseModulesList($input->getOption('disable')) as $moduleId) {
            $changeUnits[] = $this->getDisableChangeUnit($moduleId);
        }

        foreach ($this->parseModulesList($input->getOption('install')) as $moduleId) {
            $changeUnits[] = $this->getInstallChangeUnit($moduleId);
        }

        foreach ($this->parseModulesList($input->getOption('remove')) as $moduleId) {
            $changeUnits[] = $this->getRemoveChangeUnit($moduleId);
        }

        \XLite\Core\Marketplace::getInstance()->dropRebuild();

        $scenario = \XLite\Core\Marketplace::getInstance()->createScenario();

        $state = \XLite\Core\Marketplace::getInstance()->changeModulesState($scenario['id'], $changeUnits);

        if ($state['modulesTransitions'] ?? []) {
            foreach ($state['modulesTransitions'] as $transition) {
                $io->writeln($transition['id'] . "\t" . $transition['transition']);
            }

            $rebuildState = \XLite\Core\Marketplace::getInstance()->startRebuild($scenario['id']);

            $result = $this->doRebuild($io, $rebuildState);
            if ($result) {
                return $this->reportWarning($io, $result);
            }
        } else {
            return $this->reportWarning($io, 'Empty transitions list');
        }

        return $this->reportSuccess($io);
    }
}
