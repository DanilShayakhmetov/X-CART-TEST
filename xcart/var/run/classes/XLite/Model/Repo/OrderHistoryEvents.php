<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Order history events repository
 * todo: rename to OrderHistoryEvent
 *
 * @Api\Operation\Create(modelClass="XLite\Model\OrderHistoryEvents", summary="Add new order history event")
 * @Api\Operation\Read(modelClass="XLite\Model\OrderHistoryEvents", summary="Retrieve order history event by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\OrderHistoryEvents", summary="Retrieve order history events by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\OrderHistoryEvents", summary="Update order history event by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\OrderHistoryEvents", summary="Delete order history event by id")
 */
class OrderHistoryEvents extends \XLite\Model\Repo\ARepo
{
    /**
     * Returns events by given order
     *
     * @param integer|\XLite\Model\Order $order Order
     *
     * @return \XLite\Model\OrderHistoryEvents[]
     */
    public function findAllByOrder($order)
    {
        return $this->defineFindAllByOrder($order)->getResult();
    }

    /**
     * Register event to the order
     *
     * @param integer $orderId     Order identificator
     * @param string  $code        Event code
     * @param string  $description Event description
     * @param array   $data        Data for event description OPTIONAL
     * @param string  $comment     Event comment OPTIONAL
     * @param array   $details     Event details OPTIONAL
     *
     * @return void
     */
    public function registerEvent($orderId, $code, $description, array $data = array(), $comment = '', $details = array())
    {
        /** @var \Xlite\Model\Order $order */
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($order && !$order->isRemoving()) {
            $event = new \XLite\Model\OrderHistoryEvents(
                array(
                    'date'        => \XLite\Core\Converter::time(),
                    'code'        => $code,
                    'description' => $description,
                    'data'        => $data,
                    'comment'     => $comment,
                )
            );

            if (!empty($details)) {
                $event->setDetails($details);
            }

            if ($this->shouldAddAuthorToEvent($code, $order, \XLite\Core\Auth::getInstance()->getProfile())) {
                if (\XLite\Core\Auth::getInstance()->getProfile()) {
                    $event->setAuthor(\XLite\Core\Auth::getInstance()->getProfile());
                } elseif (strtolower(\XLite\Core\Request::getInstance()->target) === 'restapi') {
                    $event->setAuthorName('REST API');
                }

                if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
                    $event->setAuthorIp($_SERVER['REMOTE_ADDR']);
                }
            }

            $event->setOrder($order);

            $order->addEvents($event);

            $this->insert($event);
        }
    }

    /**
     * @param                      $code
     * @param \XLite\Model\Order   $order
     * @param \XLite\Model\Profile $profile
     *
     * @return boolean
     */
    protected function shouldAddAuthorToEvent($code, $order, $profile)
    {
        return !($order instanceof \XLite\Model\Cart);
    }

    /**
     * Returns query builder for findAllByOrder method
     *
     * @param integer|\XLite\Model\Order $order Order
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAllByOrder($order)
    {
        return $this->createQueryBuilder()
            ->andWhere('o.order = :order')
            ->setParameter('order', $order)
            ->addOrderBy('o.date', 'DESC');
    }
}
