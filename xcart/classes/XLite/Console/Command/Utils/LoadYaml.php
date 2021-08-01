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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadYaml extends Command
{
    protected function configure()
    {
        $this
            ->setName('utils:loadYaml')
            ->setDescription('Load fixtures from a yaml file')
            ->setHelp('')

            ->addArgument('files', InputArgument::IS_ARRAY, 'List of yaml files to load')
            ->addOption('allowedModels', 'a', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'List of allowed models, separated with comma')
            ->addOption('excludedModels', 'd', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'List of excluded models, separated with comma')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Loading the yaml file');
        $files = $input->getArgument('files');

        $loadYamlOptions = null;

        if ($input->getOption('allowedModels') || $input->getOption('excludedModels')) {
            $loadYamlOptions = [];

            if ($input->getOption('allowedModels')) {
                $loadYamlOptions['allowedModels'] = explode(',', $input->getOption('allowedModels'));
            }

            if ($input->getOption('excludedModels')) {
                $loadYamlOptions['excludedModels'] = explode(',', $input->getOption('excludedModels'));
            }
        }

        foreach ($files as $filePath) {
            try {
                $io->write('Loading ' . $filePath);
                $this->loadYaml($filePath, $loadYamlOptions);
                $io->writeln(' [OK]');

            } catch (\Exception $e) {
                $io->writeln(' [FAIL]    - ' . $e->getMessage());
            }
        }

        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Database::getEM()->clear();
    }

    protected function loadYaml($path, $options)
    {
        $path = $this->tryGetValidPath($path);

        try {
            \Symfony\Component\Yaml\Yaml::parse($path);

        } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
            throw new \InvalidArgumentException("$path: yaml validation failed, message: " . $e->getMessage());
        }

        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($path, $options);
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
