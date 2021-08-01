<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild;

use Includes\Utils\FileManager;

/**
 * Class OldSystemAdapter
 *
 * N.B. This class is going to be removed in later iterations of XCN-8332
 */
class OldSystemAdapter
{
    private $executors;

    /**
     * @var array
     */
    protected $request;

    /**
     *
     */
    public function processRequest()
    {
        header('Content-type: application/json');

        if ($this->isAllowedToExecuteRequest()) {
            ob_start(null, null, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE | PHP_OUTPUT_HANDLER_FLUSHABLE);
            try {
                $data = $this->readPayload();
                $executor = $this->getExecutor($data);
                $response = $executor->execute($data, $this->getRebuildId());
                $response['info'] = ob_get_contents();
            } catch (\Throwable $e) {
                \Includes\ErrorHandler::logException($e);

                $response = [
                    'errors' => [
                        $e->getMessage(),
                    ],
                ];
            }

            ob_end_clean();

            $this->echoJsonData($response);
        } else {
            $this->echoJsonData([
                'errors' => [
                    'Rebuild not started',
                ],
            ]);
        }
    }

    /**
     * @return Executor\IRebuildExecutor[]
     */
    protected function getExecutors()
    {
        if (is_null($this->executors)) {
            $this->executors = [
                new Executor\HookExecutor(),
                new Executor\ActionExecutor(),
                new Executor\StartRebuildExecutor(),
                new Executor\StepExecutor(),
            ];
        }

        return $this->executors;
    }

    /**
     * @param $payloadData
     *
     * @return Executor\IRebuildExecutor
     * @throws \Exception
     */
    protected function getExecutor($payloadData)
    {
        foreach ($this->getExecutors() as $executor) {
            if ($executor->isApplicable($payloadData)) {
                return $executor;
            }
        }

        throw new \Exception('Rebuild executor not found');
    }

    /**
     * @return mixed
     */
    protected function readPayload()
    {
        $rawData = '';
        $s = fopen(PHP_SAPI === 'cli' ? 'php://stdin' : 'php://input', 'rb');
        while ($kb = fread($s, 1024)) {
            $rawData .= $kb;
        }
        fclose($s);

        return json_decode($rawData, true);
    }

    /**
     * @param $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return bool
     */
    protected function isAllowedToExecuteRequest()
    {
        return FileManager::isExists(LC_DIR_VAR . '.rebuild.' . $this->getRebuildId());
    }

    /**
     * @return mixed
     */
    protected function getRebuildId()
    {
        return $this->request['rebuildId'];
    }

    /**
     * @param array $data
     */
    protected function echoJsonData(array $data)
    {
        $json = json_encode($data);

        echo($json);
    }
}
