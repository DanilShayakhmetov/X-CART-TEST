<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Segment;

use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class AddTo extends MailChimpRequest
{
    /**
     * @param string   $listId
     * @param string   $segmentId
     * @param string[] $emails
     */
    public function __construct($listId, $segmentId, $emails)
    {
        $data = [
            'members_to_add' => array_map(static function ($item) {
                return ['email' => $item];
            }, $emails),
        ];

        parent::__construct('Adding to segments', 'post', "lists/{$listId}/segments/{$segmentId}", $data);
    }

    /**
     * @param string   $listId
     * @param string   $segmentId
     * @param string[] $emails
     *
     * @return self
     */
    public static function getRequest($listId, $segmentId, $emails): self
    {
        return new self($listId, $segmentId, $emails);
    }

    /**
     * @param string   $listId
     * @param string   $segmentId
     * @param string[] $emails
     *
     * @return mixed
     */
    public static function scheduleAction($listId, $segmentId, $emails)
    {
        return self::getRequest($listId, $segmentId, $emails)->schedule();
    }

    /**
     * @param string   $listId
     * @param string   $segmentId
     * @param string[] $emails
     *
     * @return mixed
     */
    public static function executeAction($listId, $segmentId, $emails)
    {
        return self::getRequest($listId, $segmentId, $emails)->execute();
    }
}
