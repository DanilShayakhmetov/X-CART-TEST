<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\Other;

use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckRepoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('other:checkRepo')
            ->setDescription('Find model classes without repository class')
            ->setHelp('This command checks that there is a repository class for the corresponding model.'
                . 'Note: it can only check enabled module\'s models.'
                . 'Existing repository class is required to provide an ability to decorate the model repository.'
                . 'It can also perform i18n consistency check for a model-repo pair.')
            ->addOption('check-i18n', 'i', InputOption::VALUE_NONE, 'Perform i18n consistency check')
            ->addOption('create-missing', 'c', InputOption::VALUE_NONE, 'Create missing repositories')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \XLite\Core\Cache::getInstance()->getDriver()->deleteAll();

        $io = new SymfonyStyle($input, $output);

        $io->title('Checking for models without repos');

        $checki18n = $input->getOption('check-i18n');
        $createMode = $input->getOption('create-missing');

        $entities = $this->getAllEntities();

        $classesWithDefaultRepo = $this->checkDefaultRepositories($entities, $io);
        $classesWithIncorrectI18N = $checki18n
            ? $this->checki18nRepositories($entities, $io)
            : [];

        if ($createMode && $classesWithDefaultRepo) {
            $this->createRepositories($classesWithDefaultRepo, $io);
        }

        if ((!$classesWithDefaultRepo && !$classesWithIncorrectI18N) || $createMode) {
            $io->success('Finished');
        } else {
            $io->error('Incorrect entities found');
        }
    }

    protected function checkDefaultRepositories($entities, StyleInterface $io)
    {
        $classesWithDefaultRepo = array_filter($entities, [$this,'isDefaultRepo']);

        $io->section('Classes without a repository class');
        if ($classesWithDefaultRepo) {
            $io->listing($classesWithDefaultRepo);
        } else {
            $io->text('Everything is ok');
        }

        return $classesWithDefaultRepo;
    }

    protected function checki18nRepositories($entities, StyleInterface $io)
    {
        $classesWithIncorrectI18N = array_filter($entities, [$this,'isIncorrectI18N']);
        $io->section('Classes with inconsistent i18n state');
        if ($classesWithIncorrectI18N) {
            $io->listing($classesWithIncorrectI18N);
        } else {
            $io->text('Everything is ok');
        }

        return $classesWithIncorrectI18N;
    }

    protected function createRepositories($classesWithDefaultRepo, SymfonyStyle $io)
    {
        $io->section('Generating default repositories');

        $progress = $io->createProgressBar(count($classesWithDefaultRepo));
        $progress->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
        $progress->setMessage("Initiating...");
        $progress->start();

        foreach ($classesWithDefaultRepo as $class) {
            $progress->advance();
            $progress->setMessage($class);
            $this->createRepo($class);

            $repoClassName = \Includes\Utils\Converter::prepareClassName(str_replace('\Model\\', '\Model\Repo\\', $class), false);
            $repo = new $repoClassName(
                \XLite\Core\Database::getEM(),
                \XLite\Core\Database::getEM()->getClassMetadata($class)
            );
        }

        $progress->setMessage('Done');
        $progress->finish();
        $io->newLine(2);
    }

    protected function isDefaultRepo($entityClass) {
        $repo = \XLite\Core\Database::getRepo($entityClass);

        $class = get_class($repo);

        return $class === 'XLite\Model\Repo\Base\Common';
    }

    protected function isIncorrectI18N($entityClass) {
        $repo = \XLite\Core\Database::getRepo($entityClass);

        $relfClass = new \ReflectionClass($entityClass);
        $entityi18n = $relfClass->isSubclassOf('\XLite\Model\Base\I18n');
        $repoi18n = $repo instanceof \XLite\Model\Repo\Base\I18n;
        return ($entityi18n && !$repoi18n) || (!$entityi18n && $repoi18n);
    }

    /**
     * @return array
     */
    protected function getAllEntities()
    {
        $entities = array();
        $em =   \XLite\Core\Database::getEM();;
        $meta = $em->getMetadataFactory()->getAllMetadata();
        /** @var ClassMetadata $m */
        foreach ($meta as $m) {
            $class = $m->getName();
            $reflClass = new ReflectionClass($class);
            if (!$reflClass->isAbstract()) {
                $entities[] = $class;
            }
        }

        return $entities;
    }


    protected function createRepo($class) {
        $reflClass = new ReflectionClass($class);
        $className = $reflClass->getShortName();
        $fullNamespace = str_replace('\\Model\\', '\\Model\\Repo\\', $reflClass->getName());

        $content = $this->getRepoClassContent($className, str_replace('\\' . $className, '', $fullNamespace));
        $filePath = $this->getRepoClassPathByFQN($fullNamespace);
        $dirPath = dirname($filePath);
        if (!\Includes\Utils\FileManager::isDirWriteable($dirPath)) {
            \Includes\Utils\FileManager::mkdirRecursive($dirPath);
        }
        file_put_contents($filePath, $content);
    }

    protected function getRepoClassPathByFQN($fqn) {
        return LC_DIR_CLASSES . str_replace('\\', LC_DS, $fqn) . '.php';
    }

    protected function getRepoClassContent($className, $namespace) {
        $content = <<<CONTENT
<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace $namespace;

/**
 * $className repository
 */
class $className extends \XLite\Model\Repo\ARepo
{
}

CONTENT;

        return $content;
    }
}
