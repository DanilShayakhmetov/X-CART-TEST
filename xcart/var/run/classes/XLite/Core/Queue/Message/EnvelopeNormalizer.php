<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Message;

use Assert\Assertion;

/**
 */
class EnvelopeNormalizer extends \Bernard\Normalizer\EnvelopeNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        Assertion::notEmpty($data);

        return parent::denormalize($data, $class, $format, $context);
    }
}
