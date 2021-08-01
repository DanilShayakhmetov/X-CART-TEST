<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Repo\Subscription;

/**
 * Subscription plans repository
 */
class Plan extends \XLite\Model\Repo\ARepo
{
    const SEARCH_ACTIVE = 'active';

    /**
     * Current search condition
     *
     * @var \XLite\Core\CommonCell
     */
    protected $currentSearchCnd;

    /**
     * Default model alias
     *
     * @var string
     */
    protected $defaultAlias = 'sp';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndActive(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->where('sp.subscription != 0');
    }

}
