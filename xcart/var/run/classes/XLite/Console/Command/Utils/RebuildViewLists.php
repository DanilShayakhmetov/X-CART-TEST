<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Utils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RebuildViewLists extends Command
{
    protected function configure()
    {
        $this
            ->setName('utils:rebuildViewLists')
            ->setDescription('Recalculate view lists')
            ->setHelp('This action reloads each @ListChild annotation and updates view list records in database. Use it when you have added a new file with the @ListChild annotation or modified the params of this annotation.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Recalculate view lists');

        $plugins = [
            new \Includes\Decorator\Plugin\Templates\Plugin\ViewLists\Main(),
            new \Includes\Decorator\Plugin\Templates\Plugin\Compiler\Main(),
            new \Includes\Decorator\Plugin\ModuleHandlers\Main(),
            new \Includes\Decorator\Plugin\Templates\Plugin\ViewListsPostprocess\Main(),
        ];

        $io->progressStart(count($plugins));
        foreach ($plugins as $plugin) {
            $plugin->executeHookHandler();
            $io->progressAdvance();
        }
        $io->progressFinish();

        \XLite\Core\Database::getEM()->flush();

        $io->success('View lists recalculated');
    }
}
