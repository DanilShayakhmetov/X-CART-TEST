<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\GenerateData;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XLite\Console\Command\GenerateData\Generators\Profile;
use XLite\Core\HTTP\Request;

/**
 * Class GenerateProfileCommand
 * @package XLite\Console\Command\GenerateData
 */
class GenerateProfileCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('generate:profile')
            ->setDescription('Generate customer/admin profiles')
            ->setHelp('Generates profile entities with some related properties')
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'Amount of profiles to generate (default value: 1)')

            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Profiles type: possible values are ' . implode(' / ', Profile::getAllowedTypes()))
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Save generated profiles to file');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Generating profiles');

        $inputData = [
            'count'     => $input->getOption('count') ?: 1,
            'type'      => $input->getOption('type') ?: 'customer',
            'file'      => $input->getOption('file') ? basename($input->getOption('file')): null
        ];

        $io->section('Entities to generate (count)');
        $io->table([ 'name', 'value' ], array_map(null, array_keys($inputData), array_values($inputData)));

        $profilesGenerated = 0;
        $defaultBatchSize = 20;

        $batchSize = $inputData['count'] < $defaultBatchSize
            ? $inputData['count']
            : $defaultBatchSize;

        $io->newLine();
        $io->writeln('<info>Generating profiles ... </info>');
        $progress = $io->createProgressBar($inputData['count']);

        $generator = new Profile($inputData['type']);
        do {
            $randomUsers = $this->getRandomUsers($batchSize);
            $usersArray = [];

            foreach ($randomUsers->results as $userData) {
                $generator->generate($userData);
                $usersArray[] = [$userData->email, 'guest'];
            }

            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Database::getEM()->clear();

            if ($inputData['file'] && count($usersArray) > 0) {
                if (!file_exists($inputData['file'])) {
                    array_unshift($usersArray, ['email', 'password']);
                }

                $handle = fopen($inputData['file'], 'a');
                foreach ($usersArray as $user) {
                    fputcsv($handle, $user);
                }
                fclose($handle);
            }

            $progress->advance($batchSize);
            $profilesGenerated += $batchSize;
        } while($profilesGenerated < $inputData['count']);

        $progress->finish();
        $io->newLine(2);
        $io->success('Finished');
    }

    /**
     * @param int $count
     *
     * @return \stdClass
     */
    protected function getRandomUsers($count = 1)
    {
        $url = 'https://api.randomuser.me/?results=' . $count . '&nat=au,br,ca,de,fi,fb,nz,us';
        $request = new Request($url);
        $request->verb = 'GET';
        $request->body = '';
        $request->setHeader('Content-Type', 'application/json');

        $response = $request->sendRequest();
        return json_decode($response->body);
    }
}