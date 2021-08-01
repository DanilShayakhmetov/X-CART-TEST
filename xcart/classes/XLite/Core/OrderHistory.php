<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Order history main point of execution
 */
class OrderHistory extends \XLite\Base\Singleton
{
    /**
     * Codes for registered events of order history
     */
    const CODE_PLACE_ORDER                  = 'PLACE ORDER';
    const CODE_USER_IP                      = 'USER IP';
    const CODE_CHANGE_STATUS_ORDER          = 'CHANGE STATUS ORDER';
    const CODE_CHANGE_PAYMENT_STATUS_ORDER  = 'CHANGE PAYMENT STATUS ORDER';
    const CODE_CHANGE_SHIPPING_STATUS_ORDER = 'CHANGE SHIPPING STATUS ORDER';
    const CODE_CHANGE_NOTES_ORDER           = 'CHANGE NOTES ORDER';
    const CODE_CHANGE_CUSTOMER_NOTES_ORDER  = 'CHANGE CUSTOMER NOTES ORDER';
    const CODE_CHANGE_AMOUNT                = 'CHANGE PRODUCT AMOUNT';
    const CODE_CHANGE_AMOUNT_GROUPED        = 'CHANGE PRODUCT AMOUNT GROUPED';
    const CODE_EMAIL_CUSTOMER_SENT          = 'EMAIL CUSTOMER SENT';
    const CODE_EMAIL_CUSTOMER_FAILED        = 'EMAIL CUSTOMER FAILED';
    const CODE_EMAIL_ADMIN_SENT             = 'EMAIL ADMIN SENT';
    const CODE_EMAIL_ADMIN_FAILED           = 'EMAIL ADMIN FAILED';
    const CODE_TRANSACTION                  = 'TRANSACTION';
    const CODE_ORDER_PACKAGING              = 'ORDER PACKAGING';
    const CODE_ORDER_TRACKING               = 'ORDER TRACKING';
    const CODE_ORDER_EDITED                 = 'ORDER EDITED';

    /**
     * Texts for the order history event descriptions
     */
    const TXT_PLACE_ORDER                   = 'Order placed';
    const TXT_USER_IP                       = '{{ip}} IP address used';
    const TXT_CHANGE_STATUS_ORDER           = 'Order status changed from {{oldStatus}} to {{newStatus}}';
    const TXT_CHANGE_PAYMENT_STATUS_ORDER   = 'Order payment status changed from {{oldStatus}} to {{newStatus}}';
    const TXT_CHANGE_SHIPPING_STATUS_ORDER  = 'Order shipping status changed from {{oldStatus}} to {{newStatus}}';
    const TXT_CHANGE_NOTES_ORDER            = 'Order staff notes changed';
    const TXT_CHANGE_CUSTOMER_NOTES_ORDER   = 'Order customer notes changed';
    const TXT_CHANGE_AMOUNT_ADDED           = '[Inventory] Return back to stock: "{{product}}" product amount in stock changes from "{{oldInStock}}" to "{{newInStock}}" ({{qty}} items)';
    const TXT_CHANGE_AMOUNT_REMOVED         = '[Inventory] Removed from stock: "{{product}}" product amount in stock changes from "{{oldInStock}}" to "{{newInStock}}" ({{qty}} items)';
    const TXT_UNABLE_RESERVE_AMOUNT         = '[Inventory] Unable to reduce stock for product: "{{product}}", amount: "{{qty}}" items';
    const TXT_TRACKING_INFO_UPDATE          = '[Tracking] Tracking information was updated';
    const TXT_TRACKING_INFO_ADDED           = '"{{number}}" tracking number is added';
    const TXT_TRACKING_INFO_REMOVED         = '"{{number}}" tracking number is removed';
    const TXT_TRACKING_INFO_CHANGED         = 'Tracking number is changed from "{{old_number}}" to "{{new_number}}"';
    const TXT_EMAIL_CUSTOMER_SENT           = 'Email sent to the customer';
    const TXT_EMAIL_CUSTOMER_FAILED         = 'Failure sending email to the customer';
    const TXT_EMAIL_ADMIN_SENT              = 'Email sent to the admin';
    const TXT_EMAIL_ADMIN_FAILED            = 'Failure sending email to the admin';
    const TXT_TRANSACTION                   = 'Transaction made';
    const TXT_ORDER_PACKAGING               = 'Products have been split into parcels in order to estimate the shipping cost';
    const TXT_ORDER_EDITED                  = 'Order has been edited';

    /**
     * Register event to the order history. Main point of action.
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
    public function registerEvent($orderId, $code, $description, array $data = [], $comment = '', $details = [])
    {
        \XLite\Core\Database::getRepo('XLite\Model\OrderHistoryEvents')
            ->registerEvent($orderId, $code, $description, $data, $comment, $details);
    }

    /**
     * Register the change amount inventory
     *
     * @param integer              $orderId Order identificator
     * @param \XLite\Model\Product $product Product object
     * @param integer              $delta   Inventory delta changes
     *
     * @return void
     */
    public function registerChangeAmount($orderId, $product, $delta)
    {
        if ($product->getInventoryEnabled()) {
            $this->registerEvent(
                $orderId,
                static::CODE_CHANGE_AMOUNT,
                $this->getOrderChangeAmountDescription($orderId, $delta, $product),
                $this->getOrderChangeAmountData($orderId, $product->getName(), $product->getPublicAmount(), $delta)
            );
        }
    }

    /**
     * Register the change amount inventory
     *
     * @param integer              $orderId Order identificator
     * @param array                $data    Inventory changes data
     *
     * @return void
     */
    public function registerChangeAmountGrouped($orderId, $data)
    {
        $processedData = $this->preprocessForGroupData($orderId, $data);

        if ($processedData) {
            $this->registerEvent(
                $orderId,
                static::CODE_CHANGE_AMOUNT_GROUPED,
                '[Inventory] Inventory changed',
                [],
                '',
                $processedData
            );
        }
    }

    /**
     * Preprocess data for change amount inventory grouped
     *
     * @param integer              $orderId Order identificator
     * @param array                $data    Inventory changes data
     *
     * @return array
     */
    protected function preprocessForGroupData($orderId, $data)
    {
        $result = [];

        foreach ($data as $key => $item) {
            if ($item['item']->getProduct()
                && $item['item']->getProduct()->getInventoryEnabled()
            ) {
                $realDelta = $item['delta'] > 0 && $item['item']->getBackorderedAmount() > 0
                    ? $item['delta'] - $item['item']->getBackorderedAmount()
                    : $item['delta'];

                if ($realDelta !== 0) {
                    $item['delta'] = $realDelta;
                    $result[] = $this->preprocessForGroupDataItem($orderId, $item);
                }
            }
        }

        return $result;
    }

    /**
     * Preprocess single item for change amount inventory grouped
     *
     * @param integer              $orderId Order identificator
     * @param array                $item    Inventory changes data item
     *
     * @return array
     */
    protected function preprocessForGroupDataItem($orderId, $item)
    {
        $textRaw = $this->getOrderChangeAmountDescription($orderId, $item['delta'], $item['item']->getProduct());
        $textTranslated = static::t(
            $textRaw,
            $this->getOrderChangeAmountData(
                $orderId,
                ($item['item'] ? $item['item']->getName() : 'Unknown'),
                $item['amount'],
                $item['delta']
            )
        );

        $name   = $textTranslated;
        $value  = '';
        $partsOfText = explode(':', $textTranslated, 2);
        if (1 < count($partsOfText)) {
            $name = array_shift($partsOfText);
            $value = implode(';', $partsOfText);
        }
        $name = str_replace('[Inventory] ', '', $name);

        return [
            'name'  => $name,
            'value' => $value,
            'label' => ''
        ];
    }

    /**
     * Register "Place order" event to the order history
     *
     * @param integer $orderId Order id
     *
     * @return void
     */
    public function registerPlaceOrder($orderId)
    {
        $this->registerEvent(
            $orderId,
            static::CODE_PLACE_ORDER,
            $this->getPlaceOrderDescription($orderId),
            $this->getPlaceOrderData($orderId)
        );

        $this->registerEvent(
            $orderId,
            static::CODE_USER_IP,
            $this->getUserIpDescription(),
            $this->getUserIpData()
        );
    }

    /**
     * Register "Place order" event to the order history
     *
     * @param integer                              $orderId  Order ID
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier Order's shipping modifier
     *
     * @return void
     */
    public function registerOrderPackaging($orderId, $modifier)
    {
        $packages = ($modifier && $modifier->getMethod() && $modifier->getMethod()->getProcessorObject())
            ? $modifier->getMethod()->getProcessorObject()->getPackages($modifier)
            : [];

        if (is_array($packages) && 1 < count($packages)) {

            $result = [];

            // Correct package keys to improve appearance
            foreach ($packages as $packId => $pack) {
                $result[$packId+1] = $pack;
            }

            // Register event
            $this->registerEvent(
                $orderId,
                static::CODE_ORDER_PACKAGING,
                $this->getOrderPackagingDescription($orderId),
                [],
                $this->getOrderPackagingComment($result)
            );
        }
    }

    /**
     * Register changes of order in the order history
     *
     * @param integer $orderId Order id
     * @param array   $changes Changes
     *
     * @return void
     */
    public function registerGlobalOrderChanges($orderId, array $changes)
    {
        $comment = $this->getOrderEditedComment($changes);

        $this->registerEvent(
            $orderId,
            static::CODE_ORDER_EDITED,
            $this->getOrderEditedDescription($orderId, $changes),
            $this->getOrderEditedData($orderId, $changes),
            $comment
        );
    }

    /**
     * Get content for ORDER_EDITED event comments
     *
     * @param array $changes Order changes
     *
     * @return string
     */
    protected function getOrderEditedComment($changes)
    {
        return serialize($changes);
    }

    /**
     * Text for ORDER_EDITED event description
     *
     * @param integer $orderId Order id
     * @param array   $changes Changes
     *
     * @return string
     */
    protected function getOrderEditedDescription($orderId, $changes)
    {
        return static::TXT_ORDER_EDITED;
    }

    /**
     * Data for ORDER_EDITED event description
     *
     * @param integer $orderId Order id
     * @param array   $changes Changes
     *
     * @return array
     */
    protected function getOrderEditedData($orderId, $changes)
    {
        return [];
    }

    /**
     * Register changes of order in the order history
     * TODO: Review and remove if this method is obsolete
     *
     * @param integer $orderId Order id
     * @param array   $changes Changes
     *
     * @return void
     */
    public function registerOrderChanges($orderId, array $changes)
    {
        foreach ($changes as $name => $change) {
            if (method_exists($this, 'registerOrderChange' . ucfirst($name))) {
                $this->{'registerOrderChange' . ucfirst($name)}($orderId, $change);
            }
        }
    }

    /**
     * Register status order changes
     *
     * @param integer $orderId Order id
     * @param array   $change  Old,new structure
     *
     * @return void
     */
    public function registerOrderChangeStatus($orderId, $change)
    {
        $this->registerEvent(
            $orderId,
            $this->getOrderChangeStatusCode($orderId, $change),
            $this->getOrderChangeStatusDescription($orderId, $change),
            $this->getOrderChangeStatusData($orderId, $change)
        );
    }

    /**
     * Register order customer notes changes
     *
     * @param integer $orderId
     * @param array   $change  old,new structure
     *
     * @return void
     */
    public function registerOrderChangeCustomerNotes($orderId, $change)
    {
        $comments = [];

        if (!empty($change['old'])) {
            $comments['Old customer note'][] = [
                'old' => null,
                'new' => $change['old'],
            ];
        }

        $comments['New customer note'][] = [
            'old' => null,
            'new' => $change['new'],
        ];

        $this->registerEvent(
            $orderId,
            static::CODE_CHANGE_CUSTOMER_NOTES_ORDER,
            $this->getOrderChangeCustomerNotesDescription($orderId, $change),
            $this->getOrderChangeNotesData($orderId, $change),
            serialize($comments)
        );
    }

    /**
     * Register order notes changes
     *
     * @param integer $orderId
     * @param array   $change  old,new structure
     *
     * @return void
     */
    public function registerOrderChangeAdminNotes($orderId, $change)
    {
        $comments = [];

        if (!empty($change['old'])) {
            $comments['Old staff note'][] = [
                'old' => null,
                'new' => $change['old'],
            ];
        }

        $comments['New staff note'][] = [
            'old' => null,
            'new' => $change['new'],
        ];

        $this->registerEvent(
            $orderId,
            static::CODE_CHANGE_NOTES_ORDER,
            $this->getOrderChangeNotesDescription($orderId, $change),
            $this->getOrderChangeNotesData($orderId, $change),
            serialize($comments)
        );
    }

    /**
     * Register email sending to the customer
     *
     * @param integer $orderId
     * @param string  $comment OPTIONAL
     *
     * @return void
     */
    public function registerCustomerEmailSent($orderId, $comment = '')
    {
        $this->registerEvent(
            $orderId,
            static::CODE_EMAIL_CUSTOMER_SENT,
            $this->getCustomerEmailSentDescription($orderId),
            $this->getCustomerEmailSentData($orderId),
            $comment
        );
    }

    /**
     * Register failure sending email to the customer
     *
     * @param integer $orderId Order id
     * @param string  $comment Comment OPTIONAL
     *
     * @return void
     */
    public function registerCustomerEmailFailed($orderId, $comment = '')
    {
        $this->registerEvent(
            $orderId,
            static::CODE_EMAIL_CUSTOMER_FAILED,
            $this->getCustomerEmailFailedDescription($orderId),
            $this->getCustomerEmailFailedData($orderId),
            $comment
        );
    }

    /**
     * Register any changes on the tracking information update (chnaged, )
     *
     * @param integer $orderId
     * @param array   $added
     * @param array   $removed
     * @param array   $changed
     *
     * @return void
     */
    public function registerTrackingInfoUpdate($orderId, $added, $removed, $changed)
    {
        $this->registerEvent(
            $orderId,
            static::CODE_ORDER_TRACKING,
            $this->getTrackingInfoDescription($orderId),
            $this->getTrackingInfoData($orderId),
            $this->getTrackingInfoComment($added, $removed, $changed)
        );
    }

    /**
     * Defines the text for the tracking information update
     *
     * @param integer $orderId
     *
     * @return string
     */
    protected function getTrackingInfoDescription($orderId)
    {
        return static::TXT_TRACKING_INFO_UPDATE;
    }

    /**
     * No tracking information specific data is defined
     *
     * @param integer $orderId
     *
     * @return array
     */
    protected function getTrackingInfoData($orderId)
    {
        return [];
    }

    /**
     * Defines the comment for the tracking information update
     *
     * @param array $added
     * @param array $removed
     * @param array $changed
     *
     * @return string
     */
    public function getTrackingInfoComment($added, $removed, $changed)
    {
        return implode('<br>', $this->getTrackingInfoLines($added, $removed, $changed));
    }

    /**
     * @param array $added
     * @param array $removed
     * @param array $changed
     *
     * @return array
     */
    public function getTrackingInfoLines($added, $removed, $changed)
    {
        $comment = [];

        foreach ($added as $value) {
            $comment[] = static::t(static::TXT_TRACKING_INFO_ADDED, ['number' => $value]);
        }

        foreach ($removed as $value) {
            $comment[] = static::t(static::TXT_TRACKING_INFO_REMOVED, ['number' => $value]);
        }

        foreach ($changed as $value) {
            $comment[] = static::t(static::TXT_TRACKING_INFO_CHANGED, ['old_number' => $value['old'], 'new_number' => $value['new']]);
        }

        return $comment;
    }

    /**
     * Register email sending to the admin in the order history
     *
     * @param integer $orderId
     * @param string  $comment OPTIONAL
     *
     * @return void
     */
    public function registerAdminEmailSent($orderId, $comment = '')
    {
        $this->registerEvent(
            $orderId,
            static::CODE_EMAIL_ADMIN_SENT,
            $this->getAdminEmailSentDescription($orderId),
            $this->getAdminEmailSentData($orderId),
            $comment
        );
    }

    /**
     * Register failure sending email to the admin
     *
     * @param integer $orderId Order id
     * @param string  $comment Comment OPTIONAL
     *
     * @return void
     */
    public function registerAdminEmailFailed($orderId, $comment = '')
    {
        $this->registerEvent(
            $orderId,
            static::CODE_EMAIL_ADMIN_FAILED,
            $this->getAdminEmailFailedDescription($orderId),
            $this->getAdminEmailFailedData($orderId),
            $comment
        );
    }

    /**
     * Register transaction data to the order history
     *
     * @param integer $orderId
     * @param string  $description
     * @param array   $details OPTIONAL
     * @param string  $comment OPTIONAL
     *
     * @return void
     */
    public function registerTransaction($orderId, $description, $details = [], $comment = '')
    {
        $this->registerEvent(
            $orderId,
            static::CODE_TRANSACTION,
            $description,
            [],
            $comment,
            $details
        );
    }

    /**
     * Text for place order description
     *
     * @param integer $orderId
     *
     * @return string
     */
    protected function getPlaceOrderDescription($orderId)
    {
        return static::TXT_PLACE_ORDER;
    }

    /**
     * Data for place order description
     *
     * @param integer $orderId
     *
     * @return array
     */
    protected function getPlaceOrderData($orderId)
    {
        return [
            'orderId' => $orderId,
        ];
    }

    /**
     * Text for user ip
     *
     * @return string
     */
    protected function getUserIpDescription()
    {
        return static::TXT_USER_IP;
    }

    /**
     * Data for user ip
     *
     * @return array
     */
    protected function getUserIpData()
    {
        return [
            'ip' => \XLite\Core\Request::getInstance()->getClientIp(),
        ];
    }

    /**
     * Text for place order description
     *
     * @param integer $orderId
     *
     * @return string
     */
    protected function getOrderPackagingDescription($orderId)
    {
        return static::TXT_ORDER_PACKAGING;
    }

    /**
     * Data for place order description
     *
     * @param array $packages
     *
     * @return array
     */
    protected function getOrderPackagingComment($packages)
    {
        // Switch interface to admin
        $layout = \XLite\Core\Layout::getInstance();

        $baseSkin = $layout->getSkin();
        $baseInterface = $layout->getInterface();
        $baseInnerInterface = $layout->getInnerInterface();

        $layout->setAdminSkin();

        // Get compiled widget content
        $widget = new \XLite\View\Order\Details\Admin\Packaging(
            [
                \XLite\View\Order\Details\Admin\Packaging::PARAM_PACKAGES => $packages,
            ]
        );

        $result = $widget->getContent();

        // Restore interface
        switch ($baseInterface) {
            case \XLite::ADMIN_INTERFACE:
                $layout->setAdminSkin();
                break;

            case \XLite::CUSTOMER_INTERFACE:
                $layout->setCustomerSkin();
                break;

            case \XLite::CONSOLE_INTERFACE:
                $layout->setConsoleSkin();
                break;

            case \XLite::MAIL_INTERFACE:
                $layout->setMailSkin($baseInnerInterface);
                break;
        }

        $layout->setSkin($baseSkin);

        return $result;
    }


    /**
     * Text for change order status description
     *
     * @param integer $orderId
     * @param array   $change
     *
     * @return string
     */
    protected function getOrderChangeStatusCode($orderId, array $change)
    {
        if (isset($change['type']) && $change['type']) {
            return 'payment' === $change['type']
                ? static::CODE_CHANGE_PAYMENT_STATUS_ORDER
                : static::CODE_CHANGE_SHIPPING_STATUS_ORDER;
        }

        return static::CODE_CHANGE_STATUS_ORDER;
    }

    /**
     * Text for change order status description
     *
     * @param integer $orderId
     * @param array   $change
     *
     * @return string
     */
    protected function getOrderChangeStatusDescription($orderId, array $change)
    {
        if (isset($change['type']) && $change['type']) {
            return 'payment' === $change['type']
                ? static::TXT_CHANGE_PAYMENT_STATUS_ORDER
                : static::TXT_CHANGE_SHIPPING_STATUS_ORDER;
        }

        return static::TXT_CHANGE_STATUS_ORDER;
    }

    /**
     * Data for change order status description
     *
     * @param integer $orderId
     * @param array   $change
     *
     * @return array
     */
    protected function getOrderChangeStatusData($orderId, array $change)
    {
        return [
            'orderId'   => $orderId,
            'newStatus' => $change['new']->getName(),
            'oldStatus' => $change['old']->getName(),
        ];
    }

    /**
     * Text for change amount
     *
     * @param integer              $orderId
     * @param integer              $delta
     * @param \XLite\Model\Product $product
     *
     * @return string
     */
    protected function getOrderChangeAmountDescription($orderId, $delta, $product)
    {
        if ($delta < 0) {
            return $this->isAbleToReduceAmount($product, $delta)
                ? static::TXT_CHANGE_AMOUNT_REMOVED
                : static::TXT_UNABLE_RESERVE_AMOUNT;
        }

        return static::TXT_CHANGE_AMOUNT_ADDED;
    }

    /**
     * Data for change amount description
     *
     * @param integer $orderId
     * @param string  $productName
     * @param integer $oldAmount
     * @param integer $delta
     *
     * @return array
     */
    protected function getOrderChangeAmountData($orderId, $productName, $oldAmount, $delta)
    {
        return [
            'orderId'    => $orderId,
            'newInStock' => $oldAmount + $delta,
            'oldInStock' => $oldAmount,
            'product'    => $productName,
            'qty'        => abs($delta),
        ];
    }

    /**
     * Text for change order notes description
     *
     * @param integer $orderId
     * @param array   $change
     *
     * @return string
     */
    protected function getOrderChangeNotesDescription($orderId, $change)
    {
        return static::TXT_CHANGE_NOTES_ORDER;
    }

    /**
     * Text for change order notes description
     *
     * @param integer $orderId
     * @param array   $change
     *
     * @return string
     */
    protected function getOrderChangeCustomerNotesDescription($orderId, $change)
    {
        return static::TXT_CHANGE_CUSTOMER_NOTES_ORDER;
    }

    /**
     * Data for change order notes description
     *
     * @param integer $orderId
     * @param array   $change
     *
     * @return array
     */
    protected function getOrderChangeNotesData($orderId, $change)
    {
        return [];
    }

    /**
     * Text for customer email sent description
     *
     * @param integer $orderId
     *
     * @return string
     */
    protected function getCustomerEmailSentDescription($orderId)
    {
        return static::TXT_EMAIL_CUSTOMER_SENT;
    }

    /**
     * Data for customer email sent description
     *
     * @param integer $orderId
     *
     * @return array
     */
    protected function getCustomerEmailSentData($orderId)
    {
        return [
            'orderId' => $orderId,
        ];
    }

    /**
     * Text for customer email failed description
     *
     * @param integer $orderId Order id
     *
     * @return string
     */
    protected function getCustomerEmailFailedDescription($orderId)
    {
        return static::TXT_EMAIL_CUSTOMER_FAILED;
    }

    /**
     * Data for customer email failed description
     *
     * @param integer $orderId Order id
     *
     * @return array
     */
    protected function getCustomerEmailFailedData($orderId)
    {
        return [
            'orderId' => $orderId,
        ];
    }

    /**
     * Text for admin email sent description
     *
     * @param integer $orderId
     *
     * @return string
     */
    protected function getAdminEmailSentDescription($orderId)
    {
        return static::TXT_EMAIL_ADMIN_SENT;
    }

    /**
     * Data for admin email sent description
     *
     * @param integer $orderId
     *
     * @return array
     */
    protected function getAdminEmailSentData($orderId)
    {
        return [
            'orderId' => $orderId,
        ];
    }

    /**
     * Text for admin email failed description
     *
     * @param integer $orderId Order id
     *
     * @return string
     */
    protected function getAdminEmailFailedDescription($orderId)
    {
        return static::TXT_EMAIL_ADMIN_FAILED;
    }

    /**
     * Data for admin email failed description
     *
     * @param integer $orderId Order id
     *
     * @return array
     */
    protected function getAdminEmailFailedData($orderId)
    {
        return [
            'orderId' => $orderId,
        ];
    }

    /**
     * Check if we need to register the record concerning the product inventory change
     *
     * @param \XLite\Model\Product $product
     * @param integer              $delta
     *
     * @return boolean
     */
    protected function isAbleToReduceAmount($product, $delta)
    {
        // Do record if the product is reserved and we have enough amount in stock for it
        return $product->getPublicAmount() >= abs($delta);
    }
}
