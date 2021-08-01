<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Core\Task;
use XLite\Core\Database;


/**
 * GenerateProductFeed
 */
class GenerateProductFeed extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Generate product feed');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        $generator = \XLite\Module\XC\FacebookMarketing\Logic\Task\ProductFeedGenerator::getInstance();
        $generator->setPeriodicTaskModel($this);
        $generator->generate();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return static::getRenewalPeriod();
    }

    /**
     * Return renewal period
     *
     * @return int
     */
    public static function getRenewalPeriod()
    {
        $period = \XLite\Core\Config::getInstance()->XC->FacebookMarketing->renewal_frequency;

        return in_array($period, static::getAllowedPeriods())
            ? $period
            : static::INT_1_DAY;
    }

    /**
     * Set renewal period
     *
     * @param int $period
     */
    public static function setRenewalPeriod($period)
    {
        $period = in_array($period, static::getAllowedPeriods()) ? $period : static::INT_1_DAY;

        /** @var \XLite\Model\Task $task */
        $task = Database::getRepo('XLite\Model\Task')->findOneBy(
            ['owner' => 'XLite\Module\XC\FacebookMarketing\Core\Task\GenerateProductFeed']
        );

        if ($task && $task->getTriggerTime()) {
            $time = $task->getTriggerTime() - static::getRenewalPeriod();
            $task->setTriggerTime($time + $period);
        }

        Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'XC\FacebookMarketing',
            'name'     => 'renewal_frequency',
            'value'    => $period,
        ]);
    }

    /**
     * @return array
     */
    public static function getAllowedPeriods()
    {
        return [
            static::INT_1_HOUR,
            static::INT_1_DAY,
            static::INT_1_WEEK,
        ];
    }

    /**
     * Merge task model entity
     */
    public function mergeModel()
    {
        if (isset($this->model) && $this->model instanceof \XLite\Model\AEntity) {
            $this->model = \XLite\Core\Database::getEM()->merge($this->model);
        }
    }
}