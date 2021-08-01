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

class Upgrade extends AMarketplace
{
    protected function configure(): void
    {
        $this
            ->setName('marketplace:upgrade')
            ->setDescription('Upgrade <info>[!] BETA</info>')
            ->setHelp('')
            ->addOption('type', '-t', InputOption::VALUE_REQUIRED, 'Upgrade Type (Possible values: build, minor, major, core, self)')
            ->addOption('list', '-l', InputOption::VALUE_REQUIRED, 'Modules list to upgrade', '');
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

        $io->title('Upgrade <info>[!] BETA</info>');

        $type = $input->getOption('type');
        if (!in_array($type, $this->upgradeTypes, true)) {
            return $this->reportWarning($io, 'Unallowed upgrade type', 1);
        }

        $availableUpgradeTypes = array_map(static function ($item) {
            return $item['name'];
        }, $this->getAvailableUpgradeTypes());

        if ($type !== 'self' && in_array('self', $availableUpgradeTypes, true)) {
            return $this->reportWarning($io, 'Upgrade In-App Marketplace (self) first', 1);
        }

        if (!in_array($type, $availableUpgradeTypes, true)) {
            return $this->reportWarning($io, "No upgrade for type {$type}", 1);
        }

        $list = $this->parseModulesList($input->getOption('list'));

        $changeUnits = [];

        $upgradeEntries = $this->getUpgradeList($type);
        foreach ($upgradeEntries as $entry) {
            if (empty($list) || in_array($entry['id'], $list, true)) {
                if ($entry['canUpgrade']) {
                    $changeUnits[] = $this->getUpgradeChangeUnit($entry['id'], $entry['entry']['version']);
                } else {
                    $changeUnits[] = $this->getRemoveChangeUnit($entry['id']);
                }
            }
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
