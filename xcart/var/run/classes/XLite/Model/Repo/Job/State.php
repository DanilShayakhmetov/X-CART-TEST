<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Job;


class State extends \XLite\Model\Repo\ARepo
{
    /**
     * @return \XLite\Model\Job\State[]
     */
    public function getNotFinishedJobs()
    {
        $qb = $this->createQueryBuilder('stt');

        $qb->andWhere('stt.finished = true AND stt.progress < 100');

        return $qb->getResult();
    }
}