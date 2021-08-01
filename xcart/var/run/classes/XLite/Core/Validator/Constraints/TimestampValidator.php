<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TimestampValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        $intValue = (int) $value;
        if ($intValue < 0 || $intValue > MAX_TIMESTAMP) {
            $this->context->buildViolation($constraint->message)
                ->setCode(Timestamp::INVALID_TIME_STAMP)
                ->addViolation();
        }
    }
}
