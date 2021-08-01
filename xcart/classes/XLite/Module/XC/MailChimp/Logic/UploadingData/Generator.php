<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\UploadingData;

use XLite\Module\XC\MailChimp\Core\MailChimp;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;

/**
 * Generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    /**
     * Flag: is export in progress (true) or no (false)
     *
     * @var boolean
     */
    protected static $inProgress = false;

    /**
     * Set inProgress flag value
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setInProgress($value)
    {
        static::$inProgress = $value;
    }

    // {{{ Steps

    /**
     * @return array
     */
    protected function getStepsList()
    {
        return array(
            'XLite\Module\XC\MailChimp\Logic\UploadingData\Step\Products',
            'XLite\Module\XC\MailChimp\Logic\UploadingData\Step\Orders',
        );
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return $this->getStepsList();
    }

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $options = $this->getOptions();

        $lists = isset($options['lists'])
            ? $options['lists']
            : [];

        foreach ($lists as $listId => $value) {
            $storeId = MailChimpStore\Get::getStoreIdByDB($listId);
            MailChimpECommerce::getInstance()->updateStoreAndReference($listId, $value);

            if (!isset($options['stores'])) {
                $options['stores'] = [];
            }

            if ($value) {
                $options['stores'][] = $storeId;
            }
        }

        \XLite\Core\Database::getEM()->flush();

        $this->setOptions($options);
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            if (!isset($this->options['count'])) {
                $this->options['count'] = 0;
                foreach ($this->getSteps() as $step) {
                    $this->options['count'] += $step->count();
                    $this->options['count' . get_class($step)] = $step->count();
                }
            }
            $this->countCache = $this->options['count'];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Service variable names

    /**
     * @inheritdoc
     */
    public static function getEventName()
    {
        return 'MailChimpUploadingData';
    }

    // }}}
}
