<?php

namespace XLite\Module\Kliken\GoogleAds\Core\Schema\Complex;

class CDevTax implements \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
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
        $taxRates = [];
        foreach ($model->getRates() as $rate) {
            $taxRates[] = [
                'id'          => $rate->getId(),
                'value'       => $rate->getValue(),
                'type'        => $rate->getType(),
                'taxableBase' => $rate->getTaxableBase(),
                'zoneId'      => $rate->getZone()->getZoneId(),
            ];
        }

        return [
            'id'      => $model->getId(),
            'enabled' => $model->getEnabled(),
            'rates'   => $taxRates,
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
