<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\CDev\Wholesale\View\Model;

/**
 * Sale discount
 * @Decorator\Depend ("CDev\Wholesale")
 */
abstract class SaleDiscount extends \XLite\Module\CDev\Sale\View\Model\SaleDiscount implements \XLite\Base\IDecorator
{
    /**
     * Add apply to wholesale field right after SKU
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        $applyToWholesale = [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\YesNo',
            self::SCHEMA_LABEL    => 'Apply sale discount to wholesale prices',
        ];

        $schema = [];
        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ('value' === $name) {
                $schema['applyToWholesale'] = $applyToWholesale;
            }
        }

        if (!isset($schema['applyToWholesale'])) {
            $schema['applyToWholesale'] = $applyToWholesale;
        }

        $this->schemaDefault = $schema;
    }
}
