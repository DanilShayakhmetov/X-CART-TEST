<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

class MarketplaceException extends \Exception
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param $action
     * @param $data
     *
     * @return MarketplaceException
     */
    public static function fromInvalidResponse($action, $data): MarketplaceException
    {
        $code    = $data['error'] ?? null;
        $message = $data['message'] ?? 'Invalid marketplace response';

        return (new static($message, $code))->setAction($action);
    }

    /**
     * @param string $request
     *
     * @return MarketplaceException
     */
    public static function fromEmptyResponse($request): MarketplaceException
    {
        return (new static('Empty response received', 0))->setData(['request' => $request]);
    }

    /**
     * @param string $request
     * @param string $message
     * @param mixed  $data
     *
     * @return MarketplaceException
     */
    public static function fromValidationError($request, $message, $data): MarketplaceException
    {
        return (new static('Validation data error', 1))->setData(
            [
                'request' => $request,
                'message' => $message,
                'data'    => $data,
            ]
        );
    }

    /**
     * @param string $request
     * @param string $message
     * @param mixed  $data
     *
     * @return MarketplaceException
     */
    public static function fromParsingError($request, $message, $data): MarketplaceException
    {
        return (new static('Parsing data error', 1))->setData(
            [
                'request' => $request,
                'message' => $message,
                'data'    => $data,
            ]
        );
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return MarketplaceException
     */
    public function setAction($action): MarketplaceException
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return MarketplaceException
     */
    public function setData(array $data): MarketplaceException
    {
        $this->data = $data;

        return $this;
    }
}
