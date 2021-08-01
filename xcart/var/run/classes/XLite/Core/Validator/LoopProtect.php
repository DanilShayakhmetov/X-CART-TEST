<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

use XLite\Core\Database;
use XLite\Model\AEntity;
use XLite\Model\Repo\ARepo;

class LoopProtect extends AValidator
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var mixed
     */
    protected $entityId;

    /**
     * @var ARepo
     */
    private $repo;

    /**
     * @var array
     */
    private $chains = [];

    /**
     * LoopProtect constructor.
     *
     * @param string $fieldName parent field name
     * @param string $className entity class name
     * @param mixed $entityId current entity id
     */
    public function __construct(string $fieldName, string $className, $entityId)
    {
        parent::__construct();

        $this->fieldName = $fieldName;
        $this->className = $className;
        $this->entityId = $entityId;
        $this->repo = Database::getRepo($className);
    }

    /**
     * Validation
     *
     * @param mixed $data parent id
     * @return void
     * @throws Exception
     */
    public function validate($data)
    {
        $entity = $this->repo->find($data);
        $this->chains[] = $data;

        $this->findParents($entity);
    }

    /**
     * Recursive find all parents
     *
     * @param AEntity $entity
     * @throws Exception
     */
    protected function findParents(AEntity $entity)
    {
        /** @var AEntity $parent */
        $parent = $entity->{$this->fieldName} ?? null;

        if (!$parent) {
            return;
        }

        $parentId = $parent->getUniqueIdentifier();

        if (in_array($parentId, $this->chains, false)) {
            throw $this->throwError('Loop detected', [
                'fieldName' => $this->fieldName,
                'entityId' => $parentId
            ]);
        }

        $this->findParents($parent);
    }
}