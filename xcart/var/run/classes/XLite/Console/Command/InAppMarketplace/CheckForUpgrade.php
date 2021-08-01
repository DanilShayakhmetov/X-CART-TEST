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

class CheckForUpgrade extends AMarketplace
{
    protected function configure(): void
    {
        $this
            ->setName('marketplace:check-for-upgrade')
            ->setDescription('Check For Upgrade <info>[!] BETA</info>')
            ->setHelp('')
            ->addOption('type', '-t', InputOption::VALUE_REQUIRED, 'Upgrade Type (Possible values: build, minor, major, core, self)', '');
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

        $io->title('Check For Upgrade <info>[!] BETA</info>');

        $type = $input->getOption('type');
        if ($type && !in_array($type, $this->upgradeTypes, true)) {
            return $this->reportWarning($io, 'Unallowed upgrade type', 1);
        }

        if ($input->getOption('type') === '') {
            $rows = [];

            $availableUpgradeTypes = $this->getAvailableUpgradeTypes();
            foreach ($availableUpgradeTypes as $type) {
                if ($type['name'] === 'self') {
                    $io->writeln("<info>{$type['name']}\t{$type['count']}</info>");
                } else {
                    $io->writeln("{$type['name']}\t{$type['count']}");
                }
            }
        } else {
            $rows = [];

            $upgradeEntries = $this->getUpgradeList($type);
            foreach ($upgradeEntries as $entry) {
                if ($entry['canUpgrade']) {
                    $io->writeln("{$entry['id']}\t{$entry['entry']['installedVersion']}\t=>\t{$entry['entry']['version']}\tUpgrade");
                } else {
                    $io->writeln("<info>{$entry['id']}\t{$entry['entry']['installedVersion']}\t=>\t{$entry['entry']['version']}\tRemove</info>");
                }
            }
        }

        return $this->reportSuccess($io);
    }
}
