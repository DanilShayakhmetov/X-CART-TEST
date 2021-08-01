<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema\Complex;

/**
 * Product schema
 */
class Product implements \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
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
        $translationDefault = $model->getSoftTranslation($language);

        $images = [];
        foreach ($model->getImages() as $image) {
            $images[] = $image->getFrontURL();
        }

        $categories = [];
        foreach ($model->getCategories() as $category) {
            $categories[] = $category->getStringPath();
        }

        $memberships = [];
        foreach ($model->getMemberships() as $membership) {
            $memberships[] = $membership->getName();
        }

        $translations = [];
        foreach ($model->getTranslations() as $translation) {
            $translations[$translation->getCode()] = [
                'name'             => $translation->getName(),
                'description'      => $translation->getDescription(),
                'shortDescription' => $translation->getBriefDescription(),
            ];
        }

        return [
            'sku'              => $model->getSku(),
            'productId'        => $model->getProductId(),
            'name'             => $translationDefault->getName(),
            'description'      => $translationDefault->getDescription(),
            'shortDescription' => $translationDefault->getBriefDescription(),
            'price'            => $model->getPrice(),
            'weight'           => $model->getWeight(),
            'quantity'         => $model->getAmount(),
            'releaseDate'      => $model->getArrivalDate() ? date('c', $model->getArrivalDate()) : null,
            'image'            => $images,
            'URL'              => $model->getFrontURL(),
            'enabled'          => $model->getEnabled(),
            'freeShipping'     => $model->getFreeShipping(),
            'categories'       => $categories,
            'memberships'      => $memberships,
            'translations'     => $translations,
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
        list($checked, $result) = $this->prepareScalarFields($data, $this->getFields());

        // Images
        if ($checked && !empty($data['image']) && is_array($data['image'])) {
            $result['images'] = array();
            foreach ($data['image'] as $url) {
                if (!empty($url)) {
                    $result['images'][] = array(
                        'loadURL' => $url,
                    );

                }
            }
        }

        // Categories
        if ($checked && !empty($data['categories']) && is_array($data['categories'])) {
            $orderby = 10;
            $result['categoryProducts'] = array();
            foreach ($data['categories'] as $path) {
                if (is_string($path) && !empty($path)) {
                    $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->findOneByPath(explode('/', $path));
                    if ($category) {
                        $result['categoryProducts'][] = array(
                            'category' => array(
                                'category_id' => $category->getCategoryId(),
                            ),
                            'orderby' => $orderby,
                        );
                        $orderby += 10;

                    }
                }
            }
        }

        // Memberships
        if ($checked && !empty($data['memberships']) && is_array($data['memberships'])) {
            $result['memberships'] = array();
            foreach ($data['memberships'] as $membership) {
                if (is_string($membership) && !empty($membership)) {
                    $membership = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findOneByName($membership);
                    if ($membership) {
                        $result['memberships'][] = array(
                            'membership_id' => $membership->getMembershipId(),
                        );

                    }
                }
            }
        }

        // Translations
        if ($checked && !empty($data['translations']) && is_array($data['translations'])) {
            $result['translations'] = array();
            foreach ($data['translations'] as $code => $translation) {
                if (!empty($translation) && is_array($translation)) {
                    $result['translations'][] = array('code' => $code)
                        + $translation;

                }
            }
        }

        return array($checked, $result);
    }

    /**
     * Prepare scalar fields
     *
     * @param array $data   Data
     * @param array $fields Fields definition
     *
     * @return array
     */
    protected function prepareScalarFields(array $data, array $fields)
    {
        $checked = true;
        $result = array();

        foreach ($fields as $field) {
            if (!empty($field['required']) && (!isset($data[$field['source']]) || 0 == strlen($data[$field['source']]))) {
                $checked = false;
                break;
            }

            if (isset($data[$field['source']])) {
                $result[$field['destination']] = $data[$field['source']];
            }
        }

        return array($checked, $result);
    }

    /**
     * Get fields
     *
     * @return array
     */
    protected function getFields()
    {
        return [
            [
                'source'      => 'sku',
                'destination' => 'sku',
            ],
            [
                'source'      => 'name',
                'destination' => 'name',
                'required'    => true,
            ],
            [
                'source'      => 'description',
                'destination' => 'description',
            ],
            [
                'source'      => 'shortDescription',
                'destination' => 'brief_description',
            ],
            [
                'source'      => 'price',
                'destination' => 'price',
            ],
            [
                'source'      => 'weight',
                'destination' => 'weight',
            ],
            [
                'source'      => 'releaseDate',
                'destination' => 'arrivalDate',
            ],
            [
                'source'      => 'enabled',
                'destination' => 'enabled',
            ],
            [
                'source'      => 'URL',
                'destination' => 'cleanURL',
            ],
            [
                'source'      => 'freeShipping',
                'destination' => 'free_shipping',
            ],
            [
                'source'      => 'quantity',
                'destination' => 'amount',
            ],
        ];
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
