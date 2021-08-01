<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\InAppMarketplace;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

class AMarketplace extends Command
{
    /**
     * @var array
     */
    protected $modules;

    /**
     * @var array
     */
    protected $upgradeTypes = ['self', 'build', 'minor', 'major', 'core'];

    /**
     * @param SymfonyStyle $io
     * @param array        $rebuildState
     *
     * @return string
     */
    protected function doRebuild(SymfonyStyle $io, $rebuildState)
    {
        $progress = $io->createProgressBar($rebuildState['progressMax']);
        $progress->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
        $progress->setMessage('Start');
        $progress->start();

        do {
            $rebuildState = \XLite\Core\Marketplace::getInstance()->executeRebuild($rebuildState['id']);

            if ($rebuildState['state'] === 'aborted') {
                if (in_array($rebuildState['errorType'], ['rebuild-dialog', 'fs-errors-dialog'], true)) {
                    return $rebuildState['errorTitle'] . '(' . implode(', ', json_decode($rebuildState['errorData'], true)) . ')';
                }

                if (in_array($rebuildState['errorType'], ['file-modification-dialog', 'reload-page', 'note-post_upgrade', 'note-pre_upgrade'], true)) {
                    $rebuildState = \XLite\Core\Marketplace::getInstance()->executeRebuild($rebuildState['id'], 'release');
                } else {
                    $rebuildState = $rebuildState['errorType'];
                }
            }

            $progress->setProgress($rebuildState['progressValue']);
            $progress->setMaxSteps($rebuildState['progressMax']);
            $progress->setMessage(
                $rebuildState['currentStepInfo'][0]['translated'] ?? 'Finish'
            );
        } while ($rebuildState['state'] === 'in_progress');

        return '';
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    protected function getModulesList($filter = ''): array
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getQuery('marketplace_modules', $this->getCreteria($filter)),
            new \XLite\Core\Marketplace\Normalizer\MarketplaceModules()
        ) ?: [];
    }

    /**
     * @param string $moduleId
     *
     * @return array|null
     */
    protected function getModule($moduleId): ?array
    {
        if ($this->modules === null) {
            $this->modules = $this->getModulesList();
        }

        foreach ($this->modules as $module) {
            if ($module['id'] === $moduleId) {
                return $module;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getAvailableUpgradeTypes(): array
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getQuery('availableUpgradeTypes'),
            new \XLite\Core\Marketplace\Normalizer\Simple('availableUpgradeTypes')
        ) ?: [];
    }

    /**
     * @param string $type
     *
     * @return array
     */
    protected function getUpgradeList($type): array
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getRaw('upgradeList', null, [
                'type' => $type,
            ]),
            new \XLite\Core\Marketplace\Normalizer\Simple('upgradeList')
        ) ?: [];
    }

    /**
     * @param string $list
     *
     * @return array
     */
    protected function parseModulesList($list): array
    {
        return array_filter(explode(',', $list));
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    protected function getCreteria($filter = ''): array
    {
        switch ($filter) {
            case 'installed':
                return ['installed' => true];
            case 'enabled':
                return ['enabled' => 'enabled'];
            case 'disabled':
                return ['installed' => true, 'enabled' => 'disabled'];
            case 'marketplace':
                return ['installed' => false];
            case 'installable':
                return ['installed' => false, 'canInstall' => true];
            case 'unallowed':
                return ['licensed' => false];
            default:
                return [];
        }
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getEnableChangeUnit($moduleId): array
    {
        return [
            'id'     => $moduleId,
            'enable' => true,
        ];
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getDisableChangeUnit($moduleId): array
    {
        return [
            'id'     => $moduleId,
            'enable' => false,
        ];
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getInstallChangeUnit($moduleId): array
    {
        $module = $this->getModule($moduleId);

        return [
            'id'      => $moduleId,
            'install' => true,
            'enable'  => true,
            'version' => $module['version'] ?? '',
        ];
    }

    /**
     * @param string $moduleId
     * @param string $version
     *
     * @return array
     */
    protected function getUpgradeChangeUnit($moduleId, $version): array
    {
        return [
            'id'      => $moduleId,
            'upgrade' => true,
            'version' => $version,
        ];
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getRemoveChangeUnit($moduleId): array
    {
        $module = $this->getModule($moduleId);

        return [
            'id'     => $moduleId,
            'remove' => true,
        ];
    }

    /**
     * @param SymfonyStyle $io
     * @param string       $message
     *
     * @return int
     */
    protected function reportSuccess(SymfonyStyle $io, $message = 'Done'): int
    {
        $io->newLine(2);
        $io->block($message, 'OK', 'fg=green', ' ', true);

        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @param string       $message
     * @param int          $code
     *
     * @return int
     */
    protected function reportWarning(SymfonyStyle $io, $message = 'Error', $code = 1): int
    {
        $io->newLine(2);
        $io->block($message, 'WARNING', 'fg=red', ' ', true);

        return $code;
    }
}
