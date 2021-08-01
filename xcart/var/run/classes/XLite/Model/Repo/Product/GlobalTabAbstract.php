<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Product;

/**
 *  Api\Operation\Create(modelClass="XLite\Model\Product\GlobalTab", summary="Add global tab")
 *  Api\Operation\Read(modelClass="XLite\Model\Product\GlobalTab", summary="Retrieve global tab by id")
 *  Api\Operation\ReadAll(modelClass="XLite\Model\Product\GlobalTab", summary="Retrieve global tabs by conditions")
 *  Api\Operation\Update(modelClass="XLite\Model\Product\GlobalTab", summary="Update global tab by id")
 *  Api\Operation\Delete(modelClass="XLite\Model\Product\GlobalTab", summary="Delete global tab by id")
 *
 *  SWG\Tag(
 *   name="Product\GlobalTab",
 *   description="This repo stores user-created global product tabs.",
 * )
 */
abstract class GlobalTabAbstract extends \XLite\Model\Repo\ARepo
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('service_name'),
    );

    /**
     * Load raw fixture
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param array                $record  Record
     * @param array                $regular Regular fields info OPTIONAL
     * @param array                $assocs  Associations info OPTIONAL
     *
     * @return void
     */
    public function loadRawFixture(
        \XLite\Model\AEntity $entity,
        array $record,
        array $regular = array(),
        array $assocs = array()
    ) {
        $providerCode = \XLite\Core\Database::getInstance()->getFixturesLoadingOption('moduleName')
            ?: \XLite\Model\Product\GlobalTabProvider::PROVIDER_CORE;

        /** @var \XLite\Model\Product\GlobalTab $entity */
        if (!$entity->getProviderByCode($providerCode)) {
            /** @var \XLite\Model\Product\GlobalTabProvider $provider */
            $provider = new \XLite\Model\Product\GlobalTabProvider;
            $provider->setTab($entity);
            $provider->setCode($providerCode);
            $entity->addProvider($provider);
        }

        parent::loadRawFixture($entity, $record, $regular, $assocs);
    }
}