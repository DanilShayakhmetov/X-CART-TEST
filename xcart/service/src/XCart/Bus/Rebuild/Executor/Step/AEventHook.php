<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step;

use DirectoryIterator;
use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Client\XCart;
use XCart\Bus\Client\XCartCLI;
use XCart\Bus\Helper\HookFilter;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\SilexAnnotations\Annotations\Service;

abstract class AEventHook extends AHook
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param Application     $app
     * @param HookFilter      $hookFilter
     * @param XCart           $client
     * @param XCartCLI        $cliClient
     * @param LoggerInterface $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        HookFilter $hookFilter,
        XCart $client,
        XCartCLI $cliClient,
        LoggerInterface $logger
    ) {
        return new static(
            $app['config']['root_dir'],
            $hookFilter,
            $client,
            $cliClient,
            $logger
        );
    }

    /**
     * @param string          $rootDir
     * @param HookFilter      $hookFilter
     * @param XCart           $client
     * @param XCartCLI        $cliClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        $rootDir,
        HookFilter $hookFilter,
        XCart $client,
        XCartCLI $cliClient,
        LoggerInterface $logger
    ) {
        parent::__construct($hookFilter, $client, $cliClient, $logger);

        $this->rootDir = $rootDir;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getTransitions(ScriptState $scriptState): array
    {
        $transitions = $this->getScriptTransitions($scriptState);

        return array_filter(array_map(
            function ($transition) {
                $hooks = $this->getHooksListByTransition($transition);
                if ($hooks) {
                    return [
                        'id'           => $transition['id'],
                        'remain_hooks' => $this->sortHooks($hooks),
                        'pack_dir'     => $this->rootDir,
                    ];
                }

                return [];
            },
            $this->filterScriptTransitions($transitions)
        ));
    }

    /**
     * @param array $transition
     *
     * @return array
     */
    protected function getHooksListByTransition(array $transition): array
    {
        if ($transition['id'] === 'CDev-Core') {
            return [];
        }

        $type = $this->getType();
        [$author, $name] = explode('-', $transition['id']);

        $directory = sprintf('classes/XLite/Module/%s/%s/hooks/', $author, $name);

        if (!is_dir($this->rootDir . $directory)) {
            return [];
        }

        $result = [];
        foreach (new DirectoryIterator($this->rootDir . $directory) as $file) {
            if ($file->isDir()) {
                continue;
            }

            if (preg_match('/' . preg_quote($type, '/') . '.*\.php$/', $file->getFilename())) {
                $result[] = $directory . $file;
            }
        }

        return $result;
    }

    /**
     * @param array $list
     *
     * @return mixed
     */
    protected function sortHooks(array $list): array
    {
        natsort($list);

        return $list;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getScriptTransitions(ScriptState $scriptState): array
    {
        return $scriptState->transitions;
    }
}
