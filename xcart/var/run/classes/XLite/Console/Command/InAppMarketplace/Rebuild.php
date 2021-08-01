<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\InAppMarketplace;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Rebuild extends AMarketplace
{
    protected function configure()
    {
        $this
            ->setName('marketplace:rebuild')
            ->setDescription('Rebuild cache <info>[!] BETA</info>')
            ->setHelp('');
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

        $io->title('Rebuild cache <info>[!] BETA</info>');

        \XLite\Core\Marketplace::getInstance()->dropRebuild();

        $scenario     = \XLite\Core\Marketplace::getInstance()->createScenario();
        $rebuildState = \XLite\Core\Marketplace::getInstance()->startRebuild($scenario['id']);

        $result = $this->doRebuild($io, $rebuildState);
        if ($result) {
            return $this->reportWarning($io, $result);
        }

        return $this->reportSuccess($io);
    }
}
