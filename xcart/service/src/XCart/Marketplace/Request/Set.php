<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IRequest;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Callback;

class Set extends AAPIRequest
{
    /**
     * @var IRequest[]
     */
    private $subRequests = [];

    /**
     * @var string[]
     */
    private $unsupportedRequests = [
        Constant::REQUEST_ADDON_HASH_BATCH,
        Constant::REQUEST_ADDON_HASH,
        Constant::REQUEST_ADDON_INFO,
        Constant::REQUEST_ADDON_PACK,
        Constant::REQUEST_CORE_HASH,
        Constant::REQUEST_CORE_PACK,
        Constant::REQUEST_OUTDATED_MODULE,
        Constant::REQUEST_PAYMENT_METHODS,
        Constant::REQUEST_GDPR_MODULES,
        Constant::REQUEST_RESEND_KEY,
        Constant::REQUEST_SET,
        Constant::REQUEST_SET_KEY_WAVE,
        Constant::REQUEST_SHIPPING_METHODS,
        Constant::REQUEST_TEST,
    ];

    /**
     * @param array $params
     *
     * @throws RequestException
     */
    public function __construct(array $params = [])
    {
        $requestParams = [];

        foreach ($params as $requestName => $requestData) {
            if ($this->isRequestSupported($requestName)) {
                $request = ARequest::getRequest($requestName, $requestData);

                $this->subRequests[$requestName] = $request;

                $requestParams[$request->getAction()] = $request->getParams();
            }
        }

        parent::__construct([Constant::FIELD_QUERIES => $requestParams]);
    }

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_DATASET;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Callback(function ($data) {
            return true;
        });
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        $result = [];
        foreach ((array) $data as $action => $actionData) {
            list($requestName, $request) = $this->getSubRequestByAction($action);

            if ($request) {
                $result[$requestName] = $request->formatData($actionData);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [Constant::FIELD_QUERIES => []];
    }

    /**
     * @param string $requestName
     *
     * @return bool
     */
    private function isRequestSupported($requestName): bool
    {
        return !in_array($requestName, $this->unsupportedRequests, true);
    }

    /**
     * @param string $action
     *
     * @return array
     */
    private function getSubRequestByAction($action): array
    {
        foreach ($this->subRequests as $name => $request) {
            if ($request->getAction() === $action) {
                return [$name, $request];
            }
        }

        return ['', null];
    }
}
