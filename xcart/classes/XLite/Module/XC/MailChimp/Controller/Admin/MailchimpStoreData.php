<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use XLite\Module\XC\MailChimp\Core\MailChimp;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use \XLite\Module\XC\MailChimp\Logic\UploadingData;
use XLite\Module\XC\MailChimp\Main;

/**
 * Class MailchimpStoreData
 */
class MailchimpStoreData extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isCheckProcessNotFinished()
    {
        $eventName = UploadingData\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
               && in_array(
                   $state['state'],
                   [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS]
               )
               && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(
                UploadingData\Generator::getCancelFlagVarName()
            );
    }

    /**
     * @inheritDoc
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            [
                'startUploadProducts',
                'startUploadOrders',
                'startUploadAll',
                'updateStoresData',
            ]
        );
    }

    public function doActionUpdateStores()
    {
        $lists = \XLite\Core\Request::getInstance()->lists ?: [];

        foreach ($lists as $listId => $value) {
            MailChimpECommerce::getInstance()->updateStoreAndReference($listId, $value);
        }

        MailChimpECommerce::getInstance()->updateConnectedSites();

        \XLite\Core\Database::getEM()->flush();
    }

    public function doActionUpdateStoresData()
    {
        Main::updateAllMainStores();

        Main::setAllStoreSyncFlag(false);
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionStartUploadAll()
    {
        $lists = \XLite\Core\Request::getInstance()->lists;

        UploadingData\Generator::run(
            [
                'lists' => $lists
            ]
        );
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionStartUploadProducts()
    {
        $lists = \XLite\Core\Request::getInstance()->lists;

        UploadingData\Generator::run(
            [
                'steps' => [
                    'products',
                ],
                'lists' => $lists
            ]
        );
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionStartUploadOrders()
    {
        $lists = \XLite\Core\Request::getInstance()->lists;

        UploadingData\Generator::run(
            [
                'steps' => [
                    'orders',
                ],
                'lists' => $lists
            ]
        );
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionCancel()
    {
        UploadingData\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('Uploading data has been stopped.');

        $this->setReturnURL($this->buildURL('mailchimp_store_data'));
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->process_completed) {
            \XLite\Core\TopMessage::addInfo('Uploading data has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('mailchimp_store_data')
            );

        } elseif ($request->process_failed) {
            \XLite\Core\TopMessage::addError('Uploading data has been stopped.');

            $this->setReturnURL(
                $this->buildURL('mailchimp_store_data')
            );
        }
    }
}
