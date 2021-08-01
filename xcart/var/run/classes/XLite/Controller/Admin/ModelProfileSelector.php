<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Model profile selector controller
 */
class ModelProfileSelector extends \XLite\Controller\Admin\ModelSelector\AModelSelector
{
    const MAX_PROFILE_COUNT = 10;

    /**
     * Define specific data structure which will be sent in the triggering event (model.selected)
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function defineDataItem($item)
    {
        $data = parent::defineDataItem($item);
        $data['selected_value'] = $item->getName();
        $data['selected_login'] = '&lt;' . $item->getLogin() . '&gt;';

        return $data;
    }

    /**
     * Get data of the model request
     *
     * @return \Doctrine\ORM\PersistentCollection | array
     */
    protected function getData()
    {
        $profileRepo = \XLite\Core\Database::getRepo(\XLite\Model\Profile::class);

        $cnd = new \XLite\Core\CommonCell();
        $cnd->{$profileRepo::SEARCH_PATTERN} = strip_tags($this->getKey());
        $cnd->{$profileRepo::SEARCH_LOGIN}   = strip_tags($this->getKey());
        $cnd->{$profileRepo::SEARCH_ORDER_ID} = null;
        $cnd->{$profileRepo::P_LIMIT}   = [0, static::MAX_PROFILE_COUNT];

        return $profileRepo->search($cnd);
    }

    /**
     * Format model text presentation
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function formatItem($item)
    {
        return $item->getName() . ' &lt;' . $item->getLogin() . '&gt;';
    }

    /**
     * Defines the model value
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function getItemValue($item)
    {
        return $item->getProfileId();
    }
}
