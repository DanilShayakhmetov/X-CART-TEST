<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\FormField\Select;

/**
 * User type selector
 */
class Lists extends \XLite\View\FormField\Select\Base\Rich
{
    protected function getDefaultOptions()
    {
        $lists = $this->getLists();
        $result = [
            false => ''
        ];

        /** @var \XLite\Module\XC\MailChimp\Model\MailChimpList $list */
        foreach ($lists as $list) {
            $result[$list->getId()] = $list->getName();
        }

        return $result;
    }

    /**
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList[]
     */
    public function getLists()
    {
        /** @var \XLite\Module\XC\MailChimp\Model\Repo\MailChimpList $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList');

        return array_filter(
            $repo->getActiveMailChimpLists(),
            function($list) {
                /** @var \XLite\Module\XC\MailChimp\Model\MailChimpList $list */
                return $list->getStore()
                    && $list->getStore()->isMain();
            }
        );
    }
}
