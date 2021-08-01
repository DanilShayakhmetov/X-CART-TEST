<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

use XLite\Module\XC\MailChimp\Core\Request\Audience as MailChimpAudience;
use XLite\Module\XC\MailChimp\Core\Request\Cart as MailChimpCart;
use XLite\Module\XC\MailChimp\Core\Request\Order as MailChimpOrder;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;
use XLite\Module\XC\MailChimp\Logic\DataMapper\Order;
use XLite\Module\XC\MailChimp\Main;
use XLite\Module\XC\MailChimp\Model\MailChimpList;

require_once LC_DIR_MODULES . 'XC' . LC_DS . 'MailChimp' . LC_DS . 'lib' . LC_DS . 'MailChimp.php';

/**
 * MailChimp core class
 */
class MailChimp extends \XLite\Base\Singleton
{
    const SUBSCRIPTION_FIELD_NAME        = 'subscribe';
    const SUBSCRIPTION_TO_ALL_FIELD_NAME = 'subscribeToAll';

    const MC_FIRST_NAME = 'FNAME';
    const MC_LAST_NAME  = 'LNAME';

    /**
     * MailChimp API class
     *
     * @var \XLite\Module\XC\MailChimp\Core\MailChimpLoggableAPI
     */
    protected $mailChimpAPI = null;

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @return void
     *
     * @throws MailChimpException
     */
    protected function __construct()
    {
        parent::__construct();

        try {
            $this->mailChimpAPI = new \XLite\Module\XC\MailChimp\Core\MailChimpLoggableAPI(
                \XLite\Core\Config::getInstance()->XC->MailChimp->mailChimpAPIKey
            );

        } catch (\Exception $e) {
            if (
                MailChimpException::MAILCHIMP_NO_API_KEY_ERROR == $e->getMessage()
                && \XLite::isAdminZone()
            ) {
                if ('' != \XLite\Core\Config::getInstance()->XC->MailChimp->mailChimpAPIKey) {
                    \XLite\Core\TopMessage::addError($e->getMessage());
                }

                \XLite\Core\Operator::redirect(
                    \XLite\Core\Converter::buildURL('mailchimp_options')
                );
            }

            throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Check if current subscription select type is select box
     *
     * @return boolean
     */
    public static function isSelectBoxElement()
    {
        return \XLite\Module\XC\MailChimp\View\FormField\Select\ElementType::SELECT
            === \XLite\Core\Config::getInstance()->XC->MailChimp->subscriptionElementType;
    }

    /**
     * Check if module has API key populated
     *
     * @return boolean
     */
    public static function hasAPIKey()
    {
        return \XLite\Core\Config::getInstance()->XC
            && \XLite\Core\Config::getInstance()->XC->MailChimp
            && \XLite\Core\Config::getInstance()->XC->MailChimp->mailChimpAPIKey;
    }

    /**
     * Subscribe profile to all lists
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     * @throws MailChimpException
     */
    public static function processSubscriptionAll(\XLite\Model\Profile $profile)
    {
        if (!\XLite\Module\XC\MailChimp\Main::isMailChimpConfigured()) {
            return;
        }

        static::processSubscriptionInput(
            $profile,
            static::getAllListsDataToSubscribe(),
            static::getAllGroupNamesToSubscribe()
        );
    }

    /**
     * Subscribe/unsubscribe profile based on the form input data
     *
     * @param \XLite\Model\Profile|\XLite\Module\XC\MailChimp\Model\Profile $profile Profile
     * @param array|string                                                  $data    Subscriptions data
     *
     * @return void
     *
     * @throws MailChimpException
     */
    public static function processSubscriptionInput(\XLite\Model\Profile $profile, $data, $interests = null)
    {
        if (!\XLite\Module\XC\MailChimp\Main::isMailChimpConfigured() || !$data) {
            return;
        }

        if ($profile !== null) {
            $currentlySubscribed = $profile->getMailChimpListsIds();

            if (self::isSelectBoxElement()) {
                $tmpData = [];

                foreach ($currentlySubscribed as $listId) {
                    $tmpData[$listId] = '';
                }

                if (!empty($data)) {
                    if (!is_array($data)) {
                        $data = [$data => 1];
                    }

                    foreach ($data as $key => $value) {
                        $tmpData[$key] = $value;
                    }
                }

                $data = $tmpData;
            }

            $toSubscribe    = [];
            $toUnsubscribe  = [];
            $listGroupToSet = [];

            if (!$interests || !is_array($interests)) {
                $interests = \XLite\Core\Request::getInstance()->interest;
            }

            foreach ($data as $listId => $v) {
                if (
                    1 == $v
                    && !in_array($listId, $currentlySubscribed)
                ) {
                    $toSubscribe[] = $listId;
                } elseif (
                    1 != $v
                    && in_array($listId, $currentlySubscribed)
                ) {
                    $toUnsubscribe[] = $listId;
                }

                if (isset($interests[$listId]) && is_array($interests[$listId])) {
                    $listGroupToSet[$listId] = $interests[$listId];
                }
            }

            try {
                if (!empty($toUnsubscribe)) {
                    $profile->doUnsubscribeFromMailChimpLists($toUnsubscribe);
                }

                if (!empty($toSubscribe)) {
                    $profile->doSubscribeToMailChimpLists($toSubscribe);

                    $profile->checkSegmentsConditions();
                }

                foreach ($listGroupToSet as $listId => $values) {
                    $profile->checkGroupsConditions($listId, $values);
                }

            } catch (\Exception $e) {
                throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    /**
     * Get the error message from exception
     *
     * @param MailChimpException $e Exception
     *
     * @return string
     */
    public static function getMessageTextFromError(MailChimpException $e)
    {
        $message = $e->getMessage();

        if (
            strpos($message, self::MC_FIRST_NAME) !== false
            || strpos($message, self::MC_LAST_NAME) !== false
        ) {
            $message .= "\n<br />\n" . \XLite\Core\Translation::getInstance()->translate('First name or last name are empty. Please add a new address to your address book or modify existing and fill in those fields in order to subscribe to this list.');
        }

        return $message;
    }

    /**
     * Subscriptions data
     *
     * @return array
     */
    public static function getAllListsDataToSubscribe()
    {
        $result = [];

        $cnd = new \XLite\Core\CommonCell();

        $cnd->enabled            = true;
        $cnd->subscribeByDefault = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'subscribeByDefault',
            true
        );

        $allLists = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')
            ->search($cnd);

        foreach ($allLists as $list) {
            $result[$list->getId()] = 1;
        }

        return $result;
    }

    /**
     * Groups data
     *
     * @return array
     */
    public static function getAllGroupNamesToSubscribe()
    {
        $result = [];

        $cnd = new \XLite\Core\CommonCell();

        $cnd->enabled                 = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'enabled',
            true
        );
        $cnd->subscribeByDefault      = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'subscribeByDefault',
            true
        );
        $cnd->groupEnabled            = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'group.enabled',
            true
        );
        $cnd->listSubscribedByDefault = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'group.list.subscribeByDefault',
            true
        );
        $cnd->listEnabled             = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'group.list.enabled',
            true
        );

        $all = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpGroupName')
            ->search($cnd);

        foreach ($all as $item) {
            $listId = $item->getGroup()->getList()->getId();
            if (!isset($result[$listId])) {
                $result[$listId] = [];
            }

            $result[$listId][$item->getId()] = 1;
        }

        return $result;
    }

    /**
     * Update MailChimp lists
     *
     * @return void
     */
    public function updateMailChimpLists()
    {
        $lists = MailChimpAudience\GetAll::executeAction();

        if ($lists) {
            \XLite\Core\Database::getRepo(MailChimpList::class)
                ->updateLists($lists);
        }
    }

    /**
     * Check if current MailChimp lists has removed list
     *
     * @return boolean
     */
    public function hasRemovedMailChimpLists()
    {
        return \XLite\Core\Database::getRepo(MailChimpList::class)
            ->hasRemovedMailChimpLists();
    }

    /**
     * Subscribe email to MailChimp list
     *
     * @param string $id    MailChimp list ID
     * @param string $email E-mail
     *
     * @return array
     */
    public function doSubscribe($id, $email, $firstName, $lastName)
    {
        $hash = md5(mb_strtolower($email));

        $subscriber = $this->mailChimpAPI->get("lists/{$id}/members/{$hash}");

        $data = [
            'email_type'    => 'html',
            'email_address' => $email,
            'status'        => \XLite\Core\Config::getInstance()->XC->MailChimp->doubleOptinDisabled || $subscriber['status'] == 'subscribed'
                ? 'subscribed'
                : 'pending',
            'merge_fields'  => [
                self::MC_FIRST_NAME => $firstName,
                self::MC_LAST_NAME  => $lastName,
            ],
        ];
        $this->mailChimpAPI->setActionMessageToLog('Profile subscription');

        return $this->mailChimpAPI->put("lists/{$id}/members/{$hash}", $data);
    }

    /**
     * @param string $listId
     * @param string $email
     */
    public function doUnsubscribe($listId, $email)
    {
        $hash = md5(mb_strtolower($email));

        MailChimpAudience\UnSubscribe::executeAction($listId, $hash);
    }

    /**
     * Create batch
     *
     * @param string $id Batch id
     *
     * @return array
     */
    public function getBatch($id)
    {
        $result = $this->mailChimpAPI->get("batches/{$id}");

        return $this->mailChimpAPI->success()
            ? $result
            : null;
    }

    /**
     * Remove ECommerce360 cart data
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return bool
     */
    public function removeCart(\XLite\Model\Cart $cart)
    {
        MailChimpCart\Remove::scheduleAction($cart->getOrderId());
    }

    /**
     * @param \XLite\Model\Order $order
     */
    public function createOrder(\XLite\Model\Order $order)
    {
        MailChimpOrder\Create::scheduleAction($order);
    }

    /**
     * @param \XLite\Model\Order $order
     */
    public function updateOrder(\XLite\Model\Order $order)
    {
        /** @var \XLite\Model\Order|\XLite\Module\XC\MailChimp\Model\Order $order */
        $stores = [$order->getMailchimpStoreId()];

        if ($defaultAutomationStoreId = MailChimpStore\Get::getDefaultAutomationStoreIdByDB()) {
            $stores[] = $defaultAutomationStoreId;
        }

        foreach (array_unique($stores) as $storeId) {
            MailChimpOrder\Update::scheduleAction($storeId, $order);
        }
    }

    /**
     * Get segments
     *
     * @param string $listId MailChimp list ID
     *
     * @return array
     */
    public function getSegments($listId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Get segments');
        $segments = $this->mailChimpAPI->get("lists/{$listId}/segments");

        if (!$segments) {
            return [];
        }

        $segments = $segments['segments'];

        return [
            'static' => array_filter($segments, function ($segment) {
                return $segment['type'] === 'static';
            }),
            'saved'  => array_filter($segments, function ($segment) {
                return $segment['type'] === 'saved';
            }),
            'fuzzy'  => array_filter($segments, function ($segment) {
                return $segment['type'] === 'fuzzy';
            }),
        ];
    }

    /**
     * Add interests to a member
     *
     * @param       $listId
     * @param       $subscriberEmail
     * @param array $interests
     *
     * @return array|false
     */
    public function addInterestsToMember($listId, $subscriberEmail, array $interests)
    {
        $subscriberHash = md5(mb_strtolower($subscriberEmail));

        $this->mailChimpAPI->setActionMessageToLog('Profile subscribing to group');

        return $this->mailChimpAPI->patch("lists/{$listId}/members/{$subscriberHash}", [
            'interests' => $interests,
        ]);
    }

    /**
     * Get groups
     *
     * @param string $listId MailChimp list ID
     *
     * @return array
     */
    public function getGroups($listId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Getting groups');
        $groups = $this->mailChimpAPI->get("lists/{$listId}/interest-categories");

        if (!$groups) {
            return [];
        }

        return $groups['categories'];
    }

    /**
     * Get group names
     *
     * @param string $listId  MailChimp list ID
     * @param string $groupId MailChimp group ID
     *
     * @return array
     */
    public function getGroupNames($listId, $groupId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Getting group interests');
        $names = $this->mailChimpAPI->get("lists/{$listId}/interest-categories/{$groupId}/interests");

        if (!$names) {
            return [];
        }

        return $names['interests'];
    }
}
