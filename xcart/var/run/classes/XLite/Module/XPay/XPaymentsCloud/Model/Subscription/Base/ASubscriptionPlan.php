<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Base;

use XLite\Module\XPay\XPaymentsCloud\Core\Converter;
use XLite\Module\XPay\XPaymentsCloud\Core\Translation;
use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan;

/**
 * Subscription Plan Interface
 *
 * @MappedSuperclass
 */
class ASubscriptionPlan extends \XLite\Model\AEntity
{
    /**
     * Plan types
     */
    const TYPE_EACH  = 'E';
    const TYPE_EVERY = 'D';

    /**
     * Plan periods
     */
    const PERIOD_DAY   = 'D';
    const PERIOD_WEEK  = 'W';
    const PERIOD_MONTH = 'M';
    const PERIOD_YEAR  = 'Y';

    /**
     * Subscription statuses
     */
    const STATUS_NOT_STARTED = 'N';
    const STATUS_ACTIVE      = 'A';
    const STATUS_STOPPED     = 'S';
    const STATUS_FAILED      = 'D';
    const STATUS_FINISHED    = 'F';
    const STATUS_RESTARTED   = 'R';

    /**
     * Description type
     */
    const DESCRIPTION_SHORT = 'S';
    const DESCRIPTION_LONG  = 'L';

    /**
     * Days in seconds (for calculation)
     */
    const DAY_IN_SECONDS = 86400;

    /**
     * Minimum value of subscription fee
     */
    const MIN_FEE_VALUE = 0.0000;

    /**
     * Names of days of week
     *
     * @var array
     */
    protected $daysOfWeek = [
        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
    ];

    /**
     * Fee for plan or subscription
     *
     * @var float
     *
     * @Column (type="money", options={ "comment": "Fee for the plan" })
     */
    protected $fee = 0.0000;

    /**
     * Subscription plan or subscription type (each or every)
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $type = self::TYPE_EACH;

    /**
     * Number to calculate next bill date
     *
     * @var int
     *
     * @Column (type="integer", nullable=true)
     */
    protected $number = 1;

    /**
     * Plan period
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $period = self::PERIOD_WEEK;

    /**
     * Calculate from end of period (only for plan_type == self::TYPE_EACH)
     *
     * @var bool
     *
     * @Column (type="boolean")
     */
    protected $reverse = false;

    /**
     * Limit of periods to bill (zero mean unlimited)
     *
     * @var int
     *
     * @Column (type="integer", nullable=true)
     */
    protected $periods = 0;

    /**
     * Set fee
     *
     * @param float $fee
     * @return ASubscriptionPlan
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * Get fee
     *
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Get name of the day of the week
     *
     * @param int $dayIndex Day index
     *
     * @return string
     */
    protected function getDayOfWeek($dayIndex)
    {
        $dayOfWeek = '';

        if (array_key_exists($dayIndex, $this->daysOfWeek)) {
            $dayOfWeek = $this->daysOfWeek[$dayIndex];
        }

        return $dayOfWeek;
    }

    /**
     * Set day of month with corrections (make sure it's within the same month)
     *
     * @param int $day Day number
     * @param bool $reverse Is reverse
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    protected static function setDayOfMonth($day, $reverse = false, $timestamp = null)
    {
        $maxDay = Converter::getCountDaysInMonth($timestamp);

        if ($reverse) {
            $day = max(1, $maxDay - $day + 1);
        }

        $day = min($day, $maxDay);

        return Converter::setDayOfMonth($day, $timestamp);
    }

    /**
     * Set day of year with corrections (make sure it's within the same year)
     *
     * @param int $day Day number
     * @param bool $reverse Is reverse
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    protected static function setDayOfYear($day, $reverse = false, $timestamp = null)
    {
        $maxDay = Converter::getCountDaysInYear($timestamp);

        if ($reverse) {
            $day = max(1, $maxDay - $day + 1);
        }

        $day = min($day, $maxDay);

        return Converter::setDayOfYear($day, $timestamp);
    }

    /**
     * Get next payment date
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    public function getNextDate($checkDate = null)
    {
        $next = 0;

        if (is_null($checkDate)) {
            $checkDate = $this->getCheckDate();
        }

        $type = $this->getType();
        $period = $this->getPeriod();

        if (static::TYPE_EACH == $type) {
            if (static::PERIOD_WEEK == $period) {
                $next = $this->getNextEachWeek($checkDate);

            } elseif (static::PERIOD_MONTH == $period) {
                $next = $this->getNextEachMonth($checkDate);

            } elseif (static::PERIOD_YEAR == $period) {
                $next = $this->getNextEachYear($checkDate);
            }
        } elseif (static::TYPE_EVERY == $type) {
            if (static::PERIOD_DAY == $period) {
                $next = $this->getNextEveryDay($checkDate);

            } elseif (static::PERIOD_WEEK == $period) {
                $next = $this->getNextEveryWeek($checkDate);

            } elseif (static::PERIOD_MONTH == $period) {
                $next = $this->getNextEveryMonth($checkDate);

            } elseif (static::PERIOD_YEAR == $period) {
                $next = $this->getNextEveryYear($checkDate);
            }
        }

        return $next;
    }

    /**
     * Get subscription plan description
     *
     * @param string $type Description type
     *
     * @return string
     */
    public function getXpaymentsPlanDescription($type = self::DESCRIPTION_LONG)
    {
        if (static::DESCRIPTION_SHORT == $type) {
            $description = $this->getShortDescription();

        } else {
            $description = $this->getLongDescription();
        }

        return $description;
    }

    /**
     * Get short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        if (static::TYPE_EACH == $this->getType()) {
            $description = $this->getShortDescriptionEach();

        } else {
            $description = $this->getShortDescriptionEvery();
        }

        return $description;
    }

    /**
     * Get long description
     *
     * @return string
     */
    public function getLongDescription()
    {
        if (static::TYPE_EACH == $this->getType()) {
            $description = $this->getLongDescriptionEach();

        } else {
            $description = $this->getLongDescriptionEvery();
        }

        return $description;
    }

    /**
     * getTotalPaymentsDescription
     *
     * @return string
     */
    public function getXpaymentsTotalPaymentsDescription()
    {
        return $this->getPeriods()
            ? static::tp('xps.total_of__payments', $this->getPeriods(), ['number' => $this->getPeriods()])
            : '';
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->getValidNumber($this->number);
    }

    /**
     * Check if current object is subscription
     *
     * @return bool
     */
    protected function isXpaymentsSubscription()
    {
        return $this instanceof \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
    }

    /**
     * Check if current object is subscription plan
     *
     * @return bool
     */
    protected function isXpaymentsSubscriptionPlan()
    {
        return $this instanceof Plan;
    }

    /**
     * Get base timestamp of calculation
     *
     * @return int
     */
    protected function getCheckDate()
    {
        $checkDate = null;
        $type = $this->getType();

        if ($this->isXpaymentsSubscription()) {

            if (static::TYPE_EVERY == $type) {
                $checkDate = $this->getStartDate();
            } else {
                $checkDate = $this->getPlannedDate();
            }
        } else {
            $checkDate = Converter::now();
        }

        return $checkDate;
    }

    /**
     * Get current period index
     *
     * @return int
     */
    protected function getPeriodIndex()
    {
        return $this->isXpaymentsSubscription() ? $this->getSuccessfulAttempts() : 1;
    }

    /**
     * Get valid number
     *
     * @param int $number Number
     *
     * @return int
     */
    protected function getValidNumber($number)
    {
        $number = 0 < $number ? $number : 1;

        if (static::TYPE_EACH == $this->getType()) {
            if (static::PERIOD_WEEK == $this->getPeriod()) {
                $number = min(7, $number);

            } elseif (static::PERIOD_MONTH == $this->getPeriod()) {
                $number = min(31, $number);

            } elseif (static::PERIOD_YEAR == $this->getPeriod()) {
                $number = min(366, $number);
            }
        }

        return $number;
    }

    /**
     * get next date for TYPE_EACH and PERIOD_WEEK
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    protected function getNextEachWeek($checkDate)
    {
        $dayIndex = $this->getReverse() ? 8 - $this->getNumber() : $this->getNumber();

        return Converter::getStrtotime('next ' . $this->getDayOfWeek($dayIndex - 1), $checkDate);
    }

    /**
     * get next date for TYPE_EACH and PERIOD_MONTH
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    protected function getNextEachMonth($checkDate)
    {
        $dayIndex = $this->getNumber();
        $reverse = $this->getReverse();

        $next = static::setDayOfMonth($dayIndex, $reverse, $checkDate);

        if ($next <= $checkDate) {
            $next = static::setDayOfMonth($dayIndex, $reverse, Converter::addMonth(1, $checkDate));
        }

        return $next;
    }

    /**
     * get next date for TYPE_EACH and PERIOD_YEAR
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    protected function getNextEachYear($checkDate)
    {
        $dayIndex = $this->getNumber();
        $reverse = $this->getReverse();

        $next = static::setDayOfYear($dayIndex, $reverse, $checkDate);

        if ($next < $checkDate) {
            $next = static::setDayOfYear($dayIndex, $reverse, Converter::addYear(1, $checkDate));
        }

        return $next;
    }

    /**
     * get next date for TYPE_EVERY and PERIOD_DAY
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    protected function getNextEveryDay($checkDate)
    {
        return Converter::addDay($this->getNumber() * $this->getPeriodIndex(), $checkDate);
    }

    /**
     * get next date for TYPE_EVERY and PERIOD_WEEK
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    protected function getNextEveryWeek($checkDate)
    {
        return Converter::addDay($this->getNumber() * $this->getPeriodIndex() * 7, $checkDate);
    }

    /**
     * get next date for TYPE_EVERY and PERIOD_MONTH
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    protected function getNextEveryMonth($checkDate)
    {
        return Converter::addMonth($this->getNumber() * $this->getPeriodIndex(), $checkDate);
    }

    /**
     * get next date for TYPE_EVERY and PERIOD_MONTH
     *
     * @param int $checkDate Unix timestamp
     *
     * @return int
     */
    protected function getNextEveryYear($checkDate)
    {
        return Converter::addYear($this->getNumber() * $this->getPeriodIndex(), $checkDate);
    }

    /**
     * Get short description for TYPE_EACH
     *
     * @return string
     */
    protected function getShortDescriptionEach()
    {
        $description = '';
        $period = $this->getPeriod();

        if (static::PERIOD_WEEK == $period) {
            $description = static::t('Weekly');

        } elseif (static::PERIOD_MONTH == $period) {
            $description = static::t('Monthly');

        } elseif (static::PERIOD_YEAR == $period) {
            $description = static::t('Annually');
        }

        return $description;
    }

    /**
     * Get short description for TYPE_EVERY
     *
     * @return string
     */
    protected function getShortDescriptionEvery()
    {
        $number = $this->getNumber();

        switch ($this->getPeriod()) {
            case static::PERIOD_DAY:
                $description = 1 == $number
                    ? static::t('Daily')
                    : static::tp('xps.every__days', $number, ['number' => $number]);
                break;

            case static::PERIOD_WEEK:
                $description = 1 == $number
                    ? static::t('Weekly')
                    : static::tp('xps.every__weeks', $number, ['number' => $number]);
                break;

            case static::PERIOD_MONTH:
                $description = 1 == $number
                    ? static::t('Monthly')
                    : static::tp('xps.every__months', $number, ['number' => $number]);
                break;

            case static::PERIOD_YEAR:
                $description = 1 == $number
                    ? static::t('Annually')
                    : static::tp('xps.every__years', $number, ['number' => $number]);
                break;

            default:
                $description = '';
        }

        return $description;
    }

    /**
     * Get long description for TYPE_EACH
     *
     * @return string
     */
    protected function getLongDescriptionEach()
    {
        $description = '';
        $number = $this->getNumber();

        switch ($this->getPeriod()) {

            case static::PERIOD_WEEK:
                $description = $this->getLongDescriptionEachWeek();
                break;

            case static::PERIOD_MONTH:
                $description = $this->getReverse()
                    ? static::tp('xps.each__monthDay_reverse', $number, ['number' => $number])
                    : static::t('xps.each__monthDay', ['number' => $number]);
                break;

            case static::PERIOD_YEAR:
                $description = $this->getReverse()
                    ? static::tp('xps.each__yearDay_reverse', $number, ['number' => $number])
                    : static::t('xps.each__yearDay', ['number' => $number]);
                break;

            default:
        }

        return $description;
    }

    /**
     * getLongDescriptionEachWeek
     *
     * @return string
     */
    protected function getLongDescriptionEachWeek()
    {
        $number = $this->getReverse()
            ? 8 - $this->getNumber()
            : $this->getNumber();

        $labelName = '';

        switch ($number) {
            case 1:
                $labelName = 'xps.each_monday';
                break;

            case 2:
                $labelName = 'xps.each_tuesday';
                break;

            case 3:
                $labelName = 'xps.each_wednesday';
                break;

            case 4:
                $labelName = 'xps.each_thursday';
                break;

            case 5:
                $labelName = 'xps.each_friday';
                break;

            case 6:
                $labelName = 'xps.each_saturday';
                break;

            case 7:
                $labelName = 'xps.each_sunday';
                break;

            default:
                break;
        }

        return $labelName
            ? static::t($labelName)
            : '';
    }

    /**
     * Get long description for TYPE_EVERY
     *
     * @return string
     */
    protected function getLongDescriptionEvery()
    {
        $description = $this->getShortDescriptionEvery();

        return $description;
    }

    /**
     * Language label translation with pluralization short method
     *
     * @param string $name Label name
     * @param integer $number The number of items represented for the message
     * @param array $arguments Substitution arguments OPTIONAL
     * @param string $code Language code OPTIONAL
     *
     * @return string
     */
    protected static function tp($name, $number, array $arguments = [], $code = null)
    {
        return Translation::getInstance()->translatePlural($name, $number, $arguments, $code);
    }

    /**
     * Language label translation with pluralization short method
     *
     * @param string  $name      Label name
     * @param integer $number    The number of items represented for the message
     * @param array   $arguments Substitution arguments OPTIONAL
     * @param string  $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function tc($name, $number, array $arguments = [], $code = null)
    {
        return Translation::getInstance()->translateOrdinal($name, $number, $arguments, $code);
    }

    /**
     * Return array of field for plan form filed
     *
     * @return array
     */
    public function getPlan()
    {
        $type = $this->getType();
        $number = $this->getNumber();
        $numberSuffix = static::TYPE_EACH == $type
            ? static::t('xps.number_suffix', ['number' => $number])
            : static::t(' {{tp|период|периода|периодов|number}} в ', ['number' => $number]);

        return [
            'type'          => $type,
            'number'        => $number,
            'number_suffix' => $numberSuffix,
            'period'        => $this->getPeriod(),
            'reverse'       => $this->getReverse(),
            'description'   => $this->getPlanDescription(static::DESCRIPTION_LONG),
        ];
    }

    /**
     * Set plan fields
     *
     * @param array $data Plan data
     *
     * @return void
     */
    public function setPlan($data)
    {
        $this->setType($data['type']);
        $this->setNumber($data['number']);
        $this->setPeriod($data['period']);
        $this->setReverse($data['reverse']);
    }

}