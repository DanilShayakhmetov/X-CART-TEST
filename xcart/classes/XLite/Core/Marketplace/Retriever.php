<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace;

use Includes\Requirements;
use XLite\Core\GraphQL\ClientFactory;

/**
 * Retriever
 */
class Retriever extends \XLite\Base\Singleton
{
    /**
     * @var \XLite\Core\GraphQL\Client\AClient
     */
    private $client;

    /**
     * @param \XLite\Core\Marketplace\Query      $query
     * @param \XLite\Core\Marketplace\Normalizer $normalizer
     *
     * @return array|null
     */
    public function retrieve($query, Normalizer $normalizer)
    {
        try {
            if (!$this->checkLoopbackRequest()) {
                return [];
            }

            $client = static::getClient();

            $response = $client->query((string) $query, $query->getVariables());

            /* @var \XLite\Core\GraphQL\Response $response */
            if ($response->hasErrors()) {
                \XLite\Logger::getInstance()->log(
                    ' request errors:'
                    . PHP_EOL
                    . var_export($response->getErrors(), true),
                    LOG_ERR
                );
            }

            return $normalizer->normalize($response->getData());
        } catch (\XLite\Core\GraphQL\Exception\UnexpectedValue $e) {
            \XLite\Logger::getInstance()->log(
                $e->getMessage()
                . PHP_EOL
                . var_export($e->getErrors(), true),
                LOG_ERR
            );
        } catch (\XLite\Core\Exception $e) {
            \XLite\Logger::getInstance()->log(
                $e->getMessage(),
                LOG_ERR
            );
        }

        return null;
    }

    /**
     * @return \XLite\Core\GraphQL\Client\AClient
     */
    protected function getClient()
    {
        if (null === $this->client) {
            $this->client = ClientFactory::createWithBusAuth(
                $this->getBusUrl(),
                $this->getAuthUrl(),
                $this->getAuthCode()
            );
        }

        return $this->client;
    }

    protected function getBusUrl()
    {
        return \XLite::getInstance()->getShopURL('service.php?/api');
    }

    protected function getAuthUrl()
    {
        return \XLite::getInstance()->getShopURL('service.php?/auth');
    }

    protected function getAuthCode()
    {
        return \Includes\Utils\ConfigParser::getOptions(['installer_details', 'auth_code']);
    }

    protected function checkLoopbackRequest()
    {
        $requirementWidget = new \XLite\View\Requirement();
        $requirement       = $requirementWidget->getRequirementResult('loopback_request');

        if ($requirement['state'] !== Requirements::STATE_SUCCESS) {
            return false;
        }

        return true;
    }
}
