<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\ConsistencyCheck\Director;
use XLite\Core\ConsistencyCheck\Retriever;

/**
 * Class IntegrityCheck
 */
class ConsistencyCheck extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Consistency check');
    }

    /**
     * @return array
     * @throws \LogicException
     */
    public function retrieveInconsistencies()
    {
        $director = new Director();
        $retrievers = $director->getRetrievers();

        $result = [];

        /** @var Retriever $retriever */
        foreach ($retrievers as $name => $retrieverData) {
            if (!$retrieverData['retriever'] instanceof Retriever) {
                throw new \LogicException('Retriever of invalid class');
            }

            $result[$name] = [
                'name' => $retrieverData['name'],
                'list' => $retrieverData['retriever']->getInconsistencies()
            ];
        }

        return $this->postprocessGroups($result);
    }

    /**
     * @return array
     */
    public function getInconsistencies()
    {
        return \XLite\Core\TmpVars::getInstance()->inconsistency_check_results
            ?: [];
    }

    /**
     * @return boolean
     */
    public function hasInconsistencies()
    {
        return \XLite\Core\TmpVars::getInstance()->inconsistency_check_results !== null;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function postprocessGroups(array $result)
    {
        return $result;
    }

    public function doActionStart()
    {
        \XLite\Core\TmpVars::getInstance()->inconsistency_check_results = null;

        $inconsistencies = $this->retrieveInconsistencies();

        \XLite\Core\TmpVars::getInstance()->inconsistency_check_results = $inconsistencies;

        $this->setReturnURL($this->buildURL('consistency_check'));
    }
}
