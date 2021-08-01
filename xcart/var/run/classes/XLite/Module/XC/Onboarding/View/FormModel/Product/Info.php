<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormModel\Product;

/**
 * Product form model
 */
 class Info extends \XLite\Module\XC\ProductTags\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $files = parent::getJSFiles();

        if (isset(\XLite\Core\Request::getInstance()->prefill['name']) && \XLite\Core\Request::getInstance()->prefill['name']) {
            $files[] = 'modules/XC/Onboarding/form_model/product/info.js';
        }

        return $files;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();
        $files = isset($schema[self::SECTION_DEFAULT]['images']['files'])
            ? $schema[self::SECTION_DEFAULT]['images']['files']
            : null;

        if ($files !== null && !$this->getDataObject()->default->identity && isset(\XLite\Core\Request::getInstance()->prefill['image'])) {
            $image = \XLite\Core\Request::getInstance()->prefill['image'];

            /** @var \XLite\Module\XC\Onboarding\Model\TemporaryFile $file */
            $file = $image['temp_id']
                ? \XLite\Core\Database::getRepo('XLite\Model\TemporaryFile')->find($image['temp_id'])
                : null;

            if ($file) {
                $file->setAlt($image['alt']);
                $schema[self::SECTION_DEFAULT]['images']['files'][-1] = $file;
            }
        }

        return $schema;
    }
}
