<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\InAppMarketplace;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RegisterLicense extends AMarketplace
{
    protected function configure(): void
    {
        $this
            ->setName('marketplace:register-license')
            ->setDescription('Register license <info>[!] BETA</info>')
            ->setHelp('')
            ->addArgument('license', InputArgument::REQUIRED, 'License to register');
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

        $io->title('Register license <info>[!] BETA</info>');

        \XLite\Core\Marketplace::getInstance()->dropRebuild();

        $result = \XLite\Core\Marketplace::getInstance()->registerLicense($input->getArgument('license'));

        $resultMessage = 'Done';
        foreach ($result['alert'] ?? [] as $alert) {
            if ($alert['type'] === 'success') {
                $resultMessage = $alert['translated'];
            } else {
                return $this->reportWarning($io, $alert['translated']);
            }
        }

        if (!empty($result['action'])) {
            $id           = str_replace('rebuild/', '', $result['action']);
            $rebuildState = \XLite\Core\Marketplace::getInstance()->getRebuildState($id);

            $result = $this->doRebuild($io, $rebuildState);
            if ($result) {
                return $this->reportWarning($io, $result);
            }
        }

        return $this->reportSuccess($io, $resultMessage);
    }
}
