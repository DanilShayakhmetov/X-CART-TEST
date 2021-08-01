<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * Theme tweaker templates repository
 */
class Template extends \XLite\Model\Repo\ARepo
{
    /**
     * Disables the selected templates by ids
     *
     * @param array $templateIds
     * @return void
     */
    public function disableTemplates(array $templateIds)
    {
        if (count($templateIds) === 0) {
            return;
        }

        $this->defineDisableTemplatesQuery($templateIds)->execute();
    }

    /**
     * @param $path
     */
    public function deleteByPath($path)
    {
        $this->createPureQueryBuilder('t')
            ->delete()
            ->where('t.template LIKE :path')
            ->setParameter('path', $path)
            ->execute();
    }

    /**
     * @param array $paths
     * @return QueryBuilder|AQueryBuilder
     */
    protected function defineDisableTemplatesQuery(array $paths)
    {
        return $this->createPureQueryBuilder()
            ->update($this->_entityName, 't')
            ->set('t.enabled', 0)
            ->andWhere('t.template IN (:paths)')
            ->setParameter('paths', $paths);
    }
}
