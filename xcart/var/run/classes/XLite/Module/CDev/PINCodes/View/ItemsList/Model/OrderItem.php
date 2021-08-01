<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\ItemsList\Model;

use XLite\Core\Database;
use XLite\Core\TopMessage;
use XLite\Module\CDev\PINCodes\Model\PinCode;

/**
 * Order items list
 */
 class OrderItem extends \XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Postprocess inserted entity
     *
     * @param \XLite\Model\OrderItem $entity OrderItem entity
     * @param array                  $line   Array of entity data from request
     *
     * @return boolean
     */
    protected function postprocessInsertedEntity(\XLite\Model\AEntity $entity, array $line)
    {
        $result = parent::postprocessInsertedEntity($entity, $line);

        if ($result && \XLite\Controller\Admin\Order::isNeedProcessStock()) {

            // Process PIN codes on order save
            $order = $this->getOrder();

            if ($order->isProcessed()) {
                $order->processPINCodes();
            }
        }

        return $result;
    }

    protected function saveEntities()
    {
        $data = $this->getRequestData();
        $duplicates = [];
        $repo = Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode');

        foreach ($this->getPageDataForUpdate() as $entity) {
            /* @var \XLite\Model\OrderItem $entity */
            $entityId = $entity->getItemId();
            $product = $entity->getProduct();

            if (!empty($data['order_items'][$entityId]['pin_codes'])) {
                $codes = array_filter(array_map('trim', $data['order_items'][$entityId]['pin_codes']), 'strlen');
                foreach ($codes as $code) {
                    if (!$repo->findOneBy([
                        'product' => $product,
                        'code'    => $code,
                    ])) {
                        /* @var PinCode $pin */
                        $pin = $repo->insert(null, false);
                        $pin->setCode($code);
                        $pin->setProduct($product);
                        $pin->setOrderItem($entity);
                        $pin->setIsBlocked(true);
                        $pin->setIsSold($this->getOrder()->isAllowToProcessPinCodes());
                    } else {
                        $duplicates[] = $code;
                    }
                }
            }
        }

        if ($duplicates) {
            TopMessage::addError('The following PIN codes have already been sold: {{codes}}', [
                'codes' => implode(', ', $duplicates)
            ]);
        }

        return parent::saveEntities();
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/CDev/PINCodes/items_list/model/table/order_item/style.less',
        ]);
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/PINCodes/items_list/model/table/order_item/script.js',
        ]);
    }
}
