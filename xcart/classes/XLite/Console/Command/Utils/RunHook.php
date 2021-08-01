<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Utils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunHook extends Command
{
    protected function configure()
    {
        $this
            ->setName('utils:runHook')
            ->setDescription('Run upgrade hook')
            ->setHelp('Runs the X-Cart upgrade hooks files, even the iterative ones, displaying the progress and the memory usage.')

            ->addArgument('files', InputArgument::IS_ARRAY, 'Hook files to be loaded')
        // TODO Automatically detect hooks by module and asks for versions
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Running the hook');
        $files = $input->getArgument('files');

        foreach ($files as $filePath) {
            try {
                $io->text('Running ' . $filePath);
                $this->runHook($filePath, $io);

            } catch (\Exception $e) {
                $io->text(' [FAIL]    - ' . $e->getMessage());
            }
            $io->newLine(2);
        }
    }

    protected function runHook($path, SymfonyStyle $io)
    {
        $path = $this->tryGetValidPath($path);
        $function = require $path;

        $status = null;

        $progress = $io->createProgressBar();
        $progress->setFormat("Memory usage: %message%   | File: %file%\n %current%/%max% [%bar%] %percent:3s%%");
        $progress->setMessage("Initiating...");
        $progress->setMessage($path, 'file');

        do {
            $mem1 = memory_get_usage();

            if (is_array($status) && $status && 0 < $status[0]) {
                if (!$progress->getMaxSteps()) {
                    $progress->start($status[1]);
                }

                if ($status[0] < $progress->getMaxSteps()) {
                    $progress->setProgress($status[0]);
                }
            }
            $status = $function($status);

            $mem2 = memory_get_usage();
            $progress->setMessage(\Includes\Utils\Converter::formatFileSize($mem2 - $mem1, ''));

        } while (!is_null($status));

        $progress->setMessage('Done');
        $progress->setMessage('Done', 'file');
        if (!$progress->getMaxSteps()) {
            $progress->start(1);
        }
        $progress->finish();
    }

    protected function tryGetValidPath($origPath)
    {
        $path = $origPath;

        if (!file_exists($path)) {
            $path = LC_DIR_ROOT . $origPath;

            if (!file_exists($path)) {
                $path = LC_DIR_CLASSES . $origPath;

                if (!file_exists($path)) {
                    throw new \InvalidArgumentException("$path doesn't exists");
                }
            }
        }

        return $path;
    }
}
