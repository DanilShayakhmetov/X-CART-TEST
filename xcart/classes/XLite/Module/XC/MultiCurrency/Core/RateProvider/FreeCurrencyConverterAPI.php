<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core\RateProvider;

use XLite\Module\XC\MultiCurrency\Main;

/**
 * Currency converter (https://www.currencyconverterapi.com/)
 */
class FreeCurrencyConverterAPI extends \XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider
{
    /**
     * URL to post request for rate
     *
     * @var string
     */
    protected $url = 'https://free.currencyconverterapi.com/api/v6/';

    /**
     * Get currency conversion rate
     *
     * @param string $from Source currency code (alpha-3)
     * @param string $to   Destination currency code (alpha-3)
     *
     * @return float
     */
    public function getRate($from, $to)
    {
        $result = null;

        $response = $this->sendRequest([
            'q'       => $from . '_' . $to,
            'compact' => 'ultra',
            'apiKey'  => $this->getApiKey(),
        ]);

        if ($response !== null) {
            $rate = $this->parseResponse($from, $to, $response);

            if ($rate) {
                $result = (float) $rate;
            }
        }

        return $result;
    }

    protected function getApiKey()
    {
        return \XLite\Core\Config::getInstance()
            ->XC
            ->MultiCurrency
            ->currency_converter_api_key;
    }

    /**
     * @param array $data
     *
     * @return null|string
     */
    protected function sendRequest(array $data)
    {
        $request = new \XLite\Core\HTTP\Request($this->url . 'convert?' . http_build_query($data, null, '&'));

        $request->verb = 'GET';

        $response = $request->sendRequest();

        if ($response && $response->code === 200 && !empty($response->body)) {

            if (\Includes\Utils\ConfigParser::getOptions(['log_details', 'level']) >= LOG_DEBUG) {
                \XLite\Module\XC\MultiCurrency\Main::log('Response received', $response->body);
            }

            return $response->body;
        }

        \XLite\Module\XC\MultiCurrency\Main::log('Wrong response received', $response);

        return null;
    }

    /**
     * Parse server response
     *
     * @param string $from     Source currency code (alpha-3)
     * @param string $to       Destination currency code (alpha-3)
     * @param string $response Server response
     *
     * @return string
     */
    protected function parseResponse($from, $to, $response)
    {
        $q        = $from . '_' . $to;
        $response = @json_decode($response, true);

        return isset($response[$q]) ? $response[$q] : null;
    }
}
