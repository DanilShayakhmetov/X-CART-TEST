<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Scaffolding;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XLite\Console\Command\SourceCodeGenerators\PhpClass;
use XLite\Console\Command\SourceCodeGenerators\Utils;
use XLite\Core\Converter;

use XLite\Console\Command\SourceCodeGenerators;

class FormModel extends Command
{
    protected function configure()
    {
        $this
            ->setName('scaffolding:formModel')
            ->setDescription('Generate a FormModel and related classes for the given entity')
            ->setHelp('This command generates the entity editing\creating page.')

            ->addArgument('entity', InputArgument::REQUIRED, 'Entity class name. Example - XLite\\\\Model\\\\Product')
            ->addOption('module', 'M', InputOption::VALUE_REQUIRED, 'Put generated files in the module dir. Example - authorName\\\\moduleName')
            ->addOption('fields', 'f', InputOption::VALUE_REQUIRED, 'Comma-separated entity fields list. Example - value1,value2,value3.')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Url target for the form model page (snake_case). Default is the class short name (\XLite\Model\Product -> product)')

            ->addOption('rebuildAfterwards', 'R', InputOption::VALUE_NONE, 'Recalculate the view lists afterwards. This is required to see changes in the admin zone.')
        ;
    }

    protected function prepareOptions(InputInterface $input)
    {
        $entityClass = $input->getArgument('entity');
        $entityClass = preg_replace('/^\\\\/', '', $entityClass);
        $shortModelName = Utils::getClassShortName($entityClass);

        $target = $input->getOption('target')
            ?: str_replace('\\', '_', $shortModelName);
        $target = strtolower($target);
        $module = $input->getOption('module')
            ?: null;

        $rootNamespace = $module
            ? 'XLite\Module\\' . $module . '\\'
            : 'XLite\\';

        $rootPath = str_replace('/', LC_DS, $rootNamespace);

        $skinsRootPath = $module
            ? 'admin/' . str_replace('\\', '/', $module) . '/'
            : 'admin/';

        $fieldsRaw = $input->getOption('fields') ?: '';
        $fields = $fieldsRaw ? explode(',', $fieldsRaw) : [];

        $resultFields = [];

        foreach ($fields as $fieldName) {
            $fieldProcessed = [
                'name'      => $fieldName,
                'humanName' => Utils::convertCamelToHumanReadable($fieldName),
                'isEdit'    => true,
            ];

            $resultFields[] = $fieldProcessed;
        }


        return [
            'entityClass'       => $entityClass,
            'shortModelName'    => $shortModelName,
            'target'            => $target,
            'module'            => $module,
            'rootPath'          => $rootPath,
            'rootNamespace'     => $rootNamespace,
            'skinsRootPath'     => $skinsRootPath,
            'fields'            => $resultFields,
            'rebuildAfterwards' => $input->getOption('rebuildAfterwards'),
            'url'                  => \XLite\Core\URLManager::getShopURL(
                \XLite\Core\Converter::buildURL($target, '', [], \XLite::getAdminScript())
            ),
        ];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Scaffolding: FormModel');

        $config = $this->prepareOptions($input);

        list($path, $formModelClass) = $this->createFormModelView($config, $io);

        if ($path) {
            if (LC_DEVELOPER_MODE && !class_exists($formModelClass, true)) {
                $io->error("Failed to generate FormModel view $path");
            } else {
                $io->note($path .' FormModel view is generated');
            }
        }

        list($path, $dtoClass) = $this->createDTO($config, $io);

        if ($path) {
            if (LC_DEVELOPER_MODE && !class_exists($dtoClass, true)) {
                $io->error("Failed to generate DTO class $path");
            } else {
                $io->note($path .' DTO class is generated');
            }
        }

        list($path, $controllerClass) = $this->createController($config, $io, $dtoClass, $formModelClass);

        if ($path) {
            if (LC_DEVELOPER_MODE && !class_exists($controllerClass, true)) {
                $io->error("Failed to generate FormModel controller $path");
            } else {
                $io->note($path .' FormModel controller is generated');
            }
        }

        if ($config['rebuildAfterwards']) {
            $this->rebuildViewLists($output);
            \XLite\Core\Database::getCacheDriver()->deleteAll();
            \XLite::getInstance()
                ->getContainer()
                ->get('widget_cache')
                ->deleteAll();
        }

        $io->success('FormModel scaffolding complete');
        $io->text('<info>You can see the result</info>: ' . $config['url']);
    }

    /**
     * @param              $config
     * @param SymfonyStyle $io
     * @param              $dtoClass
     * @param              $formModelClass
     *
     * @return array
     */
    protected function createController($config, SymfonyStyle $io, $dtoClass, $formModelClass)
    {
        $controllerGenerator = new SourceCodeGenerators\ViewModel\Controller(
            $this->createBaseGenerator()
        );

        $name = Converter::convertToCamelCase($config['target']);
        $namespace = $config['rootNamespace'] . 'Controller\Admin';
        $fqn = '\\' . $namespace . '\\' . $name;

        if (class_exists($fqn, true) && !$io->confirm("Class $fqn already exists. Overwrite?")) {
            return [ false, $fqn ];
        }

        $content = $controllerGenerator->generate(
            $name,
            $namespace,
            $config['entityClass'],
            $dtoClass,
            $formModelClass
        );

        return [
            Utils::saveClass($fqn, $content),
            $fqn
        ];
    }

    /**
     * @param $config
     *
     * @return array
     */
    protected function createFormModelView($config, SymfonyStyle $io)
    {
        $controllerGenerator = new SourceCodeGenerators\ViewModel\ModelView(
            $this->createBaseGenerator()
        );

        $name = $config['shortModelName'] . 'ViewModel';
        $namespace = $config['rootNamespace'] . 'View\FormModel';
        $fqn = '\\' . $namespace . '\\' . $name;

        if (class_exists($fqn, true) && !$io->confirm("Class $fqn already exists. Overwrite?")) {
            return [ false, $fqn ];
        }

        $content = $controllerGenerator->generate(
            $name,
            $namespace,
            $config['entityClass'],
            $config['target'],
            $config['fields']
        );

        return [
            Utils::saveClass($fqn, $content),
            $fqn
        ];
    }

    /**
     * @param $config
     *
     * @return array
     */
    protected function createDTO($config, SymfonyStyle $io)
    {
        $generator = new SourceCodeGenerators\ViewModel\DTO(
            $this->createBaseGenerator()
        );

        $name = 'Common';
        $namespace =  $config['rootNamespace'] . 'Model\DTO\\' . $config['shortModelName'];
        $fqn = '\\' . $namespace . '\\' . $name;

        if (class_exists($fqn, true) && !$io->confirm("Class $fqn already exists. Overwrite?")) {
            return [ false, $fqn ];
        }

        $content = $generator->generate(
            $name,
            $namespace,
            $config['fields']
        );

        return [
            Utils::saveClass($fqn, $content),
            $fqn
        ];
    }

    /**
     * @param OutputInterface $output
     *
     * @return int|mixed
     */
    protected function rebuildViewLists(OutputInterface $output)
    {
        $command = $this->getApplication()->find('utils:rebuildViewLists');

        $arguments = [ 'command' => $command->getName() ];

        $input = new ArrayInput($arguments);
        return $command->run($input, $output);
    }

    /**
     * @return PhpClass
     */
    protected function createBaseGenerator()
    {
        return new PhpClass(
            new SourceCodeGenerators\Renderer\TwigRenderer()
        );
    }
}
