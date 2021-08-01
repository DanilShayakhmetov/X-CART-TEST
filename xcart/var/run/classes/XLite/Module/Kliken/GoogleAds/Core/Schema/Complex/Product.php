<?php

namespace XLite\Module\Kliken\GoogleAds\Core\Schema\Complex;

/**
 * Product schema
 */
class Product extends \XLite\Module\XC\RESTAPI\Core\Schema\Complex\Product
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
        $translation = $model->getSoftTranslation($language);

        $categories = [];
        // This try-catch is for the case when X-Cart has bad data of non-existing category id(s)
        // being assigned to a product. Their admin's product page blows up too, but we will be
        // proactive this time.
        try {
            foreach ($model->getCategories() as $category) {
                $categories[] = $category->getCategoryId();
            }
        } catch (\Exception $e) {
            // Ignore.
        }

        $images = [];
        foreach ($model->getImages() as $image) {
            $images[] = $image->getFrontURL();
        }

        $cleanUrls = [];
        foreach ($model->getCleanURLs() as $cleanURL) {
            $cleanUrls[] = $cleanURL->getCleanURL();
        }

        $result = [
            'sku'              => $model->getSku(),
            'productId'        => $model->getProductId(),
            'name'             => $translation->getName(),
            'description'      => $translation->getDescription(),
            'shortDescription' => $translation->getBriefDescription(),
            'price'            => $model->getPrice(),
            'weight'           => $model->getWeight(),
            'inventoryEnabled' => $model->getInventoryEnabled(),
            'quantity'         => $model->getAmount(),
            'URL'              => $model->getFrontURL(),
            'enabled'          => $model->getEnabled(),
            'cleanUrls'        => $cleanUrls,
            'images'           => $images,
            'categories'       => $categories,
            'createdDate'      => date('c', $model->getDate()),
            'modifiedDate'     => date('c', $model->getUpdateDate()),
            'arrivalDate'      => $model->getArrivalDate() ? date('c', $model->getArrivalDate()) : null,
        ];

        $attributes = $this->getProductAttributes($model);
        if (!empty($attributes)) {
            $result['attributes'] = $attributes;
        }

        $variants = $this->getProductVariants($model);
        if (!empty($variants)) {
            $result['variants'] = $variants;
        }

        if ($model->getShippable() && $model->getUseSeparateBox()) {
            $result['shippingDimensions'] = [
                'length' => $model->getBoxLength(),
                'width'  => $model->getBoxWidth(),
                'height' => $model->getBoxHeight(),
            ];
        }

        return $result;
    }

    private function getProductAttributes(\XLite\Model\AEntity $model)
    {
        // Getting product attributes
        $attributes = [];
        $repo = \XLite\Core\Database::getRepo('\XLite\Model\Attribute');
        $cnd = new \XLite\Core\CommonCell;
        $cnd->product = null;
        $cnd->productClass = null;

        foreach ($repo->search($cnd) as $a) {
            $this->processProductAttribute($attributes, $a->getName(), $a->getAttributeValue($model, true));
        }

        if ($model->getProductClass()) {
            $cnd = new \XLite\Core\CommonCell;
            $cnd->product = null;
            $cnd->productClass = $model->getProductClass();

            foreach ($repo->search($cnd) as $a) {
                $this->processProductAttribute($attributes, $a->getName(), $a->getAttributeValue($model, true));
            }
        }

        foreach ($model->getAttributes() as $a) {
            $this->processProductAttribute($attributes, $a->getName(), $a->getAttributeValue($model, true));
        }

        return $attributes;
    }

    private function getProductVariants(\XLite\Model\AEntity $model)
    {
        // Get product variants if has the Module installed
        if (! \Includes\Utils\ModulesManager::isActiveModule('XC\ProductVariants') && $model->hasVariants()) return null;

        $variants = [];
        foreach ($model->getVariants() as $variant) {
            $data = [
                'attributes' => []
            ];

            foreach ($variant->getValues() as $av) {
                $data['attributes'][$av->getAttribute()->getName()] = $av->asString();
            }

            if ($variant->getSKU()) {
                $data['sku'] = $variant->getSKU();
            }

            if (!$variant->getDefaultAmount()) {
                $data['amount'] = $variant->getAmount();
            }

            if (!$variant->getDefaultPrice()) {
                $data['price'] = $variant->getPrice();
            }

            if (!$variant->getDefaultWeight()) {
                $data['weight'] = $variant->getWeight();
            }

            $variants[$variant->getId()] = $data;
        }

        return $variants;
    }

    private function processProductAttribute(&$attrArray, $attrName, $attrValues)
    {
        // Wrap attribute in an array if it's not yet an array
        if (!is_array($attrValues)) {
            $attrValues = [ $attrValues ];
        }

        $attrArray[$attrName] = $attrValues;
    }
}
