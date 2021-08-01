<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\Button;

class ImportLink extends \XLite\View\Button\SimpleLink
{
    public function __construct(array $params = [])
    {
        parent::__construct(array_merge([
            \XLite\View\Button\AButton::PARAM_LABEL   => 'onboarding.add_product.import',
            \XLite\View\Button\AButton::PARAM_STYLE   => 'always-enabled external',
            \XLite\View\Button\Link::PARAM_LOCATION   => $this->getImportLinkUrl(),
            \XLite\View\Button\Link::PARAM_ATTRIBUTES => [
                'v-on:click' => 'cacheProductData()',
            ],
        ], $params));
    }

    protected function getImportLinkUrl()
    {
        return static::t('https://kb.x-cart.com/import-export/csv_format_by_x-cart_data_type/csv_import_products.html');
    }
}