<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Model;

use XLite\Module\CDev\GoSocial\Logic\OgMeta;

/**
 * Sale discount modify widget
 *
 * @Decorator\Depend ("CDev\Sale")
 */
class SaleDiscount extends \XLite\Module\CDev\Sale\View\Model\SaleDiscount implements \XLite\Base\IDecorator
{
    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $schema = [];
        $customOgAdded = false;

        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ('meta_desc' == $name) {
                $schema['useCustomOG'] = $this->getUseCustomOgField();
                $customOgAdded = true;
            }
        }

        if (!$customOgAdded) {
            $schema['useCustomOG'] = $this->getUseCustomOgField();
        }

        $this->schemaDefault = $schema;

        parent::__construct($params, $sections);
    }

    protected function getUseCustomOgField()
    {
        return [
            static::SCHEMA_CLASS      => 'XLite\Module\CDev\GoSocial\View\FormField\Select\CustomOpenGraph',
            static::SCHEMA_LABEL      => 'Open Graph meta tags',
            static::SCHEMA_REQUIRED   => false,
            static::SCHEMA_TRUSTED    => true,
            static::SCHEMA_FIELD_ONLY => false,
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'showInSeparateSection' => true,
                ],
            ],
        ];
    }

    protected function setModelProperties(array $data)
    {
        $data['useCustomOG'] = $this->getPostedData('useCustomOG');
        $nonFilteredData = \XLite\Core\Request::getInstance()->getNonFilteredData();
        $data['ogMeta'] = isset($nonFilteredData['postedData']['ogMeta'])
            ? OgMeta::prepareOgMeta($nonFilteredData['postedData']['ogMeta'])
            : '';

        parent::setModelProperties($data);
    }
}
