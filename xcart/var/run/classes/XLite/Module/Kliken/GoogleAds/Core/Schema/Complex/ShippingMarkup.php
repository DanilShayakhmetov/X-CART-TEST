<?php

namespace XLite\Module\Kliken\GoogleAds\Core\Schema\Complex;

class ShippingMarkup implements \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
{
    /**
     * Convert model
     *
     * @param \XLite\Model\AEntity $model            Entity
     * @param boolean              $withAssociations Convert with associations
     *
     * @return array
     */
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations)
    {
        $language = \XLite\Core\Config::getInstance()->General->default_language;
        $translation = $model->getShippingMethod()->getSoftTranslation($language);

        return [
            'markup_id'         => $model->getMarkupId(),
            'min_weight'        => $model->getMinWeight(),
            'max_weight'        => $model->getMaxWeight(),
            'min_total'         => $model->getMinTotal(),
            'max_total'         => $model->getMaxTotal(),
            'min_items'         => $model->getMinItems(),
            'max_items'         => $model->getMaxItems(),
            'markup_flat'       => $model->getMarkupFlat(),
            'markup_percent'    => $model->getMarkupPercent(),
            'markup_per_item'   => $model->getMarkupPerItem(),
            'markup_per_weight' => $model->getMarkupPerWeight(),
            'shipping_method'   => [
                'method_id'       => $model->getShippingMethod()->getMethodId(),
                'method_name'     => $translation->getName(),
                'processor'       => $model->getShippingMethod()->getProcessor(),
                'carrier'         => $model->getShippingMethod()->getCarrier(),
                'code'            => $model->getShippingMethod()->getCode(),
                'enabled'         => $model->getShippingMethod()->getEnabled(),
                'added'           => $model->getShippingMethod()->getAdded(),
                'tableType'       => $model->getShippingMethod()->getTableType(),
                'handlingFee'     => $model->getShippingMethod()->getHandlingFeeValue(),
                'handlingFeeType' => $model->getShippingMethod()->getHandlingFeeType(),
                'deliveryTime'    => $translation->getDeliveryTime(),
            ],
            'zone_id'           => $model->getZone()->getZoneId(),
        ];
    }

    /**
     * Prepare input
     *
     * @param array $data Data
     *
     * @return array
     */
    public function prepareInput(array $data)
    {
        return [true, $data];
    }

    /**
     * Preload data
     *
     * @param \XLite\Model\AEntity $entity Product
     * @param array                $data   Data
     *
     * @return void
     */
    public function preloadData(\XLite\Model\AEntity $entity, array $data)
    {
    }
}
