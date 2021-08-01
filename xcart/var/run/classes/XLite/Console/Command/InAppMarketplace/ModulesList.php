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

class ModulesList extends AMarketplace
{
    protected function configure()
    {
        $this
            ->setName('marketplace:modules-list')
            ->setDescription('Modules list <info>[!] BETA</info>')
            ->setHelp('')
            ->addOption('filter', '-f', InputOption::VALUE_REQUIRED, 'List filter (possible values: installed, enabled, disabled, marketplace, installable, unallowed)', 'installed');
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

        $io->title('Modules list <info>[!] BETA</info>');

        $list = $this->getModulesList($input->getOption('filter'));

        foreach ($list as $module) {
            $io->writeln($module['id']);
        }

        return $this->reportSuccess($io);
    }
}
