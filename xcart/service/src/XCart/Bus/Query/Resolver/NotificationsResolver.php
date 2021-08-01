<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Query\Data\IDataSource;
use XCart\Bus\Query\Data\NotificationsDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class NotificationsResolver
{
    /**
     * @var IDataSource
     */
    private $notificationsDataSource;

    /**
     * @var array
     */
    private $filter = [];

    /**
     * @param NotificationsDataSource $notificationsDataSource
     */
    public function __construct(NotificationsDataSource $notificationsDataSource)
    {
        $this->notificationsDataSource = $notificationsDataSource;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return Deferred
     *
     * @Resolver()
     */
    public function getList($value, $args, $context, ResolveInfo $info)
    {
        $this->notificationsDataSource->loadDeferred();
        $this->filter = $args;

        return new Deferred(function () {
            $notifications = $this->notificationsDataSource->getAll();

            if (!$this->filter) {
                return $notifications;
            }

            return array_filter($notifications, function ($notification) {
                if (!empty($this->filter['type']) && $notification['type'] !== $this->filter['type']) {
                    return false;
                }

                $target = $notification['pageParams']['target'] ?? '';
                $page   = $notification['pageParams']['page'] ?? '';

                return
                    (empty($target) || $target === ($this->filter['target'] ?? ''))
                    & (empty($page) || $page === ($this->filter['page'] ?? ''));
            });
        });
    }
}
