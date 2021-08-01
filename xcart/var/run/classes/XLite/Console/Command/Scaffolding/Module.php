<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Scaffolding;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XLite\Console\Application;
use XLite\Console\Command\Helpers;
use XLite\Console\Command\SourceCodeGenerators;
use XLite\Console\Command\Utils\GenerateMainYaml;

class Module extends Command
{
    use Helpers\ModuleTrait;
    use Helpers\ControllerTrait;

    protected function configure()
    {
        $this
            ->setName('scaffolding:module')
            ->setDescription('Generate a module')
            ->setHelp('')
            ->addOption('developerId', 'i', InputOption::VALUE_REQUIRED, 'Developer ID, example - CDev')
            ->addOption('moduleName', 'm', InputOption::VALUE_REQUIRED, 'Module name, example - FileAttachments')
            ->addOption('readableName', 'r', InputOption::VALUE_REQUIRED, 'Module readable name, example - "File attachments"')
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Module description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $devId = $input->getOption('developerId');
        $name = $input->getOption('moduleName');
        $rName = $input->getOption('readableName');
        $descr = $input->getOption('description');

        if (!preg_match('#[a-z_][a-z0-9_]*#i', $devId)) {
            $io->error('Dev ID should start with letter or underscore and can only contain only digits, letters and underscores');
            return;
        }

        if (strcasecmp($devId, 'new') === 0) {
            $io->error('"new" is keyword. Use another name for developer ID');
            return;
        }

        if (!preg_match('#[a-z_][a-z0-9_]*#i', $name)) {
            $io->error('Module name should start with letter or underscore and can only contain only digits, letters and underscores');
            return;
        }

        if (strcasecmp($name, 'new') === 0) {
            $io->error('"new" is keyword. Use another name for module name');
            return;
        }

        if (!strlen(trim($rName))) {
            $io->error('Module readable name is required');
            return;
        }

        if (file_exists(LC_DIR_CLASSES . "XLite/Module/$devId/$name/main.yaml")) {
            $rewrite = $io->choice("Module $devId/$name already exists, rewrite?", [
                'Yes',
                'No',
            ], 'No');

            if ($rewrite === 'No') {
                return;
            }
        }

        $path = LC_DIR_CLASSES . "XLite/Module/$devId/$name/Main.php";

        $result = \Includes\Utils\FileManager::write($path, SourceCodeGenerators\Module::generate(
            $devId,
            $name,
            $rName,
            $descr
        ));

        if ($result) {
            $this->executeYamlFileGenerationCommand($devId, $name, $output);
            \Includes\Utils\FileManager::deleteFile($path);

            $io->success(sprintf('New module path: "%s"', dirname($path)));
        } else {
            $io->error(sprintf('Unable to write file: "%s"', $path));
        }
    }

    protected function executeYamlFileGenerationCommand($devId, $name, $output)
    {
        $application = new Application();

        $application->add(new GenerateMainYaml());

        $command = $application->find('utils:generateMainYaml');

        $command->run(new ArrayInput([
            'command' => $command->getName(),
            'module'  => sprintf('%s/%s', $devId, $name),
        ]), $output);
    }
}
