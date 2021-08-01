<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request;

class MailChimpBatchRequest extends MailChimpRequest
{
    /**
     * @var array|MailChimpRequest[]
     */
    protected $operations;

    /**
     * @param MailChimpRequest[] $operations
     */
    public function __construct($operations = [])
    {
        parent::__construct('Batch operation', 'post', 'batches');

        $this->operations = $operations;
    }

    /**
     * @param MailChimpRequest[] $operations
     *
     * @return self
     */
    public static function getRequest($operations = []): self
    {
        return new self($operations);
    }

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        $this->setArgs([
            'operations' => array_map(static function ($item) {
                /** @var MailChimpRequest $item */
                return $item->getOperation();
            }, $this->getOperations()),
        ]);

        return parent::execute();
    }

    /**
     * @param MailChimpRequest $operation
     */
    public function addOperation($operation)
    {
        $this->operations[] = $operation;
    }

    /**
     * @return array|MailChimpRequest[]
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * @param array|MailChimpRequest[] $operations
     */
    public function setOperations($operations): void
    {
        $this->operations = $operations;
    }
}
