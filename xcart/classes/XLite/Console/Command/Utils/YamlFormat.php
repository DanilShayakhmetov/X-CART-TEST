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

class YamlFormat extends Command
{
    protected function configure()
    {
        $this
            ->setName('utils:yamlFormat')
            ->setDescription('Format & merge yaml files')
            ->setHelp('first argument - target file, rest args - merge files')
            ->addArgument('file', InputArgument::REQUIRED, 'File to process')
            ->addArgument('merge', InputArgument::IS_ARRAY, 'Files to be merged');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('file');
        $merge = $input->getArgument('merge');

        $this->validateFile($file, [
            'writeability' => true,
        ]);

        foreach ($merge as $mFile) {
            $this->validateFile($mFile);
        }

        $parser = new \Symfony\Component\Yaml\Parser();
        $content = file_get_contents($file);

        preg_match_all('#\n([a-z0-9\\\\]++)\s*:\s*\n#i', $content, $matches, PREG_OFFSET_CAPTURE);

        $parsed = $parser->parse($content, true);

        $types = array_keys($parsed);

        if (count($types) < 0) {
            throw new \RuntimeException("Cannot find types in file");
        }

        if (count($types) > 1) {
            $type = $io->choice('Select type', $types);
        } else {
            $type = reset($types);
            $io->text("Type \"$type\"");
        }

        $parsed = $parsed[$type];

        if ($merge) {
            $parsed = $this->processMerge($type, $merge, $parsed, $io);
        }

        $dumper = new \Symfony\Component\Yaml\Dumper();
        $dumper->setIndentation(2);

        $dump = preg_replace('#\-(\n)+[\s]*+#', '- ', $dumper->dump([
            $type => $parsed,
        ], 5));

        $pre = '';
        $after = '';

        $matches = $matches[1];

        foreach ($matches as $k => $match) {
            if ($match[0] === $type) {
                $pre = substr($content, 0, $match[1]);

                if (isset($matches[$k + 1])) {
                    $after = substr($content, $matches[$k + 1][1]);
                }

                break;
            }
        }

        file_put_contents($file, $pre . $dump . "\n" . $after);
    }

    /**
     * @param string       $type
     * @param array        $merge
     * @param array        $parsed
     * @param SymfonyStyle $io
     *
     * @return array
     */
    protected function processMerge($type, $merge, $parsed, SymfonyStyle $io)
    {
        $repo = \XLite\Core\Database::getRepo($type);

        if ($repo) {
            $identity = $this->tryToGetIdentityByRepo($repo, $io);
        }

        $row = end($parsed);

        if (!empty($identity) && !$this->checkIdentity($identity, $row)) {
            $io->warning(sprintf(
                'File does not contains following identity: %s, you need to specify it manually',
                implode(' + ', $identity)
            ));

            $identity = null;
        }

        if (empty($identity)) {
            $columns = array_keys(array_filter($row, function ($e) {
                    return !is_array($e);
                })) + [
                    's' => 'I need several columns',
                ];
            $identity = $io->choice('Select identity column', $columns);

            if ($identity === 's') {
                $i = $io->ask('How many?', null, function ($num) {
                    $num = (int)$num;
                    if ($num <= 0) {
                        throw new \RuntimeException('You must type a integer that is greater than zero.');
                    }

                    return $num;
                });

                $identity = [];

                while ($i--) {
                    $identity[] = $io->choice('Select identity column', $columns);
                }
            } else {
                $identity = [$identity];
            }
        }

        $parser = new \Symfony\Component\Yaml\Parser();

        foreach ($merge as $mergeFilePath) {
            if ($mergeContent = file_get_contents($mergeFilePath)) {
                $mergeParsed = $parser->parse($mergeContent);

                if (isset($mergeParsed[$type])) {
                    $mergeParsed = $mergeParsed[$type];

                    $parsed = $this->mergeData($identity, $parsed, $mergeParsed);
                }
            }
        }

        return $parsed;
    }

    /**
     * @param array $identity
     * @param array $parsed
     * @param array $mergeParsed
     *
     * @return array
     */
    protected function mergeData($identity, $parsed, $mergeParsed)
    {
        foreach ($mergeParsed as $mEntry) {
            foreach ($parsed as $k => $entry) {
                if (
                    isset(
                        $entry['translations'],
                        $mEntry['translations']
                    )
                    && $this->checkIdentity($identity, $entry)
                    && $this->checkIdentity($identity, $mEntry)
                    && $this->isIdentityEqual($identity, $entry, $mEntry)
                ) {
                    $translations = array_filter($mEntry['translations'], function ($e) use ($entry) {
                        return !in_array($e, $entry['translations']);
                    });

                    $result = array_merge(
                        $entry['translations'],
                        $translations
                    );

                    $parsed[$k]['translations'] = $result;
                }
            }
        }


        return $parsed;
    }

    /**
     * @param $repo
     * @param $io
     *
     * @return array
     */
    protected function tryToGetIdentityByRepo($repo, $io)
    {
        $reflection = new \ReflectionObject($repo);
        $prop = $reflection->getProperty('alternativeIdentifier');
        $prop->setAccessible(true);

        $identities = $prop->getValue($repo);

        if (!$identities) {
            $identity = [$repo->getPrimaryKeyField()];
        } elseif (count($identities) > 1) {
            $identity = $io->choice('Select desired identity', $identities);
        } else {
            $identity = reset($identities);
        }

        return $identity;
    }

    /**
     * @param $identity
     * @param $data
     *
     * @return bool
     */
    protected function checkIdentity($identity, $data)
    {
        foreach ($identity as $column) {
            if (!isset($data[$column])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $identity
     * @param $data
     *
     * @return bool
     */
    protected function isIdentityEqual($identity, $left, $right)
    {
        foreach ($identity as $column) {
            if (
                !isset($left[$column], $right[$column])
                || $left[$column] !== $right[$column]
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param       $path
     * @param array $options
     *
     * @return bool
     */
    protected function validateFile($path, array $options = [])
    {
        $return = !empty($options['return']);

        if (!file_exists($path)) {
            if ($return) {
                return false;
            }

            throw new \RuntimeException("File \"{$path}\" does not exists.");
        }

        if (!is_readable($path)) {
            if ($return) {
                return false;
            }

            throw new \RuntimeException("File \"{$path}\" is not readable.");
        }

        if (!empty($options['writeability']) && !is_writeable($path)) {
            if ($return) {
                return false;
            }

            throw new \RuntimeException("File \"{$path}\" is not writeable.");
        }

        return true;
    }
}
