<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Other;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckYamlCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('other:checkYaml')
            ->setDescription('Check yaml files for errors')
            ->setHelp('This command validates given yaml file by trying to load and parse it.')
            ->addArgument('files', InputArgument::IS_ARRAY, 'Files to check')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Yaml checker command');

        $files = $input->getArgument('files');

        $files = $this->prepareFilesList($files);

        $progress = $io->createProgressBar(count($files));
        $progress->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
        $progress->setMessage("Initiating...");
        $progress->start();

        $errors = [];

        foreach ($files as $filePath) {
            $progress->advance();
            $progress->setMessage($filePath);

            try {
                $data = \Includes\Utils\FileManager::parseYamlFile($filePath);

            } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
                $error = sprintf(
                    '<info>%s</info>: %s',
                    $filePath,
                    $e->getMessage()
                );
                $errors[$filePath] = $error;
            }
        }
        $progress->setMessage("Done");
        $progress->finish();

        $io->newLine(2);

        if ($errors) {
            $io->error('Errors found');
            $io->listing($errors);
        } else {
            $io->success('Finished');
        }

    }

    /**
     * @param $rawFiles
     *
     * @return array     *
     */
    protected function prepareFilesList($rawFiles)
    {
        $files = [];

        if (!$rawFiles) {
            $filter = new \Includes\Utils\FileFilter(LC_DIR, '/\.yaml$/', \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($filter->getIterator() as $file) {
                $files[] = $file->getPathname();
            }

            sort($files);

        } else {
            foreach ($rawFiles as $filePath) {
                if (!file_exists($filePath)) {
                    $filePath2 = LC_DIR_ROOT . '/' . $filePath;
                    if (!file_exists($filePath2)) {
                        $error = sprintf('File %s not found!', $filePath2);
                        throw new \LogicException($error);
                    }
                    $filePath = $filePath2;
                }

                $files[] = $filePath;
            }
        }

        return $files;
    }
}
