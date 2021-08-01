<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use Includes\Utils\Module\Manager;
use XLite;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\Shipping\Method;

/**
 * Shipping methods management page controller
 */
class ShippingMethods extends AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['add']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getMethod()
            ? static::t($this->getMethod()->getProcessorObject()->getProcessorName())
            : static::t('shipping');
    }

    /**
     * Returns shipping method
     *
     * @return null|Method
     */
    public function getMethod()
    {
        /** @var XLite\Model\Repo\Shipping\Method $repo */
        $repo = Database::getRepo(Method::class);

        return $repo->findOnlineCarrier($this->getProcessorId());
    }

    /**
     * Returns current processor id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return Request::getInstance()->processor;
    }

    /**
     * Returns current carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        $processorId = $this->getProcessorId();

        return $processorId && $processorId !== 'offline'
            ? $processorId
            : '';
    }

    public function doActionAdd()
    {
        $request = Request::getInstance();
        $id = $request->id;
        $rebuildId = $request->rebuildId;

        if ($rebuildId) {
            TopMessage::addInfo('If anything crops up, just rollback or contact our support team - they know how to fix it right away.', [
                'rollback_url' => XLite::getInstance()->getShopURL('service.php?/rollback', null, [
                    'id' => $rebuildId
                ])
            ]);
        }

        $url = null;

        /** @var Method $method */
        $method = Database::getRepo(Method::class)
            ->find($id);

        if ($method !== null) {
            $module = $method->getProcessorModule();


            if (Manager::getRegistry()->isModuleEnabled($module)) {
                $processor = $method->getProcessorObject();
                $url = $processor ? $processor->getSettingsURL() : null;
            }
        }

        $this->redirect($url ?: Converter::buildURL('shipping_methods'));
    }

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        \XLite\Core\Marketplace::getInstance()->updateShippingMethods();

        parent::run();
    }
}
