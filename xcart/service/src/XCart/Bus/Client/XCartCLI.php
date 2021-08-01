<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Client;

use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class XCartCLI implements XCartInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param Application $app
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app
    ) {
        return new self(
            $app['config']['root_dir']
        );
    }

    /**
     * @param string $rootDir
     */
    public function __construct(
        string $rootDir
    ) {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $name
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeRebuildStep($name, $rebuildId, $cacheId)
    {
        return $this->executeRebuildRequest(
            ['rebuildId' => $rebuildId, 'cacheId' => $cacheId],
            ['step_name' => $name]
        );
    }

    /**
     * @param string $file
     * @param array  $state
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeHook($file, $state, $rebuildId, $cacheId)
    {
        return $this->executeRebuildRequest(
            ['rebuildId' => $rebuildId, 'cacheId' => $cacheId],
            $state + ['file' => $file]
        );
    }

    /**
     * @param string $action
     * @param array  $params
     * @param array  $state
     * @param string $rebuildId
     * @param string $cacheId
     *
     * @return mixed|null
     */
    public function executeAction($action, $params, $rebuildId, $cacheId)
    {
        return $this->executeRebuildRequest(
            ['rebuildId' => $rebuildId, 'cacheId' => $cacheId],
            ['action' => $action, 'arg' => $params]
        );
    }

    /**
     * @param array $params
     * @param array $requestData
     *
     * @return mixed|null
     */
    public function executeRebuildRequest($params, array $requestData)
    {
        try {
            ob_start(null, null, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE | PHP_OUTPUT_HANDLER_FLUSHABLE);
            passthru(
                'echo "' . addslashes(json_encode($requestData)) . '" | /usr/bin/env php ' . $this->rootDir . 'rebuild.php --request="' . addslashes(json_encode($params)) . '"',
                $result
            );

            $result = ob_get_contents();
            ob_end_clean();

            return $result ? json_decode($result, true) : [];
        } catch (\Exception $e) {
        }
    }
}
