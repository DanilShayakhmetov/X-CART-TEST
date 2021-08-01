<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ConsistencyCheck;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;

/**
 * Class Result
 */
class Result extends \XLite\View\AView
{
    const PARAM_GROUPS = 'groups';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = array(
            'file'  => 'consistency_check/result.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_GROUPS   => new \XLite\Model\WidgetParam\TypeCollection('Groups', []),
        );
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return 'consistency_check';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/result.twig'; 
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        $groups = $this->getParam(static::PARAM_GROUPS);

        $processed = [];

        foreach ($groups as $groupName => $inconsistenciesData) {
            $processedGroup = [];

            /** @var Inconsistency $inconsistency */
            foreach ($inconsistenciesData['list'] as $name => $inconsistency) {

                $inconsistencyResult = [
                    'name' => $inconsistency->getMessage()
                ];

                if ($inconsistency instanceof InconsistencyEntities) {
                    $inconsistencyResult['list'] = $inconsistency->getEntities();
                }

                $processedGroup[$name] = $inconsistencyResult;
            }

            if ($processedGroup) {
                $processed[$groupName] = [
                    'name' => $inconsistenciesData['name'],
                    'list' => $processedGroup
                ];
            }
        }

        return $processed;
    }
}
