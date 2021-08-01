<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema;

/**
 * Complex schema compatibility trait
 */
trait ComplexCompatTrait
{
    /**
     * @return string
     */
    public function getModelName()
    {
        $name = '';
        $schemaClass = get_class($this->modelSchema);

        switch ($schemaClass) {
            case 'XLite\Module\XC\RESTAPI\Core\Schema\Complex\Order':
                $name = 'Order';
                break;
            case 'XLite\Module\XC\RESTAPI\Core\Schema\Complex\Product':
                $name = 'Product';
                break;
            case 'XLite\Module\XC\RESTAPI\Core\Schema\Complex\Profile':
                $name = 'Profile';
                break;
        }

        return $name;
    }

    // {{{ converMpdel block

    protected function convertModelOrder(\XLite\Model\Order $model = null, $withAssociations)
    {
        return $this->doConvertModel($model, $withAssociations);
    }

    protected function convertModelProduct(\XLite\Model\Product $model = null, $withAssociations)
    {
        return $this->doConvertModel($model, $withAssociations);
    }

    protected function convertModelProfile(\XLite\Model\Profile $model = null, $withAssociations)
    {
        return $this->doConvertModel($model, $withAssociations);
    }

    // converMpdel block }}}

    // {{{ prepareInput block

    protected function prepareInputProduct(array $data)
    {
        return $this->doPrepareInput($data);
    }

    protected function prepareInputProfile(array $data)
    {
        return $this->doPrepareInput($data);
    }

    protected function prepareInputOrder(array $data)
    {
        return $this->doPrepareInput($data);
    }

    // prepareInput block }}}

    // {{{ preloadData block

    protected function preloadProductData(\XLite\Model\AEntity $entity, array $data)
    {
        return $this->doLoadData($entity, $data);
    }

    protected function preloadProfileData(\XLite\Model\AEntity $entity, array $data)
    {
        return $this->doLoadData($entity, $data);
    }

    protected function preloadOrderData(\XLite\Model\AEntity $entity, array $data)
    {
        return $this->doLoadData($entity, $data);
    }

    // preloadData block }}}
}
