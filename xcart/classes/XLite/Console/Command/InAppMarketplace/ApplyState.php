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

class ApplyState extends AMarketplace
{
    protected function configure(): void
    {
        $this
            ->setName('marketplace:apply-state')
            ->setDescription('Apply modules state <info>[!] BETA</info>')
            ->setHelp('')
            ->addOption('list', '-l', InputOption::VALUE_REQUIRED, 'Modules list to set', '');
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

        $io->title('Apply modules state <info>[!] BETA</info>');

        \XLite\Core\Marketplace::getInstance()->dropRebuild();

        $scenario = \XLite\Core\Marketplace::getInstance()->createScenario();

        $changeUnits = [];

        $listToApply = $this->parseModulesList($input->getOption('list'));

        $enabledModules = array_map(static function ($module) {
            return $module['id'];
        }, $this->getModulesList('enabled'));

        foreach (array_diff($enabledModules, $listToApply) as $moduleId) {
            $changeUnits[] = $this->getDisableChangeUnit($moduleId);
        }

        $disabledModules = array_map(static function ($module) {
            return $module['id'];
        }, $this->getModulesList('disabled'));

        foreach ($listToApply as $moduleId) {
            if (in_array($moduleId, $disabledModules, true)) {
                $changeUnits[] = $this->getEnableChangeUnit($moduleId);
            } elseif (!in_array($moduleId, $enabledModules)) {
                $changeUnits[] = $this->getInstallChangeUnit($moduleId);
            }
        }

        $state = \XLite\Core\Marketplace::getInstance()->changeModulesState($scenario['id'], $changeUnits);

        if ($state['modulesTransitions']) {
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
