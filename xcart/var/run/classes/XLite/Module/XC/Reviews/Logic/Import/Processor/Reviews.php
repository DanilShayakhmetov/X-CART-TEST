<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Logic\Import\Processor;

/**
 * Reviews import processor
 */
class Reviews extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Reviews imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review');
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'product'      => [
                static::COLUMN_IS_KEY => true,
                static::COLUMN_LENGTH => 32,
            ],
            'review'       => [],
            'response'     => [],
            'rating'       => [],
            'additionDate' => [
                static::COLUMN_IS_KEY => true,
            ],
            'responseDate' => [],
            'respondent'   => [],
            'reviewerName' => [
                static::COLUMN_IS_KEY => true,
            ],
            'email'        => [
                static::COLUMN_IS_KEY => true,
            ],
            'status'       => [],
            'useForMeta'   => [],
        ];
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages() +
               [
                   'REVIEW-PRODUCT-FMT'       => 'Unknown product is stated',
                   'REVIEW-RATING-FMT'        => 'Rating is in wrong format',
                   'REVIEW-DATE-FMT'          => 'Date is in wrong format',
                   'REVIEW-RESPONSE-DATE-FMT' => 'Response date is in wrong format',
                   'REVIEW-EMAIL-FMT'         => 'Email is in wrong format',
                   'REVIEW-STATUS-FMT'        => 'Unknown or missing status',
                   'REVIEW-USEFORMETA-FMT'    => 'Unknown or missing useForMeta flag',
               ];
    }

    /**
     * Verify 'product' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyProduct($value, array $column)
    {
        if ($this->verifyValueAsEmpty($value) || !$this->verifyValueAsProduct($value)) {
            $this->addError('REVIEW-PRODUCT-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'review' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyReview($value, array $column)
    {
    }

    /**
     * Verify 'response' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyResponse($value, array $column)
    {
    }

    /**
     * Verify 'rating' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyRating($value, array $column)
    {
        if ($this->verifyValueAsEmpty($value) || !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('REVIEW-RATING-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'additionDate' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyAdditionDate($value, array $column)
    {
        if ($this->verifyValueAsEmpty($value) || !$this->verifyValueAsDate($value)) {
            $this->addWarning('REVIEW-DATE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'responseDate' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyResponseDate($value, array $column)
    {
        if (
            !$this->verifyValueAsEmpty($value) && (
                !$this->verifyValueAsDate($value)
                && !(int)$value
            )
        ) {
            $this->addWarning('REVIEW-RESPONSE-DATE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'reviewerName' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyReviewerName($value, array $column)
    {
    }

    /**
     * Verify 'email' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyEmail($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsEmail($value)) {
            $this->addWarning('REVIEW-EMAIL-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'status' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyStatus($value, array $column)
    {
        $acceptableStatuses = ['Approved', 'Pending'];
        if ($this->verifyValueAsEmpty($value) || !$this->verifyValueAsSet($value, $acceptableStatuses)) {
            $this->addWarning('REVIEW-STATUS-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'useForMeta' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyUseForMeta($value, array $column)
    {
        if ($this->verifyValueAsEmpty($value) || !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('REVIEW-USEFORMETA-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'product' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\Product
     */
    protected function normalizeProductValue($value)
    {
        return $this->normalizeValueAsProduct($value);
    }

    /**
     * Normalize 'additionDate' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeAdditionDateValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'responseDate' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeResponseDateValue($value)
    {
        if ($this->normalizeValueAsDate($value)) {
            return $this->normalizeValueAsDate($value);
        } else {
            return (int)$value ?: null;
        }
    }

    /**
     * Normalize 'useForMeta' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeUseForMetaValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'status' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeStatusValue($value)
    {
        return 'Approved' === $value;
    }

    // }}}

    // {{{ Import

    /**
     * Import 'email' value
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $model  Review
     * @param string                                $value  Value
     * @param array                                 $column Column info
     *
     * @return void
     */
    protected function importEmailColumn(\XLite\Module\XC\Reviews\Model\Review $model, $value, array $column)
    {
        if ($value && $profile = $this->normalizeValueAsProfile($value)) {
            $model->setProfile($profile);
        }
    }

    /**
     * Import 'respondent' value
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $model  Review
     * @param string                                $value  Value
     * @param array                                 $column Column info
     *
     * @return void
     */
    protected function importRespondentColumn(\XLite\Module\XC\Reviews\Model\Review $model, $value, array $column)
    {
        if ($value && $profile = $this->normalizeValueAsProfile($value)) {
            $model->setRespondent($profile);
        }
    }

    // }}}
}
