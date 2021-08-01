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
use XLite\Console\Command\Exception\ConfigurationError;
use XLite\Console\Command\Helpers;
use XLite\Console\Command\SourceCodeGenerators;
use XLite\Console\Command\SourceCodeGenerators\PhpClass;
use XLite\Console\Command\SourceCodeGenerators\Template;
use XLite\Console\Command\SourceCodeGenerators\Utils;
use XLite\Core\Converter;

class Page extends Command
{
    use Helpers\ModuleTrait;
    use Helpers\ControllerTrait;

    protected function configure()
    {
        $this
            ->setName('scaffolding:page')
            ->setDescription('Generate a page (in admin or customer zone)')
            ->setHelp('')
            ->addOption('module', 'm', InputOption::VALUE_REQUIRED, 'Put generated files in the module dir. Example - authorName\\\\moduleName')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Page target')
            ->addOption('interface', 'i', InputOption::VALUE_REQUIRED, 'Interface zone. Allowed values: admin, customer')
            ->addOption('menu', 'M', InputOption::VALUE_REQUIRED, 'Create link in the left menu. Works only if the "module" option is provided. Examples: admin area - "main.users", "bottom.store_setup", customer area - "{my account}", "my_awesome_page');
    }

    protected function prepareOptions(InputInterface $input, SymfonyStyle $io)
    {
        $zone = $input->getOption('interface');

        if (!$zone) {
            throw new ConfigurationError('Provide --interface option');
        }

        $target = $input->getOption('target');
        if (!$target) {
            throw new ConfigurationError('Provide --target option');
        }

        $target = strtolower($target);
        $zones = $this->isControllerExistsInZones($target);

        if ($zones[ucfirst($zone)] === true
            && !$io->confirm("Target '$target' is already in use, found in $zone zone. Overwrite?", false)
        ) {
            throw new ConfigurationError(
                "Target '$target' is already in use, found in $zone zone. Try another target"
            );
        }

        $module = $input->getOption('module')
            ?: null;

        if ($module) {
            try {
                $state = $this->getModuleStateByName($module);

            } catch(\Exception $e) {
                throw new ConfigurationError($e->getMessage());
            }

            if ($state === Helpers\Module::NOT_FOUND || $state ===  Helpers\Module::NOT_INSTALLED) {
                throw new ConfigurationError($this->getMessageByState($state, $module));
            }
        }

        $rootNamespace = $module
            ? 'XLite\Module\\' . $module . '\\'
            : 'XLite\\';

        $rootPath = str_replace('/', LC_DS, $rootNamespace);

        $skinsRootPath = $module
            ? $zone . '/modules/' . str_replace('\\', '/', $module) . '/'
            : $zone . '/';

        $templatePath = 'page/' . $target . '/body.twig';
        $templatePathForViews = $templatePath;
        if ($module) {
            $templatePathForViews = 'modules/' . str_replace('\\', '/', $module) . '/' . $templatePath;
        }

        $leftMenuPath = $input->getOption('menu');

        if ($leftMenuPath === 'none') {
            $leftMenuPath = $io->ask('Left menu path (e.g. "main.users", "bottom.look")', null);
        }

        if ($leftMenuPath
            && $zone === 'admin'
            && substr_count($leftMenuPath, '.') + 1 !== 2
        ) {
            throw new ConfigurationError(
                'Left menu path has wrong format: ' . $leftMenuPath
            );
        }

        if ($leftMenuPath && !$module) {
            $io->warning("Can't generate the left menu link because the items list files are generated in the core.");
            $leftMenuPath = null;
        }

        return [
            'target'               => $target,
            'targetReadable'       => Converter::convertToCamelCase($target),
            'module'               => $module,
            'zone'                 => $zone,
            'rootPath'             => $rootPath,
            'rootNamespace'        => $rootNamespace,
            'skinsRootPath'        => $skinsRootPath,
            'leftMenuPath'         => $leftMenuPath,
            'templatePath'         => $templatePath,
            'templatePathForViews' => $templatePathForViews,
            'url'                  => \XLite\Core\URLManager::getShopURL(
                \XLite\Core\Converter::buildURL(
                    $target, '', [],
                    $zone === 'admin'
                        ? \XLite::getAdminScript()
                        : \XLite::getCustomerScript()
                    )
            ),
        ];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Scaffolding: Page');

        try {
            $config = $this->prepareOptions($input, $io);
        } catch (ConfigurationError $e) {
            $io->error($e->getMessage());
            return;
        }

        list($path, $controllerClass) = $this->createController($config, $io);

        if ($path) {
            if (LC_DEVELOPER_MODE && class_exists($controllerClass, true)) {
                new $controllerClass();
            }
            $io->note($path .' controller is generated');
        }

        list($path, $viewClass) = $this->createView($config, $io);

        if ($path) {
            if (LC_DEVELOPER_MODE && class_exists($viewClass, true)) {
                new $viewClass();
            }
            $io->note($path .' view is generated');
        }

        list($path,) = $this->createTemplate($config, $io);

        if ($path) {
            $io->note($path .' template is generated');
        }

        list($path, $menuClass) = $this->createLeftMenuClass($config, $io);

        if ($path) {
            if (LC_DEVELOPER_MODE && class_exists($menuClass, true)) {
                new $menuClass();
            }
            $io->note($path .' menu class is generated');
        }

        $this->rebuildViewLists($output);

        $io->success('Page scaffolding complete');
        $io->text('<info>You can see the result</info>: ' . $config['url']);
    }

    /**
     * @param array        $config
     * @param SymfonyStyle $io
     *
     * @return array
     */
    protected function createController(array $config, SymfonyStyle $io)
    {
        $controllerGenerator = new SourceCodeGenerators\Page\Controller(
            $this->createBaseGenerator()
        );

        $name = Converter::convertToCamelCase($config['target']);
        $namespace =  $config['rootNamespace'] . 'Controller\\' . ucfirst($config['zone']);
        $fqn = '\\' . $namespace . '\\' . $name;

        if (class_exists($fqn, true) && !$io->confirm("Class $fqn already exists. Overwrite?")) {
            return [ false, $fqn ];
        }

        $content = $controllerGenerator->generate(
            $name,
            $namespace,
            $config['zone']
        );

        return [
            Utils::saveClass($fqn, $content),
            $fqn
        ];
    }

    /**
     * @param array        $config
     * @param SymfonyStyle $io
     *
     * @return array
     */
    protected function createView(array $config, SymfonyStyle $io)
    {
        $controllerGenerator = new SourceCodeGenerators\Page\View(
            $this->createBaseGenerator()
        );

        $name = ucfirst($config['targetReadable']) . 'Page';
        $namespace =  $config['rootNamespace'] . 'View\Page\\' . ucfirst($config['zone']);
        $fqn = '\\' . $namespace . '\\' . $name;

        if (class_exists($fqn, true) && !$io->confirm("Class $fqn already exists. Overwrite?")) {
            return [ false, $fqn ];
        }

        $content = $controllerGenerator->generate(
            $name,
            $namespace,
            $config['zone'],
            $config['target'],
            $config['templatePathForViews']
        );

        return [
            Utils::saveClass($fqn, $content),
            $fqn
        ];
    }

    /**
     * @param array        $config
     * @param SymfonyStyle $io
     *
     * @return array
     */
    protected function createTemplate(array $config, SymfonyStyle $io)
    {
        $generator = new SourceCodeGenerators\Page\Template(
            $this->createBaseTemplateGenerator()
        );

        $content = $generator->generate('body.twig');

        $path = $config['skinsRootPath'] . $config['templatePath'];

        if (file_exists($path) && !$io->confirm("Template $path already exists. Overwrite?")) {
            return [ false, $path ];
        }

        return [
            Utils::saveSkinsFileByPath($path, $content),
            $path
        ];
    }

    /**
     * @param array        $config
     * @param SymfonyStyle $io
     *
     * @return array
     */
    protected function createLeftMenuClass(array $config, SymfonyStyle $io)
    {
        $isAdminArea = $config['zone'] === 'admin';

        if ($isAdminArea) {
            $generator = new SourceCodeGenerators\Menu\LeftMenuDecorator(
                $this->createBaseGenerator()
            );
        } else {
            $generator = new SourceCodeGenerators\Menu\TopMenuDecorator(
                $this->createBaseGenerator()
            );
        }

        $name = Converter::convertToCamelCase($config['target']) . 'TopMenu';
        $namespace = $isAdminArea
            ? $config['rootNamespace'] . 'View\Menu\Admin'
            : $config['rootNamespace'] . 'View\Menu\Customer';
        $fqn = '\\' . $namespace . '\\' . $name;

        if (class_exists($fqn, true) && !$io->confirm("Class $fqn already exists. Overwrite?")) {
            return [ false, $fqn ];
        }

        $content = $generator->generate(
            $name,
            $namespace,
            $config['targetReadable'],
            $config['leftMenuPath'],
            $config['target']
        );

        return [
            Utils::saveClass($fqn, $content),
            $fqn
        ];
    }

    /**
     * @param OutputInterface $output
     */
    protected function rebuildViewLists(OutputInterface $output)
    {
        $command = $this->getApplication()->find('utils:rebuildViewLists');

        $arguments = [ 'command' => $command->getName() ];

        $input = new ArrayInput($arguments);
        $command->run($input, $output);
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

    /**
     * @return Template
     */
    protected function createBaseTemplateGenerator()
    {
        return new Template(
            new SourceCodeGenerators\Renderer\TwigRenderer()
        );
    }
}
