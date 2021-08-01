<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\ItemsList;

use XLite\Core\Auth;
use XLite\Model\Order;
use XLite\Model\Profile;
use Includes\Utils\Module\Manager;
use XLite\Module\CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint;

class AttachmentsHistory extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Allowed sort criteria
     */
    const SORT_BY_MODE_IP    = 'a.ip';
    const SORT_BY_MODE_LOGIN = 'a.login';
    const SORT_BY_MODE_DATE  = 'a.date';
    const SORT_BY_MODE_ORDER = 'a.order';
    const SORT_BY_MODE_PATH  = 'a.path';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_IP    => 'Ip',
            static::SORT_BY_MODE_LOGIN => 'Login',
            static::SORT_BY_MODE_DATE  => 'Date',
            static::SORT_BY_MODE_ORDER => 'Order',
            static::SORT_BY_MODE_PATH  => 'Path',
        ];

        parent::__construct($params);
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Egoods/product/history.css';

        return $list;
    }

    /**
     * Return true if items list should be displayed in static mode (no editable widgets, no controls)
     *
     * @return boolean
     */
    protected function isStatic()
    {
        return true;
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return false;
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['product']);
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        if (!\XLite\Core\Config::getInstance()->CDev->Egoods->enable_history) {
            $link = Manager::getRegistry()->getModuleSettingsUrl('CDev', 'Egoods');

            return static::t(
                'The history of downloads feature for this product is disabled. You can enable it here',
                ['link' => $link]
            );
        }

        return static::t('The product\'s history of downloads is empty');
    }

    /**
     * Check if list blank(no products in store for products list)
     *
     * @return boolean
     */
    protected function isListBlank()
    {
        return $this->getData($this->getSearchCondition(), true) <= 0;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'path'  => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('File title'),
                static::COLUMN_TEMPLATE => 'modules/CDev/Egoods/product/parts/column.path.twig',
                static::COLUMN_ORDERBY  => 100,
                static::COLUMN_SORT     => static::SORT_BY_MODE_PATH
            ],
            'login' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Login/E-mail'),
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_MAIN    => true,
                static::COLUMN_SORT    => static::SORT_BY_MODE_LOGIN,
                static::COLUMN_LINK    => 'profile'
            ],
            'order' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Order'),
                static::COLUMN_ORDERBY => 300,
                static::COLUMN_LINK    => 'order',
                static::COLUMN_SORT    => static::SORT_BY_MODE_ORDER
            ],
            'date'  => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Date'),
                static::COLUMN_ORDERBY => 400,
                static::COLUMN_SORT    => static::SORT_BY_MODE_DATE
            ],
            'ip'    => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Ip'),
                static::COLUMN_ORDERBY => 500,
                static::COLUMN_SORT    => static::SORT_BY_MODE_IP
            ],
        ];
    }

    /**
     * Preprocess order column
     *
     * @param                        $value
     * @param                        $column
     * @param AttachmentHistoryPoint $entity
     *
     * @return string
     */
    protected function preprocessOrder($value, $column, AttachmentHistoryPoint $entity)
    {
        return $value
            ? $value->getPrintableOrderNumber()
            : '[' . static::t('deleted') . ']';
    }

    /**
     * Preprocess login column
     *
     * @param                        $value
     * @param                        $column
     * @param AttachmentHistoryPoint $entity
     *
     * @return string
     */
    protected function preprocessLogin($value, $column, AttachmentHistoryPoint $entity)
    {
        return $value ?: static::t('Anonymous');
    }

    /**
     * Preprocess login column
     *
     * @param                        $value
     * @param                        $column
     * @param AttachmentHistoryPoint $entity
     *
     * @return string
     */
    protected function preprocessDate($value, $column, AttachmentHistoryPoint $entity)
    {
        return $value
            ? \XLite\Core\Converter::formatTime($value)
            : '-';
    }

    /**
     * Get filename
     *
     * @param AttachmentHistoryPoint $entity
     *
     * @return string
     */
    protected function getFilename(AttachmentHistoryPoint $entity)
    {
        return $entity->getAttachment()->getPublicTitle();
    }

    /**
     * Check if the column must be a link.
     * It is used if the column field is displayed via
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isLink($column, $entity)
            && (
                'order' === $column[static::COLUMN_CODE]
                && $entity->getOrder()
                && $this->isOrderLinkAvailable($entity->getOrder())
                || 'login' === $column[static::COLUMN_CODE]
                && $entity->getProfile()
                && $this->isLoginLinkAvailable($entity->getProfile())
            );

        return $result;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    protected function isOrderLinkAvailable(Order $order)
    {
        return $order
            && Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * @param Profile $profile
     *
     * @return bool
     */
    protected function isLoginLinkAvailable(Profile $profile)
    {
        return $profile
        && Auth::getInstance()->isPermissionAllowed('manage users');
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        if ('order' === $column[static::COLUMN_CODE]) {
            $order = $entity->getOrder();

            return \XLite\Core\Converter::buildURL(
                $column[static::COLUMN_LINK],
                '',
                ['order_number' => $order->getOrderNumber()]
            );
        } elseif ('login' === $column[static::COLUMN_CODE]) {
            return \XLite\Core\Converter::buildURL(
                $column[static::COLUMN_LINK],
                '',
                ['profile_id' => $entity->getProfile()->getProfileId()]
            );
        }

        return parent::buildEntityURL($entity, $column);
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' product-attachment-history';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['product_id'] = $this->getProductId();
        $this->commonParams['page'] = 'attachments';
        $this->commonParams['subpage'] = 'history';

        return $this->commonParams;
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $searchCase->{\XLite\Module\CDev\Egoods\Model\Repo\Product\Attachment\AttachmentHistoryPoint::P_PRODUCT} = $this->getProduct();

        return $searchCase;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DATE;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }
}