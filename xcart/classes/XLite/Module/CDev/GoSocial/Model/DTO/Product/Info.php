<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model\DTO\Product;

use XLite\Module\CDev\GoSocial\Logic\OgMeta;

/**
 * Product
 */
class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    protected function init($object)
    {
        parent::init($object);

        $this->marketing->og_tags_type = (string)(int)$object->getUseCustomOG();
        $this->marketing->og_tags = $object->getOpenGraphMetaTags();

    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setUseCustomOG((boolean)$this->marketing->og_tags_type);
        if ($this->marketing->og_tags_type) {
            $object->setOgMeta(OgMeta::prepareOgMeta($rawData['marketing']['og_tags']));
        } else {
            $object->setOgMeta($object->getOpenGraphMetaTags(false));
        }
    }
}
