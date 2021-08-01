<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        if ($query) {
            $processedQuery = [];
            foreach ($query as $name => $value) {
                $processedQuery[preg_replace('/.*\?/', '', $name)] = $value;
            }

            $query = $processedQuery;
        }

        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    protected function prepareRequestUri()
    {
        $requestUri = preg_replace(
            ['/' . preg_quote('?/', '/') . '/', '/\?%2F/', '/([^\?]?)&/'],
            ['/', '/', '$1?'],
            parent::prepareRequestUri(),
            1
        );

        $this->server->set('REQUEST_URI', $requestUri);

        return $requestUri;
    }
}
